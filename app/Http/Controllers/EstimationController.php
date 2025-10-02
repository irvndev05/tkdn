<?php

namespace App\Http\Controllers;

use App\Models\Equipment;
use App\Models\Estimation;
use App\Models\EstimationItem;
use App\Models\Material;
use App\Models\Worker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EstimationController extends Controller
{
    public function index()
    {
        $estimations = Estimation::withCount('items')->latest()->paginate(10);

        return view('estimation.index', compact('estimations'));
    }

    public function create()
    {
        $workers = Worker::select('id', 'name', 'unit', 'price', 'code', 'location')->get();
        $materials = Material::select('id', 'name', 'specification', 'unit', 'price', 'code', 'location')->get();
        $equipment = Equipment::select('id', 'name', 'period', 'price', 'description', 'code', 'location')->get();

        return view('estimation.create2', compact('workers', 'materials', 'equipment'));
    }

    public function store(Request $request)
    {
        try {
            $data = $request->validate([
                'title' => 'required|string|max:255',
                'total' => 'nullable|integer|min:0',
                'total_unit_price' => 'nullable|integer|min:0',
                'items' => 'array',
                'items.*.category' => 'required|in:worker,material,equipment',
                'items.*.reference_id' => 'nullable|ulid',
                'items.*.code' => 'nullable|string|max:255',
                'items.*.coefficient' => 'nullable|numeric',
                'items.*.unit_price' => 'nullable|integer',
                'items.*.total_price' => 'nullable|integer',
            ]);

            // Generate kode estimasi berdasarkan item-item yang dipilih
            $data['code'] = $this->generateEstimationCode($data['items'] ?? []);

            $estimation = Estimation::create($data);
            $this->syncEstimationItems($estimation, $data['items'] ?? []);

            return redirect()->route('master.estimation.show', $estimation->id)->with('status', 'AHS & item berhasil ditambahkan!');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->validator)->withInput();
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()])->withInput();
        }
    }

    public function show(Estimation $estimation)
    {
        $estimation->load('items');

        return view('estimation.show', compact('estimation'));
    }

    public function edit(Estimation $estimation)
    {
        $estimation->load('items');
        $workers = Worker::select('id', 'name', 'unit', 'price', 'code', 'location')->get();
        $materials = Material::select('id', 'name', 'specification', 'unit', 'price', 'code', 'location')->get();
        $equipment = Equipment::select('id', 'name', 'period', 'price', 'description', 'code', 'location')->get();

        return view('estimation.edit', compact('estimation', 'workers', 'materials', 'equipment'));
    }

    public function update(Request $request, Estimation $estimation)
    {
        try {
            $data = $request->validate([
                'title' => 'required|string|max:255',
                'total' => 'nullable|integer|min:0',
                'total_unit_price' => 'nullable|integer|min:0',
                'items' => 'array',
                'items.*.id' => 'nullable|ulid',
                'items.*.category' => 'required|in:worker,material,equipment',
                'items.*.reference_id' => 'nullable|ulid',
                'items.*.code' => 'nullable|string|max:255',
                'items.*.coefficient' => 'nullable|numeric',
                'items.*.unit_price' => 'nullable|integer',
                'items.*.total_price' => 'nullable|integer',
            ]);

            // Generate kode estimasi berdasarkan item-item yang dipilih
            $data['code'] = $this->generateEstimationCode($data['items'] ?? []);

            $estimation->update($data);
            $this->syncEstimationItems($estimation, $data['items'] ?? []);

            return redirect()->route('master.estimation.show', $estimation->id)->with('status', 'AHS & item berhasil diupdate!');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->validator)->withInput();
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Generate kode estimasi berdasarkan kombinasi kode kategori dan kode material/jasa
     * Format: AHS.{kode_kategori}.{kode_item}.{timestamp}
     * Contoh: AHS.PJ.MT.PJ001.MT001.20241201143022
     */
    private function generateEstimationCode(array $items): string
    {
        if (empty($items)) {
            // Jika tidak ada item, gunakan format default
            return 'AHS.' . date('Ymd') . '.' . str_pad(Estimation::count() + 1, 4, '0', STR_PAD_LEFT);
        }

        $categoryCodes = [];
        $itemCodes = [];

        foreach ($items as $item) {
            if (empty($item['reference_id'])) {
                continue;
            }

            $category = $item['category'];
            $referenceId = $item['reference_id'];

            // Ambil kode kategori
            $categoryCode = $this->getCategoryCode($category);
            if ($categoryCode) {
                $categoryCodes[] = $categoryCode;
            }

            // Ambil kode material/jasa berdasarkan kategori
            $itemCode = $this->getItemCode($category, $referenceId);
            if ($itemCode) {
                $itemCodes[] = $itemCode;
            }
        }

        // Gabungkan kode kategori dan item
        $combinedCodes = [];
        if (! empty($categoryCodes)) {
            $combinedCodes[] = implode('.', array_unique($categoryCodes));
        }
        if (! empty($itemCodes)) {
            $combinedCodes[] = implode('.', array_unique($itemCodes));
        }

        // Jika ada kode yang digabungkan, gunakan format: AHS-{kode_kategori}-{kode_item}-{timestamp}
        if (! empty($combinedCodes)) {
            $timestamp = date('YmdHis');

            return 'AHS.' . implode('.', $combinedCodes) . '.' . $timestamp;
        }

        // Fallback ke format default jika tidak ada kode yang valid
        return 'AHS.' . date('Ymd') . '.' . str_pad(Estimation::count() + 1, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Ambil kode kategori berdasarkan tipe item
     */
    private function getCategoryCode(string $category): ?string
    {
        $categoryMap = [
            'worker' => 'PJ', // Pekerja
            'material' => 'MT', // Material
            'equipment' => 'EQ', // Peralatan
        ];

        return $categoryMap[$category] ?? null;
    }

    /**
     * Ambil kode material/jasa berdasarkan kategori dan reference_id
     */
    private function getItemCode(string $category, string $referenceId): ?string
    {
        switch ($category) {
            case 'worker':
                $worker = Worker::with('category')->find($referenceId);
                if ($worker && $worker->code) {
                    return $worker->code;
                }
                break;

            case 'material':
                $material = Material::with('category')->find($referenceId);
                if ($material && $material->code) {
                    return $material->code;
                }
                break;

            case 'equipment':
                $equipment = Equipment::with('category')->find($referenceId);
                if ($equipment && $equipment->code) {
                    return $equipment->code;
                }
                break;
        }

        return null;
    }

    /**
     * Sync estimation items for update.
     */
    private function syncEstimationItems(Estimation $estimation, array $items)
    {
        $ids = [];
        foreach ($items as $item) {
            // Calculate total_price if not set
            if (! isset($item['total_price']) || $item['total_price'] === null) {
                $item['total_price'] = (float) ($item['coefficient'] ?? 0) * (float) ($item['unit_price'] ?? 0);
            }

            // Auto-fill code based on reference_id if code is empty
            if (empty($item['code']) && ! empty($item['reference_id'])) {
                $item['code'] = $this->getItemCode($item['category'], $item['reference_id']);
            }

            if (! empty($item['id'])) {
                $estItem = EstimationItem::find($item['id']);
                if ($estItem) {
                    $estItem->update($item);
                    $ids[] = $estItem->id;
                }
            } else {
                $item['estimation_id'] = $estimation->id;
                $newItem = EstimationItem::create($item);
                $ids[] = $newItem->id;
            }
        }
        // Delete removed items
        $estimation->items()->whereNotIn('id', $ids)->delete();
    }

    public function destroy(Estimation $estimation)
    {
        $estimation->delete();

        return redirect()->route('master.estimation.index')->with('status', 'AHS berhasil dihapus!');
    }

    /**
     * Delete all estimations from the database
     */
    public function deleteAll(Request $request)
    {
        try {
            $totalRecords = Estimation::count();
            
            if ($totalRecords === 0) {
                return redirect()->route('master.estimation.index')->with('info', 'No estimation records found to delete.');
            }

            // Use database transaction for safety
            DB::beginTransaction();
            
            // Delete all estimation items first (foreign key constraint)
            EstimationItem::query()->delete();
            
            // Delete all estimation records
            Estimation::query()->delete();
            
            // Reset auto increment counter if using MySQL
            DB::statement('ALTER TABLE estimations AUTO_INCREMENT = 1');
            DB::statement('ALTER TABLE estimation_items AUTO_INCREMENT = 1');
            
            DB::commit();
            
            return redirect()->route('master.estimation.index')->with('success', "Successfully deleted {$totalRecords} estimation records.");
            
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'An error occurred while deleting all estimations: '.$e->getMessage()]);
        }
    }
}
