<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>University Assistant Chatbot</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');
        
        body {
            font-family: 'Inter', sans-serif;
        }
        
        .chat-container {
            height: calc(100vh - 120px);
        }
        
        .messages-container {
            height: calc(100% - 80px);
        }
        
        .message-bubble {
            animation: slideIn 0.3s ease-out;
        }
        
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .typing-indicator {
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }
        
        .quick-question {
            transition: all 0.2s ease;
        }
        
        .quick-question:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }
        
        .university-card {
            transition: all 0.2s ease;
            border-left: 4px solid #3b82f6;
        }
        
        .university-card:hover {
            background-color: #f8fafc;
            border-left-color: #1d4ed8;
        }
        
        .scrollbar-hide {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
        
        .scrollbar-hide::-webkit-scrollbar {
            display: none;
        }
    </style>
</head>
<body class="bg-gray-50" x-data="chatbot()">
    <!-- Header -->
    <header class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-4">
                <div class="flex items-center space-x-3">
                    <div class="bg-blue-600 p-2 rounded-lg">
                        <i class="fas fa-graduation-cap text-white text-xl"></i>
                    </div>
                    <div>
                        <h1 class="text-xl font-bold text-gray-900">University Assistant</h1>
                        <p class="text-sm text-gray-500">Ask me anything about universities and programs</p>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <div class="flex items-center space-x-2 text-sm text-gray-600">
                        <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
                        <span>Online</span>
                    </div>
                    <a href="{{ route('home') }}" class="text-gray-600 hover:text-gray-900">
                        <i class="fas fa-home text-lg"></i>
                    </a>
                </div>
            </div>
        </div>
    </header>

    <div class="max-w-4xl mx-auto p-4">
        <!-- Chat Container -->
        <div class="bg-white rounded-xl shadow-lg chat-container">
            <!-- Messages Container -->
            <div class="messages-container overflow-y-auto p-4 scrollbar-hide" x-ref="messagesContainer">
                <!-- Welcome Message -->
                <div x-show="messages.length === 0" class="text-center py-8">
                    <div class="inline-flex items-center justify-center w-16 h-16 bg-blue-100 rounded-full mb-4">
                        <i class="fas fa-robot text-blue-600 text-2xl"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Welcome to University Assistant!</h3>
                    <p class="text-gray-600 mb-6 max-w-md mx-auto">
                        I'm here to help you find information about universities, their rankings, programs, and more. 
                        Ask me anything about higher education!
                    </p>
                    
                    <!-- Quick Questions -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 max-w-2xl mx-auto">
                        <template x-for="question in quickQuestions" :key="question">
                            <button 
                                @click="sendQuickQuestion(question)"
                                class="quick-question p-3 bg-gray-50 hover:bg-blue-50 border border-gray-200 rounded-lg text-left text-sm font-medium text-gray-700 hover:text-blue-700"
                            >
                                <i class="fas fa-chevron-right text-xs mr-2"></i>
                                <span x-text="question"></span>
                            </button>
                        </template>
                    </div>
                </div>

                <!-- Messages -->
                <template x-for="(message, index) in messages" :key="index">
                    <div class="message-bubble mb-4" :class="message.type === 'user' ? 'text-right' : 'text-left'">
                        <div class="flex items-start space-x-2" :class="message.type === 'user' ? 'flex-row-reverse space-x-reverse' : ''">
                            <!-- Avatar -->
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 rounded-full flex items-center justify-center" 
                                     :class="message.type === 'user' ? 'bg-blue-600' : 'bg-gray-300'">
                                    <i :class="message.type === 'user' ? 'fas fa-user text-white text-xs' : 'fas fa-robot text-gray-700 text-xs'"></i>
                                </div>
                            </div>
                            
                            <!-- Message Content -->
                            <div class="max-w-xs lg:max-w-md">
                                <div class="px-4 py-2 rounded-lg" 
                                     :class="message.type === 'user' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-900'">
                                    <p x-html="message.content"></p>
                                </div>
                                
                                <!-- University Results -->
                                <div x-show="message.universities && message.universities.length > 0" class="mt-3">
                                    <template x-for="university in message.universities" :key="university.name">
                                        <div class="university-card bg-white border border-gray-200 rounded-lg p-3 mb-2 text-left">
                                            <div class="flex items-start justify-between">
                                                <div class="flex-1">
                                                    <h4 class="font-semibold text-gray-900" x-text="university.name"></h4>
                                                    <p class="text-sm text-gray-600" x-text="university.location"></p>
                                                    <div class="flex items-center space-x-4 mt-2 text-xs text-gray-500">
                                                        <span x-show="university.rank !== 'N/A'">
                                                            <i class="fas fa-trophy mr-1"></i>
                                                            Rank: #<span x-text="university.rank"></span>
                                                        </span>
                                                        <span x-show="university.score !== 'N/A'">
                                                            <i class="fas fa-star mr-1"></i>
                                                            Score: <span x-text="university.score"></span>
                                                        </span>
                                                    </div>
                                                </div>
                                                <button 
                                                    @click="getUniversityPrograms(university.name)"
                                                    class="text-blue-600 hover:text-blue-800 text-sm font-medium ml-3"
                                                >
                                                    View Programs
                                                </button>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                                
                                <!-- Programs Results -->
                                <div x-show="message.programs && message.programs.length > 0" class="mt-3">
                                    <div class="bg-white border border-gray-200 rounded-lg p-3 text-left">
                                        <h4 class="font-semibold text-gray-900 mb-2">
                                            <i class="fas fa-graduation-cap mr-2"></i>
                                            Available Programs
                                        </h4>
                                        <div class="grid grid-cols-1 gap-2 max-h-32 overflow-y-auto">
                                            <template x-for="program in message.programs.slice(0, 10)" :key="program.name">
                                                <div class="flex items-center justify-between text-sm">
                                                    <span class="font-medium text-gray-800" x-text="program.name"></span>
                                                    <span class="text-xs bg-gray-100 px-2 py-1 rounded" x-text="program.category"></span>
                                                </div>
                                            </template>
                                        </div>
                                        <div x-show="message.programs.length > 10" class="mt-2 text-xs text-gray-500">
                                            And <span x-text="message.programs.length - 10"></span> more programs...
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="text-xs text-gray-500 mt-1" x-text="message.timestamp"></div>
                            </div>
                        </div>
                    </div>
                </template>

                <!-- Typing Indicator -->
                <div x-show="isTyping" class="message-bubble mb-4 text-left">
                    <div class="flex items-start space-x-2">
                        <div class="w-8 h-8 bg-gray-300 rounded-full flex items-center justify-center">
                            <i class="fas fa-robot text-gray-700 text-xs"></i>
                        </div>
                        <div class="bg-gray-100 rounded-lg px-4 py-2">
                            <div class="typing-indicator flex space-x-1">
                                <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce"></div>
                                <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0.1s"></div>
                                <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0.2s"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Input Container -->
            <div class="border-t border-gray-200 p-4">
                <form @submit.prevent="sendMessage()" class="flex space-x-3">
                    <div class="flex-1 relative">
                        <input 
                            type="text" 
                            x-model="currentMessage"
                            :disabled="isTyping"
                            placeholder="Ask me about universities, programs, rankings..."
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent disabled:bg-gray-100 disabled:cursor-not-allowed"
                            maxlength="500"
                        >
                        <div class="absolute right-3 top-2 text-xs text-gray-400" x-text="currentMessage.length + '/500'"></div>
                    </div>
                    <button 
                        type="submit"
                        :disabled="!currentMessage.trim() || isTyping"
                        class="bg-blue-600 hover:bg-blue-700 disabled:bg-gray-400 text-white px-6 py-2 rounded-lg font-medium transition-colors disabled:cursor-not-allowed flex items-center space-x-2"
                    >
                        <span x-show="!isTyping">Send</span>
                        <span x-show="isTyping">Sending...</span>
                        <i class="fas fa-paper-plane text-sm"></i>
                    </button>
                </form>
                
                <!-- Input Hints -->
                <div class="mt-2 text-xs text-gray-500 flex items-center justify-between">
                    <span>Try asking about specific universities, programs, or rankings</span>
                    <span>Press Enter to send</span>
                </div>
            </div>
        </div>
        
        <!-- Stats Footer -->
        <div class="mt-4 text-center text-sm text-gray-500">
            <span>Powered by University Data API</span>
            <span class="mx-2">•</span>
            <span x-text="'Connected to ' + (stats.universities_count || 0) + ' universities'"></span>
        </div>
    </div>

    <script>
        function chatbot() {
            return {
                messages: [],
                currentMessage: '',
                isTyping: false,
                stats: {},
                quickQuestions: [
                    "What are the top 10 universities in the world?",
                    "Tell me about MIT",
                    "What programs does Harvard offer?",
                    "Best universities in California",
                    "Universities with computer science programs",
                    "What are the QS ranking criteria?"
                ],

                init() {
                    this.loadStats();
                    this.addWelcomeMessage();
                },

                addWelcomeMessage() {
                    setTimeout(() => {
                        this.addBotMessage(
                            "Hello! I'm your university assistant. I can help you find information about universities, their rankings, programs, and more. What would you like to know?"
                        );
                    }, 1000);
                },

                async loadStats() {
                    try {
                        const response = await fetch('/api/chatbot/training-stats');
                        const data = await response.json();
                        this.stats = data.stats || {};
                    } catch (error) {
                        console.error('Failed to load stats:', error);
                    }
                },

                sendQuickQuestion(question) {
                    this.currentMessage = question;
                    this.sendMessage();
                },

                async sendMessage() {
                    const message = this.currentMessage.trim();
                    if (!message || this.isTyping) return;

                    // Add user message
                    this.addUserMessage(message);
                    this.currentMessage = '';
                    this.isTyping = true;

                    try {
                        // Check if this is a university search query
                        if (this.isUniversitySearchQuery(message)) {
                            await this.handleUniversitySearch(message);
                        } else {
                            // Handle as general university question
                            await this.handleGeneralQuestion(message);
                        }
                    } catch (error) {
                        console.error('Error processing message:', error);
                        this.addBotMessage(
                            "I apologize, but I'm having trouble processing your request right now. Please try again in a moment."
                        );
                    } finally {
                        this.isTyping = false;
                        this.scrollToBottom();
                    }
                },

                isUniversitySearchQuery(message) {
                    const searchKeywords = [
                        'find', 'search', 'list', 'show me', 'tell me about',
                        'universities in', 'colleges in', 'top universities',
                        'best universities', 'university rankings'
                    ];
                    
                    const lowerMessage = message.toLowerCase();
                    return searchKeywords.some(keyword => lowerMessage.includes(keyword));
                },

                async handleUniversitySearch(message) {
                    try {
                        // Extract search terms
                        const searchTerm = this.extractSearchTerm(message);
                        
                        const response = await fetch(`/api/chatbot/search-universities?query=${encodeURIComponent(searchTerm)}&limit=5`);
                        const data = await response.json();

                        if (data.results && data.results.length > 0) {
                            this.addBotMessage(
                                `I found ${data.total_found} universities matching "${searchTerm}". Here are the top results:`,
                                data.results
                            );
                        } else {
                            this.addBotMessage(
                                `I couldn't find any universities matching "${searchTerm}". Try searching for a different university name, location, or program.`
                            );
                        }
                    } catch (error) {
                        throw error;
                    }
                },

                async handleGeneralQuestion(message) {
                    // Check if question is university-related
                    if (!this.isUniversityRelated(message)) {
                        this.addBotMessage(
                            "I'm a university information assistant and can only help with questions about universities, their rankings, programs, and related academic information. Please ask me about universities or educational programs."
                        );
                        return;
                    }

                    // For demo purposes, provide contextual responses based on keywords
                    const response = this.generateContextualResponse(message);
                    this.addBotMessage(response);
                },

                isUniversityRelated(message) {
                    const universityKeywords = [
                        'university', 'universities', 'college', 'colleges', 'program', 'programs',
                        'degree', 'degrees', 'ranking', 'rankings', 'admission', 'admissions',
                        'academic', 'education', 'student', 'students', 'faculty', 'campus',
                        'tuition', 'fees', 'scholarship', 'scholarships', 'mit', 'harvard',
                        'stanford', 'princeton', 'yale', 'qs', 'undergraduate', 'graduate',
                        'masters', 'phd', 'doctorate', 'bachelor', 'study', 'studies'
                    ];
                    
                    const lowerMessage = message.toLowerCase();
                    return universityKeywords.some(keyword => lowerMessage.includes(keyword));
                },

                generateContextualResponse(message) {
                    const lowerMessage = message.toLowerCase();
                    
                    if (lowerMessage.includes('ranking') || lowerMessage.includes('rank')) {
                        return "University rankings are determined by various factors including academic reputation, employer reputation, faculty-to-student ratio, citations per faculty, and international diversity. The QS World University Rankings is one of the most widely recognized ranking systems. Would you like me to search for specific university rankings?";
                    }
                    
                    if (lowerMessage.includes('program') || lowerMessage.includes('course')) {
                        return "Universities offer a wide variety of programs across different fields of study. I can help you find specific programs at universities. Try asking me something like 'What programs does [University Name] offer?' or 'Find universities with computer science programs'.";
                    }
                    
                    if (lowerMessage.includes('admission') || lowerMessage.includes('apply')) {
                        return "Admission requirements vary by university and program. Generally, they consider academic performance, standardized test scores, essays, and extracurricular activities. I can provide information about specific universities if you'd like to search for them.";
                    }
                    
                    if (lowerMessage.includes('fee') || lowerMessage.includes('tuition') || lowerMessage.includes('cost')) {
                        return "University fees vary significantly by institution, program, and whether you're a domestic or international student. I have information about international fees for many universities. Which specific university would you like to know about?";
                    }
                    
                    return "I'd be happy to help you with university information! You can ask me about specific universities, compare programs, check rankings, or find universities in particular locations. What specific information are you looking for?";
                },

                extractSearchTerm(message) {
                    // Simple extraction - remove common words and get the core search term
                    const commonWords = ['find', 'search', 'list', 'show', 'me', 'about', 'tell', 'universities', 'colleges', 'in', 'the', 'top', 'best', 'good'];
                    const words = message.toLowerCase().split(' ').filter(word => 
                        word.length > 2 && !commonWords.includes(word)
                    );
                    return words.join(' ') || message;
                },

                async getUniversityPrograms(universityName) {
                    this.isTyping = true;
                    
                    try {
                        const response = await fetch(`/api/chatbot/university-programs?university=${encodeURIComponent(universityName)}`);
                        const data = await response.json();

                        if (data.programs && data.programs.length > 0) {
                            this.addBotMessage(
                                `Here are the programs offered by ${universityName}:`,
                                null,
                                data.programs
                            );
                        } else {
                            this.addBotMessage(
                                `I couldn't find specific program information for ${universityName}. This might be because the university name doesn't match exactly with our database.`
                            );
                        }
                    } catch (error) {
                        console.error('Error fetching programs:', error);
                        this.addBotMessage(
                            "I encountered an error while fetching program information. Please try again."
                        );
                    } finally {
                        this.isTyping = false;
                        this.scrollToBottom();
                    }
                },

                addUserMessage(content) {
                    this.messages.push({
                        type: 'user',
                        content: content,
                        timestamp: this.formatTime(new Date())
                    });
                    this.scrollToBottom();
                },

                addBotMessage(content, universities = null, programs = null) {
                    this.messages.push({
                        type: 'bot',
                        content: content,
                        universities: universities,
                        programs: programs,
                        timestamp: this.formatTime(new Date())
                    });
                    this.scrollToBottom();
                },

                formatTime(date) {
                    return date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
                },

                scrollToBottom() {
                    this.$nextTick(() => {
                        const container = this.$refs.messagesContainer;
                        container.scrollTop = container.scrollHeight;
                    });
                }
            };
        }

        // Setup CSRF token for all AJAX requests
        document.addEventListener('DOMContentLoaded', function() {
            const token = document.querySelector('meta[name="csrf-token"]');
            if (token) {
                window.axios = window.axios || {};
                window.axios.defaults = window.axios.defaults || {};
                window.axios.defaults.headers = window.axios.defaults.headers || {};
                window.axios.defaults.headers.common = window.axios.defaults.headers.common || {};
                window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.getAttribute('content');
            }
        });
    </script>
</body>
</html> 