<template>
  <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-purple-600 via-pink-500 to-yellow-400">
    <!-- Initial Welcome Screen -->
    <div v-if="!showRegistration" class="text-center">
      <div class="space-y-8">
        <div class="text-6xl animate-bounce">
          🚀 ⚡️ 🎯
        </div>
        
        <h1 class="text-5xl font-extrabold text-white">
          Build Your Dream Application
        </h1>
        
        <p class="text-xl text-white/90 max-w-xl mx-auto">
          Transform your ideas into reality with our powerful application platform. Get started in minutes with enterprise-grade infrastructure.
        </p>

        <div class="space-y-4">
          <button @click="showRegistration = true"
                  class="px-8 py-4 bg-white text-xl font-bold text-purple-600 rounded-xl shadow-2xl hover:scale-105 transform transition-all duration-300 hover:shadow-pink-500/20">
            Start Building My Application 
          </button>
          <p class="text-sm text-white/70">
            🛡️ Enterprise-grade Security • 🔥 Blazing Fast Setup • 💪 Scalable Infrastructure
          </p>
        </div>
      </div>
    </div>

    <!-- Registration Form -->
    <div v-else class="w-full max-w-2xl p-8 mx-auto bg-white rounded-xl shadow-2xl transform hover:scale-105 transition-all duration-300">
      <div class="text-center mb-8">
        <h1 class="text-4xl font-extrabold bg-clip-text text-transparent bg-gradient-to-r from-cyan-500 to-fuchsia-600 animate-pulse">
          Register Your App
        </h1>
        <p class="mt-2 text-lg text-gray-700">Set up your application in seconds</p>
      </div>

      <div class="space-y-6">
        <!-- Progress bar -->
        <div class="w-full bg-gray-200 rounded-full h-2.5">
          <div class="bg-gradient-to-r from-green-400 to-blue-500 h-2.5 rounded-full transition-all duration-500" 
               :style="{ width: currentStep === 1 ? '20%' : currentStep === 2 ? '40%' : currentStep === 3 ? '60%' : currentStep === 4 ? '80%' : '100%' }"></div>
        </div>
        
        <!-- Step indicator -->
        <div class="flex justify-between text-xs text-gray-600 px-1">
          <span :class="{'font-bold text-lime-500': currentStep === 1}">Info</span>
          <span :class="{'font-bold text-lime-500': currentStep === 2}">Database</span>
          <span :class="{'font-bold text-lime-500': currentStep === 3}">Processing</span>
          <span :class="{'font-bold text-lime-500': currentStep === 4}">Review</span>
          <span :class="{'font-bold text-lime-500': currentStep === 5}">Complete</span>
        </div>

        <!-- Form step one -->
        <div v-if="currentStep === 1" class="grid grid-cols-1 md:grid-cols-2 gap-4 transition-opacity duration-500 opacity-100">
          <!-- Company Details -->
          <div class="group">
            <label class="block text-sm font-bold mb-2 text-indigo-600 group-hover:text-pink-500 transition-colors">Company Name</label>
            <input v-model="formData.company_name" type="text" 
                   class="w-full px-4 py-2 border-2 border-indigo-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-transparent transition-all" 
                   placeholder="Your company name">
            <span v-if="errors?.company_name" class="text-red-500 text-sm">{{ errors.company_name[0] }}</span>
          </div>
          
          <div class="group">
            <label class="block text-sm font-bold mb-2 text-indigo-600 group-hover:text-pink-500 transition-colors">Host</label>
            <div class="flex">
              <input v-model="formData.host" type="text" 
                     class="w-full px-4 py-2 border-2 border-indigo-300 rounded-l-lg focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-transparent transition-all" 
                     placeholder="example"
                     @input="formData.host = formData.host.toLowerCase().replace(/[^a-z0-9-]/g, '')">
              <span class="px-4 py-2 bg-gray-100 border-2 border-l-0 border-indigo-300 rounded-r-lg text-gray-600">.avinertech.com</span>
            </div>
            <span v-if="errors?.host" class="text-red-500 text-sm">{{ errors.host[0] }}</span>
            <span v-if="tenantError" class="text-red-500 text-sm">{{ tenantError }}</span>
          </div>

          <!-- User Details -->
          <div class="group">
            <label class="block text-sm font-bold mb-2 text-indigo-600 group-hover:text-pink-500 transition-colors">Email</label>
            <input v-model="formData.email" type="email" 
                   class="w-full px-4 py-2 border-2 border-indigo-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-transparent transition-all" 
                   placeholder="your@email.com">
            <span v-if="errors?.email" class="text-red-500 text-sm">{{ errors.email[0] }}</span>
          </div>
          
          <div class="group">
            <label class="block text-sm font-bold mb-2 text-indigo-600 group-hover:text-pink-500 transition-colors">Username</label>
            <input v-model="formData.username" type="text" 
                   class="w-full px-4 py-2 border-2 border-indigo-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-transparent transition-all" 
                   placeholder="Your username">
            <span v-if="errors?.username" class="text-red-500 text-sm">{{ errors.username[0] }}</span>
          </div>
          
          <div class="group">
            <label class="block text-sm font-bold mb-2 text-indigo-600 group-hover:text-pink-500 transition-colors">Password</label>
            <input v-model="formData.password" type="password" 
                   class="w-full px-4 py-2 border-2 border-indigo-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-transparent transition-all" 
                   placeholder="••••••••">
            <span v-if="errors?.password" class="text-red-500 text-sm">{{ errors.password[0] }}</span>
          </div>
          
          <div class="group">
            <label class="block text-sm font-bold mb-2 text-indigo-600 group-hover:text-pink-500 transition-colors">Phone</label>
            <input v-model="formData.phone" type="tel" 
                   class="w-full px-4 py-2 border-2 border-indigo-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-transparent transition-all" 
                   placeholder="Your phone number">
            <span v-if="errors?.phone" class="text-red-500 text-sm">{{ errors.phone[0] }}</span>
          </div>
          
          <div class="group col-span-full">
            <label class="block text-sm font-bold mb-2 text-indigo-600 group-hover:text-pink-500 transition-colors">Address</label>
            <input v-model="formData.address" type="text" 
                   class="w-full px-4 py-2 border-2 border-indigo-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-transparent transition-all" 
                   placeholder="Your physical address">
            <span v-if="errors?.address" class="text-red-500 text-sm">{{ errors.address[0] }}</span>
          </div>
        </div>

        <!-- Form step two -->
        <div v-if="currentStep === 2" class="grid grid-cols-1 md:grid-cols-2 gap-4 transition-opacity duration-500 opacity-100">
          <!-- Database Details -->
          <div class="col-span-full">
            <h3 class="text-xl font-bold text-center mb-4 bg-clip-text text-transparent bg-gradient-to-r from-blue-500 to-purple-500">Database Configuration</h3>
          </div>
          
          <div class="group">
            <label class="block text-sm font-bold mb-2 text-indigo-600 group-hover:text-pink-500 transition-colors">Database Name</label>
            <input v-model="formData.database_name" type="text" 
                   class="w-full px-4 py-2 border-2 border-indigo-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-transparent transition-all" 
                   placeholder="Database name">
            <span v-if="errors?.database_name" class="text-red-500 text-sm">{{ errors.database_name[0] }}</span>
          </div>
          
          <div class="group">
            <label class="block text-sm font-bold mb-2 text-indigo-600 group-hover:text-pink-500 transition-colors">Database User</label>
            <input v-model="formData.database_user" type="text" 
                   class="w-full px-4 py-2 border-2 border-indigo-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-transparent transition-all" 
                   placeholder="Database username">
            <span v-if="errors?.database_user" class="text-red-500 text-sm">{{ errors.database_user[0] }}</span>
          </div>
          
          <div class="group col-span-full">
            <label class="block text-sm font-bold mb-2 text-indigo-600 group-hover:text-pink-500 transition-colors">Database Password</label>
            <input v-model="formData.database_password" type="password" 
                   class="w-full px-4 py-2 border-2 border-indigo-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-transparent transition-all" 
                   placeholder="••••••••">
            <span v-if="errors?.database_password" class="text-red-500 text-sm">{{ errors.database_password[0] }}</span>
          </div>
        </div>

        <!-- Loading step -->
        <div v-if="currentStep === 3" class="text-center py-8">
          <div class="spin-animation inline-block w-12 h-12 border-4 border-indigo-500 border-t-transparent rounded-full mb-4"></div>
          <p class="text-lg text-indigo-600">Processing Registration...</p>
          <div class="mt-4">
            <p class="text-gray-600">Your application is being registered</p>
            <p class="text-gray-600">This may take a few moments...</p>
          </div>
        </div>

        <!-- Review step -->
        <div v-if="currentStep === 4" class="text-center py-8">
          <div class="text-6xl mb-4">✅</div>
          <h3 class="text-2xl font-bold text-green-600 mb-4">Application Registered Successfully!</h3>
          
          <div class="bg-gray-50 p-6 rounded-lg mt-4 text-left">
            <h4 class="text-lg font-semibold mb-4">Registration Details:</h4>
            <div class="grid grid-cols-2 gap-4">
              <div>
                <p class="font-medium">Host:</p>
                <p class="text-gray-600">{{ responseData?.host }}</p>
              </div>
              <div>
                <p class="font-medium">Username:</p>
                <p class="text-gray-600">{{ responseData?.username }}</p>
              </div>
              <div>
                <p class="font-medium">Database Name:</p>
                <p class="text-gray-600">{{ responseData?.database_name }}</p>
              </div>
              <div>
                <p class="font-medium">Database User:</p>
                <p class="text-gray-600">{{ responseData?.database_user }}</p>
              </div>
              <div>
                <p class="font-medium">Created At:</p>
                <p class="text-gray-600">{{ responseData?.created_at ? new Date(responseData.created_at).toLocaleString() : '' }}</p>
              </div>
              <div>
                <p class="font-medium">Tenant ID:</p>
                <p class="text-gray-600">{{ responseData?.id }}</p>
              </div>
            </div>
          </div>

          <div class="flex justify-center space-x-4 mt-8">
            <button @click="currentStep = 1" 
                    class="px-6 py-2 bg-gray-500 text-white font-bold rounded-lg shadow-lg hover:bg-gray-600 transition-all">
              Update Information
            </button>
            <button @click="startDeployment" 
                    class="px-6 py-2 bg-green-500 text-white font-bold rounded-lg shadow-lg hover:bg-green-600 transition-all">
              Start Deployment
            </button>
          </div>
        </div>

        <!-- Final confirmation step -->
        <div v-if="currentStep === 5" class="text-center py-8">
          <div v-if="!deploymentComplete" class="spin-animation inline-block w-12 h-12 border-4 border-indigo-500 border-t-transparent rounded-full mb-4"></div>
          <h3 class="text-2xl font-bold text-indigo-600 mb-4">{{ deploymentStatus }}</h3>
          <div class="mt-4">
            <p class="text-gray-600">{{ deploymentMessage }}</p>
            <div v-if="deploymentComplete" class="mt-8">
              <div class="text-6xl mb-4">🎉</div>
              <h3 class="text-2xl font-bold text-green-600 mb-4">Setup Complete!</h3>
              <p class="text-gray-600">Your application is ready to use.</p>
              <p class="text-gray-600">You can now access your application at:</p>
              <a :href="'https://' + responseData?.host" target="_blank" 
                 class="block mt-4 text-blue-500 hover:text-blue-700 underline">
                {{ responseData?.host }}
              </a>
            </div>
          </div>
        </div>

        <!-- Navigation buttons -->
        <div class="flex justify-between pt-4">
          <button v-if="currentStep === 2"
                  @click="currentStep = 1"
                  class="px-8 py-3 bg-gray-500 text-white font-bold rounded-lg shadow-lg hover:bg-gray-600 transition-all transform hover:scale-105">
            Previous
          </button>
          <div class="flex-grow"></div>
          <button v-if="currentStep < 3" 
                  @click="handleStep" 
                  class="px-8 py-3 bg-gradient-to-r from-pink-500 to-orange-500 text-white font-bold rounded-lg shadow-lg hover:from-pink-600 hover:to-orange-600 transition-all transform hover:scale-105 hover:rotate-1">
            {{ currentStep === 1 ? 'Next Step' : 'Complete' }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref } from 'vue'

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

    const handleStep = () => {
      errors.value = {} // Clear previous errors
      tenantError.value = '' // Clear tenant error
      if (currentStep.value === 1) {
        currentStep.value = 2
      } else if (currentStep.value === 2) {
        currentStep.value = 3
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

    const startDeployment = async () => {
      currentStep.value = 5
      deploymentComplete.value = false

      try {
        // Step 1: Create Module
        deploymentStatus.value = "Creating Your Application"
        deploymentMessage.value = "Setting up your custom application environment..."
        await makeApiCall('create-module', {
          tenant_id: responseData.value.id,
          module: "tenant",
          submodule: "custom-app"
        })

        // Step 2: Deploy Module
        deploymentStatus.value = "Deploying Your Application"
        deploymentMessage.value = "Configuring servers and deploying your application..."
        await makeApiCall('deploy-module', {
          tenant_id: responseData.value.id,
          module: "tenant",
          submodule: "custom-app"
        })

        // Step 3: SSL Certificate
        deploymentStatus.value = "Securing Your Application"
        deploymentMessage.value = "Implementing SSL encryption for secure access..."
        await makeApiCall('ssl-cert', {
          tenant_id: responseData.value.id
        })

        // Step 4: Create Database
        deploymentStatus.value = "Finalizing Setup"
        deploymentMessage.value = "Creating and configuring your database..."
        await makeApiCall('create-tenant-db', {
          tenant_id: responseData.value.id
        })

        // Complete
        deploymentStatus.value = "Deployment Complete"
        deploymentMessage.value = "Your application has been successfully deployed!"
        deploymentComplete.value = true

      } catch (error) {
        console.error('Deployment error:', error)
        deploymentStatus.value = "Deployment Error"
        deploymentMessage.value = "An error occurred during deployment. Please try again."
      }
    }

    const submitForm = async () => {
      // Create separate payload with email and username
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
          currentStep.value = 4
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
              currentStep.value = 1
            } else if (Object.keys(data.errors).some(key => ['database_name', 'database_user', 'database_password'].includes(key))) {
              currentStep.value = 2
            }
          }
        }
      } catch (error) {
        console.error('Error submitting form:', error)
        // Handle error appropriately
      }
    }

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
      handleStep,
      submitForm,
      startDeployment
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

@keyframes pulse {
  0%, 100% {
    opacity: 1;
  }
  50% {
    opacity: 0.7;
  }
}

.animate-pulse {
  animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

@keyframes bounce {
  0%, 100% {
    transform: translateY(-25%);
    animation-timing-function: cubic-bezier(0.8, 0, 1, 1);
  }
  50% {
    transform: translateY(0);
    animation-timing-function: cubic-bezier(0, 0, 0.2, 1);
  }
}

.animate-bounce {
  animation: bounce 1s infinite;
}
</style>