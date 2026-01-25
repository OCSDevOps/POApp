<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\ItemPackage;
use App\Models\ItemPackageDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ItemPackageController extends Controller
{
    public function index()
    {
        $packages = ItemPackage::where('ipack_status', 1)->orderBy('ipack_name')->with('details')->get();
        $items = Item::active()->orderByName()->get();

        return view('admin.packages.index', compact('packages', 'items'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'ipack_name' => 'required|string|max:255',
            'ipack_details' => 'nullable|string|max:1000',
            'items' => 'required|array|min:1',
            'items.*.item_id' => 'required',
            'items.*.quantity' => 'required|numeric|min:1',
        ]);

        DB::transaction(function () use ($validated) {
            $package = ItemPackage::create([
                'ipack_name' => $validated['ipack_name'],
                'ipack_details' => $validated['ipack_details'] ?? '',
                'ipack_totalitem' => count($validated['items']),
                'ipack_total_qty' => collect($validated['items'])->sum('quantity'),
                'ipack_createdate' => now(),
                'ipack_createby' => Auth::id(),
                'ipack_status' => 1,
            ]);

            foreach ($validated['items'] as $idx => $line) {
                ItemPackageDetail::create([
                    'ipdetail_autogen' => uniqid('pkg_'),
                    'ipdetail_ipack_ms' => $package->ipack_id,
                    'ipdetail_item_ms' => $line['item_id'],
                    'ipdetail_quantity' => $line['quantity'],
                    'ipdetail_info' => $line['info'] ?? '',
                    'ipdetail_createdate' => now(),
                    'ipdetail_status' => 1,
                ]);
            }
        });

        return redirect()->route('admin.packages.index')->with('success', 'Package created.');
    }

    public function update(Request $request, ItemPackage $package)
    {
        $validated = $request->validate([
            'ipack_name' => 'required|string|max:255',
            'ipack_details' => 'nullable|string|max:1000',
            'items' => 'required|array|min:1',
            'items.*.item_id' => 'required',
            'items.*.quantity' => 'required|numeric|min:1',
        ]);

        DB::transaction(function () use ($validated, $package) {
            $package->update([
                'ipack_name' => $validated['ipack_name'],
                'ipack_details' => $validated['ipack_details'] ?? '',
                'ipack_totalitem' => count($validated['items']),
                'ipack_total_qty' => collect($validated['items'])->sum('quantity'),
                'ipack_modifydate' => now(),
                'ipack_modifyby' => Auth::id(),
            ]);

            ItemPackageDetail::where('ipdetail_ipack_ms', $package->ipack_id)->update(['ipdetail_status' => 0]);

            foreach ($validated['items'] as $line) {
                ItemPackageDetail::create([
                    'ipdetail_autogen' => uniqid('pkg_'),
                    'ipdetail_ipack_ms' => $package->ipack_id,
                    'ipdetail_item_ms' => $line['item_id'],
                    'ipdetail_quantity' => $line['quantity'],
                    'ipdetail_info' => $line['info'] ?? '',
                    'ipdetail_createdate' => now(),
                    'ipdetail_status' => 1,
                ]);
            }
        });

        return redirect()->route('admin.packages.index')->with('success', 'Package updated.');
    }

    public function destroy(ItemPackage $package)
    {
        $package->update(['ipack_status' => 0, 'ipack_modifydate' => now(), 'ipack_modifyby' => Auth::id()]);
        ItemPackageDetail::where('ipdetail_ipack_ms', $package->ipack_id)->update(['ipdetail_status' => 0]);

        return redirect()->route('admin.packages.index')->with('success', 'Package archived.');
    }
}
