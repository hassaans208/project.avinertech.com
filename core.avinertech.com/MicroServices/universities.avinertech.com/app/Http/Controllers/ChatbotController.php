<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\CsvReaderService;
use Illuminate\Support\Facades\Log;
use Exception;
use App\Services\ChatbotTrainingService;

class ChatbotController extends Controller
{
    protected $csvReaderService;
    protected $trainingService;

    public function __construct(CsvReaderService $csvReaderService, ChatbotTrainingService $trainingService)
    {
        $this->csvReaderService = $csvReaderService;
        $this->trainingService = $trainingService;
    }

    /**
     * Get structured university data for chatbot training
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUniversityData(Request $request)
    {
        try {
            $this->csvReaderService->ensureCsvDirectoryExists();
            
            // Get QS Rankings data
            $qsData = $this->csvReaderService->readCsv('qs-rankings.csv');
            
            // Get Programs data
            $programsData = $this->csvReaderService->readCsv('programs-database.csv');
            
            // Structure the data for AI training
            $structuredData = [
                'universities' => $this->formatUniversityData($qsData['data']),
                'programs' => $this->formatProgramData($programsData['data']),
                'metadata' => [
                    'total_universities' => count($qsData['data']),
                    'total_programs' => count($programsData['data']),
                    'last_updated' => now()->toISOString(),
                    'data_sources' => ['QS World University Rankings', 'Programs Database']
                ]
            ];

            return response()->json($structuredData);

        } catch (Exception $e) {
            Log::error('Chatbot Data API Error: ' . $e->getMessage());
            return response()->json([
                'error' => 'Unable to retrieve university data for chatbot',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Search universities by query for chatbot responses
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function searchUniversities(Request $request)
    {
        try {
            $query = $request->get('query', '');
            $limit = $request->get('limit', 10);
            
            if (empty($query)) {
                return response()->json([
                    'results' => [],
                    'message' => 'Please provide a search query'
                ]);
            }

            $qsData = $this->csvReaderService->readCsv('qs-rankings.csv');
            
            // Filter universities based on query
            $filtered = $this->csvReaderService->filterData(
                $qsData['data'], 
                $query, 
                ['University Name', 'Country', 'City', 'Region']
            );

            // Limit results and format for chatbot
            $results = array_slice($filtered, 0, $limit);
            $formattedResults = array_map(function($university) {
                return $this->formatUniversityForChat($university);
            }, $results);

            return response()->json([
                'results' => $formattedResults,
                'total_found' => count($filtered),
                'query' => $query
            ]);

        } catch (Exception $e) {
            Log::error('University Search API Error: ' . $e->getMessage());
            return response()->json([
                'error' => 'Unable to search universities',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get programs for a specific university
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUniversityPrograms(Request $request)
    {
        try {
            $universityName = $request->get('university', '');
            
            if (empty($universityName)) {
                return response()->json([
                    'programs' => [],
                    'message' => 'Please provide a university name'
                ]);
            }

            $programsData = $this->csvReaderService->readCsv('programs-database.csv');
            
            // Filter programs by university
            $universityPrograms = array_filter($programsData['data'], function($program) use ($universityName) {
                return isset($program['University Name']) && 
                       stripos($program['University Name'], $universityName) !== false;
            });

            $formattedPrograms = array_map(function($program) {
                return [
                    'name' => $program['Program Name'] ?? 'N/A',
                    'category' => $program['Program Category'] ?? 'N/A',
                    'level' => $program['Program Level'] ?? 'N/A',
                    'university' => $program['University Name'] ?? 'N/A'
                ];
            }, $universityPrograms);

            return response()->json([
                'programs' => array_values($formattedPrograms),
                'university' => $universityName,
                'total_programs' => count($formattedPrograms)
            ]);

        } catch (Exception $e) {
            Log::error('University Programs API Error: ' . $e->getMessage());
            return response()->json([
                'error' => 'Unable to retrieve university programs',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get knowledge base content for AI training
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getKnowledgeBase()
    {
        try {
            $qsData = $this->csvReaderService->readCsv('qs-rankings.csv');
            $programsData = $this->csvReaderService->readCsv('programs-database.csv');

            // Create knowledge base entries
            $knowledgeBase = [];

            // Add university knowledge entries
            foreach ($qsData['data'] as $university) {
                $knowledgeBase[] = $this->createUniversityKnowledgeEntry($university);
            }

            // Add program knowledge entries
            $programsByUniversity = [];
            foreach ($programsData['data'] as $program) {
                $universityName = $program['University Name'] ?? 'Unknown';
                if (!isset($programsByUniversity[$universityName])) {
                    $programsByUniversity[$universityName] = [];
                }
                $programsByUniversity[$universityName][] = $program;
            }

            foreach ($programsByUniversity as $universityName => $programs) {
                $knowledgeBase[] = $this->createProgramKnowledgeEntry($universityName, $programs);
            }

            return response()->json([
                'knowledge_base' => $knowledgeBase,
                'total_entries' => count($knowledgeBase),
                'generated_at' => now()->toISOString()
            ]);

        } catch (Exception $e) {
            Log::error('Knowledge Base API Error: ' . $e->getMessage());
            return response()->json([
                'error' => 'Unable to generate knowledge base',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate training dataset for AI chatbot
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function generateTrainingData(Request $request)
    {
        try {
            $format = $request->get('format', 'jsonl'); // jsonl, csv, txt
            
            $result = $this->trainingService->generateTrainingDataset($format);
            
            if ($result['success']) {
                return response()->json([
                    'success' => true,
                    'message' => 'Training dataset generated successfully',
                    'filename' => $result['filename'],
                    'total_examples' => $result['total_examples'],
                    'format' => $result['format'],
                    'download_path' => $result['path']
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'error' => $result['error']
                ], 500);
            }

        } catch (Exception $e) {
            Log::error('Training Data Generation Error: ' . $e->getMessage());
            return response()->json([
                'error' => 'Unable to generate training data',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get training data statistics
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getTrainingStats()
    {
        try {
            $stats = $this->trainingService->getTrainingDataStats();
            
            return response()->json([
                'stats' => $stats,
                'recommendations' => $this->getTrainingRecommendations($stats)
            ]);

        } catch (Exception $e) {
            Log::error('Training Stats Error: ' . $e->getMessage());
            return response()->json([
                'error' => 'Unable to retrieve training statistics',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get recommendations for chatbot training
     */
    private function getTrainingRecommendations(array $stats): array
    {
        $recommendations = [];

        if (isset($stats['universities_count'])) {
            if ($stats['universities_count'] < 100) {
                $recommendations[] = [
                    'type' => 'warning',
                    'message' => 'Consider adding more universities to improve chatbot knowledge coverage.',
                    'suggestion' => 'Current count: ' . $stats['universities_count'] . ' universities'
                ];
            }

            if ($stats['estimated_training_examples'] > 0) {
                $recommendations[] = [
                    'type' => 'info',
                    'message' => 'Estimated training examples: ' . number_format($stats['estimated_training_examples']),
                    'suggestion' => 'This should provide good coverage for a domain-specific chatbot.'
                ];
            }
        }

        $recommendations[] = [
            'type' => 'tip',
            'message' => 'For best results, use the JSONL format for fine-tuning models like GPT-3.5/GPT-4.',
            'suggestion' => 'Use the CSV format for training traditional ML models or data analysis.'
        ];

        return $recommendations;
    }

