# University Data Chatbot Setup Guide

This guide explains how to create an AI-based chatbot trained exclusively on your university data to ensure it only discusses university-related topics.

## Overview

Your Laravel application now includes a complete chatbot training system that:
- ✅ Extracts data from QS Rankings and University Programs CSV files
- ✅ Generates training datasets in multiple formats (JSONL, CSV, TXT)
- ✅ Includes boundary training to refuse non-university questions
- ✅ Provides structured APIs for real-time data access
- ✅ Creates knowledge base entries for AI training

## Available Data Sources

### 1. QS Rankings Data (`qs-rankings.csv`)
- University rankings and scores
- Academic reputation metrics
- Location and demographic information
- International fees and scholarship data

### 2. Programs Database (`programs-database.csv`) 
- University program offerings
- Program categories and levels
- Academic program details

## API Endpoints

### Core Chatbot Data APIs

```bash
# Get structured university data for training
GET /api/chatbot/university-data

# Search universities (for real-time queries)
GET /api/chatbot/search-universities?query=MIT&limit=5

# Get programs for a specific university
GET /api/chatbot/university-programs?university=Harvard

# Get formatted knowledge base content
GET /api/chatbot/knowledge-base
```

### Training Data Generation APIs

```bash
# Generate training dataset (JSONL format for GPT fine-tuning)
GET /api/chatbot/generate-training-data?format=jsonl

# Generate CSV format for traditional ML models
GET /api/chatbot/generate-training-data?format=csv

# Generate human-readable text format
GET /api/chatbot/generate-training-data?format=txt

# Get training statistics and recommendations
GET /api/chatbot/training-stats
```

## Setting Up Your Domain-Specific Chatbot

### Step 1: Generate Training Data

Generate training data in the format you need:

```bash
# For OpenAI GPT fine-tuning (recommended)
curl "http://your-domain.com/api/chatbot/generate-training-data?format=jsonl"

# For traditional ML models
curl "http://your-domain.com/api/chatbot/generate-training-data?format=csv"
```

This creates a file in `storage/app/chatbot-training/` with:
- University information examples
- Program query examples
- Comparison and ranking examples
- **Boundary examples** (refuses non-university questions)

### Step 2: Training Methods

#### Option A: OpenAI Fine-Tuning (Recommended)

1. **Upload Training Data:**
```bash
# Upload your JSONL file to OpenAI
openai api fine_tunes.create \
  -t university_chatbot_training_YYYY-MM-DD_HH-mm-ss.jsonl \
  -m gpt-3.5-turbo \
  --suffix "university-assistant"
```

2. **Use the Fine-tuned Model:**
```python
import openai

response = openai.ChatCompletion.create(
    model="ft:gpt-3.5-turbo:your-org:university-assistant:abc123",
    messages=[
        {"role": "user", "content": "Tell me about MIT's computer science programs"}
    ]
)
```

#### Option B: Local Model Training

1. **Use the CSV data with frameworks like:**
   - Hugging Face Transformers
   - LangChain for RAG (Retrieval-Augmented Generation)
   - Custom neural networks

2. **RAG Implementation Example:**
```python
# Pseudo-code for RAG setup
from langchain import VectorStore, OpenAI
from langchain.embeddings import OpenAIEmbeddings

# Load your university knowledge base
knowledge_base = load_csv_data("university_chatbot_training.csv")

# Create vector store for semantic search
vectorstore = VectorStore.from_texts(
    texts=knowledge_base['output'],
    embeddings=OpenAIEmbeddings()
)

# Query with context
def query_chatbot(question):
    relevant_docs = vectorstore.similarity_search(question, k=3)
    context = "\n".join([doc.page_content for doc in relevant_docs])
    
    prompt = f"""
    You are a university information assistant. Only answer questions about universities, their programs, rankings, and academic information.
    
    Context: {context}
    
    Question: {question}
    
    If the question is not about universities or education, politely redirect to university topics.
    """
    
    return openai.ChatCompletion.create(
        model="gpt-3.5-turbo",
        messages=[{"role": "user", "content": prompt}]
    )
```

### Step 3: Ensuring Domain Restriction

The training data includes **boundary examples** that teach the AI to refuse non-university questions:

```json
{
  "messages": [
    {"role": "user", "content": "What's the weather like today?"},
    {"role": "assistant", "content": "I'm a university information assistant. I can only help you with questions about universities, their rankings, programs, and related academic information. Please ask me about universities or educational programs."}
  ]
}
```

### Step 4: Real-Time Integration

For real-time university data access, use the search APIs:

