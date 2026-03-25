<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CostCode;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CostCodeController extends Controller
{
    /**
     * Display a listing of the cost codes.
     */
    public function index()
    {
        $costCodes = CostCode::orderBy('cc_no', 'asc')->get();

        return view('admin.costcodes.index', compact('costCodes'));
    }

    /**
     * Store a newly created cost code.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'cc_no' => 'required|string|max:191|unique:cost_code_master,cc_no',
            'cc_description' => 'required|string|max:500',
        ]);

        CostCode::create([
            'cc_no' => trim($validated['cc_no']),
            'cc_description' => trim($validated['cc_description']),
            'cc_status' => 1,
            'cc_createdate' => Carbon::now(),
            'cc_createby' => auth()->id(),
        ]);

        return redirect()->route('admin.costcodes.index')->with('success', 'Cost code created successfully.');
    }

    /**
     * Update the specified cost code.
     */
    public function update(Request $request, CostCode $costcode)
    {
        $validated = $request->validate([
            'cc_no' => 'required|string|max:191|unique:cost_code_master,cc_no,' . $costcode->cc_id . ',cc_id',
            'cc_description' => 'required|string|max:500',
            'cc_status' => 'required|boolean',
        ]);

        $costcode->update([
            'cc_no' => trim($validated['cc_no']),
            'cc_description' => trim($validated['cc_description']),
            'cc_status' => $validated['cc_status'],
            'cc_modifydate' => Carbon::now(),
            'cc_modifyby' => auth()->id(),
        ]);

        return redirect()->route('admin.costcodes.index')->with('success', 'Cost code updated successfully.');
    }

    /**
     * Remove the specified cost code.
     */
    public function destroy(CostCode $costcode)
    {
        $companyId = session('company_id');
        $canDelete = DB::table('purchase_order_details')
                ->where('po_detail_ccode', $costcode->cc_id)
                ->where('company_id', $companyId)
                ->doesntExist()
            && DB::table('budget_master')
                ->where('budget_cost_code_id', $costcode->cc_id)
                ->where('company_id', $companyId)
                ->doesntExist();

        if (! $canDelete) {
            return redirect()->route('admin.costcodes.index')->with('error', 'Cost code is in use and cannot be deleted.');
        }

        $costcode->delete();

        return redirect()->route('admin.costcodes.index')->with('success', 'Cost code deleted successfully.');
    }

    /**
     * Display hierarchical cost code structure.
     */
    public function hierarchy()
    {
        $rootCodes = CostCode::active()
            ->parents()
            ->orderByRaw('COALESCE(cc_full_code, cc_no) asc')
            ->get();

        $hierarchy = $this->buildHierarchyTree($rootCodes);
        $standardSections = [
            ['code' => '1-00-00', 'description' => 'Land'],
            ['code' => '2-00-00', 'description' => 'Hard Construction Costs'],
            ['code' => '3-00-00', 'description' => 'Soft Construction Costs'],
            ['code' => '4-00-00', 'description' => 'Sales & Marketing'],
            ['code' => '5-00-00', 'description' => 'Contingencies'],
        ];
        $standardExamples = [
            ['code' => '2-01-00', 'description' => 'General Conditions'],
            ['code' => '2-03-00', 'description' => 'Concrete'],
            ['code' => '2-03-30', 'description' => 'Concrete Supply'],
            ['code' => '2-05-10', 'description' => 'Structural Steel Frame'],
            ['code' => '2-16-10', 'description' => 'Electrical Work'],
        ];

        return view('admin.costcodes.hierarchy', compact('hierarchy', 'rootCodes', 'standardSections', 'standardExamples'));
    }

    /**
     * Store a hierarchical cost code.
     */
    public function storeHierarchical(Request $request)
    {
        $payload = $this->validateHierarchyPayload($request);
        $attributes = $this->buildHierarchyAttributes($payload);

        if (CostCode::where('cc_full_code', $attributes['cc_full_code'])->exists()) {
            return back()->withInput()->with('error', 'Cost code already exists: ' . $attributes['cc_full_code']);
        }

        CostCode::create($attributes + [
            'cc_status' => (int) $payload['cc_status'],
            'cc_createdate' => Carbon::now(),
            'cc_createby' => auth()->id(),
        ]);

        return back()->with('success', 'Hierarchical cost code created: ' . $attributes['cc_full_code']);
    }

    /**
     * Update a hierarchical cost code and cascade segment changes to descendants.
     */
    public function updateHierarchical(Request $request, CostCode $costcode)
    {
        $payload = $this->validateHierarchyPayload($request, $costcode);
        $attributes = $this->buildHierarchyAttributes($payload);

        DB::transaction(function () use ($costcode, $attributes, $payload) {
            $descendants = $costcode->descendants()->keyBy('cc_id');

            $this->assertHierarchyCodeIsAvailable(
                $attributes['cc_full_code'],
                array_merge([$costcode->cc_id], $descendants->keys()->all())
            );

            $this->assertDescendantCodesAreAvailable($costcode, $attributes, $descendants);

            $costcode->update($attributes + [
                'cc_status' => (int) $payload['cc_status'],
                'cc_modifydate' => Carbon::now(),
                'cc_modifyby' => auth()->id(),
            ]);

            if ((int) $costcode->cc_level === CostCode::LEVEL_PARENT) {
                foreach ($descendants as $descendant) {
                    $segments = $this->segmentsFor($descendant);
                    $this->updateCodeRecord($descendant, [
                        'segment_1' => $attributes['cc_parent_code'],
                        'segment_2' => $segments['segment_2'],
                        'segment_3' => $segments['segment_3'],
                        'level' => $descendant->cc_level,
                    ]);
                }
            }

            if ((int) $costcode->cc_level === CostCode::LEVEL_CATEGORY) {
                foreach ($descendants->where('cc_level', CostCode::LEVEL_SUBCATEGORY) as $descendant) {
                    $segments = $this->segmentsFor($descendant);
                    $this->updateCodeRecord($descendant, [
                        'segment_1' => $attributes['cc_parent_code'],
                        'segment_2' => $attributes['cc_category_code'],
                        'segment_3' => $segments['segment_3'],
                        'level' => $descendant->cc_level,
                    ]);
                }
            }
        });

        return back()->with('success', 'Hierarchical cost code updated: ' . $attributes['cc_full_code']);
    }

    /**
     * Get child codes for a parent (AJAX).
     */
    public function getChildCodes($parentCode)
    {
        $parent = CostCode::where('cc_full_code', $parentCode)
            ->orWhere('cc_no', $parentCode)
            ->firstOrFail();

        $children = $parent->children()
            ->sortBy(fn (CostCode $code) => $code->cc_full_code ?? $code->cc_no)
            ->values()
            ->map(function ($code) {
                return [
                    'id' => $code->cc_id,
                    'code' => $code->cc_full_code ?? $code->cc_no,
                    'cc_no' => $code->cc_no,
                    'description' => $code->cc_description,
                    'level' => $code->cc_level,
                    'has_children' => $code->children()->isNotEmpty(),
                ];
            });

        return response()->json($children);
    }

    /**
     * Build hierarchy tree recursively.
     */
    protected function buildHierarchyTree($codes)
    {
        $tree = [];

        foreach ($codes as $code) {
            $children = $code->children()
                ->sortBy(fn (CostCode $child) => $child->cc_full_code ?? $child->cc_no)
                ->values();

            $tree[] = [
                'code' => $code,
                'children' => $this->buildHierarchyTree($children),
            ];
        }

        return $tree;
    }

    protected function validateHierarchyPayload(Request $request, ?CostCode $costcode = null): array
    {
        $payload = $request->validate([
            'level' => 'required|integer|in:1,2,3',
            'segment_1' => ['required', 'string', 'max:2', 'regex:/^[A-Za-z0-9]+$/'],
            'segment_2' => ['nullable', 'string', 'max:2', 'regex:/^[A-Za-z0-9]+$/'],
            'segment_3' => ['nullable', 'string', 'max:2', 'regex:/^[A-Za-z0-9]+$/'],
            'description' => 'required|string|max:500',
            'cc_status' => 'nullable|boolean',
        ]);

        $payload['cc_status'] = (int) ($payload['cc_status'] ?? 1);
        $payload['segment_1'] = strtoupper(trim($payload['segment_1']));
        $payload['segment_2'] = isset($payload['segment_2']) ? strtoupper(trim($payload['segment_2'])) : null;
        $payload['segment_3'] = isset($payload['segment_3']) ? strtoupper(trim($payload['segment_3'])) : null;

        if ((int) $payload['level'] >= 2 && empty($payload['segment_2'])) {
            throw ValidationException::withMessages(['segment_2' => 'Segment 2 is required for category and detail cost codes.']);
        }

        if ((int) $payload['level'] === 3 && empty($payload['segment_3'])) {
            throw ValidationException::withMessages(['segment_3' => 'Segment 3 is required for detail cost codes.']);
        }

        if ((int) $payload['level'] === 2 && $payload['segment_2'] === '00') {
            throw ValidationException::withMessages(['segment_2' => 'Segment 2 must be a non-zero category code.']);
        }

        if ((int) $payload['level'] === 3 && $payload['segment_3'] === '00') {
            throw ValidationException::withMessages(['segment_3' => 'Segment 3 must be a non-zero detail code.']);
        }

        if ($costcode && (int) $payload['level'] !== (int) $costcode->cc_level) {
            throw ValidationException::withMessages(['level' => 'Changing the hierarchy level of an existing cost code is not supported.']);
        }

        if ((int) $payload['level'] === 2) {
            $rootExists = CostCode::active()
                ->where('cc_level', CostCode::LEVEL_PARENT)
                ->where('cc_parent_code', $payload['segment_1'])
                ->when($costcode, fn ($query) => $query->where('cc_id', '!=', $costcode->cc_id))
                ->exists();

            if (! $rootExists) {
                throw ValidationException::withMessages(['segment_1' => 'Create the top-level section before adding a category beneath it.']);
            }
        }

        if ((int) $payload['level'] === 3) {
            $categoryExists = CostCode::active()
                ->where('cc_level', CostCode::LEVEL_CATEGORY)
                ->where('cc_parent_code', $payload['segment_1'])
                ->where('cc_category_code', str_pad($payload['segment_2'], 2, '0', STR_PAD_LEFT))
                ->when($costcode, fn ($query) => $query->where('cc_id', '!=', $costcode->cc_id))
                ->exists();

            if (! $categoryExists) {
                throw ValidationException::withMessages(['segment_2' => 'Create the category before adding a detail code beneath it.']);
            }
        }

        return $payload;
    }

    protected function buildHierarchyAttributes(array $payload): array
    {
        $level = (int) $payload['level'];
        $segment1 = $payload['segment_1'];
        $segment2 = $level >= 2
            ? str_pad($payload['segment_2'], 2, '0', STR_PAD_LEFT)
            : '00';
        $segment3 = $level === 3
            ? str_pad($payload['segment_3'], 2, '0', STR_PAD_LEFT)
            : '00';

        $fullCode = $this->formatFullCode($segment1, $segment2, $segment3);

        return [
            'cc_no' => $fullCode,
            'cc_description' => trim($payload['description']),
            'cc_parent_code' => $segment1,
            'cc_category_code' => $level >= 2 ? $segment2 : null,
            'cc_subcategory_code' => $level === 3 ? $segment3 : null,
            'cc_full_code' => $fullCode,
            'cc_level' => $level,
        ];
    }

    protected function segmentsFor(CostCode $costcode): array
    {
        $parts = array_pad(explode('-', $costcode->cc_full_code ?? $costcode->cc_no ?? ''), 3, '00');

        if (! empty($costcode->cc_parent_code)) {
            $parts[0] = $costcode->cc_parent_code;
        }

        if ((int) $costcode->cc_level >= CostCode::LEVEL_CATEGORY && ! empty($costcode->cc_category_code)) {
            $parts[1] = $costcode->cc_category_code;
        }

        if ((int) $costcode->cc_level === CostCode::LEVEL_SUBCATEGORY && ! empty($costcode->cc_subcategory_code)) {
            $parts[2] = $costcode->cc_subcategory_code;
        }

        if ((int) $costcode->cc_level === CostCode::LEVEL_PARENT) {
            $parts[1] = '00';
            $parts[2] = '00';
        }

        if ((int) $costcode->cc_level === CostCode::LEVEL_CATEGORY) {
            $parts[2] = '00';
        }

        return [
            'segment_1' => strtoupper($parts[0] ?: ''),
            'segment_2' => strtoupper($parts[1] ?: '00'),
            'segment_3' => strtoupper($parts[2] ?: '00'),
        ];
    }

    protected function updateCodeRecord(CostCode $costcode, array $payload): void
    {
        $attributes = $this->buildHierarchyAttributes($payload + ['description' => $costcode->cc_description]);

        $costcode->update($attributes + [
            'cc_modifydate' => Carbon::now(),
            'cc_modifyby' => auth()->id(),
        ]);
    }

    protected function assertHierarchyCodeIsAvailable(string $fullCode, array $allowedIds = []): void
    {
        $query = CostCode::where('cc_full_code', $fullCode);

        if (! empty($allowedIds)) {
            $query->whereNotIn('cc_id', $allowedIds);
        }

        if ($query->exists()) {
            throw ValidationException::withMessages([
                'segment_1' => "Cost code {$fullCode} already exists.",
            ]);
        }
    }

    protected function assertDescendantCodesAreAvailable(CostCode $costcode, array $attributes, $descendants): void
    {
        $proposedCodes = [];

        if ((int) $costcode->cc_level === CostCode::LEVEL_PARENT) {
            foreach ($descendants as $descendant) {
                $segments = $this->segmentsFor($descendant);
                $proposedCodes[] = $this->formatFullCode(
                    $attributes['cc_parent_code'],
                    $segments['segment_2'],
                    $segments['segment_3']
                );
            }
        }

        if ((int) $costcode->cc_level === CostCode::LEVEL_CATEGORY) {
            foreach ($descendants->where('cc_level', CostCode::LEVEL_SUBCATEGORY) as $descendant) {
                $segments = $this->segmentsFor($descendant);
                $proposedCodes[] = $this->formatFullCode(
                    $attributes['cc_parent_code'],
                    $attributes['cc_category_code'],
                    $segments['segment_3']
                );
            }
        }

        $proposedCodes = array_unique(array_filter($proposedCodes));
        if (empty($proposedCodes)) {
            return;
        }

        $conflicts = CostCode::whereIn('cc_full_code', $proposedCodes)
            ->whereNotIn('cc_id', array_merge([$costcode->cc_id], $descendants->keys()->all()))
            ->pluck('cc_full_code')
            ->all();

        if (! empty($conflicts)) {
            throw ValidationException::withMessages([
                'segment_1' => 'Updating this cost code would collide with existing descendant codes: ' . implode(', ', $conflicts),
            ]);
        }
    }

    protected function formatFullCode(string $segment1, string $segment2, string $segment3): string
    {
        return strtoupper(trim($segment1)) . '-' . strtoupper(trim($segment2)) . '-' . strtoupper(trim($segment3));
    }
}
