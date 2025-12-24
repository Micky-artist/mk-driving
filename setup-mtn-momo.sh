#!/bin/bash

# MTN MoMo API Setup Script
# Reads configuration from .env file

echo "Reading configuration from .env file..."

# Load environment variables
if [ -f .env ]; then
    export $(grep -v '^#' .env | xargs)
else
    echo "Error: .env file not found!"
    exit 1
fi

# Get primary key from .env
PRIMARY_KEY=$MTN_MOMO_COLLECTION_PRIMARY_KEY
DOMAIN="localhost"  # Your domain (use localhost for development)
BASE_URL="https://sandbox.momodeveloper.mtn.com"  # Use sandbox for testing

if [ -z "$PRIMARY_KEY" ] || [ "$PRIMARY_KEY" = "XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX" ]; then
    echo "Error: Please set MTN_MOMO_COLLECTION_PRIMARY_KEY in your .env file"
    exit 1
fi

echo "Using Primary Key: ${PRIMARY_KEY:0:8}..."
echo ""

echo "Step 1: Generating UUID for user_id..."
USER_ID=$(uuidgen | tr '[:upper:]' '[:lower:]')
echo "Generated User ID: $USER_ID"

echo ""
echo "Step 2: Creating API user..."
curl -X POST "$BASE_URL/v1_0/apiuser" \
  -H "X-Reference-Id: $USER_ID" \
  -H "Ocp-Apim-Subscription-Key: $PRIMARY_KEY" \
  -H "Content-Type: application/json" \
  -d "{\"providerCallbackHost\": \"localhost\"}"

echo ""
echo "Step 3: Creating API key (secret)..."
echo "Note: Save this API key securely - you won't see it again!"
curl -i -X POST "$BASE_URL/v1_0/apiuser/$USER_ID/apikey" \
  -H "Ocp-Apim-Subscription-Key: $PRIMARY_KEY" \
  -H "Content-Type: application/json" \
  -d '{}'

echo ""
echo "Add these to your .env file:"
echo "MTN_MOMO_COLLECTION_API_USER_ID=$USER_ID"
echo "MTN_MOMO_COLLECTION_API_SECRET=<api_key_from_step_3>"