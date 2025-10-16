<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Tests\TestCase;

class ImprovedImportTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->actingAs($this->user);
    }

    public function test_equipment_import_with_category_name()
    {
        $category = Category::factory()->create(['name' => 'Building Equipment']);

        $file = $this->createExcelFile([
            ['Name', 'Category', 'TKDN', 'Equipment Type', 'Period', 'Price', 'Description', 'Location', 'Classification TKDN'],
            ['Excavator', 'Building Equipment', '85', 'reusable', '5', '50000000', 'Heavy equipment', 'Jakarta', '1.1'],
        ]);

        $response = $this->post(route('master.equipment.import'), [
            'excel_file' => $file,
        ]);

        $response->assertRedirect(route('master.equipment.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('equipment', [
            'name' => 'Excavator',
            'category_id' => $category->id,
            'classification_tkdn' => '1.1',
            'tkdn' => 85,
            'period' => 5,
            'price' => 50000000,
        ]);
    }

    public function test_equipment_import_with_partial_category_name()
    {
        $category = Category::factory()->create(['name' => 'Building Equipment']);

        $file = $this->createExcelFile([
            ['Name', 'Category', 'TKDN', 'Equipment Type', 'Period', 'Price', 'Description', 'Location'],
            ['Excavator', 'Building', '85', 'reusable', '5', '50000000', 'Heavy equipment', 'Jakarta'],
        ]);

        $response = $this->post(route('master.equipment.import'), [
            'excel_file' => $file,
        ]);

        $response->assertRedirect(route('master.equipment.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('equipment', [
            'name' => 'Excavator',
            'category_id' => $category->id,
        ]);
    }

    public function test_equipment_import_with_invalid_category_name()
    {
        $file = $this->createExcelFile([
            ['Name', 'Category', 'TKDN', 'Equipment Type', 'Period', 'Price', 'Description', 'Location'],
            ['Excavator', 'Non Existent Category', '85', 'reusable', '5', '50000000', 'Heavy equipment', 'Jakarta'],
        ]);

        $response = $this->post(route('master.equipment.import'), [
            'excel_file' => $file,
        ]);

        $response->assertRedirect(route('master.equipment.index'));
        $response->assertSessionHas('import_errors');
        $response->assertSessionHas('success', 'Successfully imported 0 equipment!');

        $this->assertDatabaseMissing('equipment', [
            'name' => 'Excavator',
        ]);
    }

    public function test_material_import_with_category_name()
    {
        $category = Category::factory()->create(['name' => 'Building Material']);

        $file = $this->createExcelFile([
            ['Name', 'Category', 'Brand', 'Specification', 'TKDN', 'Price', 'Unit', 'Link', 'Description', 'Location', 'Classification TKDN'],
            ['Cement', 'Building Material', 'Semen Gresik', 'Type I', '100', '85000', 'Sak', 'https://example.com', 'Portland cement', 'Jakarta', '1.2'],
        ]);

        $response = $this->post(route('master.material.import'), [
            'excel_file' => $file,
        ]);

        $response->assertRedirect(route('master.material.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('material', [
            'name' => 'Cement',
            'category_id' => $category->id,
            'classification_tkdn' => '1.2',
            'brand' => 'Semen Gresik',
            'tkdn' => 100,
            'price' => 85000,
        ]);
    }

    public function test_worker_import_with_category_name()
    {
        $category = Category::factory()->create(['name' => 'Construction Worker']);

        $file = $this->createExcelFile([
            ['Name', 'Unit', 'Category', 'Price', 'TKDN', 'Location', 'Classification TKDN'],
            ['Mason', 'Person', 'Construction Worker', '200000', '100', 'Jakarta', '3.1'],
        ]);

        $response = $this->post(route('master.worker.import'), [
            'excel_file' => $file,
        ]);

        $response->assertRedirect(route('master.worker.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('workers', [
            'name' => 'Mason',
            'category_id' => $category->id,
            'classification_tkdn' => '3.1',
            'unit' => 'Person',
            'price' => 200000,
            'tkdn' => 100,
        ]);
    }

    public function test_project_import_improved_validation()
    {
        $file = $this->createExcelFile([
            ['Name', 'Project Type', 'Status', 'Start Date', 'End Date', 'Description', 'Company', 'Location'],
            ['Test Project', 'tkdn_jasa', 'draft', '2024-01-01', '2024-12-31', 'Test description', 'Test Company', 'Jakarta'],
        ]);

        $response = $this->post(route('master.project.import'), [
            'excel_file' => $file,
        ]);

        $response->assertRedirect(route('master.project.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('projects', [
            'name' => 'Test Project',
            'project_type' => 'tkdn_jasa',
            'status' => 'draft',
            'start_date' => '2024-01-01',
            'end_date' => '2024-12-31',
        ]);
    }

    public function test_import_validation_errors_are_properly_handled()
    {
        $file = $this->createExcelFile([
            ['Name', 'Category', 'TKDN', 'Equipment Type', 'Period', 'Price', 'Description', 'Location'],
            ['', 'Building Equipment', '150', 'invalid_type', '-1', 'invalid_price', 'Heavy equipment', 'Jakarta'],
        ]);

        $response = $this->post(route('master.equipment.import'), [
            'excel_file' => $file,
        ]);

        $response->assertRedirect(route('master.equipment.index'));
        $response->assertSessionHas('import_errors');
        $response->assertSessionHas('success', 'Successfully imported 0 equipment!');

        $errors = session('import_errors');
        $this->assertIsArray($errors);
        $this->assertGreaterThan(0, count($errors));
    }

    public function test_import_with_mixed_valid_and_invalid_rows()
    {
        $category = Category::factory()->create(['name' => 'Building Equipment']);

        $file = $this->createExcelFile([
            ['Name', 'Category', 'TKDN', 'Equipment Type', 'Period', 'Price', 'Description', 'Location', 'Classification TKDN'],
            ['Valid Equipment', 'Building Equipment', '85', 'reusable', '5', '50000000', 'Valid equipment', 'Jakarta', '1.1'],
            ['Invalid Equipment', 'Non Existent', '150', 'invalid', '-1', 'invalid', 'Invalid equipment', 'Jakarta', 'invalid_format'],
        ]);

        $response = $this->post(route('master.equipment.import'), [
            'excel_file' => $file,
        ]);

        $response->assertRedirect(route('master.equipment.index'));
        $response->assertSessionHas('success', 'Successfully imported 1 equipment!');
        $response->assertSessionHas('import_errors');

        $this->assertDatabaseHas('equipment', [
            'name' => 'Valid Equipment',
            'category_id' => $category->id,
        ]);

        $this->assertDatabaseMissing('equipment', [
            'name' => 'Invalid Equipment',
        ]);
    }

    public function test_import_with_invalid_classification_tkdn_format()
    {
        $category = Category::factory()->create(['name' => 'Building Equipment']);

        $file = $this->createExcelFile([
            ['Name', 'Category', 'TKDN', 'Equipment Type', 'Period', 'Price', 'Description', 'Location', 'Classification TKDN'],
            ['Test Equipment', 'Building Equipment', '85', 'reusable', '5', '50000000', 'Test equipment', 'Jakarta', 'invalid_format'],
        ]);

        $response = $this->post(route('master.equipment.import'), [
            'excel_file' => $file,
        ]);

        $response->assertRedirect(route('master.equipment.index'));
        $response->assertSessionHas('import_errors');
        $response->assertSessionHas('success', 'Successfully imported 0 equipment!');

        $this->assertDatabaseMissing('equipment', [
            'name' => 'Test Equipment',
        ]);
    }

    protected function createExcelFile(array $data): UploadedFile
    {
        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->fromArray($data, null, 'A1');

        $filename = 'test_import_'.uniqid().'.xlsx';
        $filepath = storage_path('app/'.$filename);

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save($filepath);

        return new UploadedFile(
            $filepath,
            $filename,
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            null,
            true
        );
    }

    protected function tearDown(): void
    {
        // Clean up test files
        $files = glob(storage_path('app/test_import_*.xlsx'));
        foreach ($files as $file) {
            if (file_exists($file)) {
                unlink($file);
            }
        }

        parent::tearDown();
    }
}
