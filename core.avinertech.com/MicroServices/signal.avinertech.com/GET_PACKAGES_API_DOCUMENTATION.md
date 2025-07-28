# Get Packages API Documentation

**Base URL:** `https://signal.avinertech.com`

## Overview

The Get Packages API provides access to all available packages in the system, including their modules, pricing, and configuration details. This endpoint is useful for displaying package information, building pricing tables, or integrating package data into client applications.

---

## API Endpoint

### Get All Packages

**Endpoint:** `GET /api/packages`

**Purpose:** Retrieve all packages with their modules and detailed information.

**Authentication:** No authentication required (public endpoint)

---

## Request

### HTTP Method
```
GET
```

### URL
```
https://signal.avinertech.com/api/packages
```

### Headers
```
Content-Type: application/json
Accept: application/json
```

### cURL Example
```bash
curl -X GET https://signal.avinertech.com/api/packages \
  -H "Content-Type: application/json" \
  -H "Accept: application/json"
```

### JavaScript Example
```javascript
const response = await fetch('https://signal.avinertech.com/api/packages', {
    method: 'GET',
    headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json'
    }
});

const data = await response.json();
console.log(data);
```

---

## Response

### Success Response (200)

```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "name": "free_package",
      "cost": "0.00",
      "currency": "USD",
      "tax_rate": "0.0000",
      "modules": [
        "api_access"
      ],
      "is_free": true,
      "formatted_cost": "$0.00 USD",
      "available_modules": [
        "api_access",
        "analytics",
        "payment_methods",
        "ai_integration",
        "advanced_reporting",
        "custom_branding",
        "priority_support",
        "multi_user_access",
        "data_export",
        "webhook_integration"
      ],
      "created_at": "2024-01-15T10:00:00.000Z",
      "updated_at": "2024-01-15T10:00:00.000Z"
    },
    {
      "id": 2,
      "name": "basic_package",
      "cost": "29.99",
      "currency": "USD",
      "tax_rate": "0.0825",
      "modules": [
        "api_access",
        "analytics",
        "payment_methods"
      ],
      "is_free": false,
      "formatted_cost": "$29.99 USD",
      "available_modules": [
        "api_access",
        "analytics",
        "payment_methods",
        "ai_integration",
        "advanced_reporting",
        "custom_branding",
        "priority_support",
        "multi_user_access",
        "data_export",
        "webhook_integration"
      ],
      "created_at": "2024-01-15T10:00:00.000Z",
      "updated_at": "2024-01-15T10:00:00.000Z"
    },
    {
      "id": 3,
      "name": "premium_package",
      "cost": "99.99",
      "currency": "USD",
      "tax_rate": "0.0825",
      "modules": [
        "api_access",
        "analytics",
        "payment_methods",
        "ai_integration",
        "advanced_reporting",
        "custom_branding",
        "priority_support"
      ],
      "is_free": false,
      "formatted_cost": "$99.99 USD",
      "available_modules": [
        "api_access",
        "analytics",
        "payment_methods",
        "ai_integration",
        "advanced_reporting",
        "custom_branding",
        "priority_support",
        "multi_user_access",
        "data_export",
        "webhook_integration"
      ],
      "created_at": "2024-01-15T10:00:00.000Z",
      "updated_at": "2024-01-15T10:00:00.000Z"
    }
  ],
  "total_packages": 3,
  "available_modules": [
    "api_access",
    "analytics",
    "payment_methods",
    "ai_integration",
    "advanced_reporting",
    "custom_branding",
    "priority_support",
    "multi_user_access",
    "data_export",
    "webhook_integration"
  ],
  "message": "Packages retrieved successfully"
}
```

### Error Response (500)

```json
{
  "success": false,
  "error": "Failed to retrieve packages: Database connection error"
}
```

---

## Response Fields

### Root Level Fields

| Field | Type | Description |
|-------|------|-------------|
| `success` | boolean | Indicates if the request was successful |
| `data` | array | Array of package objects |
| `total_packages` | integer | Total number of packages returned |
| `available_modules` | array | List of all available modules in the system |
| `message` | string | Success message |

### Package Object Fields

