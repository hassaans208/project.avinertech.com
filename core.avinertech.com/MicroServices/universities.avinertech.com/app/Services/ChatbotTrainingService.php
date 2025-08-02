<?php

namespace App\Services;

use App\Services\CsvReaderService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ChatbotTrainingService
{
    protected $csvReaderService;

    public function __construct(CsvReaderService $csvReaderService)
    {
        $this->csvReaderService = $csvReaderService;
    }

    /**
     * Generate training dataset in various formats for AI chatbot
     *
     * @param string $format - 'jsonl', 'csv', 'txt'
     * @return array
     */
    public function generateTrainingDataset(string $format = 'jsonl'): array
    {
        try {
            $this->csvReaderService->ensureCsvDirectoryExists();
            
            // Get source data
            $qsData = $this->csvReaderService->readCsv('qs-rankings.csv');
            $programsData = $this->csvReaderService->readCsv('programs-database.csv');

            // Generate training examples
            $trainingExamples = [];
            
            // Add university information examples
            $trainingExamples = array_merge($trainingExamples, $this->generateUniversityTrainingExamples($qsData['data']));
            
            // Add program information examples  
            $trainingExamples = array_merge($trainingExamples, $this->generateProgramTrainingExamples($programsData['data']));
            
            // Add comparative examples
            $trainingExamples = array_merge($trainingExamples, $this->generateComparisonTrainingExamples($qsData['data']));
            
            // Add boundary examples (what NOT to answer)
            $trainingExamples = array_merge($trainingExamples, $this->generateBoundaryExamples());

            // Save training data in requested format
            $filename = $this->saveTrainingData($trainingExamples, $format);

            return [
                'success' => true,
                'filename' => $filename,
                'total_examples' => count($trainingExamples),
                'format' => $format,
                'path' => storage_path("app/chatbot-training/{$filename}")
            ];

        } catch (\Exception $e) {
            Log::error('Training Dataset Generation Error: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Generate training examples for university information
     */
    private function generateUniversityTrainingExamples(array $universities): array
    {
        $examples = [];
        
        foreach ($universities as $university) {
            $name = $university['University Name'] ?? 'Unknown';
            $country = $university['Country'] ?? 'Unknown';
            $city = $university['City'] ?? 'Unknown';
            $rank = $university['QS Rank'] ?? 'Unranked';
            $score = $university['Overall Score'] ?? 'No score';

            // Basic information examples
            $examples[] = [
                'input' => "Tell me about {$name}",
                'output' => $this->generateUniversityResponse($university),
                'category' => 'university_info'
            ];

            $examples[] = [
                'input' => "What is the ranking of {$name}?",
                'output' => $rank !== 'Unranked' ? 
                    "{$name} is ranked #{$rank} in the QS World University Rankings." :
                    "{$name} does not have a specific QS ranking available.",
                'category' => 'university_ranking'
            ];

            $examples[] = [
                'input' => "Where is {$name} located?",
                'output' => "{$name} is located in {$city}, {$country}.",
                'category' => 'university_location'
            ];

            // Reputation and scores
            if (!empty($university['Academic Reputation Score'])) {
                $examples[] = [
                    'input' => "What is the academic reputation of {$name}?",
                    'output' => "{$name} has an academic reputation score of {$university['Academic Reputation Score']} in the QS World University Rankings.",
                    'category' => 'academic_reputation'
                ];
            }

            if (!empty($university['International Fees'])) {
                $examples[] = [
                    'input' => "What are the international fees at {$name}?",
                    'output' => "The international fees at {$name} are {$university['International Fees']}.",
                    'category' => 'fees'
                ];
            }
        }

        return $examples;
    }

    /**
     * Generate training examples for program information
     */
    private function generateProgramTrainingExamples(array $programs): array
    {
        $examples = [];
        $programsByUniversity = [];

        // Group programs by university
        foreach ($programs as $program) {
            $universityName = $program['University Name'] ?? 'Unknown';
            if (!isset($programsByUniversity[$universityName])) {
                $programsByUniversity[$universityName] = [];
            }
            $programsByUniversity[$universityName][] = $program;
        }

        foreach ($programsByUniversity as $universityName => $universityPrograms) {
            // Programs at university
            $examples[] = [
                'input' => "What programs does {$universityName} offer?",
                'output' => $this->generateProgramsResponse($universityName, $universityPrograms),
                'category' => 'university_programs'
            ];

            // Program categories
            $categories = array_unique(array_column($universityPrograms, 'Program Category'));
            foreach ($categories as $category) {
                if (!empty($category)) {
                    $categoryPrograms = array_filter($universityPrograms, function($p) use ($category) {
                        return ($p['Program Category'] ?? '') === $category;
                    });
                    
                    $examples[] = [
                        'input' => "What {$category} programs does {$universityName} offer?",
                        'output' => $this->generateCategoryProgramsResponse($universityName, $category, $categoryPrograms),
                        'category' => 'category_programs'
                    ];
                }
            }
        }

        return $examples;
    }

    /**
     * Generate training examples for university comparisons
     */
    private function generateComparisonTrainingExamples(array $universities): array
    {
        $examples = [];
        
        // Country-based comparisons
        $universitiesByCountry = [];
        foreach ($universities as $university) {
            $country = $university['Country'] ?? 'Unknown';
            if (!isset($universitiesByCountry[$country])) {
                $universitiesByCountry[$country] = [];
            }
            $universitiesByCountry[$country][] = $university;
        }

        foreach ($universitiesByCountry as $country => $countryUniversities) {
            if (count($countryUniversities) > 1) {
                $examples[] = [
                    'input' => "What are the top universities in {$country}?",
                    'output' => $this->generateCountryUniversitiesResponse($country, $countryUniversities),
                    'category' => 'country_universities'
                ];
            }
        }

        // Ranking range examples
        $topUniversities = array_filter($universities, function($u) {
            $rank = $u['QS Rank'] ?? '';
            return is_numeric($rank) && (int)$rank <= 50;
        });

        if (!empty($topUniversities)) {
            $examples[] = [
                'input' => "What are the top 50 universities in the world?",
                'output' => $this->generateTopUniversitiesResponse($topUniversities),
                'category' => 'top_universities'
            ];
        }

        return $examples;
    }

    /**
     * Generate boundary examples - what the chatbot should NOT answer
     */
    private function generateBoundaryExamples(): array
    {
        return [
            [
                'input' => "What's the weather like today?",
                'output' => "I'm a university information assistant. I can only help you with questions about universities, their rankings, programs, and related academic information. Please ask me about universities or educational programs.",
                'category' => 'boundary_weather'
            ],
            [
                'input' => "How do I cook pasta?",
                'output' => "I specialize in university and educational information only. I can help you find information about universities, their programs, rankings, and academic details. What would you like to know about universities?",
                'category' => 'boundary_cooking'
            ],
            [
                'input' => "What's happening in the news?",
                'output' => "I'm designed to provide information about universities and higher education only. I can help you with university rankings, programs, admissions information, and academic details. How can I assist you with university-related questions?",
                'category' => 'boundary_news'
            ],
            [
                'input' => "Can you help me with my personal problems?",
                'output' => "I'm a specialized assistant for university and educational information. I can help you find the right university, understand program offerings, compare rankings, and provide academic guidance. What university-related information can I help you with?",
                'category' => 'boundary_personal'
            ],
            [
                'input' => "Tell me a joke",
                'output' => "I'm focused on providing accurate university and educational information. I can help you explore universities, their programs, rankings, and academic opportunities. What would you like to know about higher education?",
                'category' => 'boundary_entertainment'
            ]
        ];
    }

    /**
     * Generate comprehensive university response
     */
    private function generateUniversityResponse(array $university): string
    {
        $name = $university['University Name'] ?? 'Unknown University';
        $location = ($university['City'] ?? 'Unknown city') . ', ' . ($university['Country'] ?? 'Unknown country');
        $rank = $university['QS Rank'] ?? 'Unranked';
        $score = $university['Overall Score'] ?? 'No score available';

        $response = "{$name} is a university located in {$location}. ";
        
        if ($rank !== 'Unranked') {
            $response .= "It is ranked #{$rank} in the QS World University Rankings ";
            if ($score !== 'No score available') {
                $response .= "with an overall score of {$score}. ";
            } else {
                $response .= ". ";
            }
        }

        // Add key metrics if available
        $metrics = [];
        if (!empty($university['Academic Reputation Score'])) {
            $metrics[] = "Academic Reputation: {$university['Academic Reputation Score']}";
        }
        if (!empty($university['Employer Reputation Score'])) {
            $metrics[] = "Employer Reputation: {$university['Employer Reputation Score']}";
        }

        if (!empty($metrics)) {
            $response .= "Key scores include " . implode(', ', $metrics) . ". ";
        }

        return trim($response);
    }

    /**
     * Generate programs response for a university
     */
    private function generateProgramsResponse(string $universityName, array $programs): string
    {
        $programCount = count($programs);
        $response = "{$universityName} offers {$programCount} programs. ";

        // Group by category
        $categories = [];
        foreach ($programs as $program) {
            $category = $program['Program Category'] ?? 'Other';
            if (!isset($categories[$category])) {
                $categories[$category] = 0;
            }
            $categories[$category]++;
        }

        if (!empty($categories)) {
            $categoryStrings = [];
            foreach ($categories as $category => $count) {
                $categoryStrings[] = "{$count} {$category} programs";
            }
            $response .= "This includes " . implode(', ', $categoryStrings) . ". ";
        }

        return trim($response);
    }

    /**
     * Generate category-specific programs response
     */
    private function generateCategoryProgramsResponse(string $universityName, string $category, array $programs): string
    {
        $programNames = array_column($programs, 'Program Name');
        $programNames = array_filter($programNames); // Remove empty values
        
        if (empty($programNames)) {
            return "{$universityName} doesn't appear to have specific {$category} program details available.";
        }

        $response = "{$universityName} offers the following {$category} programs: ";
        
        if (count($programNames) <= 5) {
            $response .= implode(', ', $programNames) . ".";
        } else {
            $response .= implode(', ', array_slice($programNames, 0, 5)) . ", and " . (count($programNames) - 5) . " more.";
        }

        return $response;
    }

    /**
     * Generate country universities response
     */
    private function generateCountryUniversitiesResponse(string $country, array $universities): string
    {
        // Sort by rank (numeric ranks first, then unranked)
        usort($universities, function($a, $b) {
            $rankA = $a['QS Rank'] ?? 'Unranked';
            $rankB = $b['QS Rank'] ?? 'Unranked';
            
            if (is_numeric($rankA) && is_numeric($rankB)) {
                return (int)$rankA - (int)$rankB;
            }
            if (is_numeric($rankA)) return -1;
            if (is_numeric($rankB)) return 1;
            return 0;
        });

        $topUniversities = array_slice($universities, 0, 5);
        $universityNames = array_column($topUniversities, 'University Name');

        return "The top universities in {$country} include: " . implode(', ', $universityNames) . ".";
    }

    /**
     * Generate top universities response
     */
    private function generateTopUniversitiesResponse(array $universities): string
    {
        // Sort by rank
        usort($universities, function($a, $b) {
            return (int)($a['QS Rank'] ?? 999) - (int)($b['QS Rank'] ?? 999);
        });

        $top10 = array_slice($universities, 0, 10);
        $response = "Some of the top universities in the world include: ";
        
        $universityStrings = [];
        foreach ($top10 as $university) {
            $name = $university['University Name'] ?? 'Unknown';
            $rank = $university['QS Rank'] ?? 'Unranked';
            $universityStrings[] = "{$name} (#{$rank})";
        }

        return $response . implode(', ', $universityStrings) . ".";
    }

    /**
     * Save training data in specified format
     */
    private function saveTrainingData(array $examples, string $format): string
    {
        $timestamp = now()->format('Y-m-d_H-i-s');
        $filename = "university_chatbot_training_{$timestamp}.{$format}";
        
        // Ensure training directory exists
        $trainingDir = storage_path('app/chatbot-training');
        if (!file_exists($trainingDir)) {
            mkdir($trainingDir, 0755, true);
        }

        $filepath = "{$trainingDir}/{$filename}";

        switch ($format) {
            case 'jsonl':
                $this->saveAsJsonl($examples, $filepath);
                break;
            case 'csv':
                $this->saveAsCsv($examples, $filepath);
                break;
            case 'txt':
                $this->saveAsTxt($examples, $filepath);
                break;
            default:
                throw new \Exception("Unsupported format: {$format}");
        }

        return $filename;
    }

    /**
     * Save as JSONL format (for OpenAI fine-tuning)
     */
    private function saveAsJsonl(array $examples, string $filepath): void
    {
        $file = fopen($filepath, 'w');
        
        foreach ($examples as $example) {
            $jsonLine = json_encode([
                'messages' => [
                    ['role' => 'user', 'content' => $example['input']],
                    ['role' => 'assistant', 'content' => $example['output']]
                ],
                'metadata' => [
                    'category' => $example['category']
                ]
            ]) . "\n";
            
            fwrite($file, $jsonLine);
        }
        
        fclose($file);
    }

    /**
     * Save as CSV format
     */
    private function saveAsCsv(array $examples, string $filepath): void
    {
        $file = fopen($filepath, 'w');
        
        // Write header
        fputcsv($file, ['input', 'output', 'category']);
        
        foreach ($examples as $example) {
            fputcsv($file, [
                $example['input'],
                $example['output'],
                $example['category']
            ]);
        }
        
        fclose($file);
    }

    /**
     * Save as plain text format
     */
    private function saveAsTxt(array $examples, string $filepath): void
    {
        $content = "# University Chatbot Training Data\n";
        $content .= "# Generated on: " . now()->toDateTimeString() . "\n\n";

        foreach ($examples as $example) {
            $content .= "## Category: {$example['category']}\n";
            $content .= "**User:** {$example['input']}\n";
            $content .= "**Assistant:** {$example['output']}\n\n";
            $content .= "---\n\n";
        }

        file_put_contents($filepath, $content);
    }

    /**
     * Get training data statistics
     */
    public function getTrainingDataStats(): array
    {
        try {
            $qsData = $this->csvReaderService->readCsv('qs-rankings.csv');
            $programsData = $this->csvReaderService->readCsv('programs-database.csv');

            return [
                'universities_count' => count($qsData['data']),
                'programs_count' => count($programsData['data']),
                'estimated_training_examples' => $this->estimateTrainingExamples($qsData['data'], $programsData['data']),
                'data_freshness' => [
                    'qs_rankings_file_size' => filesize(storage_path('app/csv/qs-rankings.csv')),
                    'programs_file_size' => filesize(storage_path('app/csv/programs-database.csv'))
                ]
            ];
        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * Estimate number of training examples that will be generated
     */
    private function estimateTrainingExamples(array $universities, array $programs): int
    {
        $universityExamples = count($universities) * 4; // Basic info, ranking, location, reputation
        $programExamples = count(array_unique(array_column($programs, 'University Name'))) * 2; // Programs per university
        $comparisonExamples = 50; // Estimated comparison examples
        $boundaryExamples = 5; // Fixed boundary examples

        return $universityExamples + $programExamples + $comparisonExamples + $boundaryExamples;
    }
} 