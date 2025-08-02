<?php

namespace App\Services;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Exception;

class CsvReaderService
{
    /**
     * Read CSV file and return structured data with headers
     *
     * @param string $filename - The CSV filename (should be in storage/app/csv/)
     * @return array
     * @throws Exception
     */
    public function readCsv(string $filename): array
    {
        $filePath = storage_path("app/csv/{$filename}");
        
        if (!File::exists($filePath)) {
            throw new Exception("CSV file not found: {$filename}");
        }

        try {
            $csvData = [];
            $headers = [];
            $rowIndex = 0;

            // Open and read the CSV file
            if (($handle = fopen($filePath, 'r')) !== false) {
                while (($row = fgetcsv($handle, 0, ',')) !== false) {
                    if ($rowIndex === 0) {
                        // First row is headers
                        $headers = array_map('trim', $row);
                    } else {
                        // Data rows - create associative array with headers as keys
                        $rowData = [];
                        foreach ($headers as $index => $header) {
                            $rowData[$header] = isset($row[$index]) ? trim($row[$index]) : '';
                        }
                        $csvData[] = $rowData;
                    }
                    $rowIndex++;
                }
                fclose($handle);
            }

            return [
                'headers' => $headers,
                'data' => $csvData,
                'total_rows' => count($csvData),
                'filename' => $filename
            ];

        } catch (Exception $e) {
            Log::error("CSV Reader Error: " . $e->getMessage());
            throw new Exception("Error reading CSV file: " . $e->getMessage());
        }
    }

    /**
     * Get CSV data formatted for DataTables
     *
     * @param string $filename
     * @param array $searchableColumns - Optional: specify which columns are searchable
     * @return array
     */
    public function getDataTableData(string $filename, array $searchableColumns = []): array
    {
        $csvData = $this->readCsv($filename);
        
        // Format for DataTables
        return [
            'headers' => $csvData['headers'],
            'data' => $csvData['data'],
            'recordsTotal' => $csvData['total_rows'],
            'recordsFiltered' => $csvData['total_rows'],
            'searchableColumns' => empty($searchableColumns) ? $csvData['headers'] : $searchableColumns,
            'filename' => $csvData['filename']
        ];
    }

    /**
     * Apply search filter to CSV data
     *
     * @param array $data
     * @param string $searchValue
     * @param array $searchableColumns
     * @return array
     */
    public function filterData(array $data, string $searchValue, array $searchableColumns = []): array
    {
        if (empty($searchValue)) {
            return $data;
        }

        return array_filter($data, function ($row) use ($searchValue, $searchableColumns) {
            $searchColumns = empty($searchableColumns) ? array_keys($row) : $searchableColumns;
            
            foreach ($searchColumns as $column) {
                if (isset($row[$column]) && 
                    stripos($row[$column], $searchValue) !== false) {
                    return true;
                }
            }
            return false;
        });
    }

    /**
     * Create CSV directory if it doesn't exist
     */
    public function ensureCsvDirectoryExists(): void
    {
        $csvDir = storage_path('app/csv');
        if (!File::exists($csvDir)) {
            File::makeDirectory($csvDir, 0755, true);
        }
    }

    /**
     * List available CSV files
     *
     * @return array
     */
    public function getAvailableCsvFiles(): array
    {
        $csvDir = storage_path('app/csv');
        $this->ensureCsvDirectoryExists();
        
        $files = File::files($csvDir);
        return array_map(function ($file) {
            return $file->getFilename();
        }, $files);
    }
} 