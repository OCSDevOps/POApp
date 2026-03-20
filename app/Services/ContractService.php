<?php

namespace App\Services;

use App\Models\Contract;
use App\Models\ContractChangeOrder;
use App\Models\ContractInvoice;
use App\Models\Supplier;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ContractService
{
    /**
     * Create a new contract.
     */
    public function createContract(array $data): array
    {
        try {
            DB::beginTransaction();

            $data['contract_number'] = Contract::generateContractNumber();
            $data['contract_status'] = Contract::STATUS_DRAFT;
            $data['contract_created_by'] = auth()->id();
            $data['contract_created_at'] = now();
            $data['company_id'] = session('company_id');

            $contract = Contract::create($data);

            DB::commit();

            return ['success' => true, 'contract' => $contract];
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Contract creation failed', ['error' => $e->getMessage()]);
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Update an existing contract.
     */
    public function updateContract(int $contractId, array $data): array
    {
        try {
            $contract = Contract::findOrFail($contractId);

            if (!$contract->isEditable()) {
                return ['success' => false, 'error' => 'Contract cannot be edited in its current status.'];
            }

            $data['contract_modified_by'] = auth()->id();
            $data['contract_modified_at'] = now();

            $contract->update($data);

            return ['success' => true, 'contract' => $contract->fresh()];
        } catch (\Exception $e) {
            Log::error('Contract update failed', ['error' => $e->getMessage()]);
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Activate a contract (transition from Approved to Active).
     */
    public function activateContract(int $contractId, int $userId): array
    {
        try {
            $contract = Contract::findOrFail($contractId);

            if (!in_array($contract->contract_status, [Contract::STATUS_APPROVED, Contract::STATUS_DRAFT])) {
                return ['success' => false, 'error' => 'Contract must be in Draft or Approved status to activate.'];
            }

            $contract->activate($userId);

            return ['success' => true, 'contract' => $contract];
        } catch (\Exception $e) {
            Log::error('Contract activation failed', ['error' => $e->getMessage()]);
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Complete a contract.
     */
    public function completeContract(int $contractId, int $userId): array
    {
        try {
            $contract = Contract::findOrFail($contractId);

            if ($contract->contract_status !== Contract::STATUS_ACTIVE) {
                return ['success' => false, 'error' => 'Only active contracts can be completed.'];
            }

            $contract->complete($userId);

            return ['success' => true, 'contract' => $contract];
        } catch (\Exception $e) {
            Log::error('Contract completion failed', ['error' => $e->getMessage()]);
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Cancel a contract.
     */
    public function cancelContract(int $contractId, int $userId): array
    {
        try {
            $contract = Contract::findOrFail($contractId);

            if (in_array($contract->contract_status, [Contract::STATUS_COMPLETED, Contract::STATUS_CLOSED])) {
                return ['success' => false, 'error' => 'Completed or closed contracts cannot be cancelled.'];
            }

            $contract->contract_status = Contract::STATUS_CANCELLED;
            $contract->contract_modified_by = $userId;
            $contract->contract_modified_at = now();
            $contract->save();

            return ['success' => true, 'contract' => $contract];
        } catch (\Exception $e) {
            Log::error('Contract cancellation failed', ['error' => $e->getMessage()]);
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Create a contract change order.
     */
    public function createChangeOrder(int $contractId, array $data): array
    {
        try {
            DB::beginTransaction();

            $contract = Contract::findOrFail($contractId);

            $cco = ContractChangeOrder::create([
                'cco_number' => ContractChangeOrder::generateCcoNumber(),
                'contract_id' => $contractId,
                'cco_amount' => $data['cco_amount'],
                'cco_description' => $data['cco_description'],
                'cco_reason' => $data['cco_reason'] ?? null,
                'cco_status' => 'draft',
                'created_by' => auth()->id(),
                'company_id' => session('company_id'),
            ]);

            // Update pending COs on contract
            $contract->contract_pending_cos += $data['cco_amount'];
            $contract->contract_modified_by = auth()->id();
            $contract->contract_modified_at = now();
            $contract->save();

            DB::commit();

            return ['success' => true, 'change_order' => $cco];
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('CCO creation failed', ['error' => $e->getMessage()]);
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Approve a change order (called by ApprovalService).
     */
    public function approveChangeOrder(int $ccoId, int $userId): array
    {
        try {
            DB::beginTransaction();

            $cco = ContractChangeOrder::findOrFail($ccoId);
            $contract = $cco->contract;

            $cco->cco_status = 'approved';
            $cco->approved_by = $userId;
            $cco->approved_at = now();
            $cco->save();

            // Move amount from pending to approved
            $contract->contract_pending_cos -= $cco->cco_amount;
            $contract->contract_approved_cos += $cco->cco_amount;
            $contract->contract_modified_by = $userId;
            $contract->contract_modified_at = now();
            $contract->save();

            DB::commit();

            return ['success' => true, 'change_order' => $cco];
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('CCO approval failed', ['error' => $e->getMessage()]);
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Create an invoice for a contract.
     */
    public function createInvoice(int $contractId, array $data): array
    {
        try {
            DB::beginTransaction();

            $contract = Contract::findOrFail($contractId);

            $grossAmount = $data['cinv_gross_amount'];
            $retentionHeld = round($grossAmount * ($contract->contract_retention_pct / 100), 2);
            $netAmount = $grossAmount - $retentionHeld;

            $invoice = ContractInvoice::create([
                'cinv_contract_id' => $contractId,
                'cinv_number' => ContractInvoice::generateInvoiceNumber($contract->contract_number),
                'cinv_description' => $data['cinv_description'] ?? null,
                'cinv_gross_amount' => $grossAmount,
                'cinv_retention_held' => $retentionHeld,
                'cinv_net_amount' => $netAmount,
                'cinv_paid_amount' => 0,
                'cinv_invoice_date' => $data['cinv_invoice_date'],
                'cinv_due_date' => $data['cinv_due_date'] ?? null,
                'cinv_period_from' => $data['cinv_period_from'] ?? null,
                'cinv_period_to' => $data['cinv_period_to'] ?? null,
                'cinv_status' => ContractInvoice::STATUS_DRAFT,
                'cinv_created_by' => auth()->id(),
                'cinv_created_at' => now(),
                'company_id' => session('company_id'),
            ]);

            // Update contract totals
            $contract->contract_invoiced_amount += $grossAmount;
            $contract->contract_retention_held += $retentionHeld;
            $contract->contract_modified_by = auth()->id();
            $contract->contract_modified_at = now();
            $contract->save();

            DB::commit();

            return ['success' => true, 'invoice' => $invoice];
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Contract invoice creation failed', ['error' => $e->getMessage()]);
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Record a payment against an invoice.
     */
    public function recordPayment(int $invoiceId, float $amount, int $userId): array
    {
        try {
            DB::beginTransaction();

            $invoice = ContractInvoice::findOrFail($invoiceId);
            $contract = $invoice->contract;

            if ($amount > $invoice->balance_due) {
                return ['success' => false, 'error' => 'Payment amount exceeds balance due.'];
            }

            $invoice->cinv_paid_amount += $amount;
            $invoice->cinv_modified_by = $userId;
            $invoice->cinv_modified_at = now();

            if ($invoice->cinv_paid_amount >= $invoice->cinv_net_amount) {
                $invoice->cinv_status = ContractInvoice::STATUS_PAID;
                $invoice->cinv_paid_date = now();
            } else {
                $invoice->cinv_status = ContractInvoice::STATUS_PARTIALLY_PAID;
            }

            $invoice->save();

            // Update contract paid total
            $contract->contract_paid_amount += $amount;
            $contract->contract_modified_by = $userId;
            $contract->contract_modified_at = now();
            $contract->save();

            DB::commit();

            return ['success' => true, 'invoice' => $invoice->fresh()];
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Payment recording failed', ['error' => $e->getMessage()]);
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Release retention on a contract.
     */
    public function releaseRetention(int $contractId, float $amount, int $userId): array
    {
        try {
            DB::beginTransaction();

            $contract = Contract::findOrFail($contractId);
            $retentionBalance = $contract->total_retention_balance;

            if ($amount > $retentionBalance) {
                return ['success' => false, 'error' => "Cannot release more than the retention balance (\${$retentionBalance})."];
            }

            $contract->contract_retention_released += $amount;
            $contract->contract_modified_by = $userId;
            $contract->contract_modified_at = now();
            $contract->save();

            DB::commit();

            return ['success' => true, 'contract' => $contract->fresh()];
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Retention release failed', ['error' => $e->getMessage()]);
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Get contract financial summary.
     */
    public function getContractSummary(int $contractId): array
    {
        $contract = Contract::with(['changeOrders', 'invoices', 'supplier', 'project', 'costCode'])
            ->findOrFail($contractId);

        return [
            'contract' => $contract,
            'original_value' => $contract->contract_original_value,
            'approved_cos' => $contract->contract_approved_cos,
            'pending_cos' => $contract->contract_pending_cos,
            'revised_value' => $contract->revised_value,
            'invoiced' => $contract->contract_invoiced_amount,
            'paid' => $contract->contract_paid_amount,
            'retention_held' => $contract->contract_retention_held,
            'retention_released' => $contract->contract_retention_released,
            'retention_balance' => $contract->total_retention_balance,
            'remaining_to_invoice' => $contract->remaining_to_invoice,
            'remaining_to_pay' => $contract->remaining_to_pay,
            'completion_pct' => $contract->completion_percent,
        ];
    }

    /**
     * Get dashboard aggregate data.
     */
    public function getContractDashboardData(?int $projectId = null): array
    {
        $query = Contract::query();
        if ($projectId) {
            $query->byProject($projectId);
        }

        $contracts = $query->get();

        return [
            'total_contracts' => $contracts->count(),
            'active_contracts' => $contracts->where('contract_status', Contract::STATUS_ACTIVE)->count(),
            'total_value' => $contracts->sum('contract_original_value'),
            'total_revised' => $contracts->sum(fn($c) => $c->revised_value),
            'total_invoiced' => $contracts->sum('contract_invoiced_amount'),
            'total_paid' => $contracts->sum('contract_paid_amount'),
            'total_retention_held' => $contracts->sum('contract_retention_held'),
            'total_retention_released' => $contracts->sum('contract_retention_released'),
        ];
    }
}
