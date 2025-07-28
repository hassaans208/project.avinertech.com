# Signal API Documentation for Frontend Integration

**Base URL:** `https://signal.avinertech.com`

## Overview

The Signal API provides a secure authentication and authorization system for client applications. It uses encrypted host identifiers and time-based tokens to establish secure communication channels.

## Authentication Flow

### 1. Host Encryption
Before using the Signal API, you need to encrypt your host identifier using the encryption endpoint.

### 2. Session Management
The system uses browser session storage to maintain authentication state with keys:
- `_secret_hash_{encrypted_host}`: Stores the secret hash for subsequent API calls
- `app_signature_{encrypted_host}`: Stores the application signature after successful authentication

### 3. Periodic Signal Verification
The system requires signal verification every 3 hours or when the app opens to maintain active status.

---

## API Endpoints

### 1. Host Encryption Endpoint

**Endpoint:** `POST /api/encrypt`

**Purpose:** Encrypt your host identifier before using the Signal API.

**Request:**
```bash
curl -X POST https://signal.avinertech.com/api/encrypt \
  -H "Content-Type: application/json" \
  -d '{
    "value": "your-app-domain.com"
  }'
```

**Success Response (200):**
```json
{
  "success": true,
  "original": "your-app-domain.com",
  "encrypted": "a1b2c3d4e5f6789abcdef0123456789abcdef0123456789abcdef"
}
```

**Error Response (400):**
```json
{
  "success": false,
  "error": "Encryption failed: Invalid input"
}
```

---

### 2. Signal API Endpoint

**Endpoint:** `POST /{encryptedHostId}/signal`

**Purpose:** Establish or verify signal connection with the authentication server.

#### First-time Connection (No stored hash)

**Request:**
```bash
curl -X POST https://signal.avinertech.com/{encrypted_host_id}/signal \
  -H "Content-Type: application/json" \
  -d '{
    "hash": ""
  }'
```

**Success Response - Hash Creation (200):**
```json
{
  "success": true,
  "action": "create_hash",
  "action_data": "basic_package:2024:01:15:10:your-app-domain.com",
  "message": "Secret hash generated for new connection"
}
```

#### Subsequent Connections (With stored hash)

**Request:**
```bash
curl -X POST https://signal.avinertech.com/{encrypted_host_id}/signal \
  -H "Content-Type: application/json" \
  -d '{
    "hash": "basic_package:2024:01:15:10:your-app-domain.com"
  }'
```

**Success Response - Generate Response (200):**
```json
{
  "success": true,
  "action": "generate_response",
  "data": {
    "tenant_id": 1,
    "tenant_host": "your-app-domain.com",
    "tenant_name": "Your App Domain Tenant",
    "package_id": 2,
    "package_name": "basic_package",
    "package_cost": "29.99",
    "package_currency": "USD",
    "package_tax_rate": "0.0825",
    "package_modules": ["api_access", "analytics"],
    "signal_timestamp": "2024-01-15T10:00:00.000Z",
    "processed_at": "2024-01-15T10:00:05.000Z",
    "expires_at": "2024-01-15T11:00:05.000Z"
  },
  "signature": "sha256_signature_hash_here"
}
```

**Error Responses:**

**Invalid Client (400):**
```json
{
  "error": "Invalid Client – contact sales@avinertech.com"
}
```

**Server Error (500):**
```json
{
  "error": "Internal server error"
}
```

---

## Frontend Implementation Guide

### 1. Initial Setup

```javascript
// Configuration
const SIGNAL_BASE_URL = 'https://signal.avinertech.com';
const YOUR_HOST = 'your-app-domain.com'; // Replace with your actual domain
const SIGNAL_CHECK_INTERVAL = 3 * 60 * 60 * 1000; // 3 hours in milliseconds

// Get encrypted host ID
async function getEncryptedHost() {
    const response = await fetch(`${SIGNAL_BASE_URL}/api/encrypt`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ value: YOUR_HOST })
    });
    
    const data = await response.json();
    if (data.success) {
        return data.encrypted;
    }
    throw new Error('Failed to encrypt host');
}
```

### 2. Modal Implementation

```javascript
// Create signal modal
function createSignalModal() {
    const modal = document.createElement('div');
    modal.id = 'signal-modal';
    modal.style.cssText = `
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(59, 130, 246, 0.9);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 10000;
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    `;
    
    modal.innerHTML = `
        <div style="text-align: center; color: white;">
            <div id="signal-loader" style="
                width: 60px;
                height: 60px;
                border: 4px solid rgba(255,255,255,0.3);
                border-top: 4px solid white;
                border-radius: 50%;
                animation: spin 1s linear infinite;
                margin: 0 auto 20px;
            "></div>
            <div id="signal-status" style="font-size: 18px; font-weight: 500;">
                Connecting...
            </div>
        </div>
        <style>
            @keyframes spin {
                0% { transform: rotate(0deg); }
                100% { transform: rotate(360deg); }
            }
        </style>
    `;
    
    document.body.appendChild(modal);
    return modal;
}

// Show success state
function showSignalSuccess(modal) {
    const loader = modal.querySelector('#signal-loader');
    const status = modal.querySelector('#signal-status');
    
    loader.style.cssText = `
        width: 60px;
        height: 60px;
        background: #10B981;
        border-radius: 50%;
        display: flex;
        justify-content: center;
        align-items: center;
        margin: 0 auto 20px;
        font-size: 30px;
    `;
    loader.innerHTML = '✓';
    status.textContent = 'Signal Established';
    
    setTimeout(() => {
        document.body.removeChild(modal);
    }, 2000);
}
```

### 3. Signal API Integration

