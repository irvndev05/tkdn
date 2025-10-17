<?php

namespace App\Services;

use App\Models\Service;
use DOMDocument;
use DOMXPath;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class ServiceMainExportService
{
    protected Spreadsheet $spreadsheet;
    protected Service $service;
    protected string $html;

    public function __construct(Service $service, string $html)
    {
        $this->service = $service;
        $this->html = $html;
        $this->spreadsheet = new Spreadsheet();
        $this->setDocumentProperties();
    }

    public function export(): string
    {
        [$dom, $xpath, $mainNode] = $this->getMainDom();

        $buttonDivRows = $this->extractButtonDivRows($xpath, $mainNode);
        $buttonButtonRows = $this->extractButtonButtonRows($xpath, $mainNode);
        $divDivRows = $this->extractDivDivRows($xpath, $mainNode);

        $this->buildSheet($this->spreadsheet->getActiveSheet(), 'button-data-div-1', ['button', 'data', 'div'], $buttonDivRows);

        $sheet2 = $this->spreadsheet->createSheet();
        $this->buildSheet($sheet2, 'button-data-div-2', ['button', 'data', 'div'], $buttonDivRows);

        $sheet3 = $this->spreadsheet->createSheet();
        $this->buildSheet($sheet3, 'button-data-div-3', ['button', 'data', 'div'], $buttonDivRows);

        $sheet4 = $this->spreadsheet->createSheet();
        $this->buildSheet($sheet4, 'button-data-button', ['button', 'data', 'button'], $buttonButtonRows);

        $sheet5 = $this->spreadsheet->createSheet();
        $this->buildSheet($sheet5, 'div-data-div', ['div', 'data', 'div'], $divDivRows);

        $this->spreadsheet->setActiveSheetIndex(0);

        $filename = 'service_main_' . $this->service->id . '_' . date('Ymd_His') . '.xlsx';
        $filepath = storage_path('app/private/' . $filename);
        (new Xlsx($this->spreadsheet))->save($filepath);

        return $filepath;
    }

    protected function setDocumentProperties(): void
    {
        $this->spreadsheet->getProperties()
            ->setCreator('TKDN System')
            ->setLastModifiedBy('TKDN System')
            ->setTitle('Service Main Export')
            ->setSubject('Service Main Export')
            ->setDescription('Export of Service main area content')
            ->setCategory('Export');
    }

    protected function getMainDom(): array
    {
        libxml_use_internal_errors(true);
        $dom = new DOMDocument('1.0', 'UTF-8');
        $dom->loadHTML('<?xml encoding="utf-8" ?>' . $this->html);
        $xpath = new DOMXPath($dom);
        $main = $xpath->query('//main');
        $mainNode = $main->length ? $main->item(0) : $dom->documentElement;
        return [$dom, $xpath, $mainNode];
    }

    protected function extractButtonDivRows(DOMXPath $xpath, \DOMNode $context): array
    {
        $rows = [];
        $btns = $xpath->query('.//a | .//button', $context);
        if ($btns) {
            foreach ($btns as $btn) {
                $buttonText = $this->nodeText($btn);
                if ($buttonText === '') {
                    continue;
                }
                $dataAttr = $this->collectElementData($btn);
                // Prefer following-sibling div in the same container for tighter pairing
                $divText = $this->followingDivText($btn);
                if ($divText === '') {
                    $divText = $this->nearestDivText($btn);
                }
                $rows[] = [$buttonText, $dataAttr, $divText];
            }
        }
        return $rows ?: [['', '', '']];
    }

    protected function followingDivText(\DOMNode $node): string
    {
        $parent = $node->parentNode;
        if ($parent) {
            $capture = false;
            foreach ($parent->childNodes as $sibling) {
                if ($sibling === $node) {
                    $capture = true;
                    continue;
                }
                if ($capture && ($sibling instanceof \DOMElement) && $sibling->tagName === 'div') {
                    $text = trim($this->nodeText($sibling));
                    if ($text !== '') {
                        return $text;
                    }
                }
            }
        }
        return '';
    }

    protected function extractButtonButtonRows(DOMXPath $xpath, \DOMNode $context): array
    {
        $rows = [];
        $containers = $xpath->query('.//*[.//a or .//button]', $context);
        if ($containers) {
            foreach ($containers as $container) {
                $buttons = [];
                foreach ($container->childNodes as $child) {
                    if (($child instanceof \DOMElement) && ($child->tagName === 'a' || $child->tagName === 'button')) {
                        $text = trim($this->nodeText($child));
                        if ($text !== '') {
                            $buttons[] = $text;
                        }
                    }
                }
                if (count($buttons) >= 2) {
                    for ($i = 0; $i < count($buttons) - 1; $i += 2) {
                        $rows[] = [$buttons[$i], $this->collectElementData($container), $buttons[$i + 1]];
                    }
                }
            }
        }
        return $rows ?: [['', '', '']];
    }

    protected function extractDivDivRows(DOMXPath $xpath, \DOMNode $context): array
    {
        $rows = [];
        $containers = $xpath->query('.//*[div]', $context);
        if ($containers) {
            foreach ($containers as $container) {
                $divs = [];
                foreach ($container->childNodes as $child) {
                    if (($child instanceof \DOMElement) && $child->tagName === 'div') {
                        $text = trim($this->nodeText($child));
                        if ($text !== '') {
                            $divs[] = $text;
                        }
                    }
                }
                if (count($divs) >= 2) {
                    for ($i = 0; $i < count($divs) - 1; $i += 2) {
                        $rows[] = [$divs[$i], $this->collectElementData($container), $divs[$i + 1]];
                    }
                }
            }
        }
        return $rows ?: [['', '', '']];
    }

    protected function nearestDivText(\DOMNode $node): string
    {
        $parent = $node->parentNode;
        if ($parent) {
            foreach ($parent->childNodes as $sibling) {
                if (($sibling instanceof \DOMElement) && $sibling->tagName === 'div') {
                    $text = trim($this->nodeText($sibling));
                    if ($text !== '') {
                        return $text;
                    }
                }
            }
        }
        while ($parent) {
            foreach ($parent->childNodes as $child) {
                if (($child instanceof \DOMElement) && $child->tagName === 'div') {
                    $text = trim($this->nodeText($child));
                    if ($text !== '') {
                        return $text;
                    }
                }
            }
            $parent = $parent->parentNode;
        }
        return '';
    }

    protected function nodeText(\DOMNode $node): string
    {
        $text = '';
        foreach ($node->childNodes as $child) {
            if ($child instanceof \DOMText) {
                $text .= $child->wholeText;
            } elseif ($child instanceof \DOMElement) {
                $text .= $this->nodeText($child);
            }
        }
        return trim(preg_replace('/\s+/u', ' ', $text));
    }

    protected function collectElementData(\DOMNode $node): string
    {
        if (! ($node instanceof \DOMElement)) {
            return '';
        }
        $pairs = [];
        foreach ($node->attributes ?? [] as $attr) {
            if (
                $attr->name === 'class' ||
                $attr->name === 'id' ||
                $attr->name === 'href' ||
                $attr->name === 'type' ||
                $attr->name === 'role' ||
                str_starts_with($attr->name, 'aria-') ||
                str_starts_with($attr->name, 'data-')
            ) {
                $pairs[] = $attr->name . '=' . $attr->value;
            }
        }
        return implode('; ', $pairs);
    }

    protected function buildSheet(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet, string $title, array $headers, array $rows): void
    {
        $sheet->setTitle($title);

        // Base font and widths
        $sheet->getParent()->getDefaultStyle()->getFont()->setName('Arial')->setSize(10);
        $sheet->getColumnDimension('A')->setWidth(36);
        $sheet->getColumnDimension('B')->setWidth(44);
        $sheet->getColumnDimension('C')->setWidth(54);

        // Top-left title and top-right label like "Self Assessment"
        $sheet->setCellValue('A1', 'Formulir 3.1 : TKDN Jasa untuk Manajemen Proyek dan Perekayasaan');
        $sheet->mergeCells('A1:B1');
        $sheet->getStyle('A1')->getFont()->setBold(true);
        $sheet->setCellValue('C1', 'Self Assessment');
        $sheet->getStyle('C1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        // Draw a strong separator under the title row
        $sheet->getStyle('A1:C1')->getBorders()->getBottom()->setBorderStyle(Border::BORDER_MEDIUM);

        // Information block (labels and values)
        $infoStartRow = 2;
        $labels = [
            'Penyedia Barang / Jasa',
            'Alamat',
            'Nama Jasa',
            'Pengguna Barang/Jasa',
            'No. Dokumen Jasa',
        ];
        $values = [
            $this->service->provider_name ?? '',
            $this->service->provider_address ?? '',
            $this->service->service_name ?? '',
            $this->service->user_name ?? '',
            $this->service->document_number ?? '',
        ];
        for ($i = 0; $i < count($labels); $i++) {
            $row = $infoStartRow + $i;
            $sheet->setCellValue('A' . $row, $labels[$i]);
            $sheet->setCellValue('B' . $row, ':');
            $sheet->setCellValue('C' . $row, $values[$i]);
        }
        // Outline border for info block
        $infoEndRow = $infoStartRow + count($labels) - 1;
        $sheet->getStyle('A' . $infoStartRow . ':C' . $infoEndRow)
            ->getBorders()->getOutline()->setBorderStyle(Border::BORDER_MEDIUM);

        // Table headers and numbering row
        $headerRow = $infoEndRow + 2; // leave one empty row
        $numberRow = $headerRow + 1;

        $sheet->setCellValue('A' . $headerRow, $headers[0]);
        $sheet->setCellValue('B' . $headerRow, $headers[1]);
        $sheet->setCellValue('C' . $headerRow, $headers[2]);

        $sheet->setCellValue('A' . $numberRow, '(1)');
        $sheet->setCellValue('B' . $numberRow, '(2)');
        $sheet->setCellValue('C' . $numberRow, '(3)');

        $sheet->getStyle('A' . $headerRow . ':C' . $headerRow)->getFont()->setBold(true);
        $sheet->getStyle('A' . $headerRow . ':C' . $headerRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A' . $headerRow . ':C' . $headerRow)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);

        $sheet->getStyle('A' . $numberRow . ':C' . $numberRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Borders for header + number rows
        $sheet->getStyle('A' . $headerRow . ':C' . $numberRow)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        $sheet->getStyle('A' . $headerRow . ':C' . $numberRow)->getBorders()->getOutline()->setBorderStyle(Border::BORDER_MEDIUM);

        // Data rows start after the numbering row
        $rowIndex = $numberRow + 1;
        foreach ($rows as $row) {
            $sheet->setCellValue('A' . $rowIndex, $row[0] ?? '');
            $sheet->setCellValue('B' . $rowIndex, $row[1] ?? '');
            $sheet->setCellValue('C' . $rowIndex, $row[2] ?? '');
            $rowIndex++;
        }

        // Borders and alignment
        $sheet->getStyle('A' . ($numberRow + 1) . ':C' . ($rowIndex - 1))->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        // Dotted horizontal separators to mimic form rows
        for ($i = $numberRow + 1; $i < $rowIndex; $i++) {
            $sheet->getStyle('A' . $i . ':C' . $i)->getBorders()->getBottom()->setBorderStyle(Border::BORDER_DOTTED);
        }
        $sheet->getStyle('A' . ($numberRow + 1) . ':C' . ($rowIndex - 1))->getAlignment()->setVertical(Alignment::VERTICAL_TOP);
        $sheet->getStyle('A:C')->getAlignment()->setWrapText(true);

        // Freeze pane below header+numbering
        $sheet->freezePane('A' . ($numberRow + 1));
        // Set AutoFilter to cover header + data region to avoid Excel recovery prompt
        $filterEndRow = max($headerRow + 1, $rowIndex - 1);
        $sheet->setAutoFilter('A' . $headerRow . ':C' . $filterEndRow);

        // Outer outline border for the full table area
        $sheet->getStyle('A' . $headerRow . ':C' . ($rowIndex - 1))
            ->getBorders()->getOutline()->setBorderStyle(Border::BORDER_MEDIUM);

        // Add SUB TOTAL footer row styling beneath the data region
        // Merge first, then write value to the top-left cell to avoid unreadable content
        $sheet->mergeCells('A' . $rowIndex . ':C' . $rowIndex);
        $sheet->setCellValue('A' . $rowIndex, 'SUB TOTAL');
        $sheet->getStyle('A' . $rowIndex . ':C' . $rowIndex)->getFont()->setBold(true);
        $sheet->getStyle('A' . $rowIndex . ':C' . $rowIndex)->getBorders()->getTop()->setBorderStyle(Border::BORDER_MEDIUM);
    }
}