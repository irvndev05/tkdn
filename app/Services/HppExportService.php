<?php

namespace App\Services;

use App\Models\Hpp;
use App\Models\HppItem;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use Illuminate\Support\Facades\Log;

class HppExportService
{
    protected $spreadsheet;
    protected $hpp;
    protected int $currentRow = 1;

    public function __construct(Hpp $hpp)
    {
        $this->spreadsheet = new Spreadsheet();
        $this->hpp = $hpp;
    }

    public function export()
    {
        $this->setDocumentProperties();
        $this->setupWorksheet();
        $this->addHeaderInformation();
        $this->addTableHeaders();
        $this->addTableData();
        $this->addSummaryRows();
        $this->formatWorksheet();

        return $this->generateFile();
    }

    protected function setDocumentProperties(): void
    {
        $this->spreadsheet->getProperties()
            ->setCreator('PGN MAS')
            ->setLastModifiedBy('PGN MAS')
            ->setTitle('HPP Export')
            ->setSubject('HPP Export')
            ->setDescription('HPP Export for ' . $this->hpp->name_hpp)
            ->setKeywords('hpp export excel')
            ->setCategory('HPP');
    }

    protected function setupWorksheet(): void
    {
        $worksheet = $this->spreadsheet->getActiveSheet();
        $worksheet->setTitle('HPP');

        // Page setup
        $worksheet->getPageSetup()->setOrientation(PageSetup::ORIENTATION_LANDSCAPE);
        $worksheet->getPageSetup()->setPaperSize(PageSetup::PAPERSIZE_A4);
        $worksheet->getPageSetup()->setFitToWidth(1);
        $worksheet->getPageSetup()->setFitToHeight(0);

        // Margins
        $worksheet->getPageMargins()->setTop(0.5);
        $worksheet->getPageMargins()->setBottom(0.5);
        $worksheet->getPageMargins()->setLeft(0.5);
        $worksheet->getPageMargins()->setRight(0.5);

        // Column widths
        $worksheet->getColumnDimension('A')->setWidth(6);   // No.
        $worksheet->getColumnDimension('B')->setWidth(50);  // Uraian
        $worksheet->getColumnDimension('C')->setWidth(12);  // Volume
        $worksheet->getColumnDimension('D')->setWidth(10);  // Sat.
        $worksheet->getColumnDimension('E')->setWidth(12);  // Durasi
        $worksheet->getColumnDimension('F')->setWidth(10);  // Sat
        $worksheet->getColumnDimension('G')->setWidth(18);  // Har Sat.
        $worksheet->getColumnDimension('H')->setWidth(18);  // Jumlah Harga
        $worksheet->getColumnDimension('I')->setWidth(30);  // Keterangan
    }

