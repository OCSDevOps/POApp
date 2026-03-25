<?php

namespace Tests\Feature\Supplier;

use App\Models\Company;
use App\Models\Item;
use App\Models\ItemPricing;
use App\Models\Project;
use App\Models\Supplier;
use App\Models\SupplierUser;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ItemPricingTest extends TestCase
{
    use DatabaseTransactions;

    protected Company $company;
    protected Supplier $supplier;
    protected Supplier $otherSupplier;
    protected SupplierUser $supplierUser;
    protected Item $item;
    protected Project $project;

    protected function setUp(): void
    {
        parent::setUp();

        $this->company = Company::create([
            'name' => 'Pricing Test Company',
            'subdomain' => 'pricing-test',
            'status' => 1,
        ]);

        $this->supplier = Supplier::withoutGlobalScopes()->create([
            'sup_name' => 'Pricing Supplier',
            'sup_email' => 'pricing-supplier@test.com',
            'sup_status' => 1,
            'company_id' => $this->company->id,
        ]);

        $this->otherSupplier = Supplier::withoutGlobalScopes()->create([
            'sup_name' => 'Other Supplier',
            'sup_email' => 'other-supplier@test.com',
            'sup_status' => 1,
            'company_id' => $this->company->id,
        ]);

        $this->supplierUser = SupplierUser::withoutGlobalScopes()->create([
            'name' => 'Pricing User',
            'email' => 'pricing-user@test.com',
            'password' => Hash::make('password'),
            'supplier_id' => $this->supplier->sup_id,
            'company_id' => $this->company->id,
            'status' => 1,
            'email_verified_at' => now(),
        ]);

        $this->project = Project::create([
            'proj_name' => 'Pricing Project',
            'proj_status' => 1,
            'company_id' => $this->company->id,
        ]);

        $this->item = Item::create([
            'item_code' => 'PRICING-ITEM-001',
            'item_name' => 'Pricing Fixture Item',
            'item_status' => 1,
            'company_id' => $this->company->id,
        ]);

        session(['company_id' => $this->company->id]);
        $this->actingAs($this->supplierUser, 'supplier');
    }

    /** @test */
    public function supplier_can_create_pricing_and_expire_the_previous_active_version()
    {
        $existing = ItemPricing::create([
            'item_id' => $this->item->item_id,
            'supplier_id' => $this->supplier->sup_id,
            'project_id' => null,
            'company_id' => $this->company->id,
            'unit_price' => 100.00,
            'effective_from' => '2026-01-01',
            'effective_to' => null,
            'status' => 1,
        ]);

        $response = $this->post(route('supplier.pricing.store'), [
            'item_id' => $this->item->item_id,
            'project_id' => null,
            'unit_price' => 118.50,
            'effective_from' => '2026-03-15',
            'effective_to' => null,
        ]);

        $response->assertRedirect(route('supplier.pricing.index'));
        $response->assertSessionHas('status', 'Pricing saved.');

        $existing->refresh();

        $this->assertSame(0, (int) $existing->status);
        $this->assertSame('2026-03-14', optional($existing->effective_to)->format('Y-m-d'));

        $this->assertDatabaseHas('item_pricing', [
            'company_id' => $this->company->id,
            'item_id' => $this->item->item_id,
            'supplier_id' => $this->supplier->sup_id,
            'unit_price' => 118.50,
            'status' => 1,
        ]);
    }

    /** @test */
    public function supplier_can_import_pricing_from_csv_without_treating_headers_as_data()
    {
        $csv = implode("\n", [
            'item_id,supplier_id,project_id,unit_price,effective_from,effective_to',
            "{$this->item->item_id},{$this->supplier->sup_id},{$this->project->proj_id},125.00,2026-03-10,",
            "{$this->item->item_id},{$this->supplier->sup_id},,135.00,2026-03-20,",
        ]);

        $response = $this->post(route('supplier.pricing.import.store'), [
            'csv' => UploadedFile::fake()->createWithContent('pricing.csv', $csv),
        ]);

        $response->assertRedirect(route('supplier.pricing.index'));
        $response->assertSessionHas('status', '2 price rows imported.');

        $this->assertSame(2, ItemPricing::where('supplier_id', $this->supplier->sup_id)->count());
        $this->assertSame(0, ItemPricing::where('item_id', 0)->count());
    }

    /** @test */
    public function pricing_index_only_shows_rows_for_the_authenticated_supplier()
    {
        ItemPricing::create([
            'item_id' => $this->item->item_id,
            'supplier_id' => $this->supplier->sup_id,
            'company_id' => $this->company->id,
            'unit_price' => 111.11,
            'effective_from' => '2026-03-01',
            'status' => 1,
        ]);

        ItemPricing::create([
            'item_id' => $this->item->item_id,
            'supplier_id' => $this->otherSupplier->sup_id,
            'company_id' => $this->company->id,
            'unit_price' => 222.22,
            'effective_from' => '2026-03-01',
            'status' => 1,
        ]);

        $response = $this->get(route('supplier.pricing.index'));

        $response->assertOk();
        $response->assertSee('111.11');
        $response->assertDontSee('222.22');
    }
}
