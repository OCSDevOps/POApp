<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Contract;
use App\Models\ContractInvoice;
use App\Services\ContractService;
use Illuminate\Http\Request;

class ContractInvoiceController extends Controller
{
    protected $contractService;

    public function __construct(ContractService $contractService)
    {
        $this->contractService = $contractService;
    }

    /**
     * List invoices for a contract.
     */
    public function index($contractId)
    {
        $contract = Contract::with(['project', 'supplier'])->findOrFail($contractId);
        $invoices = ContractInvoice::byContract($contractId)
            ->orderBy('cinv_invoice_date', 'desc')
            ->get();

        return view('admin.contracts.invoices.index', compact('contract', 'invoices'));
    }

    /**
     * Show create invoice form.
     */
    public function create($contractId)
    {
        $contract = Contract::with(['project', 'supplier'])->findOrFail($contractId);

        if (!in_array($contract->contract_status, [Contract::STATUS_ACTIVE, Contract::STATUS_APPROVED])) {
            return redirect()->route('admin.contracts.show', $contractId)
                ->with('error', 'Invoices can only be created for active or approved contracts.');
        }

        return view('admin.contracts.invoices.create', compact('contract'));
    }

    /**
     * Store a new invoice.
     */
    public function store(Request $request, $contractId)
    {
        $request->validate([
            'cinv_gross_amount' => 'required|numeric|min:0.01',
            'cinv_description' => 'nullable|string|max:500',
            'cinv_invoice_date' => 'required|date',
            'cinv_due_date' => 'nullable|date',
            'cinv_period_from' => 'nullable|date',
            'cinv_period_to' => 'nullable|date',
        ]);

        $result = $this->contractService->createInvoice($contractId, $request->only([
            'cinv_gross_amount', 'cinv_description', 'cinv_invoice_date',
            'cinv_due_date', 'cinv_period_from', 'cinv_period_to',
        ]));

        if (!$result['success']) {
            return back()->withInput()->with('error', $result['error']);
        }

        return redirect()->route('admin.contracts.invoices.show', [$contractId, $result['invoice']->cinv_id])
            ->with('success', "Invoice {$result['invoice']->cinv_number} created.");
    }

    /**
     * Show invoice detail.
     */
    public function show($contractId, $id)
    {
        $contract = Contract::with(['project', 'supplier'])->findOrFail($contractId);
        $invoice = ContractInvoice::where('cinv_contract_id', $contractId)
            ->where('cinv_id', $id)
            ->firstOrFail();

        return view('admin.contracts.invoices.show', compact('contract', 'invoice'));
    }

    /**
     * Record payment on an invoice.
     */
    public function recordPayment(Request $request, $contractId, $id)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
        ]);

        $result = $this->contractService->recordPayment($id, $request->amount, auth()->id());

        if (!$result['success']) {
            return back()->with('error', $result['error']);
        }

        return back()->with('success', "Payment of \${$request->amount} recorded.");
    }
}