    /**
     * Format university data for structured output
     */
    private function formatUniversityData(array $universities): array
    {
        return array_map(function($university) {
            return [
                'name' => $university['University Name'] ?? 'N/A',
                'country' => $university['Country'] ?? 'N/A',
                'city' => $university['City'] ?? 'N/A',
                'region' => $university['Region'] ?? 'N/A',
                'qs_rank' => $university['QS Rank'] ?? 'N/A',
                'overall_score' => $university['Overall Score'] ?? 'N/A',
                'academic_reputation_score' => $university['Academic Reputation Score'] ?? 'N/A',
                'employer_reputation_score' => $university['Employer Reputation Score'] ?? 'N/A',
                'citations_per_faculty_score' => $university['Citations per Faculty Score'] ?? 'N/A',
                'international_fees' => $university['International Fees'] ?? 'N/A',
                'scholarship' => $university['Scholarship'] ?? 'N/A'
            ];
        }, $universities);
    }

    /**
     * Format program data for structured output
     */
    private function formatProgramData(array $programs): array
    {
        return array_map(function($program) {
            return [
                'university_name' => $program['University Name'] ?? 'N/A',
                'program_name' => $program['Program Name'] ?? 'N/A',
                'program_category' => $program['Program Category'] ?? 'N/A',
                'program_level' => $program['Program Level'] ?? 'N/A'
            ];
        }, $programs);
    }

    /**
     * Format university for chat response
     */
    private function formatUniversityForChat(array $university): array
    {
        return [
            'name' => $university['University Name'] ?? 'N/A',
            'location' => ($university['City'] ?? 'N/A') . ', ' . ($university['Country'] ?? 'N/A'),
            'rank' => $university['QS Rank'] ?? 'N/A',
            'score' => $university['Overall Score'] ?? 'N/A',
            'summary' => $this->generateUniversitySummary($university)
        ];
    }

    /**
     * Generate a natural language summary for a university
     */
    private function generateUniversitySummary(array $university): string
    {
        $name = $university['University Name'] ?? 'This university';
        $location = ($university['City'] ?? 'Unknown city') . ', ' . ($university['Country'] ?? 'Unknown country');
        $rank = $university['QS Rank'] ?? 'Unranked';
        $score = $university['Overall Score'] ?? 'No score';

        return "{$name} is located in {$location}. It ranks #{$rank} in the QS World University Rankings with an overall score of {$score}.";
    }

