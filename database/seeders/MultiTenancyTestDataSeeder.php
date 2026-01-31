<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Project;
use App\Models\User;
use App\Models\Supplier;
use App\Models\PurchaseOrder;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class MultiTenancyTestDataSeeder extends Seeder
{
    /**
     * Run the test data seeder.
     */
    public function run()
    {
        echo "Creating test companies with sample data...\n\n";
        
        // Company 1: Acme Construction
        $company1 = $this->createCompany('Acme Construction', 'acme');
        $this->createCompanyData($company1, [
            'projects' => ['Downtown Office Building', 'Highway Bridge Repair'],
            'suppliers' => ['Steel Masters Inc', 'Concrete Solutions LLC'],
            'users' => ['John Manager', 'Sarah Admin'],
        ]);
        
        // Company 2: BuildRight LLC
        $company2 = $this->createCompany('BuildRight LLC', 'buildright');
        $this->createCompanyData($company2, [
            'projects' => ['Residential Complex A', 'Shopping Mall Renovation'],
            'suppliers' => ['Lumber Depot', 'Electrical Pros'],
            'users' => ['Mike Supervisor', 'Lisa Coordinator'],
        ]);
        
        // Company 3: Premier Projects
        $company3 = $this->createCompany('Premier Projects', 'premier');
        $this->createCompanyData($company3, [
            'projects' => ['School Expansion', 'Hospital Wing'],
            'suppliers' => ['Medical Equipment Co', 'Safety First Supplies'],
            'users' => ['David Director', 'Emily Engineer'],
        ]);
        
        echo "\n✓ Test data created for 3 companies\n";
        echo "You can now test multi-tenancy by:\n";
        echo "1. Logging in as different company users\n";
        echo "2. Verifying data isolation between companies\n";
        echo "3. Using the company switcher as super admin\n";
    }
    
    /**
     * Create a company
     */
    protected function createCompany(string $name, string $subdomain): Company
    {
        echo "Creating company: {$name}\n";
        
        return Company::create([
            'name' => $name,
            'subdomain' => $subdomain,
            'email' => strtolower(str_replace(' ', '.', $name)) . '@example.com',
            'phone' => '555-' . rand(1000, 9999),
            'address' => rand(100, 999) . ' Main Street',
            'city' => 'Anytown',
            'state' => 'CA',
            'zip' => '9' . rand(1000, 9999),
            'country' => 'USA',
            'status' => 1,
            'settings' => [
                'currency' => 'USD',
                'timezone' => 'America/Los_Angeles',
                'date_format' => 'm/d/Y',
            ],
        ]);
    }
    
    /**
     * Create sample data for a company
     */
    protected function createCompanyData(Company $company, array $data): void
    {
        echo "  - Creating users...\n";
        foreach ($data['users'] as $userName) {
            $email = strtolower(str_replace(' ', '.', $userName)) . '@' . $company->subdomain . '.com';
            User::create([
                'name' => $userName,
                'email' => $email,
                'username' => strtolower(str_replace(' ', '_', $userName)),
                'password' => Hash::make('password123'),
                'company_id' => $company->id,
                'u_type' => 2, // Regular user
                'u_status' => 1,
            ]);
            echo "    ✓ User: {$userName} ({$email})\n";
        }
        
        echo "  - Creating projects...\n";
        foreach ($data['projects'] as $projectName) {
            Project::create([
                'proj_name' => $projectName,
                'proj_code' => strtoupper(substr(str_replace(' ', '', $projectName), 0, 3)) . rand(100, 999),
                'proj_address' => rand(100, 999) . ' Project Ave',
                'proj_city' => 'Buildsville',
                'proj_state' => 'CA',
                'proj_zip' => '9' . rand(1000, 9999),
                'proj_status' => 1,
                'proj_created_by' => 1,
                'company_id' => $company->id,
            ]);
            echo "    ✓ Project: {$projectName}\n";
        }
        
        echo "  - Creating suppliers...\n";
        foreach ($data['suppliers'] as $supplierName) {
            Supplier::create([
                'sup_name' => $supplierName,
                'sup_email' => strtolower(str_replace(' ', '.', $supplierName)) . '@supplier.com',
                'sup_phone' => '555-' . rand(1000, 9999),
                'sup_address' => rand(100, 999) . ' Supply Street',
                'sup_city' => 'Supplyville',
                'sup_state' => 'CA',
                'sup_zip' => '9' . rand(1000, 9999),
                'sup_status' => 1,
                'sup_created_by' => 1,
                'company_id' => $company->id,
            ]);
            echo "    ✓ Supplier: {$supplierName}\n";
        }
        
        echo "\n";
    }
}
