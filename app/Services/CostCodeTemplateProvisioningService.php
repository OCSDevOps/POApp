<?php

namespace App\Services;

use App\Support\StandardCostCodeCatalog;
use Illuminate\Support\Facades\DB;

class CostCodeTemplateProvisioningService
{
    public function provisionForCompany(int $companyId, ?int $userId = null): array
    {
        $now = now();
        $catalogRows = StandardCostCodeCatalog::codes();

        return DB::transaction(function () use ($companyId, $userId, $now, $catalogRows) {
            $costCodeIds = $this->syncCostCodes($companyId, $userId, $now, $catalogRows);
            $templateCount = $this->syncTemplates($companyId, $userId, $now, $costCodeIds);

            return [
                'cost_codes' => count($costCodeIds),
                'templates' => $templateCount,
            ];
        });
    }

    private function syncCostCodes(int $companyId, ?int $userId, $now, array $catalogRows): array
    {
        $catalogCodes = array_map(static fn (array $row) => $row['code'], $catalogRows);

        $existing = DB::table('cost_code_master')
            ->where('company_id', $companyId)
            ->whereIn('cc_full_code', $catalogCodes)
            ->get(['cc_id', 'cc_full_code'])
            ->keyBy('cc_full_code');

        $idsByCode = [];

        foreach ($catalogRows as $row) {
            $code = $row['code'];
            $description = $row['description'];
            [$section, $category, $detail] = StandardCostCodeCatalog::segmentsFor($code);

            $attributes = [
                'cc_no' => $code,
                'cc_description' => $description,
                'cc_details' => $description,
                'cc_parent_code' => $section,
                'cc_category_code' => $category,
                'cc_subcategory_code' => $detail,
                'cc_level' => StandardCostCodeCatalog::levelFor($code),
                'cc_full_code' => $code,
                'cc_status' => 1,
                'cc_modifyby' => $userId,
                'cc_modifydate' => $now,
                'company_id' => $companyId,
            ];

            $existingRow = $existing->get($code);

            if ($existingRow) {
                DB::table('cost_code_master')
                    ->where('cc_id', $existingRow->cc_id)
                    ->update($attributes);

                $idsByCode[$code] = (int) $existingRow->cc_id;
                continue;
            }

            $idsByCode[$code] = (int) DB::table('cost_code_master')->insertGetId(array_merge($attributes, [
                'cc_createby' => $userId,
                'cc_createdate' => $now,
            ]), 'cc_id');
        }

        return $idsByCode;
    }

    private function syncTemplates(int $companyId, ?int $userId, $now, array $costCodeIds): int
    {
        $templates = StandardCostCodeCatalog::templates();
        $keys = array_map(static fn (array $template) => $template['key'], $templates);

        $existing = DB::table('cost_code_templates')
            ->where('company_id', $companyId)
            ->whereIn('cct_key', $keys)
            ->get(['cct_id', 'cct_key'])
            ->keyBy('cct_key');

        foreach ($templates as $template) {
            $attributes = [
                'company_id' => $companyId,
                'cct_key' => $template['key'],
                'cct_name' => $template['name'],
                'cct_description' => $template['description'],
                'cct_status' => 1,
                'cct_modifyby' => $userId,
                'cct_modifydate' => $now,
            ];

            $existingRow = $existing->get($template['key']);

            if ($existingRow) {
                $templateId = (int) $existingRow->cct_id;

                DB::table('cost_code_templates')
                    ->where('cct_id', $templateId)
                    ->update($attributes);
            } else {
                $templateId = (int) DB::table('cost_code_templates')->insertGetId(array_merge($attributes, [
                    'cct_createby' => $userId,
                    'cct_createdate' => $now,
                ]), 'cct_id');
            }

            DB::table('cost_code_template_items')
                ->where('ccti_template_id', $templateId)
                ->delete();

            $itemRows = [];

            foreach ($template['codes'] as $index => $code) {
                if (!isset($costCodeIds[$code])) {
                    continue;
                }

                $itemRows[] = [
                    'ccti_template_id' => $templateId,
                    'ccti_cost_code_id' => $costCodeIds[$code],
                    'ccti_sort_order' => $index + 1,
                ];
            }

            foreach (array_chunk($itemRows, 500) as $chunk) {
                DB::table('cost_code_template_items')->insert($chunk);
            }
        }

        return count($templates);
    }
}
