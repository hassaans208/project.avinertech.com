<template>
  <div class="min-h-screen relative  bg-gradient-to-br from-indigo-900 via-purple-800 to-pink-700">
    <!-- Abstract Background Shapes -->
    <div class="absolute inset-0 overflow-hidden">
      <div class="absolute -right-24 -top-24 w-96 h-96 rounded-full bg-gradient-to-r from-pink-500 to-purple-500 opacity-20 blur-3xl"></div>
      <div class="absolute -left-24 top-1/3 w-80 h-80 rounded-full bg-gradient-to-r from-blue-500 to-teal-500 opacity-20 blur-3xl"></div>
      <div class="absolute right-1/4 bottom-0 w-64 h-64 rounded-full bg-gradient-to-r from-yellow-500 to-orange-500 opacity-20 blur-3xl"></div>
    </div>
              
                <!-- Payment Modal -->
    <div v-if="showPaymentModal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
      <div class="bg-white rounded-2xl w-full h-full max-w-none max-h-none overflow-hidden">
        <div class="flex justify-between items-center p-4 border-b">
          <h3 class="text-lg font-semibold text-gray-900">Complete Payment</h3>
          <button @click="closePaymentModal" class="text-gray-500 hover:text-gray-700">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </button>
        </div>
        <iframe 
          :src="paymentData?.checkoutUrl" 
          class="w-full h-full border-0"
          sandbox="allow-top-navigation allow-scripts allow-forms allow-same-origin allow-popups allow-popups-to-escape-sandbox"
          @load="onIframeLoad"
          @error="onIframeError">
        </iframe>
      </div>
    </div>
    
    <!-- Initial Welcome Screen -->
    <div v-if="!showRegistration" class="min-h-screen flex items-center justify-center relative z-10">
      <div class="text-center max-w-4xl mx-auto px-6">
        <div class="space-y-8">
          <!-- Badge -->
          <div class="inline-block px-4 py-1 rounded-full bg-white/10 backdrop-blur-sm border border-white/20 text-sm font-medium text-white">
            Next Generation Technology Solutions
          </div>
          
          <!-- Headline with Gradient Text -->
          <h1 class="text-5xl md:text-6xl font-bold leading-tight text-white">
            Your <span class="text-transparent bg-clip-text bg-gradient-to-r from-pink-400 to-purple-400">Unique</span> Business,<br />
            Your <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-400 to-teal-400">Perfect</span> Solution
          </h1>
          
          <!-- Description -->
          <p class="text-xl text-white/80 max-w-2xl mx-auto">
            Build a fully customized point-of-sale system with your own domain in minutes. Plus, get VPS hosting, custom software development, and API implementation services. Designed for the future of business.
          </p>

          <!-- CTA Buttons -->
          <div class="flex flex-wrap justify-center gap-4">
            <button @click="showRegistration = true"
                    class="px-8 py-4 rounded-full bg-gradient-to-r from-pink-500 to-purple-600 text-white font-medium transition-all hover:shadow-lg hover:shadow-purple-500/30 focus:outline-none focus:ring-2 focus:ring-purple-400">
              Get Started Free
            </button>
            <button @click="scrollToFeatures"
                    class="px-8 py-4 rounded-full bg-white/10 backdrop-blur-sm border border-white/20 text-white font-medium transition-all hover:bg-white/20 focus:outline-none focus:ring-2 focus:ring-white/40">
              Explore Features
            </button>
          </div>
          
          <!-- Social Proof -->
          <div class="flex items-center justify-center space-x-4 text-sm text-white/70">
            <div class="flex -space-x-2">
              <div class="w-8 h-8 rounded-full bg-gradient-to-r from-blue-400 to-indigo-400"></div>
              <div class="w-8 h-8 rounded-full bg-gradient-to-r from-purple-400 to-pink-400"></div>
              <div class="w-8 h-8 rounded-full bg-gradient-to-r from-orange-400 to-red-400"></div>
            </div>
            <!-- <span>Trusted by 500+ businesses</span> -->
          </div>
        </div>
      </div>
    </div>

    <!-- Registration Form -->
    <div v-else class="min-h-screen flex items-center justify-center relative z-10 py-12">
      <div class="w-full max-w-8xl mx-auto px-6">
        <div class="bg-white/10 backdrop-blur-sm border border-white/20 rounded-2xl p-8 shadow-2xl">
          <div class="text-center mb-8">
            <h1 class="text-4xl md:text-5xl font-bold text-white mb-4">
              Choose Your <span class="text-transparent bg-clip-text bg-gradient-to-r from-pink-400 to-purple-400">Perfect</span> Package
            </h1>
            <p class="text-xl text-white/80">Select the package that best fits your business needs</p>
          </div>

          <div class="space-y-8">
            <!-- Progress bar -->
            <div class="w-full bg-white/20 rounded-full h-3">
              <div class="bg-gradient-to-r from-pink-500 to-purple-600 h-3 rounded-full transition-all duration-500" 
                   :style="{ width: currentStep === 1 ? '12%' : currentStep === 2 ? '25%' : currentStep === 3 ? '37%' : currentStep === 4 ? '50%' : currentStep === 5 ? '62%' : currentStep === 6 ? '75%' : currentStep === 7 ? '87%' : currentStep === 8 ? '100%' : '100%' }"></div>
            </div>
            
            <!-- Step indicator -->
            <div class="flex justify-between text-sm text-white/70 px-1">
              <span :class="{'font-bold text-white': currentStep === 1}">Package</span>
              <span :class="{'font-bold text-white': currentStep === 2}">Details</span>
              <span :class="{'font-bold text-white': currentStep === 3}">Database</span>
              <span :class="{'font-bold text-white': currentStep === 4}">Processing</span>
              <span :class="{'font-bold text-white': currentStep === 5}">Complete</span>
              <span :class="{'font-bold text-white': currentStep === 6}">Payment</span>
              <span :class="{'font-bold text-white': currentStep === 7}">Deploy</span>
              <span :class="{'font-bold text-white': currentStep === 8}">Register</span>
            </div>

            <!-- Package Selection Step -->
            <div v-if="currentStep === 1" class="transition-opacity duration-500 opacity-100">
              <div v-if="loading" class="text-center py-12">
                <div class="spin-animation inline-block w-12 h-12 border-4 border-white border-t-transparent rounded-full mb-4"></div>
                <p class="text-white/80">Loading packages...</p>
              </div>
              
              <div v-else class="grid md:grid-cols-2 h-screen lg:grid-cols-4 gap-6">
                <div v-for="pkg in packages" :key="pkg.id" 
                     @click="selectPackage(pkg)"
                     :class="[
                       'relative bg-white/10 backdrop-blur-sm border-2 rounded-2xl p-6 cursor-pointer transition-all duration-300 hover:scale-105 hover:shadow-2xl',
                       selectedPackage?.id === pkg.id 
                         ? 'border-pink-400 shadow-lg shadow-pink-500/30' 
                         : 'border-white/20 hover:border-white/40'
                     ]">
                  
                  <!-- Popular Badge -->
                  <div v-if="pkg.name === 'professional_package'" 
                       class="absolute -top-3 left-1/2 transform -translate-x-1/2">
                    <span class="bg-gradient-to-r from-pink-500 to-purple-600 text-white text-xs font-bold px-3 py-1 rounded-full">
                      Most Popular
                    </span>
                  </div>
                  
                  <!-- Package Header -->
                  <div class="text-center mb-6">
                    <h3 class="text-xl font-bold text-white mb-2 capitalize">
                      {{ pkg.name.replace('_', ' ') }}
                    </h3>
                    <div class="text-3xl font-bold text-white mb-2">
                      {{ pkg.formatted_cost }}
                    </div>
                    <p v-if="pkg.is_free" class="text-green-400 text-sm font-medium">Free Forever</p>
                    <p v-else class="text-white/60 text-sm">per month</p>
                  </div>
                  
                  <!-- Service Modules -->
                  <div class="space-y-3 mb-6">
                    <div v-for="module in pkg.service_modules" :key="module.id" 
                         class="flex items-center text-white/80 text-sm">
                      <svg class="w-4 h-4 text-green-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                      </svg>
                      {{ module.display_name }}
                    </div>
                    <div v-if="pkg.service_modules.length === 0" class="text-white/60 text-sm text-center">
                      Basic package features
                    </div>
                  </div>
                  
                  <!-- Select Button -->
                  <button :class="[
                    'w-3/4 py-3 absolute rounded-lg bottom-2 left-1/2 transform -translate-x-1/2 font-medium transition-all duration-300',
                    selectedPackage?.id === pkg.id
                      ? 'bg-gradient-to-r from-pink-500 to-purple-600 text-white'
                      : 'bg-white/20 text-white hover:bg-white/30'
                  ]">
                    {{ selectedPackage?.id === pkg.id ? 'Selected' : 'Select Package' }}
                  </button>
                </div>
              </div>
            </div>

            <!-- Form Details Step -->
            <div v-if="currentStep === 2" class="transition-opacity duration-500 opacity-100">
              <div class="bg-white/5 backdrop-blur-sm border border-white/10 rounded-2xl p-6 mb-6">
                <h3 class="text-xl font-bold text-white mb-4 text-center">Selected Package</h3>
                <div class="flex items-center justify-between">
                  <div>
                    <h4 class="text-lg font-semibold text-white capitalize">{{ selectedPackage?.name?.replace('_', ' ') }}</h4>
                    <p class="text-white/70">{{ selectedPackage?.formatted_cost }} per month</p>
                  </div>
                  <div class="text-right">
                    <div class="text-2xl font-bold text-white">{{ selectedPackage?.formatted_cost }}</div>
                    <div class="text-white/60 text-sm">Total</div>
                  </div>
                </div>
              </div>
              
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Company Details -->
                <div class="group">
                  <label class="block text-sm font-bold mb-2 text-white group-hover:text-pink-300 transition-colors">Company Name</label>
                  <input v-model="formData.company_name" type="text" 
                         class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-lg text-white placeholder-white/50 focus:outline-none focus:ring-2 focus:ring-pink-400 focus:border-transparent transition-all" 
                         placeholder="Your company name">
                  <span v-if="errors?.company_name" class="text-red-400 text-sm">{{ errors.company_name[0] }}</span>
                </div>
                
                <div class="group">
                  <label class="block text-sm font-bold mb-2 text-white group-hover:text-pink-300 transition-colors">Host</label>
                  <div class="flex">
                    <input v-model="formData.host" type="text" 
                           class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-l-lg text-white placeholder-white/50 focus:outline-none focus:ring-2 focus:ring-pink-400 focus:border-transparent transition-all" 
                           placeholder="example"
                           @input="formData.host = formData.host.toLowerCase().replace(/[^a-z0-9-]/g, '')">
                    <span class="px-4 py-3 bg-white/20 border border-l-0 border-white/20 rounded-r-lg text-white/70">.avinertech.com</span>
                  </div>
                  <span v-if="errors?.host" class="text-red-400 text-sm">{{ errors.host[0] }}</span>
                  <span v-if="tenantError" class="text-red-400 text-sm">{{ tenantError }}</span>
                </div>

                <!-- User Details -->
                <div class="group">
                  <label class="block text-sm font-bold mb-2 text-white group-hover:text-pink-300 transition-colors">Email</label>
                  <input v-model="formData.email" type="email" 
                         class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-lg text-white placeholder-white/50 focus:outline-none focus:ring-2 focus:ring-pink-400 focus:border-transparent transition-all" 
                         placeholder="your@email.com">
                  <span v-if="errors?.email" class="text-red-400 text-sm">{{ errors.email[0] }}</span>
                </div>
                
                <div class="group">
                  <label class="block text-sm font-bold mb-2 text-white group-hover:text-pink-300 transition-colors">Username</label>
                  <input v-model="formData.username" type="text" 
                         class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-lg text-white placeholder-white/50 focus:outline-none focus:ring-2 focus:ring-pink-400 focus:border-transparent transition-all" 
                         placeholder="Your username">
                  <span v-if="errors?.username" class="text-red-400 text-sm">{{ errors.username[0] }}</span>
                </div>
                
                <div class="group">
                  <label class="block text-sm font-bold mb-2 text-white group-hover:text-pink-300 transition-colors">Password</label>
                  <input v-model="formData.password" type="password" 
                         class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-lg text-white placeholder-white/50 focus:outline-none focus:ring-2 focus:ring-pink-400 focus:border-transparent transition-all" 
                         placeholder="••••••••" minlength="8">
                  <span v-if="errors?.password" class="text-red-400 text-sm">{{ errors.password[0] }}</span>
                </div>
                
                <div class="group">
                  <label class="block text-sm font-bold mb-2 text-white group-hover:text-pink-300 transition-colors">Phone</label>
                  <input v-model="formData.phone" type="tel" 
                         class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-lg text-white placeholder-white/50 focus:outline-none focus:ring-2 focus:ring-pink-400 focus:border-transparent transition-all" 
                         placeholder="Your phone number">
                  <span v-if="errors?.phone" class="text-red-400 text-sm">{{ errors.phone[0] }}</span>
                </div>
                
                <div class="group col-span-full">
                  <label class="block text-sm font-bold mb-2 text-white group-hover:text-pink-300 transition-colors">Address</label>
                  <input v-model="formData.address" type="text" 
                         class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-lg text-white placeholder-white/50 focus:outline-none focus:ring-2 focus:ring-pink-400 focus:border-transparent transition-all" 
                         placeholder="Your physical address">
                  <span v-if="errors?.address" class="text-red-400 text-sm">{{ errors.address[0] }}</span>
                </div>
              </div>
            </div>

            <!-- Database Configuration Step -->
            <div v-if="currentStep === 3" class="transition-opacity duration-500 opacity-100">
              <div class="bg-white/5 backdrop-blur-sm border border-white/10 rounded-2xl p-6 mb-6">
                <h3 class="text-xl font-bold text-white mb-4 text-center">Database Configuration</h3>
                <p class="text-white/70 text-center">Configure your database settings for the application</p>
              </div>
              
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="group">
                  <label class="block text-sm font-bold mb-2 text-white group-hover:text-pink-300 transition-colors">Database Name</label>
                  <input v-model="formData.database_name" type="text" 
                         class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-lg text-white placeholder-white/50 focus:outline-none focus:ring-2 focus:ring-pink-400 focus:border-transparent transition-all" 
                         placeholder="Database name">
                  <span v-if="errors?.database_name" class="text-red-400 text-sm">{{ errors.database_name[0] }}</span>
                </div>
                
                <div class="group">
                  <label class="block text-sm font-bold mb-2 text-white group-hover:text-pink-300 transition-colors">Database User</label>
                  <input v-model="formData.database_user" type="text" 
                         class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-lg text-white placeholder-white/50 focus:outline-none focus:ring-2 focus:ring-pink-400 focus:border-transparent transition-all" 
                         placeholder="Database username">
                  <span v-if="errors?.database_user" class="text-red-400 text-sm">{{ errors.database_user[0] }}</span>
                </div>
                
                <div class="group col-span-full">
                  <label class="block text-sm font-bold mb-2 text-white group-hover:text-pink-300 transition-colors">Database Password</label>
                  <input v-model="formData.database_password" type="password" 
                         class="w-full px-4 py-3 bg-white/10 border border-white/20 rounded-lg text-white placeholder-white/50 focus:outline-none focus:ring-2 focus:ring-pink-400 focus:border-transparent transition-all" 
                         placeholder="••••••••">
                  <span v-if="errors?.database_password" class="text-red-400 text-sm">{{ errors.database_password[0] }}</span>
                </div>
              </div>
            </div>

            <!-- Processing Step -->
            <div v-if="currentStep === 4" class="text-center py-12">
              <div class="spin-animation inline-block w-16 h-16 border-4 border-white border-t-transparent rounded-full mb-6"></div>
              <h3 class="text-2xl font-bold text-white mb-4">Processing Registration...</h3>
              <div class="space-y-2">
                <p class="text-white/80">Your application is being registered</p>
                <p class="text-white/60">This may take a few moments...</p>
              </div>
            </div>

            <!-- Success Step -->
            <div v-if="currentStep === 5" class="text-center py-8">
              <div class="text-6xl mb-6">✅</div>
              <h3 class="text-3xl font-bold text-white mb-6">Application Registered Successfully!</h3>
              
              <div class="bg-white/10 backdrop-blur-sm border border-white/20 p-8 rounded-2xl mt-6 text-left">
                <h4 class="text-xl font-semibold mb-6 text-white text-center">Registration Details</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                  <div class="space-y-4">
                    <div>
                      <p class="font-medium text-white/80">Package:</p>
                      <p class="text-white capitalize">{{ selectedPackage?.name?.replace('_', ' ') }}</p>
                    </div>
                    <div>
                      <p class="font-medium text-white/80">Host:</p>
                      <p class="text-white">{{ responseData?.host }}</p>
                    </div>
                    <div>
                      <p class="font-medium text-white/80">Username:</p>
                      <p class="text-white">{{ responseData?.username }}</p>
                    </div>
                  </div>
                  <div class="space-y-4">
                    <div>
                      <p class="font-medium text-white/80">Created At:</p>
                      <p class="text-white">{{ responseData?.created_at ? new Date(responseData.created_at).toLocaleString() : '' }}</p>
                    </div>
                    <div>
                      <p class="font-medium text-white/80">Tenant ID:</p>
                      <p class="text-white">{{ responseData?.id }}</p>
                    </div>
                    <div>
                      <p class="font-medium text-white/80">Cost:</p>
                      <p class="text-white">{{ selectedPackage?.formatted_cost }}</p>
                    </div>
                  </div>
                </div>
              </div>

              <div class="flex flex-wrap justify-center gap-4 mt-8">
                <button @click="currentStep = 1" 
                        class="px-8 py-3 bg-white/20 text-white font-bold rounded-lg shadow-lg hover:bg-white/30 transition-all">
                  Update Information
                </button>
                <button @click="startPayment" 
                        class="px-8 py-3 bg-gradient-to-r from-pink-500 to-purple-600 text-white font-bold rounded-lg shadow-lg hover:shadow-pink-500/30 transition-all">
                  Pay Now - Start Free Trial
                </button>
              </div>
            </div>

            <!-- Payment Step -->
            <div v-if="currentStep === 6" class="text-center py-8">
              <div class="text-6xl mb-6">💳</div>
              <h3 class="text-3xl font-bold text-white mb-6">Complete Your Payment</h3>
              <p class="text-xl text-white/80 mb-8">Start your 30-day free trial today!</p>
              
              <div class="bg-white/10 backdrop-blur-sm border border-white/20 p-8 rounded-2xl mt-6 text-left max-w-2xl mx-auto">
                <h4 class="text-xl font-semibold mb-6 text-white text-center">Payment Summary</h4>
                
                <div class="space-y-4">
                  <div class="flex justify-between items-center">
                    <span class="text-white/80">Package:</span>
                    <span class="text-white font-semibold capitalize">{{ selectedPackage?.name?.replace('_', ' ') }}</span>
                  </div>
                  <div class="flex justify-between items-center">
                    <span class="text-white/80">Company:</span>
                    <span class="text-white font-semibold">{{ formData.company_name }}</span>
                  </div>
                  <div class="flex justify-between items-center">
                    <span class="text-white/80">Host:</span>
                    <span class="text-white font-semibold">{{ formData.host }}.avinertech.com</span>
                  </div>
                  <div class="flex justify-between items-center">
                    <span class="text-white/80">Monthly Cost:</span>
                    <span class="text-white font-semibold">{{ selectedPackage?.formatted_cost }}</span>
                  </div>
                  <div class="flex justify-between items-center border-t border-white/20 pt-4">
                    <span class="text-green-400 font-semibold">30-Day Free Trial</span>
                    <span class="text-green-400 font-bold">$0.00</span>
                  </div>
                </div>
              </div>

              <div class="flex flex-wrap justify-center gap-4 mt-8">
                <button @click="startPayment" 
                        class="px-8 py-3 bg-gradient-to-r from-pink-500 to-purple-600 text-white font-bold rounded-lg shadow-lg hover:shadow-pink-500/30 transition-all">
                  Pay Now - Start Free Trial
                </button>
                <button @click="currentStep = 1" 
                        class="px-8 py-3 bg-white/20 text-white font-bold rounded-lg shadow-lg hover:bg-white/30 transition-all">
                  Change Package
                </button>
              </div>
            </div>

            <!-- Payment Processing Step -->
            <div v-if="currentStep === 7" class="text-center py-8">
              <div v-if="!paymentComplete" class="spin-animation inline-block w-16 h-16 border-4 border-white border-t-transparent rounded-full mb-6"></div>
              <h3 class="text-2xl font-bold text-white mb-4">{{ paymentStatus }}</h3>
              <div class="mt-4">
                <p class="text-white/80">{{ paymentMessage }}</p>
  
                <div v-if="paymentComplete" class="mt-8">
                  <div class="text-6xl mb-4">✅</div>
                  <h3 class="text-2xl font-bold text-white mb-4">Payment Successful!</h3>
                  <p class="text-white/80">Your 30-day free trial has started. Starting deployment now...</p>
                </div>
              </div>
            </div>

            <!-- Deployment Step -->
            <div v-if="currentStep === 8" class="text-center py-8">
              <div v-if="!deploymentComplete" class="spin-animation inline-block w-16 h-16 border-4 border-white border-t-transparent rounded-full mb-6"></div>
              <h3 class="text-2xl font-bold text-white mb-4">{{ deploymentStatus }}</h3>
              <div class="mt-4">
                <p class="text-white/80">{{ deploymentMessage }}</p>
                <div v-if="deploymentComplete" class="mt-8">
                  <div class="text-6xl mb-4">🎉</div>
                  <h3 class="text-2xl font-bold text-white mb-4">Setup Complete!</h3>
                  <p class="text-white/80">Your application is ready to use.</p>
                  <p class="text-white/80">You can now access your application at:</p>
                  <a :href="'https://' + responseData?.host" target="_blank" 
                     class="block mt-4 text-pink-400 hover:text-pink-300 underline text-lg font-medium">
                    {{ responseData?.host }}
                  </a>
                </div>
              </div>
            </div>

            <!-- Registration Step -->
            <div v-if="currentStep === 9" class="text-center py-8">
              <div v-if="!registrationComplete" class="spin-animation inline-block w-16 h-16 border-4 border-white border-t-transparent rounded-full mb-6"></div>
              <h3 class="text-2xl font-bold text-white mb-4">{{ registrationStatus }}</h3>
              <div class="mt-4">
                <p class="text-white/80">{{ registrationMessage }}</p>
                <div v-if="registrationComplete" class="mt-8">
                  <div class="text-6xl mb-6">🎉</div>
                  <h3 class="text-3xl font-bold text-white mb-6">Registration Complete!</h3>
                  
                  <!-- Registration Details -->
                  <div class="bg-white/10 backdrop-blur-sm border border-white/20 p-8 rounded-2xl mt-6 text-left max-w-4xl mx-auto">
                    <h4 class="text-xl font-semibold mb-6 text-white text-center">Registration Details</h4>
                    
                    <!-- User Information -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                      <div class="space-y-4">
                        <h5 class="text-lg font-semibold text-pink-400 mb-3">User Information</h5>
                        <!-- <div>
                          <p class="font-medium text-white/80">User ID:</p>
                          <p class="text-white">{{ registrationData?.user?.id }}</p>
                        </div> -->
                        <div>
                          <p class="font-medium text-white/80">Name:</p>
                          <p class="text-white">{{ registrationData?.user?.name }}</p>
                        </div>
                        <div>
                          <p class="font-medium text-white/80">Email:</p>
                          <p class="text-white">{{ registrationData?.user?.email }}</p>
                        </div>
                        <!-- <div>
                          <p class="font-medium text-white/80">User Type:</p>
                          <p class="text-white">{{ registrationData?.user?.user_type }}</p>
                        </div> -->
                        <div>
                          <p class="font-medium text-white/80">Status:</p>
                          <p class="text-white">{{ registrationData?.user?.is_active ? 'Active' : 'Inactive' }}</p>
                        </div>
                      </div>
                      
                      <div class="space-y-4">
                        <h5 class="text-lg font-semibold text-blue-400 mb-3">Tenant Information</h5>
                        <div>
                          <p class="font-medium text-white/80">Tenant ID:</p>
                          <p class="text-white">{{ registrationData?.tenant?.id }}</p>
                        </div>
                        <div>
                          <p class="font-medium text-white/80">Name:</p>
                          <p class="text-white">{{ registrationData?.tenant?.name }}</p>
                        </div>
                        <div>
                          <p class="font-medium text-white/80">Host:</p>
                          <p class="text-white">{{ registrationData?.tenant?.host }}</p>
                        </div>
                        <div>
                          <p class="font-medium text-white/80">Status:</p>
                          <p class="text-white">{{ registrationData?.tenant?.status }}</p>
                        </div>
                        <div>
                          <p class="font-medium text-white/80">Website URL:</p>
                          <a :href="'https://' + registrationData?.tenant?.host" target="_blank" 
                             class="text-pink-400 hover:text-pink-300 underline text-lg font-medium">
                            {{ registrationData?.tenant?.host }}
                          </a>
                        </div>
                      </div>
                    </div>
                    
                    <!-- Package Information -->
                    <div class="mt-8 pt-6 border-t border-white/20">
                      <h5 class="text-lg font-semibold text-green-400 mb-3">Package Information</h5>
                      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                          <p class="font-medium text-white/80">Package ID:</p>
                          <p class="text-white">{{ registrationData?.package?.id }}</p>
                        </div>
                        <div>
                          <p class="font-medium text-white/80">Package Name:</p>
                          <p class="text-white">{{ registrationData?.package?.name }}</p>
                        </div>
                        <div>
                          <p class="font-medium text-white/80">Cost:</p>
                          <p class="text-white">{{ registrationData?.package?.formatted_cost }}</p>
                        </div>
                      </div>
                    </div>
                    
                    <!-- API Token -->
                    <!-- <div class="mt-8 pt-6 border-t border-white/20">
                      <h5 class="text-lg font-semibold text-yellow-400 mb-3">API Access</h5>
                      <div>
                        <p class="font-medium text-white/80">API Token:</p>
                        <p class="text-white font-mono bg-white/10 p-2 rounded break-all">{{ registrationData?.api_token }}</p>
                      </div>
                    </div> -->
                    
                    <!-- Database Configuration -->
                    <div class="mt-8 pt-6 border-t border-white/20">
                      <h5 class="text-lg font-semibold text-purple-400 mb-3">Database Configuration</h5>
                      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                          <p class="font-medium text-white/80">Database Name:</p>
                          <p class="text-white">{{ registrationData?.database_config?.database_name }}</p>
                        </div>
                        <div>
                          <p class="font-medium text-white/80">Database User:</p>
                          <p class="text-white">{{ registrationData?.database_config?.database_user }}</p>
                        </div>
                      </div>
                    </div>
                  </div>
                  
                  <!-- Action Buttons -->
                  <div class="flex flex-wrap justify-center gap-4 mt-8">
                    <a :href="'https://' + registrationData?.tenant?.host" target="_blank"
                       class="px-8 py-3 bg-gradient-to-r from-pink-500 to-purple-600 text-white font-bold rounded-lg shadow-lg hover:shadow-pink-500/30 transition-all">
                      Visit Your Application
                    </a>
                    <!-- <button @click="currentStep = 1" 
                            class="px-8 py-3 bg-white/20 text-white font-bold rounded-lg shadow-lg hover:bg-white/30 transition-all">
                      Start Over
                    </button> -->
                  </div>
                </div>
              </div>
            </div>

            <!-- Navigation buttons -->
            <div class="flex justify-between pt-8">
              <button v-if="currentStep > 1 && currentStep < 4"
                      @click="currentStep = currentStep - 1"
                      class="px-8 py-3 bg-white/20 text-white font-bold rounded-lg shadow-lg hover:bg-white/30 transition-all transform hover:scale-105">
                Previous
              </button>
              <div class="flex-grow"></div>
              <button v-if="currentStep === 1" 
                      @click="handleStep" 
                      :disabled="!selectedPackage"
                      :class="[
                        'px-8 py-3 text-white font-bold rounded-lg shadow-lg transition-all transform hover:scale-105',
                        selectedPackage 
                          ? 'bg-gradient-to-r from-pink-500 to-purple-600 hover:shadow-pink-500/30' 
                          : 'bg-white/20 cursor-not-allowed'
                      ]">
                Continue
              </button>
              <button v-if="currentStep === 2" 
                      @click="handleStep" 
                      class="px-8 py-3 bg-gradient-to-r from-pink-500 to-purple-600 text-white font-bold rounded-lg shadow-lg hover:shadow-pink-500/30 transition-all transform hover:scale-105">
                Continue
              </button>
              <button v-if="currentStep === 3" 
                      @click="handleStep" 
                      class="px-8 py-3 bg-gradient-to-r from-pink-500 to-purple-600 text-white font-bold rounded-lg shadow-lg hover:shadow-pink-500/30 transition-all transform hover:scale-105">
                Complete Registration
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, onMounted } from 'vue'

