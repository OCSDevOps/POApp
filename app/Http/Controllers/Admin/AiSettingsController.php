<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AiSetting;
use App\Services\Ai\AiTakeoffService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AiSettingsController extends Controller
{
    public function index()
    {
        $settings = AiSetting::where('company_id', session('company_id'))->first();

        return view('admin.ai-settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'api_key' => 'nullable|string|max:500',
            'model_name' => 'required|string|in:gpt-4o,gpt-4o-mini',
            'max_tokens' => 'required|integer|min:1000|max:16000',
            'temperature' => 'required|numeric|min:0|max:1',
            'is_active' => 'required|boolean',
        ]);

        $companyId = session('company_id');
        $settings = AiSetting::where('company_id', $companyId)->first();

        $data = [
            'ai_provider' => 'openai',
            'model_name' => $request->model_name,
            'max_tokens' => $request->max_tokens,
            'temperature' => $request->temperature,
            'is_active' => $request->is_active,
        ];

        // Only update API key if a new one was provided
        if ($request->filled('api_key')) {
            $data['api_key'] = encrypt($request->api_key);
        }

        if ($settings) {
            $data['ai_modifyby'] = Auth::id();
            $data['ai_modifydate'] = now();
            $settings->update($data);
        } else {
            $data['company_id'] = $companyId;
            $data['ai_createby'] = Auth::id();
            $data['ai_createdate'] = now();
            if (!isset($data['api_key'])) {
                $data['api_key'] = null;
            }
            $settings = AiSetting::create($data);
        }

        return back()->with('success', 'AI settings saved successfully.');
    }

    public function testConnection()
    {
        $settings = AiSetting::where('company_id', session('company_id'))->first();

        if (!$settings || empty($settings->api_key)) {
            return response()->json([
                'success' => false,
                'message' => 'Please save your API key first before testing the connection.',
            ]);
        }

        $service = new AiTakeoffService($settings);
        $result = $service->testConnection();

        return response()->json($result);
    }
}