    /**
     * Create knowledge base entry for a university
     */
    private function createUniversityKnowledgeEntry(array $university): array
    {
        $name = $university['University Name'] ?? 'Unknown University';
        $content = $this->generateUniversityKnowledgeContent($university);

        return [
            'type' => 'university',
            'title' => $name,
            'content' => $content,
            'keywords' => $this->extractUniversityKeywords($university),
            'metadata' => [
                'university_name' => $name,
                'country' => $university['Country'] ?? 'N/A',
                'rank' => $university['QS Rank'] ?? 'N/A'
            ]
        ];
    }

    /**
     * Create knowledge base entry for programs
     */
    private function createProgramKnowledgeEntry(string $universityName, array $programs): array
    {
        $content = $this->generateProgramKnowledgeContent($universityName, $programs);

        return [
            'type' => 'programs',
            'title' => "Programs at {$universityName}",
            'content' => $content,
            'keywords' => $this->extractProgramKeywords($programs),
            'metadata' => [
                'university_name' => $universityName,
                'program_count' => count($programs)
            ]
        ];
    }

    /**
     * Generate detailed knowledge content for a university
     */
    private function generateUniversityKnowledgeContent(array $university): string
    {
        $name = $university['University Name'] ?? 'Unknown University';
        $location = ($university['City'] ?? 'Unknown city') . ', ' . ($university['Country'] ?? 'Unknown country');
        $rank = $university['QS Rank'] ?? 'Unranked';
        $score = $university['Overall Score'] ?? 'No score available';
        
        $content = "{$name} is a university located in {$location}. ";
        
        if ($rank !== 'Unranked') {
            $content .= "It is ranked #{$rank} in the QS World University Rankings. ";
        }
        
        if ($score !== 'No score available') {
            $content .= "The university has an overall QS score of {$score}. ";
        }

        // Add specific scores if available
        $scores = [];
        if (!empty($university['Academic Reputation Score'])) {
            $scores[] = "Academic Reputation: {$university['Academic Reputation Score']}";
        }
        if (!empty($university['Employer Reputation Score'])) {
            $scores[] = "Employer Reputation: {$university['Employer Reputation Score']}";
        }
        if (!empty($university['Citations per Faculty Score'])) {
            $scores[] = "Citations per Faculty: {$university['Citations per Faculty Score']}";
        }

        if (!empty($scores)) {
            $content .= "Key performance indicators include: " . implode(', ', $scores) . ". ";
        }

        // Add fees and scholarship info if available
        if (!empty($university['International Fees'])) {
            $content .= "International fees: {$university['International Fees']}. ";
        }
        if (!empty($university['Scholarship'])) {
            $content .= "Scholarship information: {$university['Scholarship']}. ";
        }

        return trim($content);
    }

    /**
     * Generate knowledge content for programs
     */
    private function generateProgramKnowledgeContent(string $universityName, array $programs): string
    {
        $content = "{$universityName} offers " . count($programs) . " programs. ";
        
        // Group by category
        $categories = [];
        foreach ($programs as $program) {
            $category = $program['Program Category'] ?? 'Other';
            if (!isset($categories[$category])) {
                $categories[$category] = [];
            }
            $categories[$category][] = $program['Program Name'] ?? 'Unknown Program';
        }

        foreach ($categories as $category => $programNames) {
            $content .= "In {$category}, they offer: " . implode(', ', array_slice($programNames, 0, 5));
            if (count($programNames) > 5) {
                $content .= " and " . (count($programNames) - 5) . " more programs";
            }
            $content .= ". ";
        }

        return trim($content);
    }

    /**
     * Extract keywords for university search
     */
    private function extractUniversityKeywords(array $university): array
    {
        $keywords = [];
        
        if (!empty($university['University Name'])) {
            $keywords[] = $university['University Name'];
            // Add individual words from university name
            $keywords = array_merge($keywords, explode(' ', $university['University Name']));
        }
        
        if (!empty($university['Country'])) {
            $keywords[] = $university['Country'];
        }
        
        if (!empty($university['City'])) {
            $keywords[] = $university['City'];
        }
        
        if (!empty($university['Region'])) {
            $keywords[] = $university['Region'];
        }

        return array_unique(array_filter($keywords));
    }

    /**
     * Extract keywords for program search
     */
    private function extractProgramKeywords(array $programs): array
    {
        $keywords = [];
        
        foreach ($programs as $program) {
            if (!empty($program['Program Name'])) {
                $keywords[] = $program['Program Name'];
                $keywords = array_merge($keywords, explode(' ', $program['Program Name']));
            }
            
            if (!empty($program['Program Category'])) {
                $keywords[] = $program['Program Category'];
            }
            
            if (!empty($program['Program Level'])) {
                $keywords[] = $program['Program Level'];
            }
        }

        return array_unique(array_filter($keywords));
    }
} 