<?php
/**
 * Simple test script to verify chatbot API endpoints
 * Run this script from the command line: php test_chatbot_api.php
 */

// Set base URL (adjust as needed)
$baseUrl = 'http://localhost:8000'; // Change this to your actual domain

// Test endpoints
$endpoints = [
    'Training Stats' => '/api/chatbot/training-stats',
    'University Search (MIT)' => '/api/chatbot/search-universities?query=MIT&limit=3',
    'University Programs (Harvard)' => '/api/chatbot/university-programs?university=Harvard',
    'Knowledge Base (first 2 entries)' => '/api/chatbot/knowledge-base'
];

echo "🚀 Testing University Chatbot API Endpoints\n";
echo "=" . str_repeat("=", 50) . "\n\n";

foreach ($endpoints as $name => $endpoint) {
    echo "Testing: {$name}\n";
    echo "URL: {$baseUrl}{$endpoint}\n";
    
    // Make request
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $baseUrl . $endpoint);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Accept: application/json',
        'User-Agent: ChatbotAPITest/1.0'
    ]);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    
    if ($error) {
        echo "❌ Error: {$error}\n";
    } elseif ($httpCode === 200) {
        $data = json_decode($response, true);
        echo "✅ Success (HTTP {$httpCode})\n";
        
        // Show relevant data based on endpoint
        if (strpos($endpoint, 'training-stats') !== false && isset($data['stats'])) {
            echo "   Universities: " . ($data['stats']['universities_count'] ?? 'N/A') . "\n";
            echo "   Programs: " . ($data['stats']['programs_count'] ?? 'N/A') . "\n";
            echo "   Est. Examples: " . ($data['stats']['estimated_training_examples'] ?? 'N/A') . "\n";
        } elseif (strpos($endpoint, 'search-universities') !== false && isset($data['results'])) {
            echo "   Found: " . ($data['total_found'] ?? 0) . " universities\n";
            echo "   Returned: " . count($data['results']) . " results\n";
            if (!empty($data['results'])) {
                echo "   First result: " . ($data['results'][0]['name'] ?? 'N/A') . "\n";
            }
        } elseif (strpos($endpoint, 'university-programs') !== false && isset($data['programs'])) {
            echo "   Programs found: " . count($data['programs']) . "\n";
            echo "   University: " . ($data['university'] ?? 'N/A') . "\n";
        } elseif (strpos($endpoint, 'knowledge-base') !== false && isset($data['knowledge_base'])) {
            echo "   Total entries: " . ($data['total_entries'] ?? 0) . "\n";
            echo "   Generated at: " . ($data['generated_at'] ?? 'N/A') . "\n";
        }
    } else {
        echo "❌ HTTP Error {$httpCode}\n";
        if ($response) {
            $errorData = json_decode($response, true);
            if (isset($errorData['error'])) {
                echo "   Error: " . $errorData['error'] . "\n";
            }
        }
    }
    
    echo str_repeat("-", 50) . "\n\n";
}

// Test training data generation
echo "Testing: Training Data Generation\n";
echo "URL: {$baseUrl}/api/chatbot/generate-training-data?format=jsonl\n";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $baseUrl . '/api/chatbot/generate-training-data?format=jsonl');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 60); // Longer timeout for generation
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Accept: application/json',
    'User-Agent: ChatbotAPITest/1.0'
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

if ($error) {
    echo "❌ Error: {$error}\n";
} elseif ($httpCode === 200) {
    $data = json_decode($response, true);
    echo "✅ Success (HTTP {$httpCode})\n";
    if (isset($data['success']) && $data['success']) {
        echo "   Filename: " . ($data['filename'] ?? 'N/A') . "\n";
        echo "   Examples: " . ($data['total_examples'] ?? 'N/A') . "\n";
        echo "   Format: " . ($data['format'] ?? 'N/A') . "\n";
        echo "   Path: " . ($data['download_path'] ?? 'N/A') . "\n";
    }
} else {
    echo "❌ HTTP Error {$httpCode}\n";
    if ($response) {
        $errorData = json_decode($response, true);
        if (isset($errorData['error'])) {
            echo "   Error: " . $errorData['error'] . "\n";
        }
    }
}

echo "\n🎉 API testing complete!\n";
echo "\nNext steps:\n";
echo "1. Visit {$baseUrl}/chatbot to test the UI\n";
echo "2. Try asking questions like:\n";
echo "   - 'Tell me about MIT'\n";
echo "   - 'What programs does Harvard offer?'\n";
echo "   - 'Top universities in California'\n";
echo "   - 'What's the weather?' (should be refused)\n";
echo "\n3. For AI training:\n";
echo "   - Use the generated JSONL file for GPT fine-tuning\n";
echo "   - Or implement RAG with the knowledge base API\n";
?> 