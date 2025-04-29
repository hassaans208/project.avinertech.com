<?php

$servername = "91.108.104.48"; 
$username = "tenantsetter"; 
$password = "5Hassaan27901*"; 
$dbname = "avinertech_tenant_setup"; 
$url = "mysql:host=$servername;port=3306;dbname=$dbname;charset=utf8mb4";

try {
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_TIMEOUT => 10,
        PDO::ATTR_PERSISTENT => false,
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
    ];
    
    $pdo = new PDO($url, $username, $password, $options);
    
    // Get server name from request
    $server_name = $_SERVER['SERVER_NAME'];
    $server_is_localhost = ($server_name === 'localhost' || $server_name === '127.0.0.1');
    
    // Check if it's localhost or 127.0.0.1
    if ($server_is_localhost) {
        // Get tenant_url from GET request
        $tenant_url = isset($_GET['tenant_url']) ? $_GET['tenant_url'] : null;
        
        if (!$tenant_url && !isset($_COOKIE['mapping_id'])) {
            die("Tenant URL not provided in GET request");
        }
    } else {
        $tenant_url = $server_name;
    }
    
    // Prepare request and server details for logging
    $request_details = json_encode([
        'method' => $_SERVER['REQUEST_METHOD'],
        'uri' => $_SERVER['REQUEST_URI'],
        'payload' => json_encode($_POST),
        'get' => json_encode($_GET),
        'query_string' => $_SERVER['QUERY_STRING'],
        'headers' => getallheaders()
    ]);
    
    $server_details = json_encode([
        'server_name' => $_SERVER['SERVER_NAME'],
        'server_software' => $_SERVER['SERVER_SOFTWARE'],
        'server_protocol' => $_SERVER['SERVER_PROTOCOL'],
        'server_port' => $_SERVER['SERVER_PORT']
    ]);
    


    // Query to find tenant
    $stmt = $pdo->prepare("SELECT * FROM Tenants WHERE tenant_url = ?");
    $stmt->execute([$tenant_url]);
    $tenant = $stmt->fetch(PDO::FETCH_ASSOC);
    // Set mapping_id in session if tenant exists
    if ($tenant) {
        setcookie('mapping_id', $tenant['id'], time() + (86400 * 30), '/', '', true, true); // 30 days expiry, secure, httponly
    } else if (isset($_COOKIE['mapping_id'])) {
        $stmt = $pdo->prepare("SELECT * FROM Tenants WHERE id = ?");
        $stmt->execute([$_COOKIE['mapping_id']]);
        $tenant = $stmt->fetch(PDO::FETCH_ASSOC);
    }
    // Determine block status and mapping_id
    $block_status = 'blocked';
    $mapping_id = null;
    
    if ($tenant) {
        $mapping_id = $tenant['id'];
        $block_status = $tenant['block_status'];
    }
    
    // Insert into IPLogs regardless of tenant status
    $log_stmt = $pdo->prepare("
        INSERT INTO IPLogs (
            ip_address, 
            tenant_url, 
            mapping_id, 
            request_details, 
            server_details, 
            block_status
        ) VALUES (?, ?, ?, ?, ?, ?)
    ");
    
    if ($server_is_localhost) $tenant_url .= '.local';
    
    $log_stmt->execute([
        $_SERVER['REMOTE_ADDR'],
        $tenant_url,
        $mapping_id,
        $request_details,
        $server_details,
        $block_status
    ]);
    
    // $pdo->close();
    // Handle tenant access
    if (!$tenant) {
        die("Tenant not found");
    }
    
    if ($tenant['block_status'] === 'blocked') {
        die("Tenant is blocked");
    }

    require_once __DIR__ . '/' . $tenant['application_path'] . '/public/index.php';

} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage() . "\nError Code: " . $e->getCode() . "\nError Info: " . print_r($e->errorInfo, true));
}

?>  
