<?php

namespace App\Services;

use App\Models\HppItem;
use App\Models\Service;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ServiceExportService
{
    protected Service $service;

    protected string $classification;

    protected Spreadsheet $spreadsheet;

    protected int $currentRow = 1;

    protected int $headerStartRow = 0;
    protected int $subHeaderRow = 0;
    protected int $dataStartRow = 0;
    protected int $subTotalRow = 0;

    public function __construct(Service $service, string $classification)
    {
        $this->service = $service;
        $this->classification = $classification;

        // Create new spreadsheet with error handling
        try {
            $this->spreadsheet = new Spreadsheet;

            // Validate spreadsheet creation
            if (! $this->spreadsheet) {
                throw new \Exception('Gagal membuat objek Spreadsheet');
            }

            // Get active sheet and validate
            $worksheet = $this->spreadsheet->getActiveSheet();
            if (! $worksheet) {
                throw new \Exception('Gagal mendapatkan active worksheet');
            }

            // Set document properties
            $this->spreadsheet->getProperties()
                ->setCreator('TKDN System')
                ->setLastModifiedBy('TKDN System')
                ->setTitle('TKDN Form '.$classification)
                ->setSubject('TKDN Service Export')
                ->setDescription('Export TKDN form untuk service '.$service->id)
                ->setKeywords('TKDN, Excel, Export')
                ->setCategory('TKDN Forms');

            // Set default worksheet properties
            $worksheet->setTitle('TKDN Form '.$classification);

        } catch (\Exception $e) {
            Log::error('Failed to create Spreadsheet object', [
                'error' => $e->getMessage(),
                'service_id' => $service->id,
                'classification' => $classification,
                'spreadsheet_created' => $this->spreadsheet ? 'yes' : 'no',
            ]);
            throw new \Exception('Gagal membuat file Excel: '.$e->getMessage());
        }
    }

    public function export(): string
    {
        try {
            // Validate data before export
            $this->validateData();

            // Validate spreadsheet object
            if (! $this->spreadsheet) {
                throw new \Exception('Spreadsheet object tidak tersedia');
            }

            // Setup worksheet step by step with error handling
            $this->setupWorksheet();
            $this->addHeaderInformation();
            $this->addTableHeaders();
            $this->addTableData();
            $this->addSubTotal();
            $this->formatWorksheet();

            // Final validation before file generation
            if (! $this->spreadsheet->getActiveSheet()) {
                throw new \Exception('Worksheet tidak tersedia setelah setup');
            }

            // Generate and return file
            return $this->generateFile();

        } catch (\Exception $e) {
            Log::error('Failed to export TKDN form', [
                'service_id' => $this->service->id,
                'classification' => $this->classification,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'spreadsheet_status' => $this->spreadsheet ? 'available' : 'null',
                'worksheet_status' => $this->spreadsheet && $this->spreadsheet->getActiveSheet() ? 'available' : 'null',
            ]);

            throw new \Exception('Gagal membuat form TKDN: '.$e->getMessage());
        }
    }

    protected function setupWorksheet(): void
    {
        $worksheet = $this->spreadsheet->getActiveSheet();

        // Set page setup
        $worksheet->getPageSetup()->setOrientation(PageSetup::ORIENTATION_LANDSCAPE);
        $worksheet->getPageSetup()->setPaperSize(PageSetup::PAPERSIZE_A4);
        $worksheet->getPageSetup()->setFitToWidth(1);
        $worksheet->getPageSetup()->setFitToHeight(0);

        // Set margins
        $worksheet->getPageMargins()->setTop(0.5);
        $worksheet->getPageMargins()->setBottom(0.5);
        $worksheet->getPageMargins()->setLeft(0.5);
        $worksheet->getPageMargins()->setRight(0.5);

        // Set column widths
        $worksheet->getColumnDimension('A')->setWidth(5);   // No.
        $worksheet->getColumnDimension('B')->setWidth(40);  // Uraian
        $worksheet->getColumnDimension('C')->setWidth(15);  // Kualifikasi
        $worksheet->getColumnDimension('D')->setWidth(12);  // Kewarganegaraan
        $worksheet->getColumnDimension('E')->setWidth(12);  // TKDN (%)
        $worksheet->getColumnDimension('F')->setWidth(12);  // Jumlah
        $worksheet->getColumnDimension('G')->setWidth(15);  // Durasi
        $worksheet->getColumnDimension('H')->setWidth(20);  // Upah (Rupiah)
        $worksheet->getColumnDimension('I')->setWidth(18);  // KDN
        $worksheet->getColumnDimension('J')->setWidth(18);  // KLN
        $worksheet->getColumnDimension('K')->setWidth(18);  // TOTAL
    }

    protected function addHeaderInformation(): void
    {
        $worksheet = $this->spreadsheet->getActiveSheet();

        // Title based on classification
        $title = $this->getFormTitle();
        $worksheet->setCellValue('A1', $title);
        $worksheet->mergeCells('A1:K1');

        // Self Assessment box (top right)
        $worksheet->setCellValue('I2', 'Self Assessment');
        $worksheet->mergeCells('I2:K2');

        // Header information
        $row = 4;

        // Penyedia Barang / Jasa
        $worksheet->setCellValue("A{$row}", 'Penyedia Barang / Jasa:');
        $worksheet->setCellValue("B{$row}", $this->service->provider_name ?: 'PT Konstruksi Maju');
        $worksheet->mergeCells("B{$row}:K{$row}");

        $row++;

        // Alamat
        $worksheet->setCellValue("A{$row}", 'Alamat:');
        $worksheet->setCellValue("B{$row}", $this->service->provider_address ?: 'Jl. Sudirman No. 123, Jakarta Pusat');
        $worksheet->mergeCells("B{$row}:K{$row}");

        $row++;

        // Nama Jasa
        $worksheet->setCellValue("A{$row}", 'Nama Jasa:');
        $worksheet->setCellValue("B{$row}", $this->service->service_name);
        $worksheet->mergeCells("B{$row}:K{$row}");

        $row++;

        // Pengguna Barang/Jasa
        $worksheet->setCellValue("A{$row}", 'Pengguna Barang/Jasa:');
        $worksheet->setCellValue("B{$row}", $this->service->user_name ?: 'PT Pembangunan Indonesia');
        $worksheet->mergeCells("B{$row}:K{$row}");

        $row++;

        // No. Dokumen Jasa
        $worksheet->setCellValue("A{$row}", 'No. Dokumen Jasa:');
        $worksheet->setCellValue("B{$row}", $this->service->document_number ?: 'DOC-2024-001');
        $worksheet->mergeCells("B{$row}:K{$row}");

        $row += 2; // Add some space before table
        $this->setCurrentRow($row);
    }

    /**
     * Get form title based on TKDN classification
     */
    protected function getFormTitle(): string
    {
        return match ($this->classification) {
            '3.1' => 'Formulir 3.1: TKDN Jasa untuk Overhead & Manajemen',
            '3.2' => 'Formulir 3.2: TKDN Jasa untuk Alat Kerja dan Peralatan',
            '3.3' => 'Formulir 3.3: TKDN Jasa untuk Konstruksi dan Fabrikasi',
            '3.4' => 'Formulir 3.4: TKDN Jasa untuk Konsultasi dan Pengawasan',
            '3.5' => 'Formulir 3.5: Rangkuman TKDN Jasa',
            '4.1' => 'Formulir 4.1: TKDN Jasa untuk Material (Bahan Baku)',
            '4.2' => 'Formulir 4.2: TKDN Jasa untuk Peralatan (Barang Jadi)',
            '4.3' => 'Formulir 4.3: TKDN Jasa untuk Overhead & Manajemen',
            '4.4' => 'Formulir 4.4: TKDN Jasa untuk Alat / Fasilitas Kerja',
            '4.5' => 'Formulir 4.5: TKDN Jasa untuk Konstruksi & Fabrikasi',
            '4.6' => 'Formulir 4.6: TKDN Jasa untuk Peralatan (Jasa Umum)',
            '4.7' => 'Formulir 4.7: TKDN Jasa - Summary',
            'all' => 'Formulir TKDN Jasa - Semua Klasifikasi',
            default => 'Formulir TKDN Jasa',
        };
    }

    protected function addTableHeaders(): void
    {
        $worksheet = $this->spreadsheet->getActiveSheet();
        $row = $this->getCurrentRow();

        $this->headerStartRow = $row;

        // Main header row
        $worksheet->setCellValue("A{$row}", 'No.');
        $worksheet->setCellValue("B{$row}", 'Uraian');
        $worksheet->setCellValue("C{$row}", 'Kualifikasi');
        $worksheet->setCellValue("D{$row}", 'Kewarganegaraan');
        $worksheet->setCellValue("E{$row}", 'TKDN (%)');
        $worksheet->setCellValue("F{$row}", 'Jumlah');
        $worksheet->setCellValue("G{$row}", 'Durasi');
        $worksheet->setCellValue("H{$row}", 'Upah (Rupiah)');
        $worksheet->mergeCells("I{$row}:K{$row}");
        $worksheet->setCellValue("I{$row}", 'BIAYA (Rupiah)');

        $row++;

        // Sub header row
        $worksheet->setCellValue("I{$row}", 'KDN');
        $worksheet->setCellValue("J{$row}", 'KLN');
        $worksheet->setCellValue("K{$row}", 'TOTAL');

        $this->subHeaderRow = $row;
        $this->dataStartRow = $row + 1;
        $this->setCurrentRow($row + 1);
    }

    protected function addTableData(): void
    {
        $worksheet = $this->spreadsheet->getActiveSheet();
        $row = $this->getCurrentRow();

        // Special handling for Form 3.1 to match the view's fixed items pattern
        if ($this->classification === '3.1') {
            // Calculate total HPP value from all HPP items for the service's project
            $totalHppValue = HppItem::whereHas('hpp', function ($query) {
                $query->where('project_id', $this->service->project_id);
            })->sum('total_price');

            // Compute fixed items amounts
            $overheadAmount = $totalHppValue * 0.08;   // 8%
            $managementAmount = $totalHppValue * 0.12; // 12%

            // Row 1: Overhead management
            $worksheet->setCellValue("A{$row}", 1);
            $worksheet->setCellValue("B{$row}", 'Overhead management');
            $worksheet->setCellValue("C{$row}", '-');
            $worksheet->setCellValue("D{$row}", 'WNI');
            $worksheet->setCellValue("E{$row}", 1); // 100%
            $worksheet->setCellValue("F{$row}", 1); // Jumlah
            $worksheet->setCellValue("G{$row}", '1 paket'); // Durasi
            $worksheet->setCellValue("H{$row}", $overheadAmount); // Upah
            $worksheet->setCellValue("I{$row}", $overheadAmount); // KDN
            $worksheet->setCellValue("J{$row}", '-');            // KLN
            $worksheet->setCellValue("K{$row}", $overheadAmount); // TOTAL
            $row++;

            // Row 2: Management
            $worksheet->setCellValue("A{$row}", 2);
            $worksheet->setCellValue("B{$row}", 'Management');
            $worksheet->setCellValue("C{$row}", '-');
            $worksheet->setCellValue("D{$row}", 'WNI');
            $worksheet->setCellValue("E{$row}", 1); // 100%
            $worksheet->setCellValue("F{$row}", 1); // Jumlah
            $worksheet->setCellValue("G{$row}", '1 paket'); // Durasi
            $worksheet->setCellValue("H{$row}", $managementAmount); // Upah
            $worksheet->setCellValue("I{$row}", $managementAmount); // KDN
            $worksheet->setCellValue("J{$row}", '-');               // KLN
            $worksheet->setCellValue("K{$row}", $managementAmount); // TOTAL

            $this->setCurrentRow($row + 1);
            return;
        }

        // Special handling for Form 4.3 - Overhead management only (8% of total service value)
        if ($this->classification === '4.3') {
            // Calculate total from all service items for percentage calculation
            $totalServiceValue = $this->service->items()->sum('total_cost');
            $overheadAmount = $totalServiceValue * 0.08; // 8%

            // Row 1: Overhead management
            $worksheet->setCellValue("A{$row}", 1);
            $worksheet->setCellValue("B{$row}", 'Overhead management');
            $worksheet->setCellValue("C{$row}", '-');
            $worksheet->setCellValue("D{$row}", 'WNI');
            $worksheet->setCellValue("E{$row}", 1); // 100%
            $worksheet->setCellValue("F{$row}", 1); // Jumlah
            $worksheet->setCellValue("G{$row}", '1 paket'); // Durasi
            $worksheet->setCellValue("H{$row}", $overheadAmount); // Upah
            $worksheet->setCellValue("I{$row}", $overheadAmount); // KDN
            $worksheet->setCellValue("J{$row}", '-'); // KLN
            $worksheet->setCellValue("K{$row}", $overheadAmount); // TOTAL

            $this->setCurrentRow($row + 1);
            return;
        }

        // Get data using optimized service items
        if ($this->classification === 'all') {
            $items = $this->service->getOptimizedServiceItems();
        } else {
            $items = $this->service->getServiceItemsByClassification($this->classification);
        }

        if ($items->isEmpty()) {
            // If no service items, try to get from HPP items
            $items = $this->getHppItems();
        }

        // If still no items, add a placeholder row
        if ($items->isEmpty()) {
            $this->addPlaceholderRow($worksheet, $row);
            $this->setCurrentRow($row + 1);

            return;
        }

        $itemNumber = 1;

        // Group items by category if classification is 3.3 (Construction/Fabrication)
        if ($this->classification === '3.3') {
            $groupedItems = $this->groupItemsByCategory($items);
            $row = $this->addGroupedTableData($worksheet, $groupedItems, $row, $itemNumber);
        } else {
            $row = $this->addSimpleTableData($worksheet, $items, $row, $itemNumber);
        }

        $this->setCurrentRow($row);
    }

    /**
     * Add placeholder row when no data is available
     */
    protected function addPlaceholderRow($worksheet, int $row): void
    {
        $worksheet->setCellValue("A{$row}", '1');
        $worksheet->setCellValue("B{$row}", 'Tidak ada data tersedia');
        $worksheet->setCellValue("C{$row}", '-');
        $worksheet->setCellValue("D{$row}", '-');
        $worksheet->setCellValue("E{$row}", 1);
        $worksheet->setCellValue("F{$row}", '0');
        $worksheet->setCellValue("G{$row}", '0 ls');
        $worksheet->setCellValue("H{$row}", '0');
        $worksheet->setCellValue("I{$row}", '0');
        $worksheet->setCellValue("J{$row}", '-');
        $worksheet->setCellValue("K{$row}", '0');

        // Style the placeholder row
        $worksheet->getStyle("A{$row}:K{$row}")->getFont()->setItalic(true);
        $worksheet->getStyle("A{$row}:K{$row}")->getFont()->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('808080'));
    }

    /**
     * Validate data before export
     */
    protected function validateData(): void
    {
        // Check if service has basic information
        if (empty($this->service->service_name)) {
            throw new \Exception('Nama service tidak boleh kosong');
        }

        if (empty($this->service->project_id)) {
            throw new \Exception('Project ID tidak boleh kosong');
        }

        // Check if there's any data to export
        $hasServiceItems = $this->service->items()->exists();
        $hasHppItems = HppItem::whereHas('hpp', function ($query) {
            $query->where('project_id', $this->service->project_id);
        })->exists();

        if (! $hasServiceItems && ! $hasHppItems) {
            throw new \Exception('Tidak ada data yang dapat di-export untuk form ini');
        }
    }

    /**
     * Add simple table data without grouping
     */
    protected function addSimpleTableData($worksheet, $items, int $row, int $itemNumber): int
    {
        foreach ($items as $item) {
            $worksheet->setCellValue("A{$row}", $itemNumber);
            $worksheet->setCellValue("B{$row}", $item->description ?? 'Item '.$itemNumber);
            $worksheet->setCellValue("C{$row}", $item->qualification ?? '-');
            $worksheet->setCellValue("D{$row}", $item->nationality ?? 'WNI');
            $worksheet->setCellValue("E{$row}", ($item->tkdn_percentage ?? 100) / 100);
            $worksheet->setCellValue("F{$row}", $item->quantity ?? 1);
            $worksheet->setCellValue("G{$row}", ($item->duration ?? 1).' '.($item->duration_unit ?? 'ls'));

            // Calculate costs
            $wage = $item->wage ?? $item->total_price ?? 0;
            $quantity = $item->quantity ?? 1;
            $duration = $item->duration ?? 1;
            $totalItemCost = $wage * $quantity * $duration;

            $tkdnPercentage = $item->tkdn_percentage ?? 100;

            if ($tkdnPercentage == 100) {
                $domesticCost = $totalItemCost;
                $foreignCost = 0;
            } else {
                $domesticCost = ($totalItemCost * $tkdnPercentage) / 100;
                $foreignCost = $totalItemCost - $domesticCost;
            }

            $worksheet->setCellValue("H{$row}", $wage);
            $worksheet->setCellValue("I{$row}", $domesticCost);
            $worksheet->setCellValue("J{$row}", $foreignCost > 0 ? $foreignCost : '-');
            $worksheet->setCellValue("K{$row}", $totalItemCost);

            $row++;
            $itemNumber++;
        }

        return $row;
    }

    /**
     * Add grouped table data for Form 3.3 (Construction/Fabrication)
     */
    protected function addGroupedTableData($worksheet, array $groupedItems, int $row, int $itemNumber): int
    {
        foreach ($groupedItems as $category => $items) {
            // Add category header
            $worksheet->setCellValue("A{$row}", $itemNumber.'.');
            $worksheet->setCellValue("B{$row}", $category);
            $worksheet->mergeCells("B{$row}:K{$row}");
            $worksheet->getStyle("A{$row}:K{$row}")->getFont()->setBold(true);
            $worksheet->getStyle("A{$row}:K{$row}")->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('F3F4F6');
            $row++;

            // Add items under category
            foreach ($items as $item) {
                $worksheet->setCellValue("A{$row}", '');
                $worksheet->setCellValue("B{$row}", $item->description ?? 'Item '.$itemNumber);
                $worksheet->setCellValue("C{$row}", $item->qualification ?? '-');
                $worksheet->setCellValue("D{$row}", $item->nationality ?? 'WNI');
                $worksheet->setCellValue("E{$row}", ($item->tkdn_percentage ?? 100) / 100);
                $worksheet->setCellValue("F{$row}", $item->quantity ?? 1);
                $worksheet->setCellValue("G{$row}", ($item->duration ?? 1).' '.($item->duration_unit ?? 'ls'));

                // Calculate costs
                $wage = $item->wage ?? $item->total_price ?? 0;
                $quantity = $item->quantity ?? 1;
                $duration = $item->duration ?? 1;
                $totalItemCost = $wage * $quantity * $duration;

                $tkdnPercentage = $item->tkdn_percentage ?? 100;

                if ($tkdnPercentage == 100) {
                    $domesticCost = $totalItemCost;
                    $foreignCost = 0;
                } else {
                    $domesticCost = ($totalItemCost * $tkdnPercentage) / 100;
                    $foreignCost = $totalItemCost - $domesticCost;
                }

                $worksheet->setCellValue("H{$row}", $wage);
                $worksheet->setCellValue("I{$row}", $domesticCost);
                $worksheet->setCellValue("J{$row}", $foreignCost > 0 ? $foreignCost : '-');
                $worksheet->setCellValue("K{$row}", $totalItemCost);

                $row++;
                $itemNumber++;
            }
        }

        return $row;
    }

    /**
     * Group items by category for Form 3.3
     */
    protected function groupItemsByCategory($items): array
    {
        $grouped = [];

        foreach ($items as $item) {
            $description = $item->description ?? '';

            // Determine category based on description
            if (stripos($description, 'arsip') !== false) {
                if (stripos($description, 'penyimpanan') !== false) {
                    $category = 'Penyimpanan Arsip';
                } elseif (stripos($description, 'penerimaan') !== false || stripos($description, 'pengangkutan') !== false) {
                    $category = 'Penerimaan dan Pengangkutan Arsip';
                } else {
                    $category = 'Penataan Arsip';
                }
            } elseif (stripos($description, 'database') !== false || stripos($description, 'pemilahan') !== false) {
                $category = 'Pemilahan dan Update Database';
            } elseif (stripos($description, 'security') !== false) {
                $category = 'Security';
            } elseif (stripos($description, 'driver') !== false || stripos($description, 'angkut') !== false) {
                $category = 'Transportasi';
            } else {
                $category = 'Lainnya';
            }

            $grouped[$category][] = $item;
        }

        return $grouped;
    }

    protected function addSubTotal(): void
    {
        $worksheet = $this->spreadsheet->getActiveSheet();
        $row = $this->getCurrentRow();

        $this->subTotalRow = $row;

        // Special SUB TOTAL for Form 3.1 fixed items pattern
        if ($this->classification === '3.1') {
            $totalHppValue = HppItem::whereHas('hpp', function ($query) {
                $query->where('project_id', $this->service->project_id);
            })->sum('total_price');

            $overheadAmount = $totalHppValue * 0.08;
            $managementAmount = $totalHppValue * 0.12;
            $subtotal = $overheadAmount + $managementAmount;

            // SUB TOTAL row
            $worksheet->setCellValue("A{$row}", 'SUB TOTAL');
            $worksheet->mergeCells("A{$row}:G{$row}");
            $worksheet->setCellValue("H{$row}", $subtotal);
            $worksheet->setCellValue("I{$row}", $subtotal);
            $worksheet->setCellValue("J{$row}", '-');
            $worksheet->setCellValue("K{$row}", $subtotal);

            $this->setCurrentRow($row + 1);
            return;
        }

        // Special SUB TOTAL for Form 4.3 fixed Overhead item
        if ($this->classification === '4.3') {
            $totalServiceValue = $this->service->items()->sum('total_cost');
            $overheadAmount = $totalServiceValue * 0.08;
            $subtotal = $overheadAmount;

            // SUB TOTAL row
            $worksheet->setCellValue("A{$row}", 'SUB TOTAL');
            $worksheet->mergeCells("A{$row}:G{$row}");
            $worksheet->setCellValue("H{$row}", $subtotal);
            $worksheet->setCellValue("I{$row}", $subtotal);
            $worksheet->setCellValue("J{$row}", '-');
            $worksheet->setCellValue("K{$row}", $subtotal);

            $this->setCurrentRow($row + 1);
            return;
        }

        // Get data for subtotal calculation
        if ($this->classification === 'all') {
            $items = $this->service->items()->get();
        } else {
            $items = $this->service->items()
                ->where('tkdn_classification', $this->classification)
                ->get();
        }

        if ($items->isEmpty()) {
            $items = $this->getHppItems();
        }

        // Calculate totals
        $totalWage = 0;
        $totalDomestic = 0;
        $totalForeign = 0;

        if ($items->isNotEmpty()) {
            foreach ($items as $item) {
                $wage = $item->wage ?? $item->total_price ?? 0;
                $quantity = $item->quantity ?? 1;
                $duration = $item->duration ?? 1;

                // Calculate total cost for this item
                $totalItemCost = $wage * $quantity * $duration;
                $totalWage += $totalItemCost;

                $tkdnPercentage = $item->tkdn_percentage ?? 100;

                if ($tkdnPercentage == 100) {
                    $totalDomestic += $totalItemCost;
                } else {
                    $totalDomestic += ($totalItemCost * $tkdnPercentage) / 100;
                    $totalForeign += $totalItemCost - (($totalItemCost * $tkdnPercentage) / 100);
                }
            }
        }

        // SUB TOTAL row
        $worksheet->setCellValue("A{$row}", 'SUB TOTAL');
        $worksheet->mergeCells("A{$row}:G{$row}");
        $worksheet->setCellValue("H{$row}", $totalWage);
        $worksheet->setCellValue("I{$row}", $totalDomestic);
        $worksheet->setCellValue("J{$row}", $totalForeign > 0 ? $totalForeign : '-');
        $worksheet->setCellValue("K{$row}", $totalWage);

        $this->setCurrentRow($row + 1);
    }

    protected function formatWorksheet(): void
    {
        $worksheet = $this->spreadsheet->getActiveSheet();

        // Format title
        $worksheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
        $worksheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Format header information
        $worksheet->getStyle('A4:A8')->getFont()->setBold(true);
        $worksheet->getStyle('B4:B8')->getFont()->setBold(true);

        // Format table headers using recorded rows
        $headerRange = 'A'.$this->headerStartRow.':K'.$this->subHeaderRow;
        $worksheet->getStyle($headerRange)->getFont()->setBold(true);
        $worksheet->getStyle($headerRange)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $worksheet->getStyle($headerRange)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('E5E7EB');

        // Format currency columns
        $currencyColumns = ['H', 'I', 'K'];
        foreach ($currencyColumns as $col) {
            $worksheet->getStyle($col.'1:'.$col.'1000')->getNumberFormat()->setFormatCode('#,##0');
        }

        // Format percentage column
        $worksheet->getStyle('E1:E1000')->getNumberFormat()->setFormatCode('0.0%');

        // Add borders for data region
        $dataRange = 'A'.$this->dataStartRow.':K'.$this->subTotalRow;
        $worksheet->getStyle($dataRange)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

        // Format SUB TOTAL row
        $subtotalRow = $this->subTotalRow;
        $worksheet->getStyle("A{$subtotalRow}:K{$subtotalRow}")->getFont()->setBold(true);
        $worksheet->getStyle("A{$subtotalRow}:K{$subtotalRow}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('DBEAFE');

        // Center align SUB TOTAL text
        $worksheet->getStyle("A{$subtotalRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    }

    protected function generateFile(): string
    {
        try {
            // Validate spreadsheet object
            if (! $this->spreadsheet || ! $this->spreadsheet->getActiveSheet()) {
                throw new \Exception('Spreadsheet object tidak valid');
            }

            // Create Xlsx writer with error handling
            $writer = new Xlsx($this->spreadsheet);

            // Set writer properties for better compatibility
            $writer->setPreCalculateFormulas(false);
            $writer->setIncludeCharts(false);

            $filename = 'TKDN_Service_'.$this->service->id.'_'.$this->classification.'_'.date('Y-m-d_H-i-s').'.xlsx';
            $filepath = storage_path('app/public/exports/'.$filename);

            // Ensure directory exists
            if (! file_exists(dirname($filepath))) {
                mkdir(dirname($filepath), 0755, true);
            }

            // Clean up any existing file
            if (file_exists($filepath)) {
                unlink($filepath);
            }

            // Save file with error handling
            $writer->save($filepath);

            // Verify file was created and is readable
            if (! file_exists($filepath)) {
                throw new \Exception('File tidak dapat dibuat: '.$filepath);
            }

            if (! is_readable($filepath)) {
                throw new \Exception('File tidak dapat dibaca: '.$filepath);
            }

            // Check file size
            $fileSize = filesize($filepath);
            if ($fileSize === 0) {
                throw new \Exception('File Excel kosong (0 bytes)');
            }

            if ($fileSize < 1000) { // Less than 1KB is suspicious for Excel file
                throw new \Exception('File Excel terlalu kecil, kemungkinan rusak');
            }

            // Verify file extension
            $fileExtension = pathinfo($filepath, PATHINFO_EXTENSION);
            if ($fileExtension !== 'xlsx') {
                throw new \Exception('File yang dihasilkan bukan file Excel (.xlsx): '.$fileExtension);
            }

            // Verify file content (basic Excel file signature check)
            $fileContent = file_get_contents($filepath, false, null, 0, 4);
            if ($fileContent !== 'PK'.chr(0x03).chr(0x04)) {
                throw new \Exception('File Excel tidak memiliki signature yang valid');
            }

            return $filepath;

        } catch (\Exception $e) {
            // Log error for debugging
            Log::error('Error generating Excel file: '.$e->getMessage(), [
                'service_id' => $this->service->id,
                'classification' => $this->classification,
                'filepath' => $filepath ?? 'unknown',
                'spreadsheet_valid' => $this->spreadsheet ? 'yes' : 'no',
                'active_sheet' => $this->spreadsheet && $this->spreadsheet->getActiveSheet() ? 'yes' : 'no',
            ]);

            throw $e;
        }
    }

    protected function getHppItems()
    {
        if ($this->classification === 'all') {
            return HppItem::whereHas('hpp', function ($query) {
                $query->where('project_id', $this->service->project_id);
            })->get();
        }

        return HppItem::whereHas('hpp', function ($query) {
            $query->where('project_id', $this->service->project_id);
        })
            ->where('tkdn_classification', $this->classification)
            ->get();
    }

    protected function getCurrentRow(): int
    {
        return $this->currentRow ?? 1;
    }

    protected function setCurrentRow(int $row): void
    {
        $this->currentRow = $row;
    }

    /**
     * Cleanup resources and handle errors gracefully
     */
    protected function cleanup(): void
    {
        try {
            if ($this->spreadsheet) {
                $this->spreadsheet->disconnectWorksheets();
                unset($this->spreadsheet);
            }
        } catch (\Exception $e) {
            Log::warning('Error during cleanup: '.$e->getMessage());
        }
    }

    /**
     * Destructor to ensure cleanup
     */
    public function __destruct()
    {
        $this->cleanup();
    }
}
