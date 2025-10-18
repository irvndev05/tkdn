<?php

namespace App\Services;

use App\Models\Service;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Writer\Pdf\Tcpdf;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use Illuminate\Support\Facades\Log;

class ServiceExportService
{
    protected $spreadsheet;
    protected $service;
    protected $classification;
    protected int $currentRow = 1;

    public function __construct(Service $service, string $classification)
    {
        $this->spreadsheet = new Spreadsheet();
        $this->service = $service;
        $this->classification = $classification;
    }

    public function export()
    {
        $this->buildSpreadsheet();

        return $this->generateFile('xlsx');
    }

    public function exportPdf()
    {
        $this->buildSpreadsheet();

        return $this->generateFile('pdf');
    }

    protected function generateFile(string $format)
    {
        if ($format === 'xlsx') {
            $writer = new Xlsx($this->spreadsheet);
            $extension = 'xlsx';
        } elseif ($format === 'pdf') {
            $writer = new Tcpdf($this->spreadsheet);
            $extension = 'pdf';
        } else {
            throw new \Exception("Unsupported export format: {$format}");
        }

        // Keep consistent output and avoid heavy formula recalculation for export
        $writer->setPreCalculateFormulas(false);
        $writer->setIncludeCharts(false);

        $filename = 'Service_' . $this->service->id . '_' . $this->classification . '_' . date('Y-m-d_H-i-s') . '.' . $extension;
        $filepath = storage_path('app/public/exports/' . $filename);

        if (!file_exists(dirname($filepath))) {
            mkdir(dirname($filepath), 0755, true);
        }
        if (file_exists($filepath)) {
            unlink($filepath);
        }

        $writer->save($filepath);

        if (!file_exists($filepath) || filesize($filepath) === 0) {
            throw new \Exception("Failed to create or write to the file: {$filepath}");
        }

        return $filepath;
    }

    protected function buildSpreadsheet()
    {
        // Create either a single-sheet or multi-sheet workbook depending on classification
        if ($this->classification === 'all') {
            $forms = $this->service->getAvailableForms();
            $first = true;
            foreach ($forms as $code => $label) {
                if ($first) {
                    // Use default first sheet
                    $sheet = $this->spreadsheet->getActiveSheet();
                    $sheet->setTitle($code);
                    $first = false;
                } else {
                    $sheet = new \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet($this->spreadsheet, $code);
                    $this->spreadsheet->addSheet($sheet);
                }
                $this->spreadsheet->setActiveSheetIndexByName($code);
                $this->currentRow = 1;
                $this->setDocumentProperties($code);
                $this->setupWorksheet($code);
                $this->addHeaderInformation($code);
                $this->addTableHeaders($code);
                $this->addTableData($code);
                $this->formatWorksheet($code);
            }
            $this->spreadsheet->setActiveSheetIndex(0);
        } else {
            $code = $this->classification;
            $sheet = $this->spreadsheet->getActiveSheet();
            $sheet->setTitle($code);
            $this->currentRow = 1;
            $this->setDocumentProperties($code);
            $this->setupWorksheet($code);
            $this->addHeaderInformation($code);
            $this->addTableHeaders($code);
            $this->addTableData($code);
            $this->formatWorksheet($code);
        }
    }

    protected function setDocumentProperties(string $code): void
    {
        $this->spreadsheet->getProperties()
            ->setCreator('PGN MAS')
            ->setLastModifiedBy('PGN MAS')
            ->setTitle('Service Export ' . $code)
            ->setSubject('Service Export ' . $code)
            ->setDescription('Service export for Service ' . ($this->service->service_name ?: $this->service->id) . ' (' . $code . ')')
            ->setKeywords('service export tkdn ' . $code)
            ->setCategory('Service');
    }

