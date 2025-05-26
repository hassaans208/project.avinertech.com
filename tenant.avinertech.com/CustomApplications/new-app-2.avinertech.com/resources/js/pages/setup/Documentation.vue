<template>
  <div class="space-y-8">
    <div class="flex justify-between items-center">
      <h1 class="text-2xl font-bold text-white">Application Documentation</h1>
    </div>

    <!-- Application Flow Overview -->
    <div class="bg-white/5 backdrop-blur-lg rounded-xl p-6 border border-white/10">
      <h2 class="text-xl font-semibold text-white mb-4">Application Flow Overview</h2>
      
      <!-- Flow Diagram -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        <div class="space-y-4">
          <div class="bg-gradient-to-br from-blue-500/20 to-purple-500/20 p-4 rounded-lg border border-white/10">
            <h3 class="text-lg font-medium text-white mb-2">1. Initial Setup</h3>
            <ul class="list-disc list-inside text-gray-300 space-y-2">
              <li>User registration and authentication</li>
              <li>Database configuration</li>
              <li>System environment setup</li>
              <li>Initial schema creation</li>
            </ul>
          </div>

          <div class="bg-gradient-to-br from-green-500/20 to-teal-500/20 p-4 rounded-lg border border-white/10">
            <h3 class="text-lg font-medium text-white mb-2">2. Application Builder</h3>
            <ul class="list-disc list-inside text-gray-300 space-y-2">
              <li>Plugin integration and configuration</li>
              <li>Theme selection and customization</li>
              <li>Schema management and modification</li>
              <li>Build process and deployment</li>
            </ul>
          </div>
        </div>

        <div class="space-y-4">
          <div class="bg-gradient-to-br from-purple-500/20 to-pink-500/20 p-4 rounded-lg border border-white/10">
            <h3 class="text-lg font-medium text-white mb-2">3. Subscription Management</h3>
            <ul class="list-disc list-inside text-gray-300 space-y-2">
              <li>Plan selection and subscription</li>
              <li>Payment processing</li>
              <li>Usage monitoring</li>
              <li>Billing management</li>
            </ul>
          </div>

          <div class="bg-gradient-to-br from-yellow-500/20 to-orange-500/20 p-4 rounded-lg border border-white/10">
            <h3 class="text-lg font-medium text-white mb-2">4. System Maintenance</h3>
            <ul class="list-disc list-inside text-gray-300 space-y-2">
              <li>Regular updates and patches</li>
              <li>Performance monitoring</li>
              <li>Backup management</li>
              <li>Security maintenance</li>
            </ul>
          </div>
        </div>
      </div>

      <!-- Detailed Flow Description -->
      <div class="space-y-6">
        <div class="prose prose-invert max-w-none">
          <h3 class="text-lg font-medium text-white mb-4">Detailed Process Flow</h3>
          
          <div class="space-y-4">
            <div class="bg-white/5 p-4 rounded-lg">
              <h4 class="text-blue-400 font-medium mb-2">Authentication Flow</h4>
              <p class="text-gray-300">
                The application uses a secure authentication system with JWT tokens. Users can register, login, and manage their credentials. The system supports role-based access control (RBAC) for different user types.
              </p>
            </div>

            <!-- New Multi-Auth and Roles Section -->
            <div class="bg-white/5 p-4 rounded-lg">
              <h4 class="text-indigo-400 font-medium mb-2">Multi-Authentication & Role Management</h4>
              <div class="space-y-4">
                <div>
                  <h5 class="text-white font-medium mb-2">Authentication Methods</h5>
                  <ul class="list-disc list-inside text-gray-300 space-y-1">
                    <li>JWT Token-based Authentication</li>
                    <li>Session-based Authentication</li>
                    <li>OAuth2 Integration (Google, GitHub)</li>
                    <li>Two-Factor Authentication (2FA)</li>
                  </ul>
                </div>

                <div>
                  <h5 class="text-white font-medium mb-2">User Roles</h5>
                  <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-2">
                    <div class="bg-white/5 p-3 rounded-lg">
                      <h6 class="text-blue-300 font-medium mb-1">Admin</h6>
                      <ul class="text-sm text-gray-300 space-y-1">
                        <li>Full system access</li>
                        <li>User management</li>
                        <li>System configuration</li>
                        <li>Plugin management</li>
                      </ul>
                    </div>
                    <div class="bg-white/5 p-3 rounded-lg">
                      <h6 class="text-green-300 font-medium mb-1">Manager</h6>
                      <ul class="text-sm text-gray-300 space-y-1">
                        <li>Team management</li>
                        <li>Content management</li>
                        <li>Limited system access</li>
                        <li>Report generation</li>
                      </ul>
                    </div>
                    <div class="bg-white/5 p-3 rounded-lg">
                      <h6 class="text-purple-300 font-medium mb-1">User</h6>
                      <ul class="text-sm text-gray-300 space-y-1">
                        <li>Basic system access</li>
                        <li>Personal settings</li>
                        <li>Content viewing</li>
                        <li>Limited actions</li>
                      </ul>
                    </div>
                  </div>
                </div>

                <div>
                  <h5 class="text-white font-medium mb-2">Permission System</h5>
                  <div class="space-y-3">
                    <div class="bg-white/5 p-3 rounded-lg">
                      <h6 class="text-yellow-300 font-medium mb-1">Permission Types</h6>
                      <ul class="text-sm text-gray-300 space-y-1">
                        <li>Resource-based permissions (CRUD operations)</li>
                        <li>Feature-based permissions (specific functionalities)</li>
                        <li>Role-based permissions (inherited from roles)</li>
                        <li>Custom permissions (user-specific)</li>
                      </ul>
                    </div>
                    <div class="bg-white/5 p-3 rounded-lg">
                      <h6 class="text-yellow-300 font-medium mb-1">Permission Management</h6>
                      <ul class="text-sm text-gray-300 space-y-1">
                        <li>Hierarchical permission structure</li>
                        <li>Permission inheritance</li>
                        <li>Permission overrides</li>
                        <li>Permission caching</li>
                      </ul>
                    </div>
                  </div>
                </div>

                <div>
                  <h5 class="text-white font-medium mb-2">Security Features</h5>
                  <ul class="list-disc list-inside text-gray-300 space-y-1">
                    <li>Role-based access control (RBAC)</li>
                    <li>Permission-based access control (PBAC)</li>
                    <li>Session management and timeout</li>
                    <li>IP-based access restrictions</li>
                    <li>Activity logging and audit trails</li>
                  </ul>
                </div>
              </div>
            </div>

            <div class="bg-white/5 p-4 rounded-lg">
              <h4 class="text-green-400 font-medium mb-2">Plugin System</h4>
              <p class="text-gray-300">
                The application features a modular plugin system that allows for easy integration of additional functionality. Current plugins include Authentication, Mailer, and Payments. Each plugin can be configured independently.
              </p>
            </div>

            <div class="bg-white/5 p-4 rounded-lg">
              <h4 class="text-purple-400 font-medium mb-2">Theme Management</h4>
              <p class="text-gray-300">
                Users can select from multiple pre-built themes or customize their own. The theme system supports both light and dark modes, with responsive design for all screen sizes.
              </p>
            </div>

            <div class="bg-white/5 p-4 rounded-lg">
              <h4 class="text-yellow-400 font-medium mb-2">Build Process</h4>
              <p class="text-gray-300">
                The build process compiles all configurations, plugins, and themes into a production-ready application. It includes schema validation, dependency checking, and optimization steps.
              </p>
            </div>
          </div>
        </div>
      </div>

      <!-- Technical Requirements -->
      <div class="mt-8">
        <h3 class="text-lg font-medium text-white mb-4">Technical Requirements</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div class="bg-white/5 p-4 rounded-lg">
            <h4 class="text-white font-medium mb-2">Server Requirements</h4>
            <ul class="list-disc list-inside text-gray-300 space-y-1">
              <li>PHP 8.1 or higher</li>
              <li>MySQL 8.0 or higher</li>
              <li>Node.js 16.x or higher</li>
              <li>Composer 2.x</li>
            </ul>
          </div>
          <div class="bg-white/5 p-4 rounded-lg">
            <h4 class="text-white font-medium mb-2">Client Requirements</h4>
            <ul class="list-disc list-inside text-gray-300 space-y-1">
              <li>Modern web browser</li>
              <li>JavaScript enabled</li>
              <li>Minimum screen resolution: 1024x768</li>
              <li>Stable internet connection</li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import DashboardLayout from './DashboardLayout.vue';

defineOptions({
  layout: DashboardLayout,
});
// Component logic can be added here if needed
</script>

<style scoped>
.prose :deep(h1) { @refernce text-2xl font-bold text-white mb-4; }
.prose :deep(h2) { @refernce text-xl font-semibold text-white mb-3; }
.prose :deep(h3) { @refernce text-lg font-medium text-white mb-2; }
.prose :deep(p) { @refernce text-gray-300 mb-4; }
.prose :deep(ul) { @refernce list-disc list-inside text-gray-300 space-y-2; }
</style> 