| Field | Type | Description |
|-------|------|-------------|
| `id` | integer | Unique package identifier |
| `name` | string | Package name (snake_case format) |
| `cost` | string | Package cost formatted to 2 decimal places |
| `currency` | string | Currency code (e.g., "USD", "EUR") |
| `tax_rate` | string | Tax rate formatted to 4 decimal places |
| `modules` | array | List of modules included in this package |
| `is_free` | boolean | Whether this package is free (cost = 0) |
| `formatted_cost` | string | Human-readable cost with currency symbol |
| `available_modules` | array | All available modules in the system |
| `created_at` | string | ISO 8601 timestamp when package was created |
| `updated_at` | string | ISO 8601 timestamp when package was last updated |

### Available Modules

The system currently supports the following modules:

- **`api_access`** - Basic API access functionality
- **`analytics`** - Analytics and reporting features
- **`payment_methods`** - Payment processing capabilities
- **`ai_integration`** - AI-powered features
- **`advanced_reporting`** - Advanced reporting and insights
- **`custom_branding`** - Custom branding options
- **`priority_support`** - Priority customer support
- **`multi_user_access`** - Multi-user account management
- **`data_export`** - Data export functionality
- **`webhook_integration`** - Webhook integration capabilities

---

## Usage Examples

### Basic Package Listing

```javascript
async function getPackages() {
    try {
        const response = await fetch('https://signal.avinertech.com/api/packages');
        const result = await response.json();
        
        if (result.success) {
            console.log(`Found ${result.total_packages} packages:`);
            result.data.forEach(pkg => {
                console.log(`- ${pkg.name}: ${pkg.formatted_cost} (${pkg.modules.length} modules)`);
            });
        } else {
            console.error('Failed to fetch packages:', result.error);
        }
    } catch (error) {
        console.error('Network error:', error);
    }
}
```

### Building a Pricing Table

```javascript
async function buildPricingTable() {
    const response = await fetch('https://signal.avinertech.com/api/packages');
    const result = await response.json();
    
    if (result.success) {
        const pricingHTML = result.data.map(pkg => `
            <div class="pricing-card ${pkg.is_free ? 'free' : 'paid'}">
                <h3>${pkg.name.replace('_', ' ').toUpperCase()}</h3>
                <div class="price">${pkg.formatted_cost}</div>
                <div class="tax-info">Tax Rate: ${(parseFloat(pkg.tax_rate) * 100).toFixed(2)}%</div>
                <ul class="features">
                    ${pkg.modules.map(module => `<li>${module.replace('_', ' ')}</li>`).join('')}
                </ul>
                <button onclick="selectPackage('${pkg.name}')">
                    ${pkg.is_free ? 'Get Started Free' : 'Choose Plan'}
                </button>
            </div>
        `).join('');
        
        document.getElementById('pricing-table').innerHTML = pricingHTML;
    }
}
```

### Filtering Packages

```javascript
async function getPackagesByPriceRange(minPrice, maxPrice) {
    const response = await fetch('https://signal.avinertech.com/api/packages');
    const result = await response.json();
    
    if (result.success) {
        const filteredPackages = result.data.filter(pkg => {
            const cost = parseFloat(pkg.cost);
            return cost >= minPrice && cost <= maxPrice;
        });
        
        return filteredPackages;
    }
    
    return [];
}

// Usage
const affordablePackages = await getPackagesByPriceRange(0, 50);
console.log('Packages under $50:', affordablePackages);
```

### Module Availability Check

```javascript
async function getPackagesWithModule(moduleName) {
    const response = await fetch('https://signal.avinertech.com/api/packages');
    const result = await response.json();
    
    if (result.success) {
        const packagesWithModule = result.data.filter(pkg => 
            pkg.modules.includes(moduleName)
        );
        
        return packagesWithModule;
    }
    
    return [];
}

// Usage
const analyticsPackages = await getPackagesWithModule('analytics');
console.log('Packages with analytics:', analyticsPackages);
```

---

## Integration Examples

### React Component

