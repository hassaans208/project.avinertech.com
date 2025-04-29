// function getAvinerTechFolders($directory) {
    //     $folders = [];
    //     $items = scandir($directory);
        
    //     foreach ($items as $item) {
    //         if ($item === '.' || $item === '..') continue;
            
    //         $path = $directory . '/' . $item;
    //         if (is_dir($path)) {
    //             // Check if this directory is a tenant application
    //             if (strpos($item, '.avinertech.com') !== false) {
    //                 $folders[] = $path;
    //             }
    //             // Recursively search subdirectories
    //             $subfolders = getAvinerTechFolders($path);
    //             $folders = array_merge($folders, $subfolders);
    //         }
    //     }
        
    //     return $folders;
    // }

    // // Get all .avinertech.com folders recursively
    // $folders = getAvinerTechFolders(__DIR__);
   
    // // Generate INSERT queries
    // foreach ($folders as $folder) {
    //     $folder_name = basename($folder);
    //     $tenant_name = str_replace('.avinertech.com', '', $folder_name);
    //     $tenant_url = $folder_name;
    //     $application_path = str_replace(__DIR__ . '/', '', $folder);
    //     // var_dump($application_path);
    //     // die;
    //     $query = "INSERT INTO Tenants (tenant_name, tenant_url, status, block_status, application_path) 
    //             VALUES ('$tenant_name', '$tenant_url', 'unpaid', 'unblocked', '$application_path');";
    //     $stmt = $pdo->prepare($query);
    //     $result = $stmt->execute();
    //     echo $application_path . "\n";
    //     echo $result . "\n";
    // }