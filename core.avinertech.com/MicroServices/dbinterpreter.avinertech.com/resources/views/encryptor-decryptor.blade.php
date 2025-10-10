<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="mb-8">
                        <h1 class="text-3xl font-bold text-gray-900 mb-2">Encryption/Decryption Utility</h1>
                        <p class="text-gray-600">Use this tool to encrypt and decrypt host names and other values using the custom AES-256-CBC encryption.</p>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                        <!-- Encryption Section -->
                        <div class="bg-gray-50 p-6 rounded-lg">
                            <h2 class="text-xl font-semibold text-gray-800 mb-4">🔒 Encrypt Value</h2>
                            <form id="encryptForm">
                                <div class="mb-4">
                                    <label for="encryptInput" class="block text-sm font-medium text-gray-700 mb-2">
                                        Value to Encrypt
                                    </label>
                                    <input type="text" 
                                           id="encryptInput" 
                                           name="value"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                                           placeholder="e.g., prototype.avinertech.com"
                                           required>
                                </div>
                                <button type="submit" 
                                        class="w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition duration-200">
                                    Encrypt
                                </button>
                            </form>
                            
                            <div id="encryptResult" class="mt-4 hidden">
                                <div class="bg-green-50 border border-green-200 rounded-md p-4">
                                    <h3 class="text-sm font-medium text-green-800 mb-2">Encrypted Result:</h3>
                                    <div class="bg-white p-3 rounded border text-sm font-mono break-all" id="encryptedValue"></div>
                                    <button onclick="copyToClipboard('encryptedValue')" 
                                            class="mt-2 text-sm bg-green-600 text-white px-3 py-1 rounded hover:bg-green-700">
                                        Copy
                                    </button>
                                </div>
                            </div>

                            <div id="encryptError" class="mt-4 hidden">
                                <div class="bg-red-50 border border-red-200 rounded-md p-4">
                                    <h3 class="text-sm font-medium text-red-800 mb-2">Error:</h3>
                                    <div class="text-sm text-red-700" id="encryptErrorMessage"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Decryption Section -->
                        <div class="bg-gray-50 p-6 rounded-lg">
                            <h2 class="text-xl font-semibold text-gray-800 mb-4">🔓 Decrypt Value</h2>
                            <form id="decryptForm">
                                <div class="mb-4">
                                    <label for="decryptInput" class="block text-sm font-medium text-gray-700 mb-2">
                                        Encrypted Value (Hex)
                                    </label>
                                    <textarea id="decryptInput" 
                                              name="value"
                                              rows="3"
                                              class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 font-mono text-sm"
                                              placeholder="Paste encrypted hex string here..."
                                              required></textarea>
                                </div>
                                <button type="submit" 
                                        class="w-full bg-green-600 text-white py-2 px-4 rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition duration-200">
                                    Decrypt
                                </button>
                            </form>
                            
                            <div id="decryptResult" class="mt-4 hidden">
                                <div class="bg-blue-50 border border-blue-200 rounded-md p-4">
                                    <h3 class="text-sm font-medium text-blue-800 mb-2">Decrypted Result:</h3>
                                    <div class="bg-white p-3 rounded border text-sm font-mono break-all" id="decryptedValue"></div>
                                    <button onclick="copyToClipboard('decryptedValue')" 
                                            class="mt-2 text-sm bg-blue-600 text-white px-3 py-1 rounded hover:bg-blue-700">
                                        Copy
                                    </button>
                                </div>
                            </div>

                            <div id="decryptError" class="mt-4 hidden">
                                <div class="bg-red-50 border border-red-200 rounded-md p-4">
                                    <h3 class="text-sm font-medium text-red-800 mb-2">Error:</h3>
                                    <div class="text-sm text-red-700" id="decryptErrorMessage"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Usage Examples -->
                    <div class="mt-8 bg-gray-50 p-6 rounded-lg">
                        <h2 class="text-xl font-semibold text-gray-800 mb-4">📖 Usage Examples</h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <h3 class="font-medium text-gray-700 mb-2">Common Host Names:</h3>
                                <ul class="text-sm text-gray-600 space-y-1">
                                    <li><code class="bg-gray-200 px-2 py-1 rounded">prototype.avinertech.com</code></li>
                                    <li><code class="bg-gray-200 px-2 py-1 rounded">api.example.com</code></li>
                                    <li><code class="bg-gray-200 px-2 py-1 rounded">client.domain.org</code></li>
                                </ul>
                            </div>
                            <div>
                                <h3 class="font-medium text-gray-700 mb-2">API Endpoints:</h3>
                                <ul class="text-sm text-gray-600 space-y-1">
                                    <li><strong>Encrypt:</strong> <code class="bg-gray-200 px-2 py-1 rounded">POST /api/encrypt</code></li>
                                    <li><strong>Decrypt:</strong> <code class="bg-gray-200 px-2 py-1 rounded">POST /api/decrypt</code></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Encrypt form handler
        document.getElementById('encryptForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const value = formData.get('value');
            
            // Hide previous results
            document.getElementById('encryptResult').classList.add('hidden');
            document.getElementById('encryptError').classList.add('hidden');
            
            try {
                const response = await fetch('/api/encrypt', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Authorization': document.querySelector('meta[name="access-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ value: value })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    document.getElementById('encryptedValue').textContent = data.encrypted;
                    document.getElementById('encryptResult').classList.remove('hidden');
                } else {
                    document.getElementById('encryptErrorMessage').textContent = data.error;
                    document.getElementById('encryptError').classList.remove('hidden');
                }
            } catch (error) {
                document.getElementById('encryptErrorMessage').textContent = 'Network error: ' + error.message;
                document.getElementById('encryptError').classList.remove('hidden');
            }
        });

        // Decrypt form handler
        document.getElementById('decryptForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const value = formData.get('value');
            
            // Hide previous results
            document.getElementById('decryptResult').classList.add('hidden');
            document.getElementById('decryptError').classList.add('hidden');
            
            try {
                const response = await fetch('/api/decrypt', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Authorization': document.querySelector('meta[name="access-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ value: value })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    document.getElementById('decryptedValue').textContent = data.decrypted;
                    document.getElementById('decryptResult').classList.remove('hidden');
                } else {
                    document.getElementById('decryptErrorMessage').textContent = data.error;
                    document.getElementById('decryptError').classList.remove('hidden');
                }
            } catch (error) {
                document.getElementById('decryptErrorMessage').textContent = 'Network error: ' + error.message;
                document.getElementById('decryptError').classList.remove('hidden');
            }
        });

        // Copy to clipboard function
        function copyToClipboard(elementId) {
            const element = document.getElementById(elementId);
            const text = element.textContent;
            
            navigator.clipboard.writeText(text).then(function() {
                // Show temporary success message
                const button = event.target;
                const originalText = button.textContent;
                button.textContent = 'Copied!';
                button.classList.add('bg-gray-600');
                
                setTimeout(function() {
                    button.textContent = originalText;
                    button.classList.remove('bg-gray-600');
                }, 2000);
            }).catch(function(err) {
                console.error('Could not copy text: ', err);
                alert('Could not copy to clipboard');
            });
        }
    </script>
</x-app-layout> 