```jsx
import React, { useState, useEffect } from 'react';

const PackagesList = () => {
    const [packages, setPackages] = useState([]);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState(null);

    useEffect(() => {
        const fetchPackages = async () => {
            try {
                const response = await fetch('https://signal.avinertech.com/api/packages');
                const result = await response.json();
                
                if (result.success) {
                    setPackages(result.data);
                } else {
                    setError(result.error);
                }
            } catch (err) {
                setError('Failed to fetch packages');
            } finally {
                setLoading(false);
            }
        };

        fetchPackages();
    }, []);

    if (loading) return <div>Loading packages...</div>;
    if (error) return <div>Error: {error}</div>;

    return (
        <div className="packages-grid">
            {packages.map(pkg => (
                <div key={pkg.id} className="package-card">
                    <h3>{pkg.name.replace('_', ' ').toUpperCase()}</h3>
                    <div className="price">{pkg.formatted_cost}</div>
                    <ul className="modules">
                        {pkg.modules.map(module => (
                            <li key={module}>{module.replace('_', ' ')}</li>
                        ))}
                    </ul>
                    {pkg.is_free && <span className="free-badge">FREE</span>}
                </div>
            ))}
        </div>
    );
};

export default PackagesList;
```

### Vue.js Component

```vue
<template>
  <div class="packages-container">
    <div v-if="loading" class="loading">Loading packages...</div>
    <div v-else-if="error" class="error">{{ error }}</div>
    <div v-else class="packages-grid">
      <div 
        v-for="pkg in packages" 
        :key="pkg.id" 
        :class="['package-card', { 'free-package': pkg.is_free }]"
      >
        <h3>{{ formatPackageName(pkg.name) }}</h3>
        <div class="price">{{ pkg.formatted_cost }}</div>
        <div class="modules">
          <span 
            v-for="module in pkg.modules" 
            :key="module" 
            class="module-tag"
          >
            {{ formatModuleName(module) }}
          </span>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'PackagesList',
  data() {
    return {
      packages: [],
      loading: true,
      error: null
    };
  },
  async mounted() {
    try {
      const response = await fetch('https://signal.avinertech.com/api/packages');
      const result = await response.json();
      
      if (result.success) {
        this.packages = result.data;
      } else {
        this.error = result.error;
      }
    } catch (err) {
      this.error = 'Failed to fetch packages';
    } finally {
      this.loading = false;
    }
  },
  methods: {
    formatPackageName(name) {
      return name.replace('_', ' ').toUpperCase();
    },
    formatModuleName(name) {
      return name.replace('_', ' ');
    }
  }
};
</script>
```

---

## Error Handling

### Common Error Scenarios

1. **Server Error (500)**
   - Database connection issues
   - Internal server errors
   - Package repository failures

2. **Network Errors**
   - Connection timeouts
   - DNS resolution failures
   - SSL/TLS certificate issues

### Recommended Error Handling

```javascript
async function fetchPackagesWithRetry(maxRetries = 3) {
    for (let attempt = 1; attempt <= maxRetries; attempt++) {
        try {
            const response = await fetch('https://signal.avinertech.com/api/packages', {
                timeout: 10000 // 10 second timeout
            });
            
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }
            
            const result = await response.json();
            
            if (result.success) {
                return result.data;
            } else {
                throw new Error(result.error);
            }
        } catch (error) {
            console.warn(`Attempt ${attempt} failed:`, error.message);
            
            if (attempt === maxRetries) {
                throw new Error(`Failed to fetch packages after ${maxRetries} attempts: ${error.message}`);
            }
            
            // Wait before retrying (exponential backoff)
            await new Promise(resolve => setTimeout(resolve, Math.pow(2, attempt) * 1000));
        }
    }
}
```

---

## Rate Limiting

Currently, there are no rate limits on the Get Packages API endpoint. However, it's recommended to implement caching on the client side since package data doesn't change frequently.

### Recommended Caching Strategy

```javascript
class PackageCache {
    constructor(ttl = 300000) { // 5 minutes TTL
        this.cache = null;
        this.lastFetch = null;
        this.ttl = ttl;
    }
    
    async getPackages() {
        const now = Date.now();
        
        if (this.cache && this.lastFetch && (now - this.lastFetch) < this.ttl) {
            return this.cache;
        }
        
        const response = await fetch('https://signal.avinertech.com/api/packages');
        const result = await response.json();
        
        if (result.success) {
            this.cache = result.data;
            this.lastFetch = now;
            return this.cache;
        }
        
        throw new Error(result.error || 'Failed to fetch packages');
    }
}

// Usage
const packageCache = new PackageCache();
const packages = await packageCache.getPackages();
```

---

## Support

For technical support or integration questions regarding the Get Packages API, contact: **sales@avinertech.com** 