<?php

namespace Tests\Feature\Admin;

use App\Models\Company;
use App\Models\CostCode;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class CostCodeHierarchyTest extends TestCase
{
    use DatabaseTransactions;

    protected Company $company;
    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->company = Company::create([
            'name' => 'Hierarchy Test Company',
            'subdomain' => 'hierarchy-test',
            'status' => 1,
        ]);

        $this->user = User::create([
            'name' => 'Hierarchy Admin',
            'email' => 'hierarchy-admin@test.com',
            'password' => bcrypt('password'),
            'u_type' => 1,
            'u_status' => 1,
            'company_id' => $this->company->id,
        ]);

        session(['company_id' => $this->company->id]);
        $this->actingAs($this->user);
    }

    /** @test */
    public function it_creates_standard_three_segment_cost_codes_through_the_hierarchy_form()
    {
        $this->post(route('admin.costcodes.store-hierarchical'), [
            'level' => 1,
            'segment_1' => '2',
            'description' => 'Hard Construction Costs',
            'cc_status' => 1,
        ])->assertRedirect();

        $this->post(route('admin.costcodes.store-hierarchical'), [
            'level' => 2,
            'segment_1' => '2',
            'segment_2' => '03',
            'description' => 'Concrete',
            'cc_status' => 1,
        ])->assertRedirect();

        $response = $this->post(route('admin.costcodes.store-hierarchical'), [
            'level' => 3,
            'segment_1' => '2',
            'segment_2' => '03',
            'segment_3' => '30',
            'description' => 'Concrete Supply',
            'cc_status' => 1,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('cost_code_master', [
            'company_id' => $this->company->id,
            'cc_full_code' => '2-00-00',
            'cc_level' => 1,
            'cc_parent_code' => '2',
        ]);

        $this->assertDatabaseHas('cost_code_master', [
            'company_id' => $this->company->id,
            'cc_full_code' => '2-03-00',
            'cc_level' => 2,
            'cc_parent_code' => '2',
            'cc_category_code' => '03',
        ]);

        $this->assertDatabaseHas('cost_code_master', [
            'company_id' => $this->company->id,
            'cc_full_code' => '2-03-30',
            'cc_level' => 3,
            'cc_parent_code' => '2',
            'cc_category_code' => '03',
            'cc_subcategory_code' => '30',
        ]);
    }

    /** @test */
    public function it_updates_a_category_and_cascades_its_descendants()
    {
        $root = $this->createCostCode('2-00-00', 'Hard Construction Costs', CostCode::LEVEL_PARENT);
        $category = $this->createCostCode('2-03-00', 'Concrete', CostCode::LEVEL_CATEGORY, '2', '03');
        $detail = $this->createCostCode('2-03-30', 'Concrete Supply', CostCode::LEVEL_SUBCATEGORY, '2', '03', '30');

        $response = $this->put(route('admin.costcodes.update-hierarchical', $category), [
            'level' => 2,
            'segment_1' => '2',
            'segment_2' => '04',
            'description' => 'Masonry',
            'cc_status' => 1,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $category->refresh();
        $detail->refresh();
        $root->refresh();

        $this->assertSame('2-04-00', $category->cc_full_code);
        $this->assertSame('Masonry', $category->cc_description);
        $this->assertSame('04', $category->cc_category_code);

        $this->assertSame('2-04-30', $detail->cc_full_code);
        $this->assertSame('04', $detail->cc_category_code);
        $this->assertSame('30', $detail->cc_subcategory_code);

        $this->assertDatabaseMissing('cost_code_master', [
            'company_id' => $this->company->id,
            'cc_full_code' => '2-03-00',
        ]);

        $this->assertDatabaseMissing('cost_code_master', [
            'company_id' => $this->company->id,
            'cc_full_code' => '2-03-30',
        ]);
    }

    protected function createCostCode(
        string $fullCode,
        string $description,
        int $level,
        ?string $parentCode = null,
        ?string $categoryCode = null,
        ?string $subcategoryCode = null
    ): CostCode {
        return CostCode::create([
            'cc_no' => $fullCode,
            'cc_description' => $description,
            'cc_parent_code' => $parentCode ?? explode('-', $fullCode)[0],
            'cc_category_code' => $categoryCode,
            'cc_subcategory_code' => $subcategoryCode,
            'cc_level' => $level,
            'cc_full_code' => $fullCode,
            'cc_status' => 1,
            'cc_createby' => $this->user->id,
            'cc_createdate' => now(),
            'company_id' => $this->company->id,
        ]);
    }
}
