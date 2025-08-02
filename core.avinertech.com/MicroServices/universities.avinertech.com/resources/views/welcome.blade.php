<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Avinertech - University Data Intelligence Platform</title>
    <meta name="description" content="Explore comprehensive university rankings, programs, and educational data with Avinertech's advanced analytics platform.">
    <meta name="keywords" content="university rankings, QS rankings, university programs, education data, academic analytics">
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:300,400,500,600,700,800&display=swap" rel="stylesheet" />
    
    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Additional Styles -->
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap');
        
        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        .gradient-text {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .glass-effect {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .floating-animation {
            animation: floating 6s ease-in-out infinite;
        }
        
        @keyframes floating {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }
        
        .fade-in {
            opacity: 0;
            transform: translateY(30px);
            transition: all 0.8s ease-out;
        }
        
        .fade-in.visible {
            opacity: 1;
            transform: translateY(0);
        }
        
        .card-hover {
            transition: all 0.3s ease;
        }
        
        .card-hover:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        }
        
        .pulse-bg {
            animation: pulse-bg 2s ease-in-out infinite alternate;
        }
        
        @keyframes pulse-bg {
            0% { background-position: 0% 50%; }
            100% { background-position: 100% 50%; }
        }
    </style>
</head>
<body class="font-sans antialiased">
    <!-- Navigation -->
    <nav class="fixed top-0 left-0 right-0 z-50 bg-white/90 backdrop-blur-md border-b border-gray-200/50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <span class="text-2xl font-bold gradient-text">Avinertech</span>
                    </div>
                    <div class="hidden md:block ml-10">
                        <div class="flex items-baseline space-x-8">
                            <a href="#home" class="text-gray-700 hover:text-blue-600 transition-colors">Home</a>
                            <a href="#features" class="text-gray-700 hover:text-blue-600 transition-colors">Features</a>
                            <a href="#analytics" class="text-gray-700 hover:text-blue-600 transition-colors">Analytics</a>
                            <a href="#contact" class="text-gray-700 hover:text-blue-600 transition-colors">Contact</a>
                        </div>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('qs-rankings') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                        Explore Data
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section id="home" class="min-h-screen flex items-center gradient-bg relative overflow-hidden">
        <div class="absolute inset-0 bg-black/10"></div>
        <div class="absolute top-10 left-10 w-72 h-72 bg-white/10 rounded-full blur-3xl"></div>
        <div class="absolute bottom-10 right-10 w-96 h-96 bg-purple-500/20 rounded-full blur-3xl"></div>
        
        <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-32">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                <div class="text-white">
                    <h1 class="text-5xl lg:text-7xl font-bold mb-6 leading-tight">
                        University Data
                        <span class="block text-yellow-300">Intelligence</span>
                    </h1>
                    <p class="text-xl mb-8 opacity-90 leading-relaxed">
                        Explore comprehensive university rankings, programs, and educational insights with our advanced analytics platform powered by real-time data.
                    </p>
                    <div class="flex flex-col sm:flex-row gap-4">
                        <a href="{{ route('qs-rankings') }}" class="bg-white text-blue-600 px-8 py-4 rounded-lg font-semibold hover:bg-gray-100 transition-all transform hover:scale-105 text-center">
                            Explore QS Rankings
                        </a>
                        <a href="{{ route('programs-database') }}" class="border-2 border-white text-white px-8 py-4 rounded-lg font-semibold hover:bg-white hover:text-blue-600 transition-all text-center">
                            Browse Programs
                        </a>
                    </div>
                </div>
                
                <div class="relative">
                    <div class="floating-animation">
                        <div class="glass-effect rounded-2xl p-8 max-w-md mx-auto">
                            <div class="text-white">
                                <h3 class="text-xl font-semibold mb-4">Live Statistics</h3>
                                <div class="space-y-3">
                                    <div class="flex justify-between">
                                        <span>Universities Tracked</span>
                                        <span class="font-bold text-yellow-300">1,400+</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span>Programs Listed</span>
                                        <span class="font-bold text-yellow-300">25,000+</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span>Countries Covered</span>
                                        <span class="font-bold text-yellow-300">80+</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span>Data Points</span>
                                        <span class="font-bold text-yellow-300">1M+</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="py-24 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16 fade-in">
                <h2 class="text-4xl font-bold text-gray-900 mb-4">Powerful Features</h2>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                    Our platform provides comprehensive tools and insights to help you make informed decisions about higher education
                </p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Feature 1 -->
                <div class="bg-white p-8 rounded-2xl shadow-lg card-hover fade-in">
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mb-6">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold mb-4">Real-time Rankings</h3>
                    <p class="text-gray-600">
                        Access up-to-date QS World University Rankings with comprehensive scoring metrics and detailed analytics.
                    </p>
                </div>

                <!-- Feature 2 -->
                <div class="bg-white p-8 rounded-2xl shadow-lg card-hover fade-in">
                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center mb-6">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold mb-4">Program Database</h3>
                    <p class="text-gray-600">
                        Explore thousands of academic programs across universities worldwide with detailed categorization and levels.
                    </p>
                </div>

                <!-- Feature 3 -->
                <div class="bg-white p-8 rounded-2xl shadow-lg card-hover fade-in">
                    <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center mb-6">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold mb-4">Advanced Search</h3>
                    <p class="text-gray-600">
                        Find exactly what you're looking for with powerful filtering and search capabilities across all data points.
                    </p>
                </div>

                <!-- Feature 4 -->
                <div class="bg-white p-8 rounded-2xl shadow-lg card-hover fade-in">
                    <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center mb-6">
                        <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold mb-4">Data Analytics</h3>
                    <p class="text-gray-600">
                        Gain insights with comprehensive analytics, trends, and comparisons across universities and programs.
                    </p>
                </div>

                <!-- Feature 5 -->
                <div class="bg-white p-8 rounded-2xl shadow-lg card-hover fade-in">
                    <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center mb-6">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold mb-4">Accessibility First</h3>
                    <p class="text-gray-600">
                        Built with accessibility in mind, ensuring everyone can access and navigate our educational data platform.
                    </p>
                </div>

                <!-- Feature 6 -->
                <div class="bg-white p-8 rounded-2xl shadow-lg card-hover fade-in">
                    <div class="w-12 h-12 bg-indigo-100 rounded-lg flex items-center justify-center mb-6">
                        <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold mb-4">Dynamic CSV Reader</h3>
                    <p class="text-gray-600">
                        Powerful CSV processing engine that automatically adapts to any data structure for seamless data import.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Analytics Section -->
    <section id="analytics" class="py-24 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                <div class="fade-in">
                    <h2 class="text-4xl font-bold text-gray-900 mb-6">
                        Comprehensive University Analytics
                    </h2>
                    <p class="text-xl text-gray-600 mb-8">
                        Make data-driven decisions with our comprehensive analytics dashboard featuring real-time insights, trend analysis, and comparative metrics.
                    </p>
                    
                    <div class="space-y-6">
                        <div class="flex items-start space-x-4">
                            <div class="w-6 h-6 bg-blue-600 rounded-full flex-shrink-0 mt-1"></div>
                            <div>
                                <h4 class="font-semibold text-gray-900">Multi-dimensional Analysis</h4>
                                <p class="text-gray-600">Compare universities across multiple metrics including academic reputation, faculty ratios, and international diversity.</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start space-x-4">
                            <div class="w-6 h-6 bg-green-600 rounded-full flex-shrink-0 mt-1"></div>
                            <div>
                                <h4 class="font-semibold text-gray-900">Interactive Data Tables</h4>
                                <p class="text-gray-600">Sort, filter, and search through thousands of data points with our responsive, accessible data tables.</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start space-x-4">
                            <div class="w-6 h-6 bg-purple-600 rounded-full flex-shrink-0 mt-1"></div>
                            <div>
                                <h4 class="font-semibold text-gray-900">Real-time Updates</h4>
                                <p class="text-gray-600">Stay current with the latest rankings and program information updated in real-time.</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="relative fade-in">
                    <div class="bg-gradient-to-br from-blue-50 to-purple-50 rounded-2xl p-8">
                        <div class="grid grid-cols-2 gap-6">
                            <div class="bg-white rounded-xl p-6 shadow-lg">
                                <div class="text-3xl font-bold text-blue-600 mb-2">1,400+</div>
                                <div class="text-gray-600">Universities</div>
                            </div>
                            <div class="bg-white rounded-xl p-6 shadow-lg">
                                <div class="text-3xl font-bold text-green-600 mb-2">25,000+</div>
                                <div class="text-gray-600">Programs</div>
                            </div>
                            <div class="bg-white rounded-xl p-6 shadow-lg">
                                <div class="text-3xl font-bold text-purple-600 mb-2">80+</div>
                                <div class="text-gray-600">Countries</div>
                            </div>
                            <div class="bg-white rounded-xl p-6 shadow-lg">
                                <div class="text-3xl font-bold text-yellow-600 mb-2">1M+</div>
                                <div class="text-gray-600">Data Points</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-24 gradient-bg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <div class="max-w-3xl mx-auto fade-in">
                <h2 class="text-4xl font-bold text-white mb-6">
                    Ready to Explore University Data?
                </h2>
                <p class="text-xl text-white/90 mb-8">
                    Start exploring comprehensive university rankings and programs with our powerful analytics platform.
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="{{ route('qs-rankings') }}" class="bg-white text-blue-600 px-8 py-4 rounded-lg font-semibold hover:bg-gray-100 transition-all transform hover:scale-105">
                        Explore QS Rankings
                    </a>
                    <a href="{{ route('programs-database') }}" class="border-2 border-white text-white px-8 py-4 rounded-lg font-semibold hover:bg-white hover:text-blue-600 transition-all">
                        Browse Programs Database
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer id="contact" class="bg-gray-900 text-white py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div>
                    <h3 class="text-2xl font-bold gradient-text mb-4">Avinertech</h3>
                    <p class="text-gray-400 mb-6">
                        Leading the future of educational data analytics with comprehensive university insights and program intelligence.
                    </p>
                    <div class="flex space-x-4">
                        <a href="#" class="text-gray-400 hover:text-white transition-colors">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z"/>
                            </svg>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-white transition-colors">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/>
                            </svg>
                        </a>
                    </div>
                </div>
                
                <div>
                    <h4 class="text-lg font-semibold mb-4">Quick Links</h4>
                    <ul class="space-y-2">
                        <li><a href="{{ route('qs-rankings') }}" class="text-gray-400 hover:text-white transition-colors">QS Rankings</a></li>
                        <li><a href="{{ route('programs-database') }}" class="text-gray-400 hover:text-white transition-colors">Programs Database</a></li>
                        <li><a href="{{ route('api.csv-files') }}" class="text-gray-400 hover:text-white transition-colors">API</a></li>
                    </ul>
                </div>
                
                <div>
                    <h4 class="text-lg font-semibold mb-4">Technology</h4>
                    <ul class="space-y-2">
                        <li><span class="text-gray-400">Laravel Framework</span></li>
                        <li><span class="text-gray-400">Dynamic CSV Processing</span></li>
                        <li><span class="text-gray-400">Real-time Analytics</span></li>
                        <li><span class="text-gray-400">Accessible Design</span></li>
                    </ul>
                </div>
            </div>
            
            <div class="border-t border-gray-800 pt-8 mt-8 text-center">
                <p class="text-gray-400">
                    © {{ date('Y') }} Avinertech. All rights reserved. Built with ❤️ for educational excellence.
                </p>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script>
        // Fade in animation on scroll
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('visible');
                }
            });
        }, observerOptions);

        document.querySelectorAll('.fade-in').forEach(el => {
            observer.observe(el);
        });

        // Smooth scrolling for navigation links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Mobile menu toggle
        const mobileMenuButton = document.querySelector('[data-mobile-menu]');
        const mobileMenu = document.querySelector('[data-mobile-menu-content]');
        
        if (mobileMenuButton && mobileMenu) {
            mobileMenuButton.addEventListener('click', () => {
                mobileMenu.classList.toggle('hidden');
            });
        }
    </script>
</body>
</html>
