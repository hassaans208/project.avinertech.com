#!/bin/bash

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

if [ "$#" -ne 1 ]; then
    echo -e "${RED}Usage: $0 <app_name>${NC}"
    exit 1
fi

APP_NAME=$1

sudo certbot --nginx -d $APP_NAME
echo -e "${GREEN}SSL certificate installed successfully${NC}"
echo -e "${GREEN}SUCCESS${NC}"
