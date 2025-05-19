#!/bin/bash

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Check if all required arguments are provided
if [ "$#" -ne 2 ]; then
    echo -e "${RED}Usage: $0 <source_path> <target_path>${NC}"
    exit 1
fi

SOURCE_PATH=$1
TARGET_PATH=$2

# Check if target directory already exists
if [ -d "$TARGET_PATH" ]; then
    echo -e "${RED}Error: Directory [$TARGET_PATH] already exists!${NC}"
    exit 1
fi

# Check if source directory exists
if [ ! -d "$SOURCE_PATH" ]; then
    echo -e "${RED}Error: Source path [$SOURCE_PATH] does not exist!${NC}"
    exit 1
fi

# Create target directory
mkdir -p "$TARGET_PATH"

# Copy files with error handling
if cp -R "$SOURCE_PATH"/* "$TARGET_PATH"/; then
    cp "$SOURCE_PATH/.env.example" "$TARGET_PATH/.env"
    cd "$TARGET_PATH" && php artisan key:generate && php artisan optimize:clear && php artisan optimize
    chown -R www-data:www-data "$TARGET_PATH/storage"
    chown -R www-data:www-data "$TARGET_PATH/storage/framework/views"
    chown -R www-data:www-data "$TARGET_PATH/bootstrap/cache"
    chown -R www-data:root "$TARGET_PATH/database/database.sqlite"
    chmod -R 664 "$TARGET_PATH/database/database.sqlite"
    chmod -R 775 "$TARGET_PATH/database"
    chown -R www-data:root "$TARGET_PATH/database"
    cd "$TARGET_PATH" && php artisan migrate:fresh --seed
    echo -e "${GREEN}Success: Files copied successfully!${NC}"
    echo -e "${GREEN}Success: Database migrated and seeded successfully!${NC}"
    echo -e "${YELLOW}Location: $TARGET_PATH${NC}"
    exit 0
else
    echo -e "${RED}Error: Failed to copy files${NC}"
    # Cleanup on failure
    rm -rf "$TARGET_PATH"
    exit 1
fi

