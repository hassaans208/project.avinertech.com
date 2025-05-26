<template>
  <div class="min-h-screen bg-gradient-to-br from-gray-900 via-gray-800 to-gray-900">
    <!-- Navigation -->
    <nav class="fixed w-full z-50 bg-white/5 backdrop-blur-lg border-b border-white/10">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
          <!-- Logo -->
          <div class="flex-shrink-0">
            <h1 class="text-2xl font-bold text-white">AvinerTech</h1>
          </div>
          
          <!-- Navigation Links -->
          <div class="hidden md:block">
            <div class="ml-10 flex items-center space-x-8">
              <a href="/setup/dashboard" class="text-gray-300 hover:text-white transition-colors">Setup Dashboard</a>
              <a href="#features" class="text-gray-300 hover:text-white transition-colors">Features</a>
              <a href="#solutions" class="text-gray-300 hover:text-white transition-colors">Solutions</a>
              <a href="#pricing" class="text-gray-300 hover:text-white transition-colors">Pricing</a>
              <a href="#contact" class="text-gray-300 hover:text-white transition-colors">Contact</a>
              <router-link 
                to="/login" 
                class="px-4 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700 transition-colors"
              >
                Login
              </router-link>
            </div>
          </div>
        </div>
      </div>
    </nav>

    <!-- Hero Section -->
    <section class="pt-32 pb-20 px-4 sm:px-6 lg:px-8">
      <div class="max-w-7xl mx-auto">
        <div class="text-center">
          <h1 class="text-4xl sm:text-5xl md:text-6xl font-bold text-white mb-6">
            Transform Your Business with
            <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-400 to-purple-500">
              Modern Technology
            </span>
          </h1>
          <p class="text-xl text-gray-300 mb-8 max-w-3xl mx-auto">
            Streamline your operations, enhance productivity, and drive growth with our cutting-edge business solutions.
          </p>
          <div class="flex justify-center space-x-4">
            <router-link 
              to="/register" 
              class="px-8 py-3 rounded-lg bg-gradient-to-r from-blue-600 to-purple-600 text-white hover:from-blue-700 hover:to-purple-700 transition-all transform hover:scale-105"
            >
              Get Started
            </router-link>
            <a 
              href="#demo" 
              class="px-8 py-3 rounded-lg bg-white/10 text-white hover:bg-white/20 transition-all"
            >
              Watch Demo
            </a>
          </div>
        </div>
      </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="py-20 bg-white/5">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="text-3xl font-bold text-white text-center mb-12">Powerful Features</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
          <div class="bg-white/5 backdrop-blur-lg rounded-xl p-6 border border-white/10 hover:border-blue-500/50 transition-all">
            <div class="w-12 h-12 bg-blue-500/20 rounded-lg flex items-center justify-center mb-4">
              <svg class="w-6 h-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
              </svg>
            </div>
            <h3 class="text-xl font-semibold text-white mb-2">Lightning Fast</h3>
            <p class="text-gray-300">Optimized performance for seamless user experience across all devices.</p>
          </div>
          <div class="bg-white/5 backdrop-blur-lg rounded-xl p-6 border border-white/10 hover:border-purple-500/50 transition-all">
            <div class="w-12 h-12 bg-purple-500/20 rounded-lg flex items-center justify-center mb-4">
              <svg class="w-6 h-6 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
              </svg>
            </div>
            <h3 class="text-xl font-semibold text-white mb-2">Enterprise Security</h3>
            <p class="text-gray-300">Advanced security features to protect your business data and operations.</p>
          </div>
          <div class="bg-white/5 backdrop-blur-lg rounded-xl p-6 border border-white/10 hover:border-green-500/50 transition-all">
            <div class="w-12 h-12 bg-green-500/20 rounded-lg flex items-center justify-center mb-4">
              <svg class="w-6 h-6 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
              </svg>
            </div>
            <h3 class="text-xl font-semibold text-white mb-2">Scalable Solution</h3>
            <p class="text-gray-300">Grow your business with our flexible and scalable platform.</p>
          </div>
        </div>
      </div>
    </section>

    <!-- Solutions Section -->
    <section id="solutions" class="py-20">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="text-3xl font-bold text-white text-center mb-12">Business Solutions</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
          <div class="relative overflow-hidden rounded-xl">
            <div class="absolute inset-0 bg-gradient-to-r from-blue-600/20 to-purple-600/20 backdrop-blur-lg"></div>
            <div class="relative p-8">
              <h3 class="text-2xl font-semibold text-white mb-4">Custom Development</h3>
              <p class="text-gray-300 mb-6">Tailored solutions designed to meet your specific business needs and challenges.</p>
              <ul class="space-y-3">
                <li class="flex items-center text-gray-300">
                  <svg class="w-5 h-5 text-green-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                  </svg>
                  Custom Application Development
                </li>
                <li class="flex items-center text-gray-300">
                  <svg class="w-5 h-5 text-green-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                  </svg>
                  API Integration Services
                </li>
                <li class="flex items-center text-gray-300">
                  <svg class="w-5 h-5 text-green-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                  </svg>
                  Cloud Solutions
                </li>
              </ul>
            </div>
          </div>
          <div class="relative overflow-hidden rounded-xl">
            <div class="absolute inset-0 bg-gradient-to-r from-purple-600/20 to-pink-600/20 backdrop-blur-lg"></div>
            <div class="relative p-8">
              <h3 class="text-2xl font-semibold text-white mb-4">Enterprise Solutions</h3>
              <p class="text-gray-300 mb-6">Comprehensive enterprise-grade solutions for large-scale operations.</p>
              <ul class="space-y-3">
                <li class="flex items-center text-gray-300">
                  <svg class="w-5 h-5 text-green-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                  </svg>
                  Multi-tenant Architecture
                </li>
                <li class="flex items-center text-gray-300">
                  <svg class="w-5 h-5 text-green-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                  </svg>
                  Advanced Analytics
                </li>
                <li class="flex items-center text-gray-300">
                  <svg class="w-5 h-5 text-green-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                  </svg>
                  Enterprise Security
                </li>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Contact Section -->
    <section id="contact" class="py-20 bg-white/5">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
          <h2 class="text-3xl font-bold text-white mb-4">Get in Touch</h2>
          <p class="text-gray-300 max-w-2xl mx-auto">
            Ready to transform your business? Contact us today to learn more about our solutions.
          </p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
          <div class="bg-white/5 backdrop-blur-lg rounded-xl p-8 border border-white/10">
            <h3 class="text-xl font-semibold text-white mb-4">Contact Information</h3>
            <div class="space-y-4">
              <div class="flex items-center text-gray-300">
                <svg class="w-5 h-5 text-blue-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
                support@avinertech.com
              </div>
              <div class="flex items-center text-gray-300">
                <svg class="w-5 h-5 text-blue-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                </svg>
                +1 (555) 123-4567
              </div>
            </div>
          </div>
          <div class="bg-white/5 backdrop-blur-lg rounded-xl p-8 border border-white/10">
            <h3 class="text-xl font-semibold text-white mb-4">Send us a Message</h3>
            <form class="space-y-4">
              <div>
                <input 
                  type="text" 
                  placeholder="Your Name" 
                  class="w-full px-4 py-2 rounded-lg bg-white/10 border border-white/20 text-white placeholder-gray-400 focus:outline-none focus:border-blue-500"
                >
              </div>
              <div>
                <input 
                  type="email" 
                  placeholder="Your Email" 
                  class="w-full px-4 py-2 rounded-lg bg-white/10 border border-white/20 text-white placeholder-gray-400 focus:outline-none focus:border-blue-500"
                >
              </div>
              <div>
                <textarea 
                  placeholder="Your Message" 
                  rows="4" 
                  class="w-full px-4 py-2 rounded-lg bg-white/10 border border-white/20 text-white placeholder-gray-400 focus:outline-none focus:border-blue-500"
                ></textarea>
              </div>
              <button 
                type="submit" 
                class="w-full px-6 py-3 rounded-lg bg-gradient-to-r from-blue-600 to-purple-600 text-white hover:from-blue-700 hover:to-purple-700 transition-all"
              >
                Send Message
              </button>
            </form>
          </div>
        </div>
      </div>
    </section>

    <!-- Footer -->
    <footer class="bg-white/5 border-t border-white/10 py-12">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
          <div>
            <h3 class="text-lg font-semibold text-white mb-4">AvinerTech</h3>
            <p class="text-gray-300">Transforming businesses through innovative technology solutions.</p>
          </div>
          <div>
            <h4 class="text-white font-medium mb-4">Quick Links</h4>
            <ul class="space-y-2">
              <li><a href="#features" class="text-gray-300 hover:text-white transition-colors">Features</a></li>
              <li><a href="#solutions" class="text-gray-300 hover:text-white transition-colors">Solutions</a></li>
              <li><a href="#pricing" class="text-gray-300 hover:text-white transition-colors">Pricing</a></li>
              <li><a href="#contact" class="text-gray-300 hover:text-white transition-colors">Contact</a></li>
            </ul>
          </div>
          <div>
            <h4 class="text-white font-medium mb-4">Legal</h4>
            <ul class="space-y-2">
              <li><a href="#" class="text-gray-300 hover:text-white transition-colors">Privacy Policy</a></li>
              <li><a href="#" class="text-gray-300 hover:text-white transition-colors">Terms of Service</a></li>
              <li><a href="#" class="text-gray-300 hover:text-white transition-colors">Cookie Policy</a></li>
            </ul>
          </div>
          <div>
            <h4 class="text-white font-medium mb-4">Connect</h4>
            <div class="flex space-x-4">
              <a href="#" class="text-gray-300 hover:text-white transition-colors">
                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                </svg>
              </a>
              <a href="#" class="text-gray-300 hover:text-white transition-colors">
                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/>
                </svg>
              </a>
              <a href="#" class="text-gray-300 hover:text-white transition-colors">
                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/>
                </svg>
              </a>
            </div>
          </div>
        </div>
        <div class="mt-8 pt-8 border-t border-white/10 text-center text-gray-300">
          <p>&copy; 2024 AvinerTech. All rights reserved.</p>
        </div>
      </div>
    </footer>
  </div>
</template>

<script setup>
// Component logic can be added here if needed
</script>

<style scoped>
/* Add any component-specific styles here */
</style> 