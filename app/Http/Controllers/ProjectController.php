<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Services\ImportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ProjectController extends Controller
{
    protected $importService;

    public function __construct(ImportService $importService)
    {
        $this->middleware('auth');
        $this->importService = $importService;
    }

    public function index()
    {
        $projects = Project::latest()->paginate(10);

        return view('project.index', compact('projects'));
    }

    public function create()
    {
        $projectTypes = Project::getProjectTypes();

        return view('project.create', compact('projectTypes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'project_type' => 'required|in:tkdn_jasa,tkdn_barang_jasa',
            'status' => 'required|in:draft,on_progress,completed',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'category' => 'nullable|string|max:255', // ada tambahan kolom category
            'description' => 'nullable|string|max:1000',
            'company' => 'nullable|string|max:255',
            'location' => 'nullable|string|max:255',
        ]);
        Project::create($validated);

        return redirect()->route('master.project.index')->with('success', 'Project created successfully.');
    }

    public function show(Project $project)
    {
        return view('project.show', compact('project'));
    }

    public function edit(Project $project)
    {
        $projectTypes = Project::getProjectTypes();

        return view('project.edit', compact('project', 'projectTypes'));
    }

    public function update(Request $request, Project $project)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'project_type' => 'required|in:tkdn_jasa,tkdn_barang_jasa',
            'status' => 'required|in:draft,on_progress,completed',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'category' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:1000',
            'company' => 'nullable|string|max:255',
            'location' => 'nullable|string|max:255',
        ]);
        $project->update($validated);

        return redirect()->route('master.project.index')->with('success', 'Project updated successfully.');
    }

    public function destroy(Project $project)
    {
        $project->delete();

        return redirect()->route('master.project.index')->with('success', 'Project deleted successfully.');
    }

    /**
     * Download Excel template for project import
     */
    public function downloadTemplate()
    {
        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();

        // Set headers
        $headers = ['Name', 'Project Type', 'Status', 'Start Date', 'End Date', 'Description', 'Company', 'Location'];
        $sheet->fromArray($headers, null, 'A1');

        // Set example data
        $exampleData = [
            ['Project A', 'tkdn_jasa', 'draft', '2024-01-01', '2024-12-31', 'Sample project description', 'Company A', 'Jakarta'],
            ['Project B', 'tkdn_barang_jasa', 'on_progress', '2024-06-01', '2024-11-30', 'Another project', 'Company B', 'Bandung'],
        ];
        $sheet->fromArray($exampleData, null, 'A2');

        // Style headers
        $sheet->getStyle('A1:H1')->getFont()->setBold(true);
        $sheet->getStyle('A1:H1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('E5E7EB');

        // Auto size columns
        foreach (range('A', 'H') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Create response
        $writer = new Xlsx($spreadsheet);
        $filename = 'project_import_template.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }

    /**
     * Import projects from Excel file
     */
    public function import(Request $request)
    {
        $request->validate([
            'excel_file' => 'required|file|mimes:xlsx,xls,csv|max:2048',
        ]);

        try {
            DB::beginTransaction();

            $file = $request->file('excel_file');
            $spreadsheet = IOFactory::load($file->getPathname());
            $worksheet = $spreadsheet->getActiveSheet();
            $rows = $worksheet->toArray();

            // Remove header row
            $headers = array_shift($rows);

            $imported = 0;
            $errors = [];
            $rowNumber = 2; // Start from row 2 (after header)

            // Clear cache untuk memastikan data fresh
            $this->importService->clearCache();

            foreach ($rows as $row) {
                if (empty(array_filter($row))) {
                    $rowNumber++;

                    continue; // Skip empty rows
                }

                // Validasi field required
                $requiredErrors = $this->importService->validateRequiredFields(
                    $row,
                    [0 => 'Name', 1 => 'Project Type', 2 => 'Status', 3 => 'Start Date', 4 => 'End Date'],
                    $rowNumber
                );

                if (! empty($requiredErrors)) {
                    $errors = array_merge($errors, $requiredErrors);
                    $rowNumber++;

                    continue;
                }

                // Validasi project type
                $typeErrors = $this->importService->validateInArray(
                    $row[1],
                    'Project Type',
                    $rowNumber,
                    ['tkdn_jasa', 'tkdn_barang_jasa']
                );

                if (! empty($typeErrors)) {
                    $errors = array_merge($errors, $typeErrors);
                    $rowNumber++;

                    continue;
                }

                // Validasi status
                $statusErrors = $this->importService->validateInArray(
                    $row[2],
                    'Status',
                    $rowNumber,
                    ['draft', 'on_progress', 'completed']
                );

                if (! empty($statusErrors)) {
                    $errors = array_merge($errors, $statusErrors);
                    $rowNumber++;

                    continue;
                }

                // Validasi format tanggal
                $startDateErrors = $this->importService->validateDate($row[3], 'Start Date', $rowNumber);
                $endDateErrors = $this->importService->validateDate($row[4], 'End Date', $rowNumber);

                if (! empty($startDateErrors) || ! empty($endDateErrors)) {
                    $errors = array_merge($errors, $startDateErrors, $endDateErrors);
                    $rowNumber++;

                    continue;
                }

                // Validasi range tanggal
                $dateRangeErrors = $this->importService->validateDateRange($row[3], $row[4], $rowNumber);

                if (! empty($dateRangeErrors)) {
                    $errors = array_merge($errors, $dateRangeErrors);
                    $rowNumber++;

                    continue;
                }

                try {
                    // Create project
                    Project::create([
                        'name' => trim($row[0]),
                        'project_type' => trim($row[1]),
                        'status' => trim($row[2]),
                        'start_date' => $row[3],
                        'end_date' => $row[4],
                        'description' => ! empty($row[5]) ? trim($row[5]) : null,
                        'company' => ! empty($row[6]) ? trim($row[6]) : null,
                        'location' => ! empty($row[7]) ? trim($row[7]) : null,
                    ]);

                    $imported++;
                } catch (\Exception $e) {
                    $errors[] = "Row {$rowNumber}: " . $e->getMessage();
                }

                $rowNumber++;
            }

            DB::commit();

            // Log progress
            $this->importService->logImportProgress('project', $imported, count($rows), $errors);

            if (empty($errors)) {
                return redirect()->route('master.project.index')
                    ->with('success', "Successfully imported {$imported} projects!");
            } else {
                return redirect()->route('master.project.index')
                    ->with('success', "Successfully imported {$imported} projects!")
                    ->with('import_errors', $errors);
            }
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->route('master.project.index')
                ->with('error', 'Import failed: ' . $e->getMessage());
        }
    }
}