export default {
  name: 'RegistrationForm',
  setup() {
    const showRegistration = ref(false)
    const currentStep = ref(1)
    const errors = ref({})
    const responseData = ref({})
    const tenantError = ref('')
    const deploymentStatus = ref('')
    const deploymentMessage = ref('')
    const deploymentComplete = ref(false)
    const registrationStatus = ref('')
    const registrationMessage = ref('')
    const registrationComplete = ref(false)
    const registrationData = ref({})
    const paymentStatus = ref('')
    const paymentMessage = ref('')
    const paymentComplete = ref(false)
    const paymentData = ref({})
    const showPaymentModal = ref(false)
    const packages = ref([])
    const selectedPackage = ref(null)
    const loading = ref(false)
    
    const formData = ref({
      company_name: '',
      host: '',
      email: '',
      username: '',
      password: '',
      phone: '',
      address: '',
      database_name: '',
      database_user: '',
      database_password: ''
    })

    // Fetch packages from API
    const fetchPackages = async () => {
      loading.value = true
      try {
        console.log('Fetching packages...')
        const response = await fetch('https://signal.avinertech.com/api/packages')
        console.log('Response status:', response.status)
        
        if (!response.ok) {
          throw new Error(`HTTP error! status: ${response.status}`)
        }
        
        const data = await response.json()
        console.log('API response:', data)
        
        if (data.success) {
          packages.value = data.data
          console.log('Packages loaded:', packages.value)
        } else {
          console.error('API returned success: false')
        }
      } catch (error) {
        console.error('Error fetching packages:', error)
        // Fallback data for testing
        packages.value = [
          {
            id: 1,
            name: 'free_package',
            cost: '0.00',
            formatted_cost: '0.00 USD',
            is_free: true,
            service_modules: []
          },
          {
            id: 2,
            name: 'basic_package',
            cost: '29.99',
            formatted_cost: '29.99 USD',
            is_free: false,
            service_modules: []
          }
        ]
      } finally {
        loading.value = false
      }
    }

    // Select package
    const selectPackage = (pkg) => {
      selectedPackage.value = pkg
    }

    // Scroll to features (placeholder)
    const scrollToFeatures = () => {
      // This would scroll to a features section if it existed
      console.log('Scroll to features')
    }

    const handleStep = () => {
      errors.value = {} // Clear previous errors
      tenantError.value = '' // Clear tenant error
      if (currentStep.value === 1) {
        if (selectedPackage.value) {
          currentStep.value = 2
        }
      } else if (currentStep.value === 2) {
        currentStep.value = 3
      } else if (currentStep.value === 3) {
        currentStep.value = 4
        submitForm()
      }
    }

    const makeApiCall = async (endpoint, data) => {
      const response = await fetch(`https://manager.avinertech.com/api/deployment/${endpoint}?token=aGFzc2FhblNoYXJpcTI3OTAx`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify(data)
      })
      return await response.json()
    }

    const startPayment = async () => {
      currentStep.value = 7
      paymentComplete.value = false

      try {
        paymentStatus.value = "Initializing Payment"
        paymentMessage.value = "Setting up your payment process..."

        // Step 1: Encrypt payment data
        const paymentPayload = {
          tenantId: responseData.value.id,
          providerCode: "payoneer",
          providerAccountId: "550e8400-e29b-41d4-a716-446655440000",
          amountMinor: Math.round(parseFloat(selectedPackage.value.cost) * 100), // Convert to cents
          currency: "USD",
          method: "credit_card",
          orderId: `ORDER-${Date.now()}`,
          productName: selectedPackage.value.name.replace('_', ' '),
          serviceName: "AvinerTech Application Service",
          companyName: formData.value.company_name,
          serviceHost: "https://avinertech.com",
          clientDomain: `${formData.value.host}.avinertech.com`,
          clientId: responseData.value.id,
          clientReferenceId: `REF-${Date.now()}`,
          callbackUrl: "https://webhook.site/test",
          metadata: {
            customerId: responseData.value.id,
            subscriptionId: `sub_${Date.now()}`
          }
        }

        console.log('Payment payload:', paymentPayload)

        // Encrypt the data
        const encryptResponse = await fetch('https://signal.avinertech.com/api/encrypt', {
          method: 'POST',
          headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json'
          },
          body: JSON.stringify({
            value: JSON.stringify(paymentPayload)
          })
        })

        const encryptData = await encryptResponse.json()
        console.log('Encrypt response:', encryptData)

        if (!encryptData.success) {
          throw new Error('Failed to encrypt payment data')
        }

        // Step 2: Create payment
        paymentStatus.value = "Creating Payment"
        paymentMessage.value = "Processing your payment request..."

        const paymentResponse = await fetch('https://payments.avinertech.com/v1/payments', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-APP-SIGNATURE': 'test-signature',
            'X-ENC-SUB': 'test-subdomain'
          },
          body: JSON.stringify({
            signature: `${encryptData.encrypted}`
          })
        })

        const paymentResult = await paymentResponse.json()
        console.log('Payment response:', paymentResult)

        if (paymentResult.checkoutUrl) {
          paymentData.value = paymentResult
          showPaymentModal.value = true
          paymentStatus.value = "Payment Ready"
          paymentMessage.value = "Complete your payment in the secure checkout form"
        } else {
          throw new Error('Failed to create payment')
        }

      } catch (error) {
        console.error('Payment error:', error)
        paymentStatus.value = "Payment Error"
        paymentMessage.value = "An error occurred during payment setup. Please try again."
        
        // Redirect back to deployment step after 3 seconds
        setTimeout(() => {
          currentStep.value = 8 // Go to deployment step
        }, 3000)
      }
    }

    const checkPaymentStatus = () => {
      // This would typically check the payment status via API
      // For now, we'll simulate a successful payment after a delay
      setTimeout(() => {
        if (showPaymentModal.value) {
          paymentComplete.value = true
          paymentStatus.value = "Payment Successful"
          paymentMessage.value = "Your payment has been processed successfully!"
          showPaymentModal.value = false
          
          // Start deployment after successful payment
          setTimeout(() => {
            startDeployment()
          }, 2000)
        }
      }, 5000) // Simulate 5 second payment process
    }

    const onIframeLoad = () => {
      console.log('Iframe loaded successfully')
      paymentStatus.value = "Payment Form Ready"
      paymentMessage.value = "Please complete your payment in the form below"
    }

    const onIframeError = () => {
      console.error('Iframe failed to load')
      paymentStatus.value = "Payment Error"
      paymentMessage.value = "Failed to load payment form. Please try again."
      showPaymentModal.value = false
      
      // Redirect back to deployment step after 3 seconds
      setTimeout(() => {
        currentStep.value = 8
      }, 3000)
    }

    const closePaymentModal = () => {
      showPaymentModal.value = false
      // If payment is not complete, go back to deployment step
      if (!paymentComplete.value) {
        currentStep.value = 8 // Go to deployment step
      }
    }

    const startDeployment = async () => {
      currentStep.value = 8
      deploymentComplete.value = false

      try {
        // Step 1: Deploy Module
        deploymentStatus.value = "Deploying Your Application"
        deploymentMessage.value = "Configuring servers and deploying your application..."
        await makeApiCall('deploy-module', {
          tenant_id: responseData.value.id,
          module: "tenant",
          submodule: "custom-app"
        })

        // Step 2: SSL Certificate
        deploymentStatus.value = "Securing Your Application"
        deploymentMessage.value = "Implementing SSL encryption for secure access..."
        await makeApiCall('ssl-cert', {
          tenant_id: responseData.value.id
        })

        // Step 3: Create Database
        deploymentStatus.value = "Finalizing Setup"
        deploymentMessage.value = "Creating and configuring your database..."
        await makeApiCall('create-tenant-db', {
          tenant_id: responseData.value.id
        })

        // Complete
        deploymentStatus.value = "Deployment Complete"
        deploymentMessage.value = "Your application has been successfully deployed!"
        deploymentComplete.value = true

        // Start registration process
        await startRegistration()

      } catch (error) {
        console.error('Deployment error:', error)
        deploymentStatus.value = "Deployment Error"
        deploymentMessage.value = "An error occurred during deployment. Please try again."
      }
    }

    const startRegistration = async () => {
      currentStep.value = 9
      registrationComplete.value = false

      try {
        registrationStatus.value = "Registering Application"
        registrationMessage.value = "Registering your application in the central system..."

        const registrationPayload = {
          package_id: selectedPackage.value.id,
          package_name: selectedPackage.value.name.replace('_', ' '),
          package_price_per_month: parseFloat(selectedPackage.value.cost),
          total_price: parseFloat(selectedPackage.value.cost),
          company_name: formData.value.company_name,
          email: formData.value.email,
          password: formData.value.password,
          password_confirmation: formData.value.password,
          address: formData.value.address,
          host: formData.value.host,
          username: formData.value.username,
          phone: formData.value.phone,
          database_name: formData.value.database_name,
          database_user: formData.value.database_user,
          database_password: formData.value.database_password
        }

        console.log('Registration payload:', registrationPayload)

        const response = await fetch('https://signal.avinertech.com/api/register-application', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json'
          },
          body: JSON.stringify(registrationPayload)
        })

        const data = await response.json()
        console.log('Registration response:', data)

        if (data.success) {
          registrationData.value = data.data
          registrationStatus.value = "Registration Complete"
          registrationMessage.value = "Your application has been successfully registered!"
          registrationComplete.value = true
        } else {
          throw new Error(data.message || 'Registration failed')
        }

      } catch (error) {
        console.error('Registration error:', error)
        registrationStatus.value = "Registration Error"
        registrationMessage.value = "An error occurred during registration. Please try again."
      }
    }

    const submitForm = async () => {
      // Create separate payload with email and username
      if (formData.value.host === '') {
        tenantError.value = 'Host is required';
        currentStep.value = 1;
        return
      }

      const fullFormData = {
        ...formData.value,
        host: formData.value.host + '.avinertech.com',
        email: formData.value.email,
        username: formData.value.username
      }

      try {
        const response = await fetch('https://manager.avinertech.com/api/deployment/create-tenant?token=aGFzc2FhblNoYXJpcTI3OTAx', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
          },
          body: JSON.stringify(fullFormData)
        })

        const data = await response.json()
        
        if (data.status) {
          // Store response data
          responseData.value = data.server_response.data.data
          // Move to review step after successful response
          currentStep.value = 5
        } else {
          console.log(data)
          // Handle validation errors
          errors.value = data.errors
          if (typeof data.error === 'string') {
            console.log(data.error)
            tenantError.value = data.error
            currentStep.value = 1
          } else {
            // Move back to appropriate step based on errors
            if (Object.keys(data.errors).some(key => ['username', 'password', 'email', 'host', 'company_name', 'phone', 'address'].includes(key))) {
              currentStep.value = 2
            } else if (Object.keys(data.errors).some(key => ['database_name', 'database_user', 'database_password'].includes(key))) {
              currentStep.value = 3
            }
          }
        }
      } catch (error) {
        console.error('Error submitting form:', error)
        // Handle error appropriately
      }
    }

    // Load packages on component mount
    onMounted(() => {
      fetchPackages()
    })

    return {
      showRegistration,
      currentStep,
      formData,
      errors,
      responseData,
      tenantError,
      deploymentStatus,
      deploymentMessage,
      deploymentComplete,
      registrationStatus,
      registrationMessage,
      registrationComplete,
      registrationData,
      paymentStatus,
      paymentMessage,
      paymentComplete,
      paymentData,
      showPaymentModal,
      packages,
      selectedPackage,
      loading,
      handleStep,
      submitForm,
      startDeployment,
      startPayment,
      checkPaymentStatus,
      onIframeLoad,
      onIframeError,
      closePaymentModal,
      selectPackage,
      scrollToFeatures
    }
  }
}
</script>

