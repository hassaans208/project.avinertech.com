<?php

namespace App\Services;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Exception;

class CsvReaderService
{
    /**
     * Read CSV file and return data with headers
     */
    public function readCsv(string $filename): array
    {
        $filePath = $this->getFilePath($filename);
        
        if (!file_exists($filePath)) {
            throw new \Exception("CSV file not found: {$filename}");
        }
        
        $data = [];
        $headers = [];
        
        if (($handle = fopen($filePath, 'r')) !== false) {
            // Read header row
            if (($headerRow = fgetcsv($handle)) !== false) {
                // Filter out NID columns and clean headers
                $filteredHeaders = [];
                $headerIndexMap = [];
                
                foreach ($headerRow as $index => $header) {
                    $cleanHeader = trim($header, '"');
                    // Skip columns that contain 'NID' or 'Logo' (case insensitive)
                    if (stripos($cleanHeader, 'nid') === false && stripos($cleanHeader, 'logo') === false) {
                        $filteredHeaders[] = $cleanHeader;
                        $headerIndexMap[] = $index;
                    }
                }
                $headers = $filteredHeaders;
            }
            
            // Read data rows
            while (($row = fgetcsv($handle)) !== false) {
                $filteredRow = [];
                
                // Only include columns that are not filtered out
                foreach ($headerIndexMap as $originalIndex) {
                    $value = isset($row[$originalIndex]) ? trim($row[$originalIndex], '"') : '';
                    $filteredRow[] = $value;
                }
                
                if (count($filteredRow) === count($headers)) {
                    $associativeRow = array_combine($headers, $filteredRow);
                    
                    // Transform university path to clickable link if it exists
                    if (isset($associativeRow['University Path']) && !empty($associativeRow['University Path'])) {
                        $universityPath = $associativeRow['University Path'];
                        // Ensure path starts with /
                        if (!str_starts_with($universityPath, '/')) {
                            $universityPath = '/' . $universityPath;
                        }
                        $fullUrl = 'https://www.topuniversities.com' . $universityPath;
                        $associativeRow['University Path'] = '<a href="' . htmlspecialchars($fullUrl) . '" target="_blank" class="text-blue-600 hover:text-blue-800 underline font-medium">Visit University</a>';
                    }
                    
                    $data[] = $associativeRow;
                }
            }
            
            fclose($handle);
        } else {
            throw new \Exception("Failed to open CSV file: {$filename}");
        }
        
        return [
            'headers' => $headers,
            'data' => $data,
            'total' => count($data)
        ];
    }

    /**
     * Get the full file path for a CSV file
     * 
     * @param string $filename
     * @return string
     */
    private function getFilePath(string $filename): string
    {
        return storage_path("app/csv/{$filename}");
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
            'recordsTotal' => $csvData['total'],
            'recordsFiltered' => $csvData['total'],
            'searchableColumns' => empty($searchableColumns) ? $csvData['headers'] : $searchableColumns,
            'filename' => $filename
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