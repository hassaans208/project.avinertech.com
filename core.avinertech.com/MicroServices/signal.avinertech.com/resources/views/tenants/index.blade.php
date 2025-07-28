<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Tenants') }}
            </h2>
            <a href="{{ route('tenants.create', ['access_token' => $accessToken]) }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                Add New Tenant
            </a>
        </div>
    </x-slot>

    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
        <div class="p-6">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Host</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Current Package</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($tenants as $tenant)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{ $tenant->name }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $tenant->host }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        @if($tenant->status === 'active') bg-green-100 text-green-800
                                        @elseif($tenant->status === 'blocked') bg-red-100 text-red-800
                                        @else bg-yellow-100 text-yellow-800 @endif">
                                        {{ ucfirst($tenant->status) }}
                                    </span>
                                    @if($tenant->status === 'blocked' && $tenant->block_reason)
                                        <div class="text-xs text-gray-500 mt-1">{{ $tenant->block_reason }}</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    @php $currentPackage = $tenant->getCurrentPackage(); @endphp
                                    @if($currentPackage)
                                        <span class="font-medium">{{ $currentPackage->name }}</span>
                                        <div class="text-xs text-gray-400">${{ $currentPackage->cost }} {{ $currentPackage->currency }}</div>
                                    @else
                                        <span class="text-gray-400">No package</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $tenant->created_at->format('M d, Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                    <a href="{{ route('tenants.show', ['id' => $tenant->id, 'access_token' => $accessToken]) }}" class="text-indigo-600 hover:text-indigo-900">View</a>
                                    <a href="{{ route('tenants.edit', ['id' => $tenant->id, 'access_token' => $accessToken]) }}" class="text-blue-600 hover:text-blue-900">Edit</a>
                                    
                                    @if($tenant->status === 'blocked')
                                        <form action="{{ route('tenants.unblock', ['id' => $tenant->id, 'access_token' => $accessToken]) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="text-green-600 hover:text-green-900" onclick="return confirm('Are you sure you want to unblock this tenant?')">
                                                Unblock
                                            </button>
                                        </form>
                                    @else
                                        <button onclick="showBlockModal({{ $tenant->id }})" class="text-red-600 hover:text-red-900">
                                            Block
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                    No tenants found. <a href="{{ route('tenants.create', ['access_token' => $accessToken]) }}" class="text-blue-600 hover:text-blue-900">Create one now</a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($tenants->hasPages())
                <div class="mt-6">
                    {{ $tenants->appends(['access_token' => $accessToken])->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- Block Modal -->
    <div id="blockModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Block Tenant</h3>
                <form id="blockForm" method="POST">
                    @csrf
                    <input type="hidden" name="access_token" value="{{ $accessToken }}">
                    <div class="mb-4">
                        <label for="block_reason" class="block text-sm font-medium text-gray-700 mb-2">Block Reason</label>
                        <textarea name="block_reason" id="block_reason" rows="3" required
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                                  placeholder="Enter reason for blocking this tenant..."></textarea>
                    </div>
                    <div class="flex justify-end space-x-3">
                        <button type="button" onclick="hideBlockModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400">
                            Cancel
                        </button>
                        <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">
                            Block Tenant
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function showBlockModal(tenantId) {
            document.getElementById('blockForm').action = `/tenants/${tenantId}/block?access_token={{ $accessToken }}`;
            document.getElementById('blockModal').classList.remove('hidden');
        }

        function hideBlockModal() {
            document.getElementById('blockModal').classList.add('hidden');
            document.getElementById('block_reason').value = '';
        }
    </script>
</x-app-layout> 