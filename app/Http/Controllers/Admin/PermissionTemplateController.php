<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PermissionTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class PermissionTemplateController extends Controller
{
    public function index()
    {
        $templates = PermissionTemplate::where('status', 1)->orderBy('pt_id', 'desc')->get();

        return view('admin.permissions.index', compact('templates'));
    }

    public function store(Request $request)
    {
        $data = $this->validateRequest($request);

        PermissionTemplate::create(array_merge($data, [
            'pt_template_users' => json_encode($request->input('pt_template_users', [])),
            'created_date' => Carbon::now(),
            'status' => 1,
        ]));

        return redirect()->route('admin.permissions.index')->with('success', 'Permission template created.');
    }

    public function update(Request $request, PermissionTemplate $permission)
    {
        $data = $this->validateRequest($request, $permission->pt_id);

        $permission->update(array_merge($data, [
            'pt_template_users' => json_encode($request->input('pt_template_users', [])),
        ]));

        return redirect()->route('admin.permissions.index')->with('success', 'Permission template updated.');
    }

    public function destroy(PermissionTemplate $permission)
    {
        $permission->update(['status' => 0]);

        return redirect()->route('admin.permissions.index')->with('success', 'Permission template archived.');
    }

    private function validateRequest(Request $request, $id = null): array
    {
        return $request->validate([
            'pt_template_name' => 'required|string|max:191',
            'pt_t_porder' => 'required|integer|min:0',
            'pt_t_rorder' => 'required|integer|min:0',
            'pt_t_rcorder' => 'required|integer|min:0',
            'pt_t_rfq' => 'required|integer|min:0',
            'pt_m_item' => 'required|integer|min:0',
            'pt_m_uom' => 'required|integer|min:0',
            'pt_m_costcode' => 'required|integer|min:0',
            'pt_m_projects' => 'required|integer|min:0',
            'pt_m_suppliers' => 'required|integer|min:0',
            'pt_m_taxgroup' => 'required|integer|min:0',
            'pt_m_budget' => 'required|integer|min:0',
            'pt_m_email' => 'required|integer|min:0',
            'pt_i_item' => 'required|integer|min:0',
            'pt_i_itemp' => 'required|integer|min:0',
            'pt_i_supplierc' => 'required|integer|min:0',
            'pt_e_eq' => 'required|integer|min:0',
            'pt_e_eqm' => 'required|integer|min:0',
            'pt_e_checklist' => 'required|integer|min:0',
            'pt_a_user' => 'required|integer|min:0',
            'pt_a_permissions' => 'required|integer|min:0',
            'pt_a_cinfo' => 'required|integer|min:0',
            'pt_a_procore' => 'required|integer|min:0',
        ]);
    }
}
