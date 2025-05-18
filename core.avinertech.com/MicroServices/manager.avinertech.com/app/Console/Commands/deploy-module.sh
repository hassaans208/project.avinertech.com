#!/bin/bash

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Check if all required arguments are provided
if [ "$#" -ne 2 ]; then
    echo -e "${RED}Usage: $0 <app_name> <app_path>${NC}"
    exit 1
fi

APP_NAME=$1
APP_PATH=$2

# Define paths
SITES_AVAILABLE="/etc/nginx/sites-available"
SITES_ENABLED="/etc/nginx/sites-enabled"
CONFIG_FILE="${SITES_AVAILABLE}/${APP_NAME}"
TEMPLATE_FILE="${SITES_AVAILABLE}/demo.avinertech.com"

# Check if template exists
if [ ! -f "$TEMPLATE_FILE" ]; then
    echo -e "${RED}Error: Template file not found at ${TEMPLATE_FILE}${NC}"
    exit 1
fi

# Create nginx configuration file by copying template
cp "$TEMPLATE_FILE" "$CONFIG_FILE"

# Replace placeholders in the copied file
sed -i "s|server_name .*;|server_name ${APP_NAME};|g" "$CONFIG_FILE"
sed -i "s|root .*;|root ${APP_PATH}/public;|g" "$CONFIG_FILE"

# Create symbolic link if it doesn't exist
if [ ! -L "${SITES_ENABLED}/${APP_NAME}" ]; then
    ln -s "$CONFIG_FILE" "${SITES_ENABLED}/${APP_NAME}.avinertech.com"
fi

# Test nginx configuration
nginx -t
if [ $? -ne 0 ]; then
    echo -e "${RED}Error: Nginx configuration test failed${NC}"
    exit 1
fi

# Restart nginx
systemctl restart nginx
if [ $? -eq 0 ]; then
    echo -e "${GREEN}Success: Nginx configuration deployed and service restarted${NC}"
    echo -e "${YELLOW}Site available at: ${APP_NAME}.avinertech.com${NC}"
    exit 0
else
    echo -e "${RED}Error: Failed to restart nginx${NC}"
    exit 1
fi