    protected function setupWorksheet(string $code): void
    {
        $worksheet = $this->spreadsheet->getActiveSheet();
        $worksheet->setTitle($code);

        // Page setup for consistent PDF output
        $worksheet->getPageSetup()->setOrientation(PageSetup::ORIENTATION_LANDSCAPE);
        $worksheet->getPageSetup()->setPaperSize(PageSetup::PAPERSIZE_A4);
        $worksheet->getPageSetup()->setFitToWidth(1);
        $worksheet->getPageSetup()->setFitToHeight(0);
        // The header row will be set to row 6 (after header block)
        $worksheet->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd(6, 6);
        $worksheet->getPageSetup()->setHorizontalCentered(true);

        // Global font
        $this->spreadsheet->getDefaultStyle()->getFont()->setName('Arial')->setSize(9);

        // Margins
        $worksheet->getPageMargins()->setTop(0.5);
        $worksheet->getPageMargins()->setBottom(0.5);
        $worksheet->getPageMargins()->setLeft(0.3);
        $worksheet->getPageMargins()->setRight(0.3);

        // Column widths depend on classification layout
        if ($this->isBarangLayout($code)) {
            // 4.1 / 4.2 layout
            $worksheet->getColumnDimension('A')->setWidth(6);   // No.
            $worksheet->getColumnDimension('B')->setWidth(36);  // Uraian
            $worksheet->getColumnDimension('C')->setWidth(24);  // Spesifikasi
            $worksheet->getColumnDimension('D')->setWidth(20);  // Pemasok/Negara Asal
            $worksheet->getColumnDimension('E')->setWidth(10);  // TKDN (%)
            $worksheet->getColumnDimension('F')->setWidth(9);   // Jumlah
            $worksheet->getColumnDimension('G')->setWidth(9);   // Satuan
            $worksheet->getColumnDimension('H')->setWidth(14);  // Harga Satuan
            $worksheet->getColumnDimension('I')->setWidth(14);  // KDN
            $worksheet->getColumnDimension('J')->setWidth(14);  // KLN
            $worksheet->getColumnDimension('K')->setWidth(16);  // TOTAL
            $worksheet->getColumnDimension('L')->setWidth(14);  // TKDN Barang (%)
        } else {
            // 3.x and 4.3–4.7 Jasa layout
            $worksheet->getColumnDimension('A')->setWidth(6);   // No.
            $worksheet->getColumnDimension('B')->setWidth(40);  // Uraian
            $worksheet->getColumnDimension('C')->setWidth(18);  // Kualifikasi
            $worksheet->getColumnDimension('D')->setWidth(10);  // WN
            $worksheet->getColumnDimension('E')->setWidth(10);  // TKDN (%)
            $worksheet->getColumnDimension('F')->setWidth(9);   // Jumlah
            $worksheet->getColumnDimension('G')->setWidth(9);   // Durasi
            $worksheet->getColumnDimension('H')->setWidth(16);  // Upah (Rupiah)
            $worksheet->getColumnDimension('I')->setWidth(14);  // KDN
            $worksheet->getColumnDimension('J')->setWidth(14);  // KLN
            $worksheet->getColumnDimension('K')->setWidth(16);  // TOTAL
        }
    }

    protected function addHeaderInformation(string $code): void
    {
        $ws = $this->spreadsheet->getActiveSheet();

        // Title
        $ws->setCellValue('A1', 'Form ' . $code);
        $ws->mergeCells('A1:K1');
        $ws->getStyle('A1')->getFont()->setBold(true)->setSize(16);
        $ws->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Provider / Service / User / Doc No
        $ws->setCellValue('A2', 'Penyedia Barang/Jasa: ' . ($this->service->provider_name ?: '-'));
        $ws->mergeCells('A2:K2');
        $ws->setCellValue('A3', 'Nama Jasa: ' . ($this->service->service_name ?: '-'));
        $ws->mergeCells('A3:K3');
        $ws->setCellValue('A4', 'Pengguna Barang/Jasa: ' . ($this->service->user_name ?: '-'));
        $ws->mergeCells('A4:K4');
        $ws->setCellValue('A5', 'No. Dokumen Jasa: ' . ($this->service->document_number ?: '-'));
        $ws->mergeCells('A5:K5');

        $this->currentRow = 6;
    }

