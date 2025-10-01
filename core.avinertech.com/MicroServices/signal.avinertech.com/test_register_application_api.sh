#!/bin/bash

# Test the register-application API endpoint
# Make sure your Laravel server is running on localhost:8000

echo "Testing Register Application API..."
echo "=================================="

# Test 1: Successful registration with Professional Package
echo "Test 1: Register Professional Package"
curl -X POST http://localhost:8000/api/register-application \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "package_name": "Professional Package",
    "package_price_per_month": 99.99,
    "total_price": 99.99,
    "company_name": "Test Company Ltd",
    "email": "test@example.com",
    "password": "password123",
    "password_confirmation": "password123",
    "address": "123 Business Street, Tech City, TC 12345",
    "host": "testcompany",
    "username": "testuser",
    "phone": "+1234567890",
    "database_name": "test_db",
    "database_user": "test_user",
    "database_password": "test_password"
  }' | jq '.'

echo -e "\n\n"

# Test 2: Register Basic Package
echo "Test 2: Register Basic Package"
curl -X POST http://localhost:8000/api/register-application \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "package_name": "Basic Package",
    "package_price_per_month": 29.99,
    "total_price": 29.99,
    "company_name": "Basic Company Inc",
    "email": "basic@example.com",
    "password": "password123",
    "password_confirmation": "password123",
    "address": "456 Basic Avenue, Simple City, SC 54321",
    "host": "basiccompany",
    "username": "basicuser",
    "phone": "+1987654321"
  }' | jq '.'

echo -e "\n\n"

# Test 3: Register Free Package
echo "Test 3: Register Free Package"
curl -X POST http://localhost:8000/api/register-application \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "package_name": "Free Package",
    "package_price_per_month": 0.00,
    "total_price": 0.00,
    "company_name": "Free Company LLC",
    "email": "free@example.com",
    "password": "password123",
    "password_confirmation": "password123",
    "address": "789 Free Street, Budget City, BC 98765",
    "host": "freecompany",
    "username": "freeuser",
    "phone": "+1122334455"
  }' | jq '.'

echo -e "\n\n"

# Test 4: Validation Error - Missing required fields
echo "Test 4: Validation Error - Missing Required Fields"
curl -X POST http://localhost:8000/api/register-application \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "package_name": "Test Package",
    "email": "incomplete@example.com"
  }' | jq '.'

echo -e "\n\n"

# Test 5: Validation Error - Password confirmation mismatch
echo "Test 5: Validation Error - Password Mismatch"
curl -X POST http://localhost:8000/api/register-application \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "package_name": "Test Package",
    "package_price_per_month": 50.00,
    "total_price": 50.00,
    "company_name": "Test Company",
    "email": "mismatch@example.com",
    "password": "password123",
    "password_confirmation": "differentpassword",
    "address": "123 Test Street",
    "host": "testhost",
    "username": "testuser",
    "phone": "+1234567890"
  }' | jq '.'

echo -e "\n\n"

# Test 6: Check registration status
echo "Test 6: Check Registration Status"
curl -X GET http://localhost:8000/api/registration-status/test@example.com \
  -H "Accept: application/json" | jq '.'

echo -e "\n\n"

# Test 7: Check non-existent user status
echo "Test 7: Check Non-existent User Status"
curl -X GET http://localhost:8000/api/registration-status/nonexistent@example.com \
  -H "Accept: application/json" | jq '.'

echo -e "\n"
echo "API Testing Complete!"
