<?php

namespace App\Services;

use App\Models\ItemPricing;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ItemPricingService
{
    /**
    * Create or update pricing; close prior active versions for same context.
    */
    public function upsert(array $data): ItemPricing
    {
        DB::beginTransaction();

        try {
            // Expire overlapping active records
            ItemPricing::where('item_id', $data['item_id'])
                ->where('supplier_id', $data['supplier_id'])
                ->when(isset($data['project_id']), function ($q) use ($data) {
                    $q->where('project_id', $data['project_id']);
                }, function ($q) {
                    $q->whereNull('project_id');
                })
                ->where('status', 1)
                ->update([
                    'status' => 0,
                    'effective_to' => Carbon::parse($data['effective_from'])->subDay()->toDateString(),
                    'updated_at' => now(),
                ]);

            $pricing = ItemPricing::create([
                'item_id' => $data['item_id'],
                'supplier_id' => $data['supplier_id'],
                'project_id' => $data['project_id'] ?? null,
                'company_id' => $data['company_id'] ?? session('company_id'),
                'unit_price' => $data['unit_price'],
                'effective_from' => $data['effective_from'],
                'effective_to' => $data['effective_to'] ?? null,
                'status' => $data['status'] ?? 1,
            ]);

            DB::commit();
            return $pricing;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Bulk import pricing rows.
     * Expects rows: item_id,supplier_id,project_id(optional),unit_price,effective_from,effective_to(optional)
     */
    public function import(array $rows, ?int $companyId = null): int
    {
        $imported = 0;
        foreach ($rows as $row) {
            if (!isset($row['item_id'], $row['supplier_id'], $row['unit_price'], $row['effective_from'])) {
                continue;
            }

            $this->upsert([
                'item_id' => $row['item_id'],
                'supplier_id' => $row['supplier_id'],
                'project_id' => $row['project_id'] ?? null,
                'unit_price' => $row['unit_price'],
                'effective_from' => $row['effective_from'],
                'effective_to' => $row['effective_to'] ?? null,
                'company_id' => $companyId ?? session('company_id'),
            ]);

            $imported++;
        }

        return $imported;
    }

    /**
     * Get current price for an item/supplier/project.
     */
    public function currentPrice(int $itemId, int $supplierId, ?int $projectId = null): ?ItemPricing
    {
        return ItemPricing::active()
            ->where('item_id', $itemId)
            ->where('supplier_id', $supplierId)
            ->when($projectId, fn($q) => $q->where('project_id', $projectId)->orWhereNull('project_id'))
            ->orderByRaw('project_id IS NULL') // prefer project-specific
            ->orderBy('effective_from', 'desc')
            ->first();
    }
}