    protected function addHeaderInformation(): void
    {
        $worksheet = $this->spreadsheet->getActiveSheet();

        // Title block
        $worksheet->setCellValue('A1', 'HPP');
        $worksheet->mergeCells('A1:I1');
        $worksheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
        $worksheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $title2 = $this->hpp->work_description ?: $this->hpp->name_hpp;
        $worksheet->setCellValue('A2', $title2);
        $worksheet->mergeCells('A2:I2');
        $worksheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $company = $this->hpp->company_name ?: ($this->hpp->project->company ?? '');
        if (!empty($company)) {
            $worksheet->setCellValue('A3', $company);
            $worksheet->mergeCells('A3:I3');
            $worksheet->getStyle('A3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        }

        $this->setCurrentRow(5);
    }

    protected function addTableHeaders(): void
    {
        $worksheet = $this->spreadsheet->getActiveSheet();
        $row = $this->getCurrentRow();

        $worksheet->setCellValue("A{$row}", 'NO.');
        $worksheet->setCellValue("B{$row}", 'URAIAN BARANG/PEKERJAAN');
        $worksheet->setCellValue("C{$row}", 'VOLUME');
        $worksheet->setCellValue("D{$row}", 'SAT.');
        $worksheet->setCellValue("E{$row}", 'Durasi');
        $worksheet->setCellValue("F{$row}", 'Sat');
        $worksheet->setCellValue("G{$row}", 'HAR SAT.');
        $worksheet->setCellValue("H{$row}", 'JUMLAH HARGA');
        $worksheet->setCellValue("I{$row}", 'Keterangan');

        // Style header
        $worksheet->getStyle("A{$row}:I{$row}")->getFont()->setBold(true);
        $worksheet->getStyle("A{$row}:I{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $worksheet->getStyle("A{$row}:I{$row}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('E5E7EB');

        $this->setCurrentRow($row + 1);
    }

    protected function addTableData(): void
    {
        $worksheet = $this->spreadsheet->getActiveSheet();
        $row = $this->getCurrentRow();

        $ahsGroups = $this->hpp->ahs()->with('hppItems.estimationItem')->get();

        // If no AHS groups are found, but items exist, render items directly
        if ($ahsGroups->isEmpty() && $this->hpp->items()->exists()) {
            // Order by created_at to ensure stable ordering; some deployments
            // may not have an 'item_number' column on hpp_items
            $allItems = $this->hpp->items()->with('estimationItem')->orderBy('created_at')->get();
            $grouped = $allItems->groupBy('name_ahs');
            $groupIndex = 1;
            $itemNumber = 1;

            foreach ($grouped as $groupName => $items) {
                // Group header using name_ahs when available
                $worksheet->setCellValue("A{$row}", $this->toRoman($groupIndex));
                $worksheet->setCellValue("B{$row}", $groupName ?: 'LAIN-LAIN');
                $worksheet->mergeCells("B{$row}:I{$row}");
                $worksheet->getStyle("A{$row}:I{$row}")->getFont()->setBold(true);
                $worksheet->getStyle("A{$row}:I{$row}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('F3F4F6');
                $row++;

                foreach ($items as $item) {
                    $worksheet->setCellValue("A{$row}", $itemNumber);
                    $worksheet->setCellValue("B{$row}", $item->description ?? '-');
                    $worksheet->setCellValue("C{$row}", $item->volume ?? 1);
                    $worksheet->setCellValue("D{$row}", $item->unit ?? 'Unit');
                    $worksheet->setCellValue("E{$row}", $item->duration ?? 1);
                    $worksheet->setCellValue("F{$row}", $item->duration_unit ?? 'Hari');

                    $unitPrice = (float) ($item->unit_price ?? 0);
                    $totalPrice = (float) ($item->total_price ?? ($unitPrice * ($item->koefisien ?? 1)));

                    $worksheet->setCellValue("G{$row}", $unitPrice);
                    $worksheet->setCellValue("H{$row}", $totalPrice);

                    $worksheet->setCellValue("I{$row}", "");

                    $row++;
                    $itemNumber++;
                }

                $groupIndex++;
            }

            $this->setCurrentRow($row);
            return;
        }

        if ($ahsGroups->isEmpty()) {
            // No data placeholder
            $worksheet->setCellValue("A{$row}", '1');
            $worksheet->setCellValue("B{$row}", 'Tidak ada data tersedia');
            $worksheet->setCellValue("C{$row}", '0');
            $worksheet->setCellValue("D{$row}", '-');
            $worksheet->setCellValue("E{$row}", '0');
            $worksheet->setCellValue("F{$row}", '-');
            $worksheet->setCellValue("G{$row}", '0');
            $worksheet->setCellValue("H{$row}", '0');
            $worksheet->setCellValue("I{$row}", '-');
            $worksheet->getStyle("A{$row}:I{$row}")->getFont()->setItalic(true);
            $worksheet->getStyle("A{$row}:I{$row}")->getFont()->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('808080'));
            $this->setCurrentRow($row + 1);
            return;
        }

        $groupIndex = 1;
        $itemNumber = 1;

        foreach ($ahsGroups as $group) {
            // Group header
            $worksheet->setCellValue("A{$row}", $this->toRoman($groupIndex));
            $worksheet->setCellValue("B{$row}", $group->name_ahs);
            $worksheet->mergeCells("B{$row}:I{$row}");
            $worksheet->getStyle("A{$row}:I{$row}")->getFont()->setBold(true);
            $worksheet->getStyle("A{$row}:I{$row}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('F3F4F6');
            $row++;

            // Items under group
            $itemsForGroup = $group->hppItems;
            // Fallback: find items linked by hpp_ahs_id in case relation isn't populated
            if ($itemsForGroup->isEmpty()) {
                $itemsForGroup = $this->hpp->items()->where('hpp_ahs_id', $group->id)->get();
            }
            // Additional fallback: match by name_ahs for legacy data
            if ($itemsForGroup->isEmpty()) {
                $itemsForGroup = $this->hpp->items()->where('name_ahs', $group->name_ahs)->get();
            }

            foreach ($itemsForGroup as $item) {
                $worksheet->setCellValue("A{$row}", $itemNumber);
                $worksheet->setCellValue("B{$row}", $item->description ?? '-');
                $worksheet->setCellValue("C{$row}", $item->volume ?? 1);
                $worksheet->setCellValue("D{$row}", $item->unit ?? 'Unit');
                $worksheet->setCellValue("E{$row}", $item->duration ?? 1);
                $worksheet->setCellValue("F{$row}", $item->duration_unit ?? 'Hari');

                $unitPrice = (float) ($item->unit_price ?? 0);
                $totalPrice = (float) ($item->total_price ?? ($unitPrice * ($item->koefisien ?? 1)));

                $worksheet->setCellValue("G{$row}", $unitPrice);
                $worksheet->setCellValue("H{$row}", $totalPrice);

                // Optional note or code
                $note = $item->estimationItem ? ($item->estimationItem->code . ' ' . $item->estimationItem->category) : '';
                $worksheet->setCellValue("I{$row}", $note);

                $row++;
                $itemNumber++;
            }

            $groupIndex++;
        }

        $this->setCurrentRow($row);
    }

    protected function addSummaryRows(): void
    {
        $worksheet = $this->spreadsheet->getActiveSheet();
        $row = $this->getCurrentRow();

        // Sub Total HPP
        $worksheet->setCellValue("A{$row}", 'SUB TOTAL HPP');
        $worksheet->mergeCells("A{$row}:G{$row}");
        $worksheet->setCellValue("H{$row}", (float) ($this->hpp->sub_total_hpp ?? 0));
        $worksheet->getStyle("A{$row}:I{$row}")->getFont()->setBold(true);
        $worksheet->getStyle("A{$row}:I{$row}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('DBEAFE');
        $row++;

        // Overhead
        $worksheet->setCellValue("A{$row}", $this->toRoman(6));
        $worksheet->setCellValue("B{$row}", 'Overhead');
        $worksheet->mergeCells("B{$row}:G{$row}");
        $worksheet->setCellValue("G{$row}", ($this->hpp->overhead_percentage ?? 0).'%');
        $worksheet->setCellValue("H{$row}", (float) ($this->hpp->overhead_amount ?? 0));
        $row++;

        // Margin
        $worksheet->setCellValue("A{$row}", $this->toRoman(7));
        $worksheet->setCellValue("B{$row}", 'Margin');
        $worksheet->mergeCells("B{$row}:G{$row}");
        $worksheet->setCellValue("G{$row}", ($this->hpp->margin_percentage ?? 0).'%');
        $worksheet->setCellValue("H{$row}", (float) ($this->hpp->margin_amount ?? 0));
        $row++;

        // Sub Total (after overhead & margin)
        $worksheet->setCellValue("A{$row}", 'SUB TOTAL');
        $worksheet->mergeCells("A{$row}:G{$row}");
        $worksheet->setCellValue("H{$row}", (float) ($this->hpp->sub_total ?? 0));
        $worksheet->getStyle("A{$row}:I{$row}")->getFont()->setBold(true);
        $row++;

        // PPN
        $worksheet->setCellValue("A{$row}", 'PPN');
        $worksheet->mergeCells("A{$row}:G{$row}");
        $worksheet->setCellValue("G{$row}", ($this->hpp->ppn_percentage ?? 0).'%');
        $worksheet->setCellValue("H{$row}", (float) ($this->hpp->ppn_amount ?? 0));
        $row++;

        // Grand Total
        $worksheet->setCellValue("A{$row}", 'GRAND TOTAL');
        $worksheet->mergeCells("A{$row}:G{$row}");
        $worksheet->setCellValue("H{$row}", (float) ($this->hpp->grand_total ?? 0));
        $worksheet->getStyle("A{$row}:I{$row}")->getFont()->setBold(true);
        $worksheet->getStyle("A{$row}:I{$row}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('DBEAFE');
        $row++;

        // Notes
        if (!empty($this->hpp->notes)) {
            $worksheet->setCellValue("A{$row}", 'Note: ' . $this->hpp->notes);
            $worksheet->mergeCells("A{$row}:I{$row}");
        }

        $this->setCurrentRow($row + 1);
    }

    protected function formatWorksheet(): void
    {
        $worksheet = $this->spreadsheet->getActiveSheet();

        // Currency formats
        foreach (['G', 'H'] as $col) {
            $worksheet->getStyle($col . '5:' . $col . '1000')->getNumberFormat()->setFormatCode('#,##0');
        }

        // Borders for table region
        $startRow = 5;
        $endRow = max($this->getCurrentRow() - 1, $startRow);
        $worksheet->getStyle("A{$startRow}:I{$endRow}")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
    }

    protected function generateFile(): string
    {
        try {
            if (!$this->spreadsheet || !$this->spreadsheet->getActiveSheet()) {
                throw new \Exception('Spreadsheet object tidak valid');
            }

            $writer = new Xlsx($this->spreadsheet);
            $writer->setPreCalculateFormulas(false);
            $writer->setIncludeCharts(false);

            $filename = 'HPP_' . ($this->hpp->code ?? $this->hpp->id) . '_' . date('Y-m-d_H-i-s') . '.xlsx';
            $filepath = storage_path('app/public/exports/' . $filename);

            if (!file_exists(dirname($filepath))) {
                mkdir(dirname($filepath), 0755, true);
            }

            if (file_exists($filepath)) {
                unlink($filepath);
            }

            $writer->save($filepath);

            if (!file_exists($filepath)) {
                throw new \Exception('File tidak dapat dibuat: ' . $filepath);
            }
            if (!is_readable($filepath)) {
                throw new \Exception('File tidak dapat dibaca: ' . $filepath);
            }

            $fileSize = filesize($filepath);
            if ($fileSize === 0) {
                throw new \Exception('File Excel kosong (0 bytes)');
            }
            if ($fileSize < 1000) {
                throw new \Exception('File Excel terlalu kecil, kemungkinan rusak');
            }

            $ext = pathinfo($filepath, PATHINFO_EXTENSION);
            if ($ext !== 'xlsx') {
                throw new \Exception('Ekstensi file bukan .xlsx: ' . $ext);
            }

            $header = file_get_contents($filepath, false, null, 0, 4);
            if ($header !== 'PK' . chr(0x03) . chr(0x04)) {
                throw new \Exception('Signature file Excel tidak valid');
            }

            return $filepath;

        } catch (\Exception $e) {
            Log::error('Error generating HPP Excel file: ' . $e->getMessage(), [
                'hpp_id' => $this->hpp->id,
                'filepath' => $filepath ?? 'unknown',
                'spreadsheet_valid' => $this->spreadsheet ? 'yes' : 'no',
                'active_sheet' => $this->spreadsheet && $this->spreadsheet->getActiveSheet() ? 'yes' : 'no',
            ]);
            throw $e;
        }
    }

    protected function getCurrentRow(): int
    {
        return $this->currentRow ?? 1;
    }

    protected function setCurrentRow(int $row): void
    {
        $this->currentRow = $row;
    }

    protected function toRoman(int $num): string
    {
        $map = [
            'M' => 1000,
            'CM' => 900,
            'D' => 500,
            'CD' => 400,
            'C' => 100,
            'XC' => 90,
            'L' => 50,
            'XL' => 40,
            'X' => 10,
            'IX' => 9,
            'V' => 5,
            'IV' => 4,
            'I' => 1,
        ];
        $result = '';
        foreach ($map as $roman => $value) {
            $count = intdiv($num, $value);
            $result .= str_repeat($roman, $count);
            $num -= $value * $count;
        }
        return $result ?: 'I';
    }
}