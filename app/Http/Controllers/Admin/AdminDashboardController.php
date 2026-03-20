<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\PurchaseOrder;
use App\Models\Project;
use App\Models\User;

class AdminDashboardController extends Controller
{
    /**
     * Display the admin dashboard.
     */
    public function index()
    {
        $user = Auth::user();
        $data = [];

        // Get PO statistics
        $data['total_po'] = $this->getTotalPO();
        $data['pending_po'] = $this->getPendingPO();
        $data['submitted_po'] = $this->getSubmittedPO();
        $data['rte_po'] = $this->getRTEPO();
        
        // Get project list
        $data['proj_list'] = Project::active()->orderByName()->get();
        
        // Get receive statistics
        $data['total_receive'] = $this->getTotalReceive();
        $data['partially_received'] = $this->getPartiallyReceive();
        $data['fully_received'] = $this->getFullyReceive();
        $data['not_received'] = $this->getNotReceive();

        // Supplier-specific data (u_type == 4)
        if ($user->u_type == 4) {
            $companyId = session('company_id');

            // Try to find supplier linked to this user
            $supplierInfo = DB::table('supplier_master')
                ->where('sup_status', 1)
                ->where('company_id', $companyId)
                ->first();

            if ($supplierInfo) {
                $supplierId = $supplierInfo->sup_id;

                $data['total_rfqs'] = 0;
                $data['waiting_rfqs'] = 0;

                $data['total_items'] = DB::table('supplier_catalog_tab')
                    ->where('supcat_supplier', $supplierId)
                    ->where('supcat_status', 1)
                    ->count();

                $currentDate = date('Y-m-d');
                $checkDate = date('Y-m-d', strtotime($currentDate . ' +7 day'));

                $data['expiring_items'] = DB::table('supplier_catalog_tab')
                    ->where('supcat_supplier', $supplierId)
                    ->where('supcat_lastdate', '<=', $checkDate)
                    ->where('supcat_status', 1)
                    ->count();
            }
        }

        $data['u_details'] = $this->getUserDetails($user->id);

        return view('admin.main', $data);
    }

    /**
     * Get PO data for chart (AJAX).
     */
    public function getPODataForChart(Request $request)
    {
        $projId = $request->get('proj_id', 0);

        $data = [
            'total_po' => $this->getTotalPO($projId),
            'pending_po' => $this->getPendingPO($projId),
            'submitted_po' => $this->getSubmittedPO($projId),
            'rte_po' => $this->getRTEPO($projId),
            'integration_sync_po' => $this->getIntegrationSyncPO($projId),
            'integration_pending' => $this->getIntegrationPendingPO($projId),
            'partially_received' => $this->getPartiallyReceive($projId),
            'fully_received' => $this->getFullyReceive($projId),
            'not_received' => $this->getNotReceive($projId),
            'material_po' => $this->getPOByType('Material PO', $projId),
            'rental_po' => $this->getPOByType('Rental PO', $projId),
        ];

        return response()->json($data);
    }

    /**
     * Logout user.
     */
    public function logout()
    {
        Auth::logout();
        session()->flush();
        
        return redirect()->route('login');
    }

    /**
     * Get user details.
     */
    private function getUserDetails($uid)
    {
        return DB::table('users')
            ->leftJoin('master_user_type', 'master_user_type.mu_id', '=', 'users.u_type')
            ->where('users.id', $uid)
            ->select('users.*', 'master_user_type.mu_name')
            ->first();
    }

    /**
     * Get total PO count.
     */
    private function getTotalPO($projId = 0)
    {
        $query = PurchaseOrder::whereNotNull('porder_description');

        if ($projId != 0) {
            $query->where('porder_project_ms', $projId);
        }

        return $query->count();
    }

    /**
     * Get pending PO count (active POs).
     */
    private function getPendingPO($projId = 0)
    {
        $query = PurchaseOrder::whereNotNull('porder_description')
            ->where('porder_status', 1);
        
        if ($projId != 0) {
            $query->where('porder_project_ms', $projId);
        }
        
        return $query->count();
    }

    /**
     * Get submitted PO count.
     */
    private function getSubmittedPO($projId = 0)
    {
        $query = PurchaseOrder::whereNotNull('porder_description')
            ->where('porder_status', 1);
        
        if ($projId != 0) {
            $query->where('porder_project_ms', $projId);
        }
        
        return $query->count();
    }

    /**
     * Get RTE PO count.
     */
    private function getRTEPO($projId = 0)
    {
        $query = PurchaseOrder::whereNotNull('porder_description')
            ->where('integration_status', 'rte');
        
        if ($projId != 0) {
            $query->where('porder_project_ms', $projId);
        }
        
        return $query->count();
    }

    /**
     * Get total receive count.
     */
    private function getTotalReceive()
    {
        $companyId = session('company_id');
        return DB::table('receive_order_master')
            ->where('company_id', $companyId)
            ->distinct('rorder_porder_ms')
            ->count('rorder_porder_ms');
    }

    /**
     * Get partially received count.
     */
    private function getPartiallyReceive($projId = 0)
    {
        $query = PurchaseOrder::where('porder_delivery_status', '2');
        
        if ($projId != 0) {
            $query->where('porder_project_ms', $projId);
        }
        
        return $query->count();
    }

    /**
     * Get integration sync PO count.
     */
    private function getIntegrationSyncPO($projId = 0)
    {
        $query = PurchaseOrder::where('integration_status', 'synced');
        
        if ($projId != 0) {
            $query->where('porder_project_ms', $projId);
        }
        
        return $query->count();
    }

    /**
     * Get PO by type count.
     */
    private function getPOByType($type, $projId = 0)
    {
        $query = PurchaseOrder::where('porder_description', $type);
        
        if ($projId != 0) {
            $query->where('porder_project_ms', $projId);
        }
        
        return $query->count();
    }

    /**
     * Get integration pending PO count.
     */
    private function getIntegrationPendingPO($projId = 0)
    {
        $query = PurchaseOrder::where('integration_status', 'pending');
        
        if ($projId != 0) {
            $query->where('porder_project_ms', $projId);
        }
        
        return $query->count();
    }

    /**
     * Get fully received count.
     */
    private function getFullyReceive($projId = 0)
    {
        $query = PurchaseOrder::where('porder_delivery_status', '1');
        
        if ($projId != 0) {
            $query->where('porder_project_ms', $projId);
        }
        
        return $query->count();
    }

    /**
     * Get not received count.
     */
    private function getNotReceive($projId = 0)
    {
        $query = PurchaseOrder::where('porder_delivery_status', '0');
        
        if ($projId != 0) {
            $query->where('porder_project_ms', $projId);
        }
        
        return $query->count();
    }
}