    protected function addTableHeaders(string $code): void
    {
        $ws = $this->spreadsheet->getActiveSheet();
        $row = $this->currentRow;

        if ($this->isBarangLayout($code)) {
            $ws->setCellValue("A{$row}", 'No.');
            $ws->setCellValue("B{$row}", 'Uraian');
            $ws->setCellValue("C{$row}", 'Spesifikasi');
            $ws->setCellValue("D{$row}", 'Pemasok/ Negara Asal');
            $ws->setCellValue("E{$row}", 'TKDN (%)');
            $ws->setCellValue("F{$row}", 'Jumlah');
            $ws->setCellValue("G{$row}", 'Satuan');
            $ws->setCellValue("H{$row}", 'Harga Satuan (Rupiah)');
            $ws->setCellValue("I{$row}", 'KDN');
            $ws->setCellValue("J{$row}", 'KLN');
            $ws->setCellValue("K{$row}", 'TOTAL');
            $ws->setCellValue("L{$row}", 'TKDN Barang (%)');

            $ws->getStyle("A{$row}:L{$row}")->getFont()->setBold(true);
            $ws->getStyle("A{$row}:L{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $ws->getStyle("A{$row}:L{$row}")->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
            $ws->getStyle("A{$row}:L{$row}")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        } else {
            $ws->setCellValue("A{$row}", 'No.');
            $ws->setCellValue("B{$row}", 'Uraian');
            $ws->setCellValue("C{$row}", 'Kualifikasi');
            $ws->setCellValue("D{$row}", 'WN');
            $ws->setCellValue("E{$row}", 'TKDN (%)');
            $ws->setCellValue("F{$row}", 'Jumlah');
            $ws->setCellValue("G{$row}", 'Durasi');
            $ws->setCellValue("H{$row}", 'Upah (Rupiah)');
            $ws->setCellValue("I{$row}", 'KDN');
            $ws->setCellValue("J{$row}", 'KLN');
            $ws->setCellValue("K{$row}", 'TOTAL');

            $ws->getStyle("A{$row}:K{$row}")->getFont()->setBold(true);
            $ws->getStyle("A{$row}:K{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $ws->getStyle("A{$row}:K{$row}")->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
            $ws->getStyle("A{$row}:K{$row}")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        }

        $this->currentRow++;
    }

    protected function addTableData(string $code): void
    {
        $ws = $this->spreadsheet->getActiveSheet();
        $row = $this->currentRow;

        // Fetch items for given classification code
        $items = $this->service->items()->where('tkdn_classification', $code)->orderBy('item_number')->get();
        $index = 1;

        foreach ($items as $item) {
            $isBarang = $this->isBarangLayout($code);
            $domestic = is_null($item->domestic_cost) ? (($item->wage ?? 0) * ($item->tkdn_percentage ?? 0) / 100) : $item->domestic_cost;
            $foreign = is_null($item->foreign_cost) ? (($item->wage ?? 0) - $domestic) : $item->foreign_cost;
            $total = is_null($item->total_cost) ? ($item->wage ?? 0) : $item->total_cost;

            $ws->setCellValue("A{$row}", $index);
            $ws->setCellValue("B{$row}", $item->description ?? '-');
            if ($isBarang) {
                $ws->setCellValue("C{$row}", $item->qualification ?? '');
                $ws->setCellValue("D{$row}", $item->nationality ?? '');
                $ws->setCellValue("E{$row}", $item->tkdn_percentage ?? 0);
                $ws->setCellValue("F{$row}", $item->quantity ?? 0);
                $ws->setCellValue("G{$row}", $item->duration_unit ?? '');
                $ws->setCellValue("H{$row}", $item->wage ?? 0);
                $ws->setCellValue("I{$row}", $domestic);
                $ws->setCellValue("J{$row}", $foreign);
                $ws->setCellValue("K{$row}", $total);
                $ws->setCellValue("L{$row}", $item->tkdn_percentage ?? 0);
                // Formats
                $ws->getStyle("H{$row}:K{$row}")->getNumberFormat()->setFormatCode('#,##0');
                $ws->getStyle("E{$row}")->getNumberFormat()->setFormatCode('0.00');
                $ws->getStyle("L{$row}")->getNumberFormat()->setFormatCode('0.00');
            } else {
                $ws->setCellValue("C{$row}", $item->qualification ?? '');
                $ws->setCellValue("D{$row}", $item->nationality ?? '');
                $ws->setCellValue("E{$row}", $item->tkdn_percentage ?? 0);
                $ws->setCellValue("F{$row}", $item->quantity ?? 0);
                $ws->setCellValue("G{$row}", $item->duration ?? 0);
                $ws->setCellValue("H{$row}", $item->wage ?? 0);
                $ws->setCellValue("I{$row}", $domestic);
                $ws->setCellValue("J{$row}", $foreign);
                $ws->setCellValue("K{$row}", $total);
                // Formats
                $ws->getStyle("H{$row}:K{$row}")->getNumberFormat()->setFormatCode('#,##0');
                $ws->getStyle("E{$row}")->getNumberFormat()->setFormatCode('0.00');
            }

            // Borders for the row
            $endCol = $isBarang ? 'L' : 'K';
            $ws->getStyle("A{$row}:{$endCol}{$row}")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
            $ws->getStyle("A{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $ws->getStyle("E{$row}:G{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            $row++;
            $index++;
        }

        $this->currentRow = $row;

        // Optional subtotal row
        if ($index > 1) {
            $ws->setCellValue("A{$row}", 'SUB TOTAL');
            $ws->mergeCells("A{$row}:H{$row}");
            // Sum of totals in column K (or K for jasa, K for barang totals as well)
            $sumColTotal = $this->isBarangLayout($code) ? 'K' : 'K';
            $sumRangeStart = $this->currentRow - ($index - 1);
            $ws->setCellValue("{$sumColTotal}{$row}", "=SUM({$sumColTotal}{$sumRangeStart}:{$sumColTotal}" . ($row - 1) . ")");
            $ws->getStyle("A{$row}:{$sumColTotal}{$row}")->getFont()->setBold(true);
            $ws->getStyle("A{$row}:{$sumColTotal}{$row}")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
            $ws->getStyle("{$sumColTotal}{$row}")->getNumberFormat()->setFormatCode('#,##0');
            $this->currentRow++;
        }
    }

    protected function formatWorksheet(string $code): void
    {
        $ws = $this->spreadsheet->getActiveSheet();
        $headerRow = 6;
        $lastRow = max($ws->getHighestRow(), $headerRow);

        // Header fill
        $endCol = $this->isBarangLayout($code) ? 'L' : 'K';
        $ws->getStyle("A{$headerRow}:{$endCol}{$headerRow}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('F3F4F6');
        $ws->getStyle("A{$headerRow}:{$endCol}{$headerRow}")->getFont()->setBold(true);

        // Alignments
        $ws->getStyle("A1:A5")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        $ws->getStyle("E{$headerRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Freeze header row for Excel viewing (not impacting PDF)
        $ws->freezePane("A" . ($headerRow + 1));

        // Ensure header/footer replication (repeat header row already set in PageSetup)
        $ws->getHeaderFooter()->setOddHeader('&C&16 Service TKDN Export');
        $ws->getHeaderFooter()->setOddFooter('&LPGN MAS &RPage &P of &N');
    }

    protected function isBarangLayout(string $code): bool
    {
        return in_array($code, ['4.1', '4.2'], true);
    }
}