```javascript
// Frontend integration example
async function searchUniversities(query) {
    const response = await fetch(`/api/chatbot/search-universities?query=${encodeURIComponent(query)}&limit=5`);
    const data = await response.json();
    return data.results;
}

async function getUniversityPrograms(universityName) {
    const response = await fetch(`/api/chatbot/university-programs?university=${encodeURIComponent(universityName)}`);
    const data = await response.json();
    return data.programs;
}
```

## Training Data Structure

### Example Training Entry (JSONL format):
```json
{
  "messages": [
    {"role": "user", "content": "Tell me about Stanford University"},
    {"role": "assistant", "content": "Stanford University is located in Stanford, California. It is ranked #3 in the QS World University Rankings with an overall score of 98.4. Key scores include Academic Reputation: 100, Employer Reputation: 100."}
  ],
  "metadata": {
    "category": "university_info"
  }
}
```

### Training Categories:
- `university_info` - Basic university information
- `university_ranking` - Ranking and scoring data
- `university_location` - Geographic information
- `academic_reputation` - Academic metrics
- `university_programs` - Program offerings
- `category_programs` - Programs by category
- `country_universities` - Country-based comparisons
- `boundary_*` - Non-university topic refusals

## Monitoring and Updates

### Check Training Statistics:
```bash
curl "http://your-domain.com/api/chatbot/training-stats"
```

### Regular Data Updates:
1. Update your CSV files with new university data
2. Regenerate training data
3. Retrain or fine-tune your model
4. Deploy updated model

## Best Practices

### 1. Domain Restriction
- Always include boundary examples in training
- Test with non-university questions to ensure refusal
- Monitor conversations for topic drift

### 2. Data Quality
- Regularly update university rankings and program data
- Validate CSV data for accuracy
- Remove or flag outdated information

### 3. Model Performance
- Start with GPT-3.5-turbo fine-tuning for best results
- Use temperature=0.3 for consistent, factual responses
- Implement fallback to knowledge base search for unknown queries

### 4. Production Deployment
```env
# Add these to your .env file
OPENAI_API_KEY=your_openai_key
CHATBOT_MODEL=ft:gpt-3.5-turbo:org:university-assistant:abc123
CHATBOT_TEMPERATURE=0.3
CHATBOT_MAX_TOKENS=500
```

## Example Chatbot Implementation

```php
<?php
// app/Services/UniversityChatbotService.php

class UniversityChatbotService
{
    public function askQuestion(string $question): array
    {
        // Check if question seems university-related
        if (!$this->isUniversityRelated($question)) {
            return [
                'response' => "I'm a university information assistant. I can only help you with questions about universities, their rankings, programs, and related academic information. Please ask me about universities or educational programs.",
                'confidence' => 1.0,
                'source' => 'boundary_detection'
            ];
        }

        // Use fine-tuned model or RAG
        $response = $this->queryFineTunedModel($question);
        
        return [
            'response' => $response,
            'confidence' => 0.9,
            'source' => 'ai_model'
        ];
    }

    private function isUniversityRelated(string $question): bool
    {
        $universityKeywords = [
            'university', 'college', 'program', 'degree', 'ranking', 
            'admission', 'academic', 'education', 'student', 'faculty'
        ];
        
        $questionLower = strtolower($question);
        foreach ($universityKeywords as $keyword) {
            if (strpos($questionLower, $keyword) !== false) {
                return true;
            }
        }
        
        return false;
    }
}
```

## Testing Your Chatbot

Test with these example queries:

### ✅ Should Answer (University-related):
- "Tell me about MIT"
- "What programs does Harvard offer?"
- "Top universities in California"
- "Stanford's ranking"
- "Computer science programs at CMU"

### ❌ Should Refuse (Non-university):
- "What's the weather?"
- "How to cook pasta?"
- "Latest news"
- "Tell me a joke"
- "Help with personal problems"

## Troubleshooting

### Common Issues:

1. **Empty training data:**
   - Ensure CSV files exist in `storage/app/csv/`
   - Check file permissions
   - Verify CSV format and headers

2. **Model not refusing non-university questions:**
   - Increase boundary examples in training
   - Lower model temperature
   - Add pre-processing filter

3. **Inaccurate responses:**
   - Update source CSV data
   - Regenerate training data
   - Retrain model with fresh data

## Security Considerations

- Use rate limiting on API endpoints
- Implement authentication for training endpoints
- Monitor usage for abuse
- Sanitize user inputs
- Log conversations for quality monitoring

This setup ensures your chatbot will only discuss university-related topics while providing accurate, up-to-date information from your curated dataset. 