<style>
@keyframes spin {
  from {
    transform: rotate(0deg);
  }
  to {
    transform: rotate(360deg);
  }
}

.spin-animation {
  animation: spin 1s linear infinite;
}

@keyframes fadeIn {
  0% { 
    opacity: 0; 
    transform: translateY(30px); 
  }
  100% { 
    opacity: 1; 
    transform: translateY(0); 
  }
}

.animate-fade-in {
  animation: fadeIn 0.6s ease-out forwards;
}

.fade-in-section {
  opacity: 0;
  transform: translateY(30px);
  transition: all 0.6s ease-out;
}

/* 3D Perspective for Mockup */
.perspective-1200 {
  perspective: 1200px;
}

.rotateY-6 {
  transform: rotateY(6deg);
}

.rotateX-6 {
  transform: rotateX(6deg);
}

/* Glassmorphism effects */
.glassmorphism {
  background: rgba(255, 255, 255, 0.1);
  backdrop-filter: blur(10px);
  border: 1px solid rgba(255, 255, 255, 0.2);
}

/* Custom scrollbar */
::-webkit-scrollbar {
  width: 8px;
}

::-webkit-scrollbar-track {
  background: rgba(255, 255, 255, 0.1);
}

::-webkit-scrollbar-thumb {
  background: rgba(255, 255, 255, 0.3);
  border-radius: 4px;
}

::-webkit-scrollbar-thumb:hover {
  background: rgba(255, 255, 255, 0.5);
}
</style>