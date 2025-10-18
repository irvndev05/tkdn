<?php

namespace App\Services;

use App\Models\Hpp;
use App\Models\HppItem;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Writer\Pdf\Tcpdf;
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
        $worksheet->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd(5, 5);
        $worksheet->getPageSetup()->setHorizontalCentered(true);

        // Global font for better legibility (slightly smaller to ensure full fit)
        $this->spreadsheet->getDefaultStyle()->getFont()->setName('Arial')->setSize(9);

        // Margins tightened to reclaim horizontal space
        $worksheet->getPageMargins()->setTop(0.5);
        $worksheet->getPageMargins()->setBottom(0.5);
        $worksheet->getPageMargins()->setLeft(0.2);
        $worksheet->getPageMargins()->setRight(0.2);

        // Column widths tuned to fully fit on A4 Landscape
        $worksheet->getColumnDimension('A')->setWidth(5);
        $worksheet->getColumnDimension('B')->setWidth(48);
        $worksheet->getColumnDimension('C')->setWidth(11);
        $worksheet->getColumnDimension('D')->setWidth(8);
        $worksheet->getColumnDimension('E')->setWidth(10);
        $worksheet->getColumnDimension('F')->setWidth(8);
        $worksheet->getColumnDimension('G')->setWidth(15);
        $worksheet->getColumnDimension('H')->setWidth(15);
        $worksheet->getColumnDimension('I')->setWidth(36);
    }

    protected function addHeaderInformation(): void
    {
        $worksheet = $this->spreadsheet->getActiveSheet();

        // Title block
        $worksheet->setCellValue('A1', 'HPP');
        $worksheet->mergeCells('A1:I1');
        $worksheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
        $worksheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        $title2 = $this->hpp->work_description ?: $this->hpp->name_hpp;
        $worksheet->setCellValue('A2', $title2);
        $worksheet->mergeCells('A2:I2');
        $worksheet->getStyle('A2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        $company = $this->hpp->company_name ?: ($this->hpp->project?->company ?? '');
        if (!empty($company)) {
            $worksheet->setCellValue('A3', $company);
            $worksheet->mergeCells('A3:I3');
            $worksheet->getStyle('A3')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
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

        $worksheet->getStyle("A{$row}:I{$row}")->getFont()->setBold(true);
        $worksheet->getStyle("A{$row}:I{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $worksheet->getStyle("A{$row}:I{$row}")->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        $worksheet->getStyle("A{$row}:I{$row}")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

        $row++;
        $worksheet->setCellValue("A{$row}", 'I');
        $worksheet->mergeCells("A{$row}:B{$row}");
        $worksheet->setCellValue("B{$row}", 'LAIN-LAIN');
        $worksheet->setCellValue("C{$row}", '');
        $worksheet->setCellValue("D{$row}", '');
        $worksheet->setCellValue("E{$row}", '');
        $worksheet->setCellValue("F{$row}", '');
        $worksheet->setCellValue("G{$row}", '');
        $worksheet->setCellValue("H{$row}", '');
        $worksheet->setCellValue("I{$row}", '');

        $worksheet->getStyle("A{$row}:I{$row}")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

        $this->setCurrentRow($row + 1);
    }

    protected function addTableData(): void
    {
        $worksheet = $this->spreadsheet->getActiveSheet();
        $row = $this->getCurrentRow();

        $groupIndex = 1;
        $itemNumber = 1;
        $ahsGroups = $this->hpp->ahs; // Could be null or collection

        if (!empty($ahsGroups) && count($ahsGroups) > 0) {
            foreach ($ahsGroups as $group) {
                foreach ($group['details'] ?? [] as $detail) {
                    $worksheet->setCellValue("A{$row}", $itemNumber);
                    $worksheet->setCellValue("B{$row}", $detail['description'] ?? '-');
                    $worksheet->setCellValue("C{$row}", (float) ($detail['quantity'] ?? 1));
                    $worksheet->setCellValue("D{$row}", $detail['unit'] ?? 'Unit');
                    $worksheet->setCellValue("E{$row}", (float) ($detail['duration'] ?? 1));
                    $worksheet->setCellValue("F{$row}", $detail['duration_unit'] ?? 'Hari');

                    $unitPrice = (float) ($detail['unit_price'] ?? 0);
                    $qty = (float) ($detail['quantity'] ?? 0);
                    $totalPrice = ($unitPrice * $qty);

                    $worksheet->setCellValue("G{$row}", $unitPrice);
                    $worksheet->setCellValue("H{$row}", $totalPrice);
                    $worksheet->setCellValue("I{$row}", "");

                    $row++;
                    $itemNumber++;
                }

                $groupIndex++;
            }
        } else {
            $items = HppItem::where('hpp_id', $this->hpp->id)->get();
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
        }

        $this->setCurrentRow($row);
    }

    protected function addSummaryRows(): void
    {
        $worksheet = $this->spreadsheet->getActiveSheet();
        $row = $this->getCurrentRow();

        $worksheet->setCellValue("A{$row}", 'SUB TOTAL HPP');
        $worksheet->mergeCells("A{$row}:G{$row}");
        $worksheet->setCellValue("H{$row}", (float) ($this->hpp->sub_total ?? 0));
        $worksheet->getStyle("A{$row}:I{$row}")->getFont()->setBold(true);
        $row++;

        $worksheet->setCellValue("A{$row}", 'Overhead');
        $worksheet->mergeCells("A{$row}:G{$row}");
        $worksheet->setCellValue("G{$row}", ($this->hpp->overhead_percentage ?? 0).'%');
        $worksheet->setCellValue("H{$row}", (float) ($this->hpp->overhead_amount ?? 0));
        $row++;

        $worksheet->setCellValue("A{$row}", 'Margin');
        $worksheet->mergeCells("A{$row}:G{$row}");
        $worksheet->setCellValue("G{$row}", ($this->hpp->margin_percentage ?? 0).'%');
        $worksheet->setCellValue("H{$row}", (float) ($this->hpp->margin_amount ?? 0));
        $row++;

        $worksheet->setCellValue("A{$row}", 'SUB TOTAL');
        $worksheet->mergeCells("A{$row}:G{$row}");
        $worksheet->setCellValue("H{$row}", (float) ($this->hpp->sub_total ?? 0));
        $worksheet->getStyle("A{$row}:I{$row}")->getFont()->setBold(true);
        $row++;

        $worksheet->setCellValue("A{$row}", 'PPN');
        $worksheet->mergeCells("A{$row}:G{$row}");
        $worksheet->setCellValue("G{$row}", ($this->hpp->ppn_percentage ?? 0).'%');
        $worksheet->setCellValue("H{$row}", (float) ($this->hpp->ppn_amount ?? 0));
        $row++;

        $worksheet->setCellValue("A{$row}", 'GRAND TOTAL');
        $worksheet->mergeCells("A{$row}:G{$row}");
        $worksheet->setCellValue("H{$row}", (float) ($this->hpp->grand_total ?? 0));
        $worksheet->getStyle("A{$row}:I{$row}")->getFont()->setBold(true);
        $worksheet->getStyle("A{$row}:I{$row}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('DBEAFE');
        $row++;

        if (!empty($this->hpp->notes)) {
            $worksheet->setCellValue("A{$row}", 'Note: ' . $this->hpp->notes);
            $worksheet->mergeCells("A{$row}:I{$row}");
        }

        $this->setCurrentRow($row + 1);
    }

    protected function formatWorksheet(): void
    {
        $worksheet = $this->spreadsheet->getActiveSheet();

        $startRow = 5;
        $endRow = max($this->getCurrentRow() - 1, $startRow);

        if ($endRow >= $startRow) {
            foreach (['G', 'H'] as $col) {
                $worksheet->getStyle($col . $startRow . ':' . $col . $endRow)
                    ->getNumberFormat()->setFormatCode('#,##0');
            }

            $worksheet->getStyle("A{$startRow}:A{$endRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            foreach (['C','D','E','F'] as $col) {
                $worksheet->getStyle($col . $startRow . ':' . $col . $endRow)
                    ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);
            }
            foreach (['G','H'] as $col) {
                $worksheet->getStyle($col . $startRow . ':' . $col . $endRow)
                    ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT)->setVertical(Alignment::VERTICAL_CENTER);
            }
            $worksheet->getStyle("B{$startRow}:B{$endRow}")->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_LEFT)->setVertical(Alignment::VERTICAL_TOP)->setWrapText(true)->setIndent(1);
            $worksheet->getStyle("I{$startRow}:I{$endRow}")->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_LEFT)->setVertical(Alignment::VERTICAL_TOP)->setWrapText(true);

            $worksheet->getStyle("A{$startRow}:I{$endRow}")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

            // Explicit print area to guarantee column I is included
            $worksheet->getPageSetup()->setPrintArea("A1:I{$endRow}");
        }
    }

    public function exportPdf()
    {
        @ini_set('memory_limit', '512M');
        @set_time_limit(120);

        $this->setDocumentProperties();
        $this->setupWorksheet();
        $this->addHeaderInformation();
        $this->addTableHeaders();
        $this->addTableData();
        $this->addSummaryRows();
        $this->formatWorksheet();

        try {
            $writer = new Tcpdf($this->spreadsheet);
            $writer->setPreCalculateFormulas(false);
            $writer->setIncludeCharts(false);

            $filename = 'HPP_' . ($this->hpp->code ?? $this->hpp->id) . '_' . date('Y-m-d_H-i-s') . '.pdf';
            $filepath = storage_path('app/public/exports/' . $filename);
            if (!file_exists(dirname($filepath))) {
                mkdir(dirname($filepath), 0755, true);
            }
            if (file_exists($filepath)) {
                unlink($filepath);
            }

            \Log::info('HPP PDF writer about to save', [
                'hpp_id' => $this->hpp->id,
                'filepath' => $filepath,
                'mem_usage' => memory_get_usage(true),
                'mem_peak' => memory_get_peak_usage(true),
            ]);

            $writer->save($filepath);

            \Log::info('HPP PDF writer saved', [
                'hpp_id' => $this->hpp->id,
                'filepath' => $filepath,
                'exists' => file_exists($filepath),
                'size' => file_exists($filepath) ? filesize($filepath) : null,
                'mem_usage' => memory_get_usage(true),
                'mem_peak' => memory_get_peak_usage(true),
            ]);

            if (!file_exists($filepath)) {
                throw new \Exception('File tidak dapat dibuat: ' . $filepath);
            }
            if (!is_readable($filepath)) {
                throw new \Exception('File tidak dapat dibaca: ' . $filepath);
            }
            $fileSize = filesize($filepath);
            if ($fileSize === 0) {
                throw new \Exception('File PDF kosong (0 bytes)');
            }
            if ($fileSize < 500) {
                throw new \Exception('File PDF terlalu kecil, kemungkinan rusak');
            }
            $ext = pathinfo($filepath, PATHINFO_EXTENSION);
            if ($ext !== 'pdf') {
                throw new \Exception('Ekstensi file bukan .pdf: ' . $ext);
            }
            $header = file_get_contents($filepath, false, null, 0, 4);
            if ($header !== '%PDF') {
                throw new \Exception('Signature file PDF tidak valid');
            }

            return $filepath;
        } catch (\Throwable $e) {
            \Log::error('Error generating HPP PDF file: ' . $e->getMessage(), [
                'hpp_id' => $this->hpp->id,
                'filepath' => $filepath ?? 'unknown',
                'trace' => $e->getTraceAsString(),
                'mem_usage' => memory_get_usage(true),
                'mem_peak' => memory_get_peak_usage(true),
            ]);
            throw $e;
        }
    }

    protected function generateFile()
    {
        try {
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
            if ($fileSize < 500) {
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
        return $this->currentRow;
    }

    protected function setCurrentRow(int $row): void
    {
        $this->currentRow = $row;
    }
}