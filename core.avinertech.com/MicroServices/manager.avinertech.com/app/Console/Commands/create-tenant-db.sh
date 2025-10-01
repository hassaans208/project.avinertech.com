#!/bin/bash

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Check if required arguments are provided
if [ "$#" -lt 4 ]; then
    echo -e "${RED}Usage: $0 <database_name> <database_user> <database_password> <database_host>${NC}"
    echo -e "${YELLOW}Example: $0 tenant_db tenant_user 'secure_password' localhost${NC}"
    exit 1
fi

DB_NAME="$1"
DB_USER="$2"
DB_PASS="$3"
DB_HOST="$4"

# Validate database name (alphanumeric and underscore only)
if ! [[ $DB_NAME =~ ^[a-zA-Z0-9_]+$ ]]; then
    echo -e "${RED}Error: Database name can only contain letters, numbers, and underscores${NC}"
    exit 1
fi

# Validate database user (alphanumeric and underscore only)
if ! [[ $DB_USER =~ ^[a-zA-Z0-9_]+$ ]]; then
    echo -e "${RED}Error: Database user can only contain letters, numbers, and underscores${NC}"
    exit 1
fi

# Create database and user
echo -e "${YELLOW}Creating database '$DB_NAME' and user '$DB_USER' on host '$DB_HOST'...${NC}"
mysql -u root -p27901 <<EOF
CREATE DATABASE IF NOT EXISTS \`$DB_NAME\`;
CREATE USER IF NOT EXISTS '$DB_USER'@'$DB_HOST' IDENTIFIED BY '$DB_PASS';
GRANT ALL PRIVILEGES ON \`$DB_NAME\`.* TO '$DB_USER'@'$DB_HOST';
FLUSH PRIVILEGES;
EOF

echo -e "${GREEN}Database and user created successfully!${NC}"
exit 0