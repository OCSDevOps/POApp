<?php

namespace App\Services\Ai;

use App\Models\AiSetting;
use App\Models\Item;
use App\Models\SupplierCatalog;
use App\Models\TakeoffDrawing;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class AiTakeoffService
{
    protected AiSetting $settings;

    public function __construct(AiSetting $settings)
    {
        $this->settings = $settings;
    }

    /**
     * Process a drawing through OpenAI Vision API and extract materials.
     */
    public function processDrawing(TakeoffDrawing $drawing): array
    {
        $filePath = $drawing->tdr_path;

        if (!Storage::disk('public')->exists($filePath)) {
            throw new \RuntimeException("Drawing file not found: {$filePath}");
        }

        $fileContent = Storage::disk('public')->get($filePath);
        $base64 = base64_encode($fileContent);
        $mimeType = $drawing->tdr_mime;

        // For PDFs, use the generic media type
        if ($drawing->isPdf()) {
            $mimeType = 'application/pdf';
        }

        $dataUrl = "data:{$mimeType};base64,{$base64}";

        $response = $this->callOpenAiVision($dataUrl);

        if (!$response['success']) {
            throw new \RuntimeException($response['error'] ?? 'AI processing failed');
        }

        $rawContent = $response['content'];

        // Parse the JSON from AI response
        $materials = $this->parseAiResponse($rawContent);

        // Match materials to existing items in database
        $matched = $this->matchItemsToDatabase($materials);

        return [
            'materials' => $matched,
            'raw_response' => $rawContent,
        ];
    }

    /**
     * Call OpenAI Vision API.
     */
    protected function callOpenAiVision(string $imageDataUrl): array
    {
        $apiKey = $this->settings->decrypted_api_key;
        if (empty($apiKey)) {
            return ['success' => false, 'error' => 'API key not configured'];
        }

        $model = $this->settings->model_name ?: 'gpt-4o';

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type' => 'application/json',
            ])->timeout(120)->post('https://api.openai.com/v1/chat/completions', [
                'model' => $model,
                'max_tokens' => $this->settings->max_tokens ?: 4096,
                'temperature' => (float) ($this->settings->temperature ?: 0.2),
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => $this->buildSystemPrompt(),
                    ],
                    [
                        'role' => 'user',
                        'content' => [
                            [
                                'type' => 'text',
                                'text' => 'Analyze this construction drawing and extract all materials, quantities, and units of measure. Return ONLY valid JSON.',
                            ],
                            [
                                'type' => 'image_url',
                                'image_url' => [
                                    'url' => $imageDataUrl,
                                    'detail' => 'high',
                                ],
                            ],
                        ],
                    ],
                ],
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $content = $data['choices'][0]['message']['content'] ?? '';
                return ['success' => true, 'content' => $content];
            }

            $errorBody = $response->json();
            $errorMsg = $errorBody['error']['message'] ?? $response->body();
            return ['success' => false, 'error' => "OpenAI API error ({$response->status()}): {$errorMsg}"];
        } catch (\Exception $e) {
            Log::error('OpenAI Vision API call failed', [
                'error' => $e->getMessage(),
            ]);
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Build the system prompt for material extraction.
     */
    protected function buildSystemPrompt(): string
    {
        return <<<'PROMPT'
You are an expert construction material takeoff specialist. Analyze the provided construction drawing or blueprint and extract ALL materials, quantities, and units of measure you can identify.

Return ONLY a valid JSON array with objects containing these fields:
- "material_name": descriptive name of the material (e.g., "2x4 SPF Lumber", "1/2\" Drywall", "#4 Rebar")
- "quantity": numeric quantity needed (number, not string)
- "unit": unit of measure abbreviation (e.g., "LF" for linear feet, "SF" for square feet, "EA" for each, "CY" for cubic yards, "BF" for board feet, "SHT" for sheets, "TON", "GAL", "BOX", "ROLL")
- "confidence": your confidence level 0-100 (integer)
- "notes": any relevant specifications, dimensions, or notes (string, can be empty)
- "category": material category (e.g., "Lumber", "Concrete", "Electrical", "Plumbing", "HVAC", "Drywall", "Roofing", "Insulation", "Hardware", "Finishes", "Doors & Windows")

Focus on identifying:
- Structural materials (lumber, steel, concrete, rebar, masonry)
- Finishing materials (drywall, paint, flooring, trim, ceiling tiles)
- MEP materials (pipes, wires, conduit, fixtures, ductwork)
- Envelope materials (roofing, siding, insulation, vapor barriers)
- Doors, windows, and hardware
- Fasteners and connectors

If you cannot identify specific materials or quantities from the drawing, still list what you can see with lower confidence scores. Always provide your best estimate.

Return ONLY the JSON array, no additional text or markdown formatting.
PROMPT;
    }

    /**
     * Parse AI response text into structured materials array.
     */
    protected function parseAiResponse(string $content): array
    {
        // Strip markdown code fences if present
        $content = trim($content);
        if (str_starts_with($content, '```')) {
            $content = preg_replace('/^```(?:json)?\s*/', '', $content);
            $content = preg_replace('/\s*```$/', '', $content);
        }

        $materials = json_decode($content, true);

        if (!is_array($materials)) {
            Log::warning('AI response was not valid JSON', ['content' => substr($content, 0, 500)]);
            return [];
        }

        // Validate and normalize each material entry
        $valid = [];
        foreach ($materials as $mat) {
            if (empty($mat['material_name']) || !isset($mat['quantity'])) {
                continue;
            }
            $valid[] = [
                'material_name' => (string) ($mat['material_name'] ?? ''),
                'quantity' => (float) ($mat['quantity'] ?? 0),
                'unit' => (string) ($mat['unit'] ?? 'EA'),
                'confidence' => (int) min(100, max(0, $mat['confidence'] ?? 50)),
                'notes' => (string) ($mat['notes'] ?? ''),
                'category' => (string) ($mat['category'] ?? ''),
            ];
        }

        return $valid;
    }

    /**
     * Match AI-extracted materials to existing items in the database.
     */
    public function matchItemsToDatabase(array $materials): array
    {
        $items = Item::active()->get(['item_id', 'item_code', 'item_name', 'item_unit_ms']);

        foreach ($materials as &$mat) {
            $mat['matched_item_code'] = null;
            $mat['matched_item_name'] = null;
            $mat['unit_price'] = 0;
            $mat['uom_id'] = null;

            $bestMatch = null;
            $bestScore = 0;

            $searchName = strtolower($mat['material_name']);

            foreach ($items as $item) {
                $itemName = strtolower($item->item_name);

                // Exact match
                if ($itemName === $searchName) {
                    $bestMatch = $item;
                    $bestScore = 100;
                    break;
                }

                // Contains match
                if (str_contains($itemName, $searchName) || str_contains($searchName, $itemName)) {
                    $score = similar_text($searchName, $itemName) * 2;
                    if ($score > $bestScore) {
                        $bestScore = $score;
                        $bestMatch = $item;
                    }
                    continue;
                }

                // Fuzzy match using similar_text
                similar_text($searchName, $itemName, $percent);
                if ($percent > 60 && $percent > $bestScore) {
                    $bestScore = $percent;
                    $bestMatch = $item;
                }
            }

            if ($bestMatch && $bestScore >= 50) {
                $mat['matched_item_code'] = $bestMatch->item_code;
                $mat['matched_item_name'] = $bestMatch->item_name;
                $mat['uom_id'] = $bestMatch->item_unit_ms;

                // Get best supplier price
                $catalog = SupplierCatalog::getBestPrice($bestMatch->item_code);
                if ($catalog) {
                    $mat['unit_price'] = (float) $catalog->supcat_price;
                }
            }
        }
        unset($mat);

        return $materials;
    }

    /**
     * Test the OpenAI API connection with a simple request.
     */
    public function testConnection(): array
    {
        $apiKey = $this->settings->decrypted_api_key;
        if (empty($apiKey)) {
            return ['success' => false, 'message' => 'API key is not configured.'];
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
            ])->timeout(10)->get('https://api.openai.com/v1/models');

            if ($response->successful()) {
                $models = collect($response->json('data', []))->pluck('id');
                $hasVision = $models->contains($this->settings->model_name ?: 'gpt-4o');
                return [
                    'success' => true,
                    'message' => 'Connection successful! ' . ($hasVision ? 'Selected model is available.' : 'Warning: Selected model not found in your account.'),
                ];
            }

            return ['success' => false, 'message' => 'API returned error: ' . $response->status()];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'Connection failed: ' . $e->getMessage()];
        }
    }
}
