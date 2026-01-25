<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CompanyController extends Controller
{
    public function index()
    {
        $company = Company::first();
        $smtp = $this->settingsMap([
            'smtp_host', 'smtp_username', 'smtp_password', 'smtp_port',
            'smtp_encryption', 'smtp_from_address', 'smtp_from_name', 'smtp_cc', 'smtp_bcc', 'smtp_mail_body',
        ]);

        return view('admin.company.index', compact('company', 'smtp'));
    }

    public function updateCompany(Request $request)
    {
        $request->validate([
            'company_name' => 'required|string|max:255',
            'company_address' => 'required|string|max:1000',
            'company_logo' => 'nullable|image|max:5120',
            'app_logo_one' => 'nullable|image|max:5120',
            'app_logo_two' => 'nullable|image|max:5120',
        ]);

        $company = Company::firstOrNew(['company_id' => 1]);
        $company->company_name = $request->company_name;
        $company->company_address = $request->company_address;
        $company->company_status = $company->company_status ?? 1;
        $company->company_createdate = $company->company_createdate ?? now();

        foreach (['company_logo', 'app_logo_one', 'app_logo_two'] as $logoField) {
            if ($request->hasFile($logoField)) {
                $path = $request->file($logoField)->store('company', 'public');
                $company->{$logoField} = $path;
            }
        }

        $company->save();

        return redirect()->route('admin.company.index')->with('success', 'Company profile updated.');
    }

    public function updateSmtp(Request $request)
    {
        $request->validate([
            'smtp_host' => 'required|string|max:255',
            'smtp_username' => 'required|string|max:255',
            'smtp_password' => 'required|string|max:255',
            'smtp_port' => 'required|string|max:50',
            'smtp_encryption' => 'required|string|max:50',
            'smtp_from_address' => 'required|email',
            'smtp_from_name' => 'required|string|max:255',
            'smtp_cc' => 'nullable|string|max:500',
            'smtp_bcc' => 'nullable|string|max:500',
            'smtp_mail_body' => 'nullable|string',
        ]);

        $this->persistSettings($request->only([
            'smtp_host', 'smtp_username', 'smtp_password', 'smtp_port',
            'smtp_encryption', 'smtp_from_address', 'smtp_from_name', 'smtp_cc', 'smtp_bcc', 'smtp_mail_body',
        ]));

        return redirect()->route('admin.company.index')->with('success', 'SMTP settings updated.');
    }

    protected function settingsMap(array $keys): array
    {
        $settings = Setting::whereIn('key', $keys)->pluck('value', 'key')->toArray();
        $defaults = array_fill_keys($keys, '');
        return array_merge($defaults, $settings);
    }

    protected function persistSettings(array $values): void
    {
        foreach ($values as $key => $value) {
            Setting::updateOrCreate(
                ['key' => $key],
                [
                    'display_name' => $key,
                    'value' => $value,
                    'type' => 'text',
                    'order' => 1,
                    'group' => 'company',
                ]
            );
        }
    }
}
