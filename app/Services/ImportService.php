<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Project;
use Illuminate\Support\Facades\Log;

class ImportService
{
    /**
     * Cache untuk menyimpan mapping nama ke ID
     */
    private static array $categoryCache = [];

    private static array $projectCache = [];

    /**
     * Cari kategori berdasarkan nama dengan fuzzy matching
     */
    public function findCategoryByName(string $name): ?Category
    {
        $name = trim($name);

        // Cek cache terlebih dahulu
        if (isset(self::$categoryCache[$name])) {
            return self::$categoryCache[$name];
        }

        // Pencarian exact match terlebih dahulu
        $category = Category::where('name', $name)->first();

        if (! $category) {
            // Pencarian dengan LIKE untuk partial match
            $category = Category::where('name', 'LIKE', '%'.$name.'%')->first();
        }

        // Cache hasil pencarian
        self::$categoryCache[$name] = $category;

        return $category;
    }

    /**
     * Cari project berdasarkan nama dengan fuzzy matching
     */
    public function findProjectByName(string $name): ?Project
    {
        $name = trim($name);

        // Cek cache terlebih dahulu
        if (isset(self::$projectCache[$name])) {
            return self::$projectCache[$name];
        }

        // Pencarian exact match terlebih dahulu
        $project = Project::where('name', $name)->first();

        if (! $project) {
            // Pencarian dengan LIKE untuk partial match
            $project = Project::where('name', 'LIKE', '%'.$name.'%')->first();
        }

        // Cache hasil pencarian
        self::$projectCache[$name] = $project;

        return $project;
    }

    /**
     * Validasi dan konversi kategori dari nama ke ID
     */
    public function validateAndGetCategoryId(?string $categoryName, int $rowNumber): array
    {
        if (empty($categoryName)) {
            return [null, []];
        }

        $category = $this->findCategoryByName($categoryName);

        if (! $category) {
            return [null, ["Row {$rowNumber}: Kategori \"{$categoryName}\" tidak ditemukan"]];
        }

        return [$category->id, []];
    }

    /**
     * Validasi dan konversi project dari nama ke ID
     */
    public function validateAndGetProjectId(?string $projectName, int $rowNumber): array
    {
        if (empty($projectName)) {
            return [null, []];
        }

        $project = $this->findProjectByName($projectName);

        if (! $project) {
            return [null, ["Row {$rowNumber}: Project \"{$projectName}\" tidak ditemukan"]];
        }

        return [$project->id, []];
    }

    /**
     * Validasi field required
     */
    public function validateRequiredFields(array $row, array $requiredIndices, int $rowNumber): array
    {
        $errors = [];

        foreach ($requiredIndices as $index => $fieldName) {
            if (empty($row[$index])) {
                $errors[] = "Row {$rowNumber}: Field '{$fieldName}' is required";
            }
        }

        return $errors;
    }

    /**
     * Validasi format tanggal
     */
    public function validateDate(?string $date, string $fieldName, int $rowNumber): array
    {
        if (empty($date)) {
            return [];
        }

        if (! strtotime($date)) {
            return ["Row {$rowNumber}: Invalid {$fieldName} format (use YYYY-MM-DD)"];
        }

        return [];
    }

    /**
     * Validasi range numerik
     */
    public function validateNumericRange($value, string $fieldName, int $rowNumber, ?float $min = null, ?float $max = null): array
    {
        if (empty($value)) {
            return [];
        }

        if (! is_numeric($value)) {
            return ["Row {$rowNumber}: {$fieldName} must be a number"];
        }

        $numValue = (float) $value;

        if ($min !== null && $numValue < $min) {
            return ["Row {$rowNumber}: {$fieldName} must be at least {$min}"];
        }

        if ($max !== null && $numValue > $max) {
            return ["Row {$rowNumber}: {$fieldName} must be at most {$max}"];
        }

        return [];
    }

    /**
     * Validasi nilai dalam array
     */
    public function validateInArray($value, string $fieldName, int $rowNumber, array $allowedValues): array
    {
        if (empty($value)) {
            return [];
        }

        if (! in_array($value, $allowedValues)) {
            $allowedList = implode(', ', $allowedValues);

            return ["Row {$rowNumber}: {$fieldName} must be one of: {$allowedList}"];
        }

        return [];
    }

    /**
     * Validasi tanggal end >= start
     */
    public function validateDateRange(?string $startDate, ?string $endDate, int $rowNumber): array
    {
        if (empty($startDate) || empty($endDate)) {
            return [];
        }

        $start = strtotime($startDate);
        $end = strtotime($endDate);

        if ($end < $start) {
            return ["Row {$rowNumber}: End date must be after or equal to start date"];
        }

        return [];
    }

    /**
     * Bersihkan cache
     */
    public function clearCache(): void
    {
        self::$categoryCache = [];
        self::$projectCache = [];
    }

    /**
     * Validasi classification TKDN
     */
    public function validateClassificationTkdn(?string $classification, int $rowNumber): array
    {
        if (empty($classification)) {
            return [];
        }

        $classification = trim($classification);
        
        // Validasi harus berupa integer 1-7
        if (!is_numeric($classification)) {
            return ["Row {$rowNumber}: Classification TKDN must be a number (1-7)"];
        }
        
        $intValue = (int) $classification;
        if ($intValue < 1 || $intValue > 7) {
            return ["Row {$rowNumber}: Classification TKDN must be between 1-7 (1=Overhead & Manajemen, 2=Alat Kerja/Fasilitas, 3=Konstruksi & Fabrikasi, 4=Peralatan Jasa Umum, 5=Material Bahan Baku, 6=Peralatan Barang Jadi, 7=Summary)"];
        }

        return [];
    }

    /**
     * Log import progress
     */
    public function logImportProgress(string $type, int $imported, int $total, array $errors = []): void
    {
        Log::info("Import {$type} progress", [
            'imported' => $imported,
            'total' => $total,
            'errors_count' => count($errors),
            'errors' => $errors,
        ]);
    }
}
