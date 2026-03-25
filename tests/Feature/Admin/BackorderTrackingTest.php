<?php

namespace Tests\Feature\Admin;

use App\Models\Company;
use App\Models\CostCode;
use App\Models\Item;
use App\Models\Project;
use App\Models\PurchaseOrderItem;
use App\Models\Supplier;
use App\Models\User;
use App\Services\PurchaseOrderService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class BackorderTrackingTest extends TestCase
{
    use DatabaseTransactions;

    protected Company $company;
    protected User $user;
    protected Project $project;
    protected Supplier $supplier;
    protected CostCode $costCode;
    protected Item $item;

    protected function setUp(): void
    {
        parent::setUp();

        $this->company = Company::create([
            'name' => 'Backorder Test Company',
            'subdomain' => 'backorder-test',
            'status' => 1,
        ]);

        $this->user = User::create([
            'name' => 'Backorder Admin',
            'email' => 'backorder-admin@test.com',
            'password' => bcrypt('password'),
            'u_type' => 1,
            'u_status' => 1,
            'company_id' => $this->company->id,
        ]);

        $this->project = Project::create([
            'proj_name' => 'Backorder Project',
            'proj_status' => 1,
            'company_id' => $this->company->id,
        ]);

        $this->supplier = Supplier::create([
            'sup_name' => 'Backorder Supplier',
            'sup_email' => 'supplier-backorder@test.com',
            'sup_status' => 1,
            'company_id' => $this->company->id,
        ]);

        $this->costCode = CostCode::create([
            'cc_no' => '2-03-30',
            'cc_description' => 'Concrete Supply',
            'cc_parent_code' => '2',
            'cc_category_code' => '03',
            'cc_subcategory_code' => '30',
            'cc_level' => 3,
            'cc_full_code' => '2-03-30',
            'cc_status' => 1,
            'company_id' => $this->company->id,
        ]);

        $this->item = Item::create([
            'item_code' => 'CONC-BACKORDER',
            'item_name' => 'Concrete Batch',
            'item_ccode_ms' => $this->costCode->cc_id,
            'item_status' => 1,
            'company_id' => $this->company->id,
        ]);

        config(['app.budget_constraints_enabled' => false]);
        Notification::fake();

        session(['company_id' => $this->company->id]);
        $this->actingAs($this->user);
    }

    /** @test */
    public function it_tracks_partial_receipts_as_backorders_and_exposes_them_on_the_dashboard()
    {
        $poService = app(PurchaseOrderService::class);

        $po = $poService->createPurchaseOrder([
            'project_id' => $this->project->proj_id,
            'supplier_id' => $this->supplier->sup_id,
            'description' => 'Backorder validation PO',
        ], [
            [
                'item_code' => $this->item->item_code,
                'quantity' => 10,
                'unit_price' => 25.00,
                'tax_rate' => 0,
            ],
        ]);

        $poService->createReceiveOrder($po->porder_id, 'RCV-BACKORDER', [
            [
                'item_code' => $this->item->item_code,
                'quantity' => 4,
            ],
        ], now()->toDateString());

        $poItem = PurchaseOrderItem::where('po_detail_porder_ms', $po->porder_id)->firstOrFail();

        $this->assertSame('6.0000', (string) $poItem->backordered_qty);
        $this->assertSame(1, (int) $poItem->backorder_status);
        $this->assertSame(2, (int) $po->fresh()->porder_delivery_status);

        $response = $this->get(route('admin.backorders.index'));

        $response->assertOk();
        $response->assertSee($po->porder_no);
        $response->assertSee($this->supplier->sup_name);
        $response->assertSee($this->project->proj_name);
        $response->assertSee($this->item->item_code);
    }
}
