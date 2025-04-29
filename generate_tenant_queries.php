<?php

// Function to get all .avinertech.com folders
function getAvinerTechFolders($directory) {
    $folders = [];
    $items = scandir($directory);
    
    foreach ($items as $item) {
        if ($item === '.' || $item === '..') continue;
        
        $path = $directory . '/' . $item;
        if (is_dir($path) && strpos($item, '.avinertech.com') !== false) {
            $folders[] = $item;
        }
    }
    
    return $folders;
}

// Get all .avinertech.com folders
$folders = getAvinerTechFolders(__DIR__);

// Generate INSERT queries
foreach ($folders as $folder) {
    $tenant_name = str_replace('.avinertech.com', '', $folder);
    $tenant_url = $folder;
    $application_path = $folder;
    
    $query = "INSERT INTO Tenants (tenant_name, tenant_url, status, block_status, application_path) 
              VALUES ('$tenant_name', '$tenant_url', 'unpaid', 'unblocked', '$application_path');";
    
    echo $query . "\n";
}

?> 