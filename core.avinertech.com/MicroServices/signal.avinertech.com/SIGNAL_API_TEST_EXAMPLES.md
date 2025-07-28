# Signal API Test Examples

## Quick Test Commands

### 1. Test Host Encryption

```bash
# Encrypt a host
curl -X POST https://signal.avinertech.com/api/encrypt \
  -H "Content-Type: application/json" \
  -d '{"value": "prototype.avinertech.com"}'
```

**Expected Response:**
```json
{
  "success": true,
  "original": "prototype.avinertech.com",
  "encrypted": "a1b2c3d4e5f6789abcdef0123456789abcdef0123456789abcdef"
}
```

### 2. First-Time Signal Connection (Empty Hash)

```bash
# Replace {encrypted_host_id} with the encrypted value from step 1
curl -X POST https://signal.avinertech.com/{encrypted_host_id}/signal \
  -H "Content-Type: application/json" \
  -d '{"hash": ""}'
```

**Expected Response:**
```json
{
  "success": true,
  "action": "create_hash",
  "action_data": "free_package:2024:01:15:10:prototype.avinertech.com",
  "message": "Secret hash generated for new connection"
}
```

### 3. Subsequent Signal Connection (With Hash)

```bash
# Use the action_data from step 2 as the hash value
curl -X POST https://signal.avinertech.com/{encrypted_host_id}/signal \
  -H "Content-Type: application/json" \
  -d '{"hash": "free_package:2024:01:15:10:prototype.avinertech.com"}'
```

**Expected Response:**
```json
{
  "success": true,
  "action": "generate_response",
  "data": {
    "tenant_id": 1,
    "tenant_host": "prototype.avinertech.com",
    "tenant_name": "Prototype Avinertech Com Tenant",
    "package_id": 1,
    "package_name": "free_package",
    "package_cost": "0.00",
    "package_currency": "USD",
    "package_tax_rate": "0.0000",
    "package_modules": ["api_access"],
    "signal_timestamp": "2024-01-15T10:00:00.000Z",
    "processed_at": "2024-01-15T10:00:05.000Z",
    "expires_at": "2024-01-15T11:00:05.000Z"
  },
  "signature": "sha256_signature_hash_here"
}
```

## Complete JavaScript Test Implementation

```html
<!DOCTYPE html>
<html>
<head>
    <title>Signal API Test</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        .test-section { margin: 20px 0; padding: 15px; border: 1px solid #ddd; }
        .success { background: #d4edda; border-color: #c3e6cb; }
        .error { background: #f8d7da; border-color: #f5c6cb; }
        pre { background: #f8f9fa; padding: 10px; overflow-x: auto; }
        button { padding: 10px 15px; margin: 5px; cursor: pointer; }
    </style>
</head>
<body>
    <h1>Signal API Test Interface</h1>
    
    <div class="test-section">
        <h3>Configuration</h3>
        <input type="text" id="hostInput" value="prototype.avinertech.com" placeholder="Your host domain">
        <button onclick="runFullTest()">Run Complete Test</button>
    </div>
    
    <div id="results"></div>

    <script>
        const SIGNAL_BASE_URL = 'https://signal.avinertech.com';
        
        function log(message, type = 'info') {
            const results = document.getElementById('results');
            const div = document.createElement('div');
            div.className = `test-section ${type}`;
            div.innerHTML = `<pre>${message}</pre>`;
            results.appendChild(div);
        }
        
        async function runFullTest() {
            const host = document.getElementById('hostInput').value;
            document.getElementById('results').innerHTML = '';
            
            try {
                // Step 1: Encrypt host
                log('Step 1: Encrypting host...', 'info');
                const encryptResponse = await fetch(`${SIGNAL_BASE_URL}/api/encrypt`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ value: host })
                });
                
                const encryptData = await encryptResponse.json();
                log(`Encryption Result:\n${JSON.stringify(encryptData, null, 2)}`, 'success');
                
                if (!encryptData.success) {
                    throw new Error('Encryption failed');
                }
                
                const encryptedHost = encryptData.encrypted;
                
                // Step 2: First signal call (empty hash)
                log('Step 2: First signal call (empty hash)...', 'info');
                const firstSignalResponse = await fetch(`${SIGNAL_BASE_URL}/${encryptedHost}/signal`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ hash: '' })
                });
                
                const firstSignalData = await firstSignalResponse.json();
                log(`First Signal Result:\n${JSON.stringify(firstSignalData, null, 2)}`, 'success');
                
                if (!firstSignalData.success) {
                    throw new Error('First signal call failed');
                }
                
                // Step 3: Second signal call (with hash)
                log('Step 3: Second signal call (with hash)...', 'info');
                const secondSignalResponse = await fetch(`${SIGNAL_BASE_URL}/${encryptedHost}/signal`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ hash: firstSignalData.action_data })
                });
                
                const secondSignalData = await secondSignalResponse.json();
                log(`Second Signal Result:\n${JSON.stringify(secondSignalData, null, 2)}`, 'success');
                
                // Simulate session storage
                log('Session Storage Simulation:', 'info');
                log(`_secret_hash_${encryptedHost} = ${firstSignalData.action_data}`, 'info');
                log(`app_signature_${encryptedHost} = ${secondSignalData.signature}`, 'info');
                
                log('✅ All tests completed successfully!', 'success');
                
            } catch (error) {
                log(`❌ Test failed: ${error.message}`, 'error');
            }
        }
        
        // Auto-run test on page load
        window.onload = () => runFullTest();
    </script>
</body>
</html>
```

## Error Scenarios

### Invalid Encrypted Host ID
```bash
curl -X POST https://signal.avinertech.com/invalid-host/signal \
  -H "Content-Type: application/json" \
  -d '{"hash": ""}'
```

**Expected Response (400):**
```json
{
  "error": "Invalid Client – contact sales@avinertech.com"
}
```

### Expired Hash
```bash
# Use an old timestamp in the hash
curl -X POST https://signal.avinertech.com/{encrypted_host_id}/signal \
  -H "Content-Type: application/json" \
  -d '{"hash": "free_package:2024:01:14:08:prototype.avinertech.com"}'
```

**Expected Response (400):**
```json
{
  "error": "Invalid Client – contact sales@avinertech.com"
}
```

### Blocked Tenant
```bash
# If tenant is blocked in admin panel
curl -X POST https://signal.avinertech.com/{encrypted_host_id}/signal \
  -H "Content-Type: application/json" \
  -d '{"hash": ""}'
```

**Expected Response (400):**
```json
{
  "error": "Invalid Client – contact sales@avinertech.com"
}
```

## Integration Checklist for Lovable.dev

- [ ] Replace `prototype.avinertech.com` with actual client domain
- [ ] Implement the modal with blue background and loader
- [ ] Add session storage management for hash and signature
- [ ] Create header indicator with green tick
- [ ] Implement blocked screen with red theme
- [ ] Set up 3-hour periodic checks
- [ ] Handle app focus events for signal verification
- [ ] Test all error scenarios
- [ ] Ensure no close option on modal
- [ ] Implement proper error handling and redirects

## Support Commands

```bash
# Test if API is up
curl https://signal.avinertech.com/api/up

# Check encryption endpoint
curl -X POST https://signal.avinertech.com/api/encrypt \
  -H "Content-Type: application/json" \
  -d '{"value": "test.com"}'

# Test decryption endpoint
curl -X POST https://signal.avinertech.com/api/decrypt \
  -H "Content-Type: application/json" \
  -d '{"value": "encrypted_hex_string_here"}'
``` 