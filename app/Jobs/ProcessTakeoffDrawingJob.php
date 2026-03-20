<?php

namespace App\Jobs;

use App\Models\AiSetting;
use App\Models\Takeoff;
use App\Models\TakeoffDrawing;
use App\Models\TakeoffItem;
use App\Models\UnitOfMeasure;
use App\Services\Ai\AiTakeoffService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessTakeoffDrawingJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 2;
    public int $timeout = 120;

    public function __construct(
        public int $drawingId,
        public int $companyId
    ) {
        $this->onQueue('takeoffs');
    }

    public function handle(): void
    {
        $drawing = TakeoffDrawing::find($this->drawingId);
        if (!$drawing || $drawing->tdr_status !== 1) {
            return;
        }

        $drawing->update(['tdr_ai_status' => 'processing']);

        $aiSettings = AiSetting::withoutGlobalScopes()
            ->where('company_id', $this->companyId)
            ->where('is_active', 1)
            ->first();

        if (!$aiSettings) {
            $drawing->update([
                'tdr_ai_status' => 'failed',
                'tdr_ai_error' => 'AI settings not configured or inactive.',
            ]);
            return;
        }

        try {
            $service = new AiTakeoffService($aiSettings);
            $result = $service->processDrawing($drawing);

            $drawing->update([
                'tdr_ai_status' => 'completed',
                'tdr_ai_processed_at' => now(),
                'tdr_ai_raw_response' => $result['raw_response'] ?? null,
                'tdr_ai_error' => null,
            ]);

            // Create takeoff line items from AI results
            $takeoff = $drawing->takeoff;
            foreach ($result['materials'] as $material) {
                // Find UOM by abbreviation
                $uomId = $material['uom_id'] ?? null;
                if (!$uomId && !empty($material['unit'])) {
                    $uom = UnitOfMeasure::where('uom_name', 'LIKE', $material['unit'])->first();
                    $uomId = $uom->uom_id ?? null;
                }

                $qty = (float) ($material['quantity'] ?? 0);
                $price = (float) ($material['unit_price'] ?? 0);

                TakeoffItem::create([
                    'tod_takeoff_id' => $takeoff->to_id,
                    'tod_item_code' => $material['matched_item_code'] ?? null,
                    'tod_description' => $material['material_name'],
                    'tod_quantity' => $qty,
                    'tod_uom_id' => $uomId,
                    'tod_unit_price' => $price,
                    'tod_subtotal' => round($qty * $price, 2),
                    'tod_cost_code_id' => null,
                    'tod_source' => 'ai',
                    'tod_ai_confidence' => $material['confidence'] ?? null,
                    'tod_notes' => trim(($material['category'] ?? '') . ($material['notes'] ? ': ' . $material['notes'] : '')) ?: null,
                    'tod_status' => 1,
                    'tod_createdate' => now(),
                    'company_id' => $this->companyId,
                ]);
            }

            // Update takeoff status to review and recalculate
            if ($takeoff->to_status === Takeoff::STATUS_PROCESSING) {
                // Check if all drawings are processed
                $pendingDrawings = $takeoff->activeDrawings()
                    ->whereIn('tdr_ai_status', ['pending', 'processing'])
                    ->count();

                if ($pendingDrawings === 0) {
                    $takeoff->to_status = Takeoff::STATUS_REVIEW;
                }
            }
            $takeoff->recalculateTotals();

        } catch (\Throwable $e) {
            $drawing->update([
                'tdr_ai_status' => 'failed',
                'tdr_ai_error' => substr($e->getMessage(), 0, 1000),
            ]);

            Log::error('Takeoff drawing AI processing failed', [
                'drawing_id' => $this->drawingId,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
