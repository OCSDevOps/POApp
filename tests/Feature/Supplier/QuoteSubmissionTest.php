<?php

namespace Tests\Feature\Supplier;

use App\Models\Company;
use App\Models\Item;
use App\Models\Project;
use App\Models\Rfq;
use App\Models\RfqItem;
use App\Models\RfqQuote;
use App\Models\RfqSupplier;
use App\Models\Supplier;
use App\Models\SupplierUser;
use App\Models\UnitOfMeasure;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class QuoteSubmissionTest extends TestCase
{
    use DatabaseTransactions;

    protected Company $company;
    protected Supplier $supplier;
    protected Supplier $otherSupplier;
    protected SupplierUser $supplierUser;
    protected SupplierUser $otherSupplierUser;
    protected Project $project;
    protected Item $item;
    protected UnitOfMeasure $uom;
    protected Rfq $rfq;
    protected RfqItem $rfqItem;
    protected RfqSupplier $rfqSupplier;

    protected function setUp(): void
    {
        parent::setUp();

        $this->company = Company::create([
            'name' => 'RFQ Test Company',
            'subdomain' => 'rfq-test',
            'status' => 1,
        ]);

        $this->supplier = Supplier::withoutGlobalScopes()->create([
            'sup_name' => 'Assigned Supplier',
            'sup_email' => 'assigned-supplier@test.com',
            'sup_status' => 1,
            'company_id' => $this->company->id,
        ]);

        $this->otherSupplier = Supplier::withoutGlobalScopes()->create([
            'sup_name' => 'Unassigned Supplier',
            'sup_email' => 'unassigned-supplier@test.com',
            'sup_status' => 1,
            'company_id' => $this->company->id,
        ]);

        $this->supplierUser = SupplierUser::withoutGlobalScopes()->create([
            'name' => 'Assigned Supplier User',
            'email' => 'assigned-user@test.com',
            'password' => Hash::make('password'),
            'supplier_id' => $this->supplier->sup_id,
            'company_id' => $this->company->id,
            'status' => 1,
            'email_verified_at' => now(),
        ]);

        $this->otherSupplierUser = SupplierUser::withoutGlobalScopes()->create([
            'name' => 'Unassigned Supplier User',
            'email' => 'unassigned-user@test.com',
            'password' => Hash::make('password'),
            'supplier_id' => $this->otherSupplier->sup_id,
            'company_id' => $this->company->id,
            'status' => 1,
            'email_verified_at' => now(),
        ]);

        $this->project = Project::create([
            'proj_name' => 'RFQ Project',
            'proj_status' => 1,
            'company_id' => $this->company->id,
        ]);

        $this->uom = UnitOfMeasure::create([
            'uom_name' => 'Each',
            'uom_status' => 1,
        ]);

        $this->item = Item::create([
            'item_code' => 'RFQ-ITEM-001',
            'item_name' => 'RFQ Fixture Item',
            'item_unit_ms' => $this->uom->uom_id,
            'item_status' => 1,
            'company_id' => $this->company->id,
        ]);

        session(['company_id' => $this->company->id]);

        $this->rfq = Rfq::create([
            'rfq_no' => 'RFQ000777',
            'rfq_project_id' => $this->project->proj_id,
            'rfq_title' => 'Fixture RFQ',
            'rfq_due_date' => now()->addWeek()->toDateString(),
            'rfq_status' => Rfq::STATUS_SENT,
            'rfq_created_by' => 1,
            'rfq_created_at' => now(),
        ]);

        $this->rfqItem = RfqItem::create([
            'rfqi_rfq_id' => $this->rfq->rfq_id,
            'rfqi_item_id' => $this->item->item_id,
            'rfqi_uom_id' => $this->uom->uom_id,
            'project_id' => $this->project->proj_id,
            'company_id' => $this->company->id,
            'rfqi_quantity' => 25,
            'rfqi_target_price' => 95.00,
            'rfqi_created_at' => now(),
        ]);

        $this->rfqSupplier = RfqSupplier::create([
            'rfqs_rfq_id' => $this->rfq->rfq_id,
            'rfqs_supplier_id' => $this->supplier->sup_id,
            'company_id' => $this->company->id,
            'rfqs_status' => RfqSupplier::STATUS_SENT,
            'rfqs_created_at' => now(),
        ]);
    }

    /** @test */
    public function assigned_supplier_can_submit_a_quote_and_advance_the_rfq_status()
    {
        $this->actingAs($this->supplierUser, 'supplier');

        $this->get(route('supplier.rfq.show', $this->rfq->rfq_id))
            ->assertOk()
            ->assertSee($this->rfq->rfq_no)
            ->assertSee($this->item->item_name);

        $response = $this->post(route('supplier.rfq.quote', $this->rfq->rfq_id), [
            'quotes' => [
                [
                    'rfq_item_id' => $this->rfqItem->rfqi_id,
                    'price' => 92.50,
                    'lead_time_days' => 7,
                ],
            ],
        ]);

        $response->assertRedirect(route('supplier.rfq.index'));
        $response->assertSessionHas('status', 'Quote submitted successfully.');

        $this->assertDatabaseHas('rfq_quotes', [
            'rfqq_rfqs_id' => $this->rfqSupplier->rfqs_id,
            'rfqq_rfqi_id' => $this->rfqItem->rfqi_id,
            'company_id' => $this->company->id,
            'rfqq_quoted_price' => 92.50,
            'rfqq_lead_time_days' => 7,
        ]);

        $this->assertSame(RfqSupplier::STATUS_RESPONDED, (int) $this->rfqSupplier->fresh()->rfqs_status);
        $this->assertSame(Rfq::STATUS_RECEIVED, (int) $this->rfq->fresh()->rfq_status);
    }

    /** @test */
    public function quote_resubmission_updates_the_existing_row_instead_of_duplicating_it()
    {
        $this->actingAs($this->supplierUser, 'supplier');

        $this->post(route('supplier.rfq.quote', $this->rfq->rfq_id), [
            'quotes' => [
                [
                    'rfq_item_id' => $this->rfqItem->rfqi_id,
                    'price' => 92.50,
                    'lead_time_days' => 7,
                ],
            ],
        ])->assertRedirect();

        $this->post(route('supplier.rfq.quote', $this->rfq->rfq_id), [
            'quotes' => [
                [
                    'rfq_item_id' => $this->rfqItem->rfqi_id,
                    'price' => 89.75,
                    'lead_time_days' => 5,
                ],
            ],
        ])->assertRedirect();

        $this->assertSame(1, RfqQuote::where('rfqq_rfqs_id', $this->rfqSupplier->rfqs_id)->count());

        $quote = RfqQuote::where('rfqq_rfqs_id', $this->rfqSupplier->rfqs_id)->firstOrFail();
        $this->assertSame('89.75', (string) $quote->rfqq_quoted_price);
        $this->assertSame(5, (int) $quote->rfqq_lead_time_days);
    }

    /** @test */
    public function suppliers_cannot_view_or_quote_rfqs_that_are_not_assigned_to_them()
    {
        $this->actingAs($this->otherSupplierUser, 'supplier');

        $this->get(route('supplier.rfq.show', $this->rfq->rfq_id))->assertNotFound();

        $this->post(route('supplier.rfq.quote', $this->rfq->rfq_id), [
            'quotes' => [
                [
                    'rfq_item_id' => $this->rfqItem->rfqi_id,
                    'price' => 101.00,
                ],
            ],
        ])->assertNotFound();
    }
}
