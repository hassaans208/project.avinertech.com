#!/bin/bash

echo "Testing Package Scenarios..."
echo "============================"

# First, let's get available packages to see their IDs
echo "1. Getting available packages..."
curl -X GET http://localhost:8000/api/packages \
  -H "Accept: application/json" | jq '.'

echo -e "\n\n"

# Test 1: Register with package_id (if packages exist)
echo "2. Register with package_id (Professional Package - ID 1)"
curl -X POST http://localhost:8000/api/register-application \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "package_id": 1,
    "package_name": "Professional Package",
    "package_price_per_month": 99.99,
    "total_price": 99.99,
    "company_name": "Test Company with ID",
    "email": "testwithid@example.com",
    "password": "password123",
    "password_confirmation": "password123",
    "address": "123 Test Street, Test City, TC 12345",
    "host": "testwithid",
    "username": "testuserid",
    "phone": "+1234567890"
  }' | jq '.'

echo -e "\n\n"

# Test 2: Register without package_id (name-based lookup)
echo "3. Register without package_id (name-based lookup)"
curl -X POST http://localhost:8000/api/register-application \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "package_name": "Basic Package",
    "package_price_per_month": 29.99,
    "total_price": 29.99,
    "company_name": "Test Company without ID",
    "email": "testwithoutid@example.com",
    "password": "password123",
    "password_confirmation": "password123",
    "address": "456 Test Avenue, Test City, TC 54321",
    "host": "testwithoutid",
    "username": "testuserno",
    "phone": "+1987654321"
  }' | jq '.'

echo -e "\n\n"

# Test 3: Register with non-existent package_id
echo "4. Register with non-existent package_id (should fallback to name)"
curl -X POST http://localhost:8000/api/register-application \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "package_id": 999,
    "package_name": "Enterprise Package",
    "package_price_per_month": 299.99,
    "total_price": 299.99,
    "company_name": "Test Company with Bad ID",
    "email": "testbadid@example.com",
    "password": "password123",
    "password_confirmation": "password123",
    "address": "789 Test Boulevard, Test City, TC 98765",
    "host": "testbadid",
    "username": "testuserbad",
    "phone": "+1122334455"
  }' | jq '.'

echo -e "\n\n"

# Test 4: Register with new package name (should create new package)
echo "5. Register with new package name (should create new package)"
curl -X POST http://localhost:8000/api/register-application \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "package_name": "Custom Package",
    "package_price_per_month": 149.99,
    "total_price": 149.99,
    "company_name": "Test Company Custom",
    "email": "testcustom@example.com",
    "password": "password123",
    "password_confirmation": "password123",
    "address": "321 Custom Street, Custom City, CC 11111",
    "host": "testcustom",
    "username": "testusercustom",
    "phone": "+1555666777"
  }' | jq '.'

echo -e "\n\n"

# Test 5: Register same package name with different price (should update existing)
echo "6. Register same package name with different price (should update existing)"
curl -X POST http://localhost:8000/api/register-application \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "package_name": "Custom Package",
    "package_price_per_month": 199.99,
    "total_price": 199.99,
    "company_name": "Test Company Custom Updated",
    "email": "testcustom2@example.com",
    "password": "password123",
    "password_confirmation": "password123",
    "address": "321 Custom Street, Custom City, CC 11111",
    "host": "testcustom2",
    "username": "testusercustom2",
    "phone": "+1555666888"
  }' | jq '.'

echo -e "\n"
echo "Package Scenario Testing Complete!"
