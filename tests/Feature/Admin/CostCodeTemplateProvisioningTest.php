<?php

namespace Tests\Feature\Admin;

use App\Models\Company;
use App\Models\User;
use App\Services\CostCodeTemplateProvisioningService;
use App\Support\StandardCostCodeCatalog;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class CostCodeTemplateProvisioningTest extends TestCase
{
    use DatabaseTransactions;

    protected Company $company;
    protected User $superAdmin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->company = Company::create([
            'name' => 'Template Provisioning Source Company',
            'subdomain' => 'template-provisioning-source',
            'status' => 1,
        ]);

        $this->superAdmin = User::create([
            'name' => 'Template Provisioning Admin',
            'email' => 'template-provisioning-admin@test.com',
            'password' => bcrypt('password'),
            'u_type' => 1,
            'u_status' => 1,
            'company_id' => $this->company->id,
        ]);

        session(['company_id' => $this->company->id]);
        $this->actingAs($this->superAdmin);
    }

    /** @test */
    public function creating_a_company_provisions_the_full_march_2020_catalog_and_template_pack(): void
    {
        $response = $this->withSession([
            'u_type' => 1,
            'company_id' => $this->company->id,
        ])->post(route('admin.companies.store'), [
            'name' => 'Provisioned Tenant',
            'subdomain' => 'provisioned-tenant',
        ]);

        $response->assertRedirect(route('admin.companies.index'));

        $provisionedCompany = Company::where('subdomain', 'provisioned-tenant')->firstOrFail();
        $templateDefinitions = StandardCostCodeCatalog::templates();

        $this->assertSame(
            StandardCostCodeCatalog::codeCount(),
            DB::table('cost_code_master')->where('company_id', $provisionedCompany->id)->count()
        );

        $this->assertDatabaseHas('cost_code_master', [
            'company_id' => $provisionedCompany->id,
            'cc_full_code' => '2-03-30',
            'cc_description' => 'Concrete Supply',
        ]);

        $this->assertDatabaseHas('cost_code_master', [
            'company_id' => $provisionedCompany->id,
            'cc_full_code' => '5-22-04',
            'cc_description' => 'Design Contingency',
        ]);

        $this->assertSame(
            count($templateDefinitions),
            DB::table('cost_code_templates')->where('company_id', $provisionedCompany->id)->count()
        );

        foreach ($templateDefinitions as $templateDefinition) {
            $templateId = DB::table('cost_code_templates')
                ->where('company_id', $provisionedCompany->id)
                ->where('cct_key', $templateDefinition['key'])
                ->value('cct_id');

            $this->assertNotNull($templateId, "Template [{$templateDefinition['key']}] was not provisioned.");
            $this->assertSame(
                count($templateDefinition['codes']),
                DB::table('cost_code_template_items')->where('ccti_template_id', $templateId)->count()
            );
        }
    }

    /** @test */
    public function template_provisioning_is_idempotent_for_existing_companies(): void
    {
        $company = Company::create([
            'name' => 'Idempotent Tenant',
            'subdomain' => 'idempotent-tenant',
            'status' => 1,
        ]);

        $service = app(CostCodeTemplateProvisioningService::class);

        $service->provisionForCompany($company->id, $this->superAdmin->id);
        $service->provisionForCompany($company->id, $this->superAdmin->id);

        $this->assertSame(
            StandardCostCodeCatalog::codeCount(),
            DB::table('cost_code_master')->where('company_id', $company->id)->count()
        );

        $this->assertSame(
            count(StandardCostCodeCatalog::templates()),
            DB::table('cost_code_templates')->where('company_id', $company->id)->count()
        );

        $this->assertSame(
            count(StandardCostCodeCatalog::templates()),
            DB::table('cost_code_templates')
                ->where('company_id', $company->id)
                ->whereNotNull('cct_key')
                ->distinct('cct_key')
                ->count('cct_key')
        );
    }
}
