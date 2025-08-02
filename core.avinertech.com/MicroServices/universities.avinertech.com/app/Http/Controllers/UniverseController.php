<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\CsvReaderService;
use Illuminate\Support\Facades\Log;
use Exception;

class UniverseController extends Controller
{
    protected $csvReaderService;

    public function __construct(CsvReaderService $csvReaderService)
    {
        $this->csvReaderService = $csvReaderService;
    }

    /**
     * Display QS Rankings data
     *
     * @param Request $request
     * @return \Illuminate\View\View|\Illuminate\Http\JsonResponse
     */
    public function qsRankings(Request $request)
    {
        try {
            // Ensure CSV directory exists
            $this->csvReaderService->ensureCsvDirectoryExists();
            
            $filename = 'qs-rankings.csv'; // Expected filename
            
            // Check if this is an AJAX request for DataTables
            if ($request->ajax()) {
                return $this->getDataTableResponse($request, $filename);
            }

            // Get CSV data for initial page load
            $csvData = $this->csvReaderService->getDataTableData($filename, [
                "QS Rank","Overall Score","University Name","Region","Country","City","Advanced Profile","Stars","Dagger","Redact","Is Shortlisted","International Fees","Scholarship","Student Mix","English Tests","Academic Tests","Citations per Faculty Score","Citations per Faculty Rank","Academic Reputation Score","Academic Reputation Rank","Faculty Student Ratio Score","Faculty Student Ratio Rank","Employer Reputation Score","Employer Reputation Rank","Employment Outcomes Score","Employment Outcomes Rank","International Student Ratio Score","International Student Ratio Rank","International Research Network Score","International Research Network Rank","International Faculty Ratio Score","International Faculty Ratio Rank","International Student Diversity Score","International Student Diversity Rank","Sustainability Score","Sustainability Rank"
            ]);

            return view('universe.qs-rankings', [
                'title' => 'QS World University Rankings',
                'headers' => $csvData['headers'],
                'totalRecords' => $csvData['recordsTotal'],
                'filename' => $csvData['filename'],
                'searchableColumns' => $csvData['searchableColumns']
            ]);

        } catch (Exception $e) {
            Log::error('QS Rankings Error: ' . $e->getMessage());
            
            if ($request->ajax()) {
                return response()->json([
                    'error' => 'Unable to load QS Rankings data',
                    'message' => $e->getMessage()
                ], 500);
            }

            return view('universe.qs-rankings', [
                'title' => 'QS World University Rankings',
                'error' => 'Unable to load QS Rankings data: ' . $e->getMessage(),
                'headers' => [],
                'totalRecords' => 0,
                'filename' => $filename
            ]);
        }
    }

    /**
     * Display Programs Database data
     *
     * @param Request $request
     * @return \Illuminate\View\View|\Illuminate\Http\JsonResponse
     */
    public function programsDatabase(Request $request)
    {
        try {
            // Ensure CSV directory exists
            $this->csvReaderService->ensureCsvDirectoryExists();
            
            $filename = 'programs-database.csv'; // Expected filename
            
            // Check if this is an AJAX request for DataTables
            if ($request->ajax()) {
                return $this->getDataTableResponse($request, $filename);
            }

            // Get CSV data for initial page load
            $csvData = $this->csvReaderService->getDataTableData($filename, [
                'University Name', 'Program Name', 'Program Category', 'Program Level'
            ]);

            return view('universe.programs-database', [
                'title' => 'University Programs Database',
                'headers' => $csvData['headers'],
                'totalRecords' => $csvData['recordsTotal'],
                'filename' => $csvData['filename'],
                'searchableColumns' => $csvData['searchableColumns']
            ]);

        } catch (Exception $e) {
            Log::error('Programs Database Error: ' . $e->getMessage());
            
            if ($request->ajax()) {
                return response()->json([
                    'error' => 'Unable to load Programs Database data',
                    'message' => $e->getMessage()
                ], 500);
            }

            return view('universe.programs-database', [
                'title' => 'University Programs Database',
                'error' => 'Unable to load Programs Database data: ' . $e->getMessage(),
                'headers' => [],
                'totalRecords' => 0,
                'filename' => $filename
            ]);
        }
    }

    /**
     * Handle DataTables AJAX requests
     *
     * @param Request $request
     * @param string $filename
     * @return \Illuminate\Http\JsonResponse
     */
    private function getDataTableResponse(Request $request, string $filename)
    {
        try {
            $csvData = $this->csvReaderService->readCsv($filename);
            
            // Get request parameters
            $draw = $request->get('draw', 1);
            $start = (int) $request->get('start', 0);
            $length = (int) $request->get('length', 10);
            $searchValue = $request->get('search')['value'] ?? '';
            
            // Apply search filter if provided
            $filteredData = $csvData['data'];
            if (!empty($searchValue)) {
                $filteredData = $this->csvReaderService->filterData(
                    $csvData['data'], 
                    $searchValue, 
                    $csvData['headers']
                );
            }

            // Apply pagination
            $paginatedData = array_slice($filteredData, $start, $length);

            return response()->json([
                'draw' => $draw,
                'recordsTotal' => $csvData['total_rows'],
                'recordsFiltered' => count($filteredData),
                'data' => array_values($paginatedData),
                'headers' => $csvData['headers']
            ]);

        } catch (Exception $e) {
            Log::error('DataTable Response Error: ' . $e->getMessage());
            return response()->json([
                'draw' => $request->get('draw', 1),
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * API endpoint to get available CSV files
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAvailableFiles()
    {
        try {
            $files = $this->csvReaderService->getAvailableCsvFiles();
            return response()->json([
                'files' => $files,
                'count' => count($files)
            ]);
        } catch (Exception $e) {
            return response()->json([
                'error' => 'Unable to retrieve CSV files',
                'message' => $e->getMessage()
            ], 500);
        }
    }
} 