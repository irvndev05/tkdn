<?php

namespace App\Http\Controllers;

use App\Models\Equipment;
use App\Models\Estimation;
use App\Models\EstimationItem;
use App\Models\Hpp;
use App\Models\HppItem;
use App\Models\Material;
use App\Models\Project;
use App\Models\Worker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class HppController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except(['getAhsDataOnly', 'getAhsItems']);
        $this->middleware('can:manage-hpp')->only(['create', 'store', 'edit', 'update', 'destroy']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $hpps = Hpp::with(['items', 'project'])->latest()->paginate(10);

        return view('hpp.index', compact('hpps'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $projects = Project::all();
        $ahsData = $this->getAhsData();
        // dd($ahsData);

        return view('hpp.create', compact('projects', 'ahsData'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        \Log::info('HPP Store method called', [
            'request_data' => $request->all(),
            'user_id' => auth()->id(),
        ]);

        $request->validate([
            'project_id' => 'required|exists:projects,id',
            'overhead_percentage' => 'required|numeric|min:0|max:100',
            'margin_percentage' => 'required|numeric|min:0|max:100',
            'ppn_percentage' => 'required|numeric|min:0|max:100',
            'notes' => 'nullable|string',

            // Header AHS groups
            'ahs' => 'required|array|min:1',
            'ahs.*.description' => 'nullable|string',
            'ahs.*.volume' => 'nullable|numeric|min:0',
            'ahs.*.unit' => 'nullable|string',
            'ahs.*.duration' => 'nullable|integer|min:1',
            'ahs.*.duration_unit' => 'nullable|string',
            // 'ahs.*.unit_price' => 'nullable|numeric|min:0',
            'ahs.*.total_price' => 'nullable|numeric|min:0',
            'ahs.*.ahs_id' => 'nullable|exists:estimations,id', // Added ahs_id for fallback

            // Nested detail items under each group
            'items' => 'required|array|min:1',
            'items.*.detail' => 'required|array|min:1',
            'items.*.detail.*.description' => 'required|string',
            'items.*.detail.*.estimation_item_id' => 'nullable|exists:estimation_items,id',
            'items.*.detail.*.unit_price' => 'required|numeric|min:0',
            // 'items.*.detail.*.quantity' => 'required|numeric|min:0',
            'items.*.detail.*.coefficient' => 'nullable|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();

            // Generate kode HPP
            $code = 'HPP-' . date('Ymd') . '-' . strtoupper(Str::random(4));
            $name_hpp = 'HPP - ' . Project::find($request->project_id)->name;
            $name_hpp_AHS = 'HPP ' . Project::find($request->project_id)->name;

            // Compute totals based on grouped AHS and nested details
            $ahsGroups = $request->input('ahs', []);
            $itemGroups = $request->input('items', []);

            $subTotalHppAhs = 0.0; // sum of each group's total_price

            // Pre-compute per-group unit_price (sum of item grand totals) and total_price
            $computedGroups = [];
            foreach ($ahsGroups as $groupIndex => $ahsHeader) {
                $details = $itemGroups[$groupIndex]['detail'] ?? [];
                $unitPriceSum = 0.0;
                foreach ($details as $detail) { 
                    $coef = (float) ($detail['coefficient'] ?? 0);
                    $qty = (float) ($detail['quantity'] ?? 0);
                    $unitPrice = (float) ($detail['unit_price'] ?? 0);
                    $unitPriceSum += ($unitPrice * $coef);
                }

                $volume = (float) ($ahsHeader['volume'] ?? 1);
                $duration = (int) ($ahsHeader['duration'] ?? 1);
                $groupTotal = $unitPriceSum * $volume * $duration;
                $subTotalHppAhs += $groupTotal;

                $computedGroups[$groupIndex] = [
                    'unit_price_sum' => $unitPriceSum,
                    'group_total' => $groupTotal,
                ];
            }

            // Overhead, Margin based on subTotalHppAhs
            $overheadAmount = $subTotalHppAhs * ($request->overhead_percentage / 100);
            $marginAmount = $subTotalHppAhs * ($request->margin_percentage / 100);
            $subTotal = $subTotalHppAhs + $overheadAmount + $marginAmount;
            $ppnAmount = $subTotal * ($request->ppn_percentage / 100);
            $grandTotal = $subTotal + $ppnAmount;

            // Buat HPP
            $hpp = Hpp::create([
                'code' => $code,
                'project_id' => $request->project_id,
                'name_hpp' => $name_hpp,
                'sub_total_hpp' => $subTotalHppAhs, // sum of AHS grup before overhead/margin/ppn
                //Hitung overhead, margin, ppn, grand total
                'overhead_percentage' => $request->overhead_percentage,
                'overhead_amount' => $overheadAmount,
                'margin_percentage' => $request->margin_percentage,
                'margin_amount' => $marginAmount,
                'sub_total' => $subTotal,
                'ppn_percentage' => $request->ppn_percentage,
                'ppn_amount' => $ppnAmount,
                'grand_total' => $grandTotal,
                'notes' => $request->notes,
                'status' => 'draft',
            ]);


            // Buat HPP- AHS & Hpp - Items 
            foreach ($ahsGroups as $groupIndex => $ahsHeader) {
                $unitPriceSum = $computedGroups[$groupIndex]['unit_price_sum'] ?? 0.0;
                $groupTotal = $computedGroups[$groupIndex]['group_total'] ?? 0.0;

                // Resolve AHS name from description or fallback by ahs_id
                $nameAhsHeader = $ahsHeader['description'] ?? null;
                if (! $nameAhsHeader && ! empty($ahsHeader['ahs_id'])) {
                    $est = Estimation::find($ahsHeader['ahs_id']);
                    if ($est) {
                        $nameAhsHeader = $est->code . ' - ' . $est->title;
                    }
                }



                $createdAhs = $hpp->ahs()->create([
                    'name_ahs' =>  $name_hpp_AHS . ' - ' . $nameAhsHeader,
                    'volume' => $ahsHeader['volume'] ?? 1,
                    'unit' => $ahsHeader['unit'] ?? 'Unit',
                    'duration' => $ahsHeader['duration'] ?? 1,
                    'duration_unit' => $ahsHeader['duration_unit'] ?? 'Hari',
                    'unit_price' => $unitPriceSum, // sum of detail grand totals
                    'total_price' => $groupTotal, // volume * unit_price_sum * duration
                ]);

                // Buat HPP items per AHS
                $details = $itemGroups[$groupIndex]['detail'] ?? [];
                foreach ($details as $detail) {
                    $hppAhsid = $createdAhs->id;
                    $estimationItemId = $detail['estimation_item_id'] ?? null;
                    $nameAhs = $createdAhs->name_ahs;
                    $description = $detail['description'] ?? '';
                    $unit = $detail['unit'] ?? ($estimationItemId ? $this->getItemUnit(EstimationItem::find($estimationItemId)) : 'Unit');
                    $coef = (float) ($detail['coefficient'] ?? 0);
                    $qty = (float) ($detail['quantity'] ?? 0);
                    $unitPrice = (float) ($detail['unit_price'] ?? 0);
                    $totalPrice = $unitPrice * $coef;

                    $hpp->items()->create([
                        'hpp_ahs_id' => $hppAhsid,
                        'estimation_item_id' => $estimationItemId,
                        'name_ahs' => $nameAhs,
                        'description' => $description,
                        'volume' => 1,
                        'unit' => $unit,
                        'duration' => 1,
                        'duration_unit' => 'Hari',
                        'koefisien' => $coef,
                        'unit_price' => $unitPrice,
                        'jumlah' => $qty,
                        'total_price' => $totalPrice,
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('hpp.index')->with('success', 'HPP berhasil dibuat!');
        } catch (\Exception $e) {
            DB::rollback();

            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $hpp = Hpp::with(['items', 'project'])->findOrFail($id);
        $hppahs = $hpp->ahs;
        $hppitems = HppItem::where('hpp_id', $hpp->id)->get();

        return view('hpp.show', compact('hpp', 'hppahs', 'hppitems'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $hpp = Hpp::with(['items.estimationItem.estimation', 'ahs', 'project'])->findOrFail($id);
        $projects = Project::all();
        $hppitems = HppItem::where('hpp_id', $hpp->id)->get();
        $ahsData = $this->getAhsData();

        return view('hpp.edit', compact('hpp', 'projects', 'ahsData', 'hppitems'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // Check if user can manage HPP
        if (! Auth::user()->can('manage-hpp')) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'project_id' => 'required|exists:projects,id',
            'overhead_percentage' => 'required|numeric|min:0|max:100',
            'margin_percentage' => 'required|numeric|min:0|max:100',
            'ppn_percentage' => 'required|numeric|min:0|max:100',
            'notes' => 'nullable|string',

            // Header AHS groups
            'ahs' => 'required|array|min:1',
            'ahs.*.description' => 'nullable|string',
            'ahs.*.volume' => 'nullable|numeric|min:0',
            'ahs.*.unit' => 'nullable|string',
            'ahs.*.duration' => 'nullable|integer|min:1',
            'ahs.*.duration_unit' => 'nullable|string',
            'ahs.*.unit_price' => 'nullable|numeric|min:0',
            'ahs.*.total_price' => 'nullable|numeric|min:0',
            'ahs.*.ahs_id' => 'nullable|exists:estimations,id', // Added ahs_id for fallback

            // Nested detail items under each group
            'items' => 'required|array|min:1',
            'items.*.detail' => 'required|array|min:1',
            'items.*.detail.*.description' => 'required|string',
            'items.*.detail.*.estimation_item_id' => 'nullable|exists:estimation_items,id',
            'items.*.detail.*.unit_price' => 'required|numeric|min:0',
            'items.*.detail.*.quantity' => 'required|numeric|min:0',
            'items.*.detail.*.coefficient' => 'nullable|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();

            $hpp = Hpp::findOrFail($id);

            // Generate kode HPP
            $code = 'HPP-' . date('Ymd') . '-' . strtoupper(Str::random(4));
            $name_hpp = 'HPP - ' . Project::find($request->project_id)->name;
            $name_hpp_AHS = 'HPP ' . Project::find($request->project_id)->name;

            // Compute totals based on grouped AHS and nested details
            $ahsGroups = $request->input('ahs', []);
            $itemGroups = $request->input('items', []);

            $subTotalHppAhs = 0.0; // sum of each group's total_price
            // Pre-compute per-group unit_price (sum of item grand totals) and total_price
            $computedGroups = [];
            foreach ($ahsGroups as $groupIndex => $ahsHeader) {
                $details = $itemGroups[$groupIndex]['detail'] ?? [];
                $unitPriceSum = 0.0;
                foreach ($details as $detail) {
                    $qty = (float) ($detail['quantity'] ?? 0);
                    $coef = (float) ($detail['coefficient'] ?? 0);
                    $unitPrice = (float) ($detail['unit_price'] ?? 0);
                    $unitPriceSum += ($unitPrice * $coef);
                }

                $volume = (float) ($ahsHeader['volume'] ?? 1);
                $duration = (int) ($ahsHeader['duration'] ?? 1);
                $groupTotal = $unitPriceSum * $volume * $duration;
                $subTotalHppAhs += $groupTotal;

                $computedGroups[$groupIndex] = [
                    'unit_price_sum' => $unitPriceSum,
                    'group_total' => $groupTotal,
                ];
            }
            // Overhead, Margin based on subTotalHppAhs
            $overheadAmount = $subTotalHppAhs * ($request->overhead_percentage / 100);
            $marginAmount = $subTotalHppAhs * ($request->margin_percentage / 100);
            $subTotal = $subTotalHppAhs + $overheadAmount + $marginAmount;
            $ppnAmount = $subTotal * ($request->ppn_percentage / 100);
            $grandTotal = $subTotal + $ppnAmount;
            // Update HPP
            $hpp->update([
                'code' => $code,
                'project_id' => $request->project_id,
                'name_hpp' => $name_hpp,
                'sub_total_hpp' => $subTotalHppAhs, // sum of AHS grup before overhead/margin/ppn
                //Hitung overhead, margin, ppn, grand total
                'overhead_percentage' => $request->overhead_percentage,
                'overhead_amount' => $overheadAmount,
                'margin_percentage' => $request->margin_percentage,
                'margin_amount' => $marginAmount,
                'sub_total' => $subTotal,
                'ppn_percentage' => $request->ppn_percentage,
                'ppn_amount' => $ppnAmount,
                'grand_total' => $grandTotal,
                'notes' => $request->notes,
                'status' => 'draft',
            ]);
            // Hapus AHS lama
            $hpp->ahs()->delete();
            // Buat HPP- AHS & Hpp - Items
            foreach ($ahsGroups as $groupIndex => $ahsHeader) {
                $unitPriceSum = $computedGroups[$groupIndex]['unit_price_sum'] ?? 0.0;
                $groupTotal = $computedGroups[$groupIndex]['group_total'] ?? 0.0;

                // Resolve AHS name from description or fallback by ahs_id
                $nameAhsHeader = $ahsHeader['description'] ?? null;
                if (! $nameAhsHeader && ! empty($ahsHeader['ahs_id'])) {
                    $est = Estimation::find($ahsHeader['ahs_id']);
                    if ($est) {
                        $nameAhsHeader = $est->code . ' - ' . $est->title;
                    }
                }

                $createdAhs = $hpp->ahs()->create([
                    'name_ahs' =>  $name_hpp_AHS . ' - ' . $nameAhsHeader,
                    'volume' => $ahsHeader['volume'] ?? 1,
                    'unit' => $ahsHeader['unit'] ?? 'Unit',
                    'duration' => $ahsHeader['duration'] ?? 1,
                    'duration_unit' => $ahsHeader['duration_unit'] ?? 'Hari',
                    'unit_price' => $unitPriceSum, // sum of detail grand totals
                    'total_price' => $groupTotal, // volume * unit_price_sum * duration
                ]);

                // Buat HPP items per AHS
                $details = $itemGroups[$groupIndex]['detail'] ?? [];
                foreach ($details as $detail) {
                    $hppAhsid = $createdAhs->id;
                    $estimationItemId = $detail['estimation_item_id'] ?? null;
                    $nameAhs = $createdAhs->name_ahs;
                    $description = $detail['description'] ?? '';
                    $unit = $detail['unit'] ?? ($estimationItemId ? $this->getItemUnit(EstimationItem::find($estimationItemId)) : 'Unit');
                    $coef = (float) ($detail['coefficient'] ?? 0);
                    $qty = (float) ($detail['quantity'] ?? 0);
                    $unitPrice = (float) ($detail['unit_price'] ?? 0);
                    $totalPrice = $unitPrice * $coef;

                    $hpp->items()->create([
                        'hpp_ahs_id' => $hppAhsid,
                        'estimation_item_id' => $estimationItemId,
                        'name_ahs' => $nameAhs,
                        'description' => $description,
                        'volume' => 1,
                        'unit' => $unit,
                        'duration' => 1,
                        'duration_unit' => 'Hari',
                        'koefisien' => $coef,
                        'unit_price' => $unitPrice,
                        'jumlah' => $qty,
                        'total_price' => $totalPrice,
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('hpp.index')->with('success', 'HPP berhasil diperbarui!');
        } catch (\Exception $e) {
            DB::rollback();

            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $hpp = Hpp::findOrFail($id);
            $hpp->items()->delete();
            $hpp->delete();

            return redirect()->route('hpp.index')->with('success', 'HPP berhasil dihapus!');
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Get estimation items for AJAX request
     */
    public function getEstimationItems(Request $request, string $id)
    {
        $hpp = Hpp::with(['items'])->findOrFail($id);
        $items = $hpp->items;

        return response()->json($items);
    }

    /**
     * Get AHS data for dropdown
     */
    public function getAhsData()
    {
        $ahsData = [];

        // Get Estimations (AHS) only
        $estimations = Estimation::with('items')->get();
        foreach ($estimations as $estimation) {
            $ahsData[] = [
                'type' => 'ahs',
                'id' => $estimation->id,
                'code' => $estimation->code,
                'title' => $estimation->title,
                'description' => $estimation->code . ' - ' . $estimation->title,
                'category' => 'AHS',
                'item_count' => $estimation->items->count(),
            ];
        }

        // Get Workers
        $workers = Worker::with('category')->get();
        foreach ($workers as $worker) {
            $ahsData[] = [
                'type' => 'worker',
                'id' => $worker->id,
                'code' => $worker->code,
                'title' => $worker->name,
                'description' => $worker->code . ' - ' . $worker->name,
                'unit_price' => $worker->price,
                'category' => 'Pekerja',
                'unit' => $worker->unit,
                'tkdn' => $worker->tkdn,
            ];
        }

        // Get Materials
        $materials = Material::with('category')->get();
        foreach ($materials as $material) {
            $ahsData[] = [
                'type' => 'material',
                'id' => $material->id,
                'code' => $material->code,
                'title' => $material->name,
                'description' => $material->code . ' - ' . $material->name,
                'unit_price' => $material->price,
                'category' => 'Material',
                'unit' => $material->unit,
                'tkdn' => $material->tkdn,
            ];
        }

        // Get Equipment
        $equipment = Equipment::with('category')->get();
        foreach ($equipment as $eq) {
            $ahsData[] = [
                'type' => 'equipment',
                'id' => $eq->id,
                'code' => $eq->code,
                'title' => $eq->name,
                'description' => $eq->code . ' - ' . $eq->name,
                'unit_price' => $eq->price,
                'category' => 'Peralatan',
                'period' => $eq->period,
                'tkdn' => $eq->tkdn,
            ];
        }

        return $ahsData;
    }

    /**
     * Get item name based on category
     */
    private function getItemName(EstimationItem $item): string
    {
        switch ($item->category) {
            case 'worker':
                return $item->worker ? $item->worker->name : 'Pekerja';
            case 'material':
                return $item->material ? $item->material->name : 'Material';
            case 'equipment':
                return $item->equipment ? $item->equipment->name : 'Peralatan';
            default:
                return 'Item';
        }
    }

    /**
     * Get item unit based on category
     */
    private function getItemUnit(EstimationItem $item): string
    {
        switch ($item->category) {
            case 'worker':
                return 'OH';
            case 'material':
                return 'Unit';
            case 'equipment':
                return 'Hari';
            default:
                return 'Unit';
        }
    }

    /**
     * Get AHS data for AJAX request
     */
    public function getAhsDataAjax(Request $request)
    {
        $ahsData = $this->getAhsData();

        return response()->json($ahsData);
    }

    /**
     * Approve HPP
     */
    public function approve(Hpp $hpp)
    {
        // Check if user can manage HPP
        if (! Auth::user()->can('manage-hpp')) {
            abort(403, 'Unauthorized action.');
        }

        // Check if HPP status is submitted
        if ($hpp->status !== 'submitted') {
            return redirect()->route('hpp.index')
                ->with('error', 'HPP hanya dapat disetujui jika statusnya "Diajukan".');
        }

        try {
            $hpp->update(['status' => 'approved']);

            return redirect()->route('hpp.index')
                ->with('success', 'HPP berhasil disetujui.');
        } catch (\Exception $e) {
            return redirect()->route('hpp.index')
                ->with('error', 'Terjadi kesalahan saat menyetujui HPP.');
        }
    }

    /**
     * Reject HPP
     */
    public function reject(Hpp $hpp)
    {
        // Check if user can manage HPP
        if (! Auth::user()->can('manage-hpp')) {
            abort(403, 'Unauthorized action.');
        }

        // Check if HPP status is submitted
        if ($hpp->status !== 'submitted') {
            return redirect()->route('hpp.index')
                ->with('error', 'HPP hanya dapat ditolak jika statusnya "Diajukan".');
        }

        try {
            $hpp->update(['status' => 'rejected']);

            return redirect()->route('hpp.index')
                ->with('success', 'HPP berhasil ditolak.');
        } catch (\Exception $e) {
            return redirect()->route('hpp.index')
                ->with('error', 'Terjadi kesalahan saat menolak HPP.');
        }
    }

    /**
     * Get AHS data filtered by project type
     */
    public function getAhsDataByProjectType($projectType)
    {
        $ahsData = [];

        // Get Estimations (AHS) filtered by project type
        $estimations = Estimation::with(['items' => function ($query) use ($projectType) {
            $query->forProjectType($projectType);
        }])->get();

        foreach ($estimations as $estimation) {
            $ahsData[] = [
                'type' => 'ahs',
                'id' => $estimation->id,
                'code' => $estimation->code,
                'title' => $estimation->title,
                'description' => $estimation->code . ' - ' . $estimation->title,
                'category' => 'AHS',
                'item_count' => $estimation->items->count(),
            ];
        }

        // Get Workers filtered by project type
        $workers = Worker::with('category')
            ->whereIn('classification_tkdn', $this->getClassificationsForProjectType($projectType))
            ->get();
        foreach ($workers as $worker) {
            $ahsData[] = [
                'type' => 'worker',
                'id' => $worker->id,
                'code' => $worker->code,
                'title' => $worker->name,
                'description' => $worker->code . ' - ' . $worker->name,
                'unit_price' => $worker->price,
                'category' => 'Pekerja',
                'unit' => $worker->unit,
                'tkdn' => $worker->tkdn,
                'classification_tkdn' => $worker->classification_tkdn,
            ];
        }

        // Get Materials filtered by project type
        $materials = Material::with('category')
            ->whereIn('classification_tkdn', $this->getClassificationsForProjectType($projectType))
            ->get();
        foreach ($materials as $material) {
            $ahsData[] = [
                'type' => 'material',
                'id' => $material->id,
                'code' => $material->code,
                'title' => $material->name,
                'description' => $material->code . ' - ' . $material->name,
                'unit_price' => $material->price,
                'category' => 'Material',
                'unit' => $material->unit,
                'tkdn' => $material->tkdn,
                'classification_tkdn' => $material->classification_tkdn,
            ];
        }

        // Get Equipment filtered by project type
        $equipment = Equipment::with('category')
            ->whereIn('classification_tkdn', $this->getClassificationsForProjectType($projectType))
            ->get();
        foreach ($equipment as $eq) {
            $ahsData[] = [
                'type' => 'equipment',
                'id' => $eq->id,
                'code' => $eq->code,
                'title' => $eq->name,
                'description' => $eq->code . ' - ' . $eq->name,
                'unit_price' => $eq->price,
                'category' => 'Peralatan',
                'period' => $eq->period,
                'tkdn' => $eq->tkdn,
                'classification_tkdn' => $eq->classification_tkdn,
            ];
        }

        return $ahsData;
    }

    /**
     * Get classifications for project type
     */
    private function getClassificationsForProjectType(string $projectType): array
    {
        // Menggunakan integer classification sesuai dengan StringHelper mapping
        return $projectType === 'tkdn_jasa'
            ? [1, 2, 3, 4] // Overhead & Manajemen, Alat Kerja / Fasilitas, Konstruksi & Fabrikasi, Peralatan (Jasa Umum)
            : [1, 2, 3, 4, 5, 6]; // Semua classification termasuk Material (Bahan Baku) dan Peralatan (Barang Jadi)
    }

    /**
     * Get AHS data only (filtered by project type)
     */
    public function getAhsDataOnly($projectType)
    {
        // Get Estimations (AHS) filtered by project type
        $estimations = Estimation::with(['items' => function ($query) use ($projectType) {
            $query->forProjectType($projectType);
        }])->get();

        $ahsData = [];
        foreach ($estimations as $estimation) {
            // Only include estimations that have items matching the project type
            if ($estimation->items->count() > 0) {
                $ahsData[] = [
                    'type' => 'ahs',
                    'id' => $estimation->id,
                    'code' => $estimation->code,
                    'title' => $estimation->title,
                    'description' => $estimation->code . ' - ' . $estimation->title, // data AHS 

                    'category' => 'AHS',
                    'item_count' => $estimation->items->count(),
                ];
            }
        }

        return response()->json($ahsData);
    }

    /**
     * Get AHS items with project type filtering
     */
    public function getAhsItems($estimationId, $projectType)
    {
        $estimation = Estimation::with(['items' => function ($query) use ($projectType) {
            $query->forProjectType($projectType);
        }])->findOrFail($estimationId);

        $items = $estimation->items->map(function ($item) {
            return [
                'id' => $item->id,
                'description' => $this->getItemName($item),
                'code' => $item->code,
                'category' => $item->category,
                'unit_price' => $item->unit_price,
                'coefficient' => $item->coefficient,
                'tkdn_value' => $item->tkdn_value,
                'unit' => $this->getItemUnit($item),
                'classification_tkdn' => $item->classification_tkdn,
            ];
        });

        return response()->json([
            'estimation' => [
                'id' => $estimation->id,
                'code' => $estimation->code,
                'title' => $estimation->title,
                'description' => $estimation->code . ' - ' . $estimation->title,
            ],
            'items' => $items,
        ]);
    }
    // public function getAhsItems($estimationId, $projectType = null)
    // {
    //     // Get estimation with all items (no project type filtering)
    //     $estimation = Estimation::with('items')->findOrFail($estimationId);

    //     $items = $estimation->items->map(function ($item) {
    //         return [
    //             'id' => $item->id,
    //             'description' => $this->getItemName($item),
    //             'code' => $item->code,
    //             'category' => $item->category,
    //             'unit_price' => $item->unit_price,
    //             'coefficient' => $item->coefficient,
    //             'tkdn_value' => $item->tkdn_value,
    //             'unit' => $this->getItemUnit($item),
    //             'classification_tkdn' => $item->classification_tkdn,
    //         ];
    //     });

    //     return response()->json([
    //         'estimation' => [
    //             'id' => $estimation->id,
    //             'code' => $estimation->code,
    //             'title' => $estimation->title,
    //             'description' => $estimation->code . ' - ' . $estimation->title,
    //         ],
    //         'items' => $items,
    //     ]);
    // }
}