```javascript
// Main signal function
async function establishSignal(showModal = true) {
    let modal = null;
    
    if (showModal) {
        modal = createSignalModal();
    }
    
    try {
        const encryptedHost = await getEncryptedHost();
        const sessionKey = `_secret_hash_${encryptedHost}`;
        const signatureKey = `app_signature_${encryptedHost}`;
        
        // Get stored hash from session
        const storedHash = sessionStorage.getItem(sessionKey) || '';
        
        // Make signal API call
        const response = await fetch(`${SIGNAL_BASE_URL}/${encryptedHost}/signal`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ hash: storedHash })
        });
        
        if (response.status === 500 || !response.ok) {
            throw new Error('Server error or blocked');
        }
        
        const data = await response.json();
        
        if (!data.success) {
            throw new Error('Invalid client');
        }
        
        // Handle response based on action
        if (data.action === 'create_hash') {
            // First time connection - store the hash
            sessionStorage.setItem(sessionKey, data.action_data);
        } else if (data.action === 'generate_response') {
            // Subsequent connection - store signature
            sessionStorage.setItem(signatureKey, data.signature);
        }
        
        // Show success
        if (modal) {
            showSignalSuccess(modal);
        }
        
        // Update header indicator
        updateHeaderIndicator(true);
        
        return true;
        
    } catch (error) {
        if (modal) {
            document.body.removeChild(modal);
        }
        
        // Redirect to blocked screen
        showBlockedScreen();
        return false;
    }
}
```

### 4. Header Status Indicator

```javascript
// Create header status indicator
function createHeaderIndicator() {
    const indicator = document.createElement('div');
    indicator.id = 'signal-indicator';
    indicator.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        width: 24px;
        height: 24px;
        border-radius: 50%;
        z-index: 1000;
        display: flex;
        justify-content: center;
        align-items: center;
        font-size: 12px;
        color: white;
        font-weight: bold;
    `;
    
    document.body.appendChild(indicator);
    return indicator;
}

// Update header indicator
function updateHeaderIndicator(isConnected) {
    let indicator = document.getElementById('signal-indicator');
    if (!indicator) {
        indicator = createHeaderIndicator();
    }
    
    if (isConnected) {
        indicator.style.background = '#10B981';
        indicator.innerHTML = '✓';
    } else {
        indicator.style.background = '#EF4444';
        indicator.innerHTML = '✗';
    }
}
```

### 5. Blocked Screen Implementation

```javascript
// Show application blocked screen
function showBlockedScreen() {
    document.body.innerHTML = `
        <div style="
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background: linear-gradient(135deg, #EF4444, #DC2626);
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            margin: 0;
            padding: 20px;
        ">
            <div style="
                background: white;
                padding: 40px;
                border-radius: 12px;
                box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
                text-align: center;
                max-width: 500px;
                width: 100%;
            ">
                <div style="
                    width: 80px;
                    height: 80px;
                    background: #FEE2E2;
                    border-radius: 50%;
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    margin: 0 auto 24px;
                    font-size: 40px;
                    color: #EF4444;
                ">
                    🚫
                </div>
                
                <h1 style="
                    color: #1F2937;
                    font-size: 28px;
                    font-weight: 700;
                    margin: 0 0 16px;
                ">
                    Application Blocked
                </h1>
                
                <p style="
                    color: #6B7280;
                    font-size: 16px;
                    line-height: 1.6;
                    margin: 0 0 32px;
                ">
                    Your access to this application has been restricted. 
                    Please contact our sales team for assistance.
                </p>
                
                <a href="mailto:sales@avinertech.com" style="
                    display: inline-block;
                    background: #EF4444;
                    color: white;
                    padding: 12px 24px;
                    border-radius: 8px;
                    text-decoration: none;
                    font-weight: 600;
                    font-size: 16px;
                    transition: background 0.2s;
                " 
                onmouseover="this.style.background='#DC2626'"
                onmouseout="this.style.background='#EF4444'">
                    Contact Sales Team
                </a>
            </div>
        </div>
    `;
}
```

### 6. Application Initialization

```javascript
// Initialize signal system
async function initializeSignalSystem() {
    // Initial connection with modal
    const connected = await establishSignal(true);
    
    if (connected) {
        // Set up periodic checks (every 3 hours)
        setInterval(async () => {
            updateHeaderIndicator(false); // Show loading state
            await establishSignal(false); // No modal for periodic checks
        }, SIGNAL_CHECK_INTERVAL);
        
        // Check signal when app regains focus
        window.addEventListener('focus', async () => {
            updateHeaderIndicator(false);
            await establishSignal(false);
        });
    }
}

// Start the system when DOM is ready
document.addEventListener('DOMContentLoaded', initializeSignalSystem);
```

---

## Implementation Checklist

- [ ] **Setup Configuration**: Replace `YOUR_HOST` with actual domain
- [ ] **Modal Integration**: Implement blue loading modal with no close option
- [ ] **Session Management**: Store `_secret_hash_` and `app_signature_` keys
- [ ] **Header Indicator**: Add persistent status indicator
- [ ] **Blocked Screen**: Implement application blocked screen
- [ ] **Periodic Checks**: Set up 3-hour interval checks
- [ ] **Focus Events**: Handle app focus for signal verification
- [ ] **Error Handling**: Redirect to blocked screen on failures

---

## Security Notes

1. **Encrypted Host IDs**: Always encrypt host identifiers before API calls
2. **Session Storage**: Use browser session storage for temporary data
3. **Token Expiration**: Tokens expire after 1 hour and need renewal
4. **Error Handling**: Always redirect to blocked screen on authentication failures
5. **No Bypass**: Do not allow users to bypass the signal verification system

---

## Support

For technical support or integration questions, contact: **sales@avinertech.com** 