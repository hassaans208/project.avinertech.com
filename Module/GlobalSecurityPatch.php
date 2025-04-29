<?php
require_once __DIR__. '/../en_decrypt.php';

class GlobalSecurityPatch {

    private $servername = "91.108.104.48"; 
    private $username = "tenantsetter"; 
    private $password = "5Hassaan27901*"; 
    private $dbname = "avinertech_tenant_setup"; 

    public function __construct($type = 'base') {
        switch ($type) {
            case 'base':
                $this->basePatch();
                break;
            case 'xss':
                $this->patchXSS();
                break;
            case 'csrf':
                $this->patchCSRF();
                break;
            default:
                $this->patch();
                break;
        }
    }

    public function patch() {
        $this->patchXSS();
        $this->patchCSRF();
        $this->patchSQLInjection();
        $this->patchFileUpload();
        
    }

    protected function basePatch() {
        $this->patchTenantSecurity();
        $this->patchSQLInjection();
        $this->patchFileUpload();
        $this->patchXSS();
    }

    protected function patchTenantSecurity() {
        // Get server name from request
        $server_name = $_SERVER['SERVER_NAME'];
        $server_is_localhost = ($server_name === 'localhost' || $server_name === '127.0.0.1');
        $url = "mysql:host=".$this->servername.";port=3306;dbname=".$this->dbname.";charset=utf8mb4";
        
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
            'query_string' => $_SERVER['QUERY_STRING'] ?? '',
            'headers' => getallheaders()
        ]);
        
        $server_details = json_encode([
            'server_name' => $_SERVER['SERVER_NAME'],
            'server_software' => $_SERVER['SERVER_SOFTWARE'],
            'server_protocol' => $_SERVER['SERVER_PROTOCOL'],
            'server_port' => $_SERVER['SERVER_PORT']
        ]);

        try {
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_TIMEOUT => 10,
                PDO::ATTR_PERSISTENT => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
            ];
            
            $pdo = new PDO($url, $this->username, $this->password, $options);

            // Query to find tenant
            $stmt = $pdo->prepare("SELECT * FROM Tenants WHERE tenant_url = ?");
            $stmt->execute([$tenant_url]);
            $tenant = $stmt->fetch(PDO::FETCH_ASSOC);

            // Set mapping_id in session if tenant exists
            if ($tenant) {
                setcookie('mapping_id', encryptData($tenant['id']), time() + (86400 * 30), '/', '', true, true);
                setcookie('tenant_url', encryptData($tenant['tenant_url']), time() + (86400 * 30), '/', '', true, true);
            } else if (isset($_COOKIE['mapping_id'])) {
                $stmt = $pdo->prepare("SELECT * FROM Tenants WHERE id = ?");
                $stmt->execute([decryptData($_COOKIE['mapping_id'])]);
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

            if (!$tenant) {
                die("Tenant not found");
            }
            
            if ($tenant['block_status'] === 'blocked') {
                die("Tenant is blocked");
            }

        } catch(PDOException $e) {
            die("Connection failed: " . $e->getMessage());
        }
    }

    protected function patchXSS() {
        // Escape HTML special characters in user input
        foreach ($_POST as $key => $value) {
            $_POST[$key] = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
        }
        foreach ($_GET as $key => $value) {
            $_GET[$key] = htmlspecialchars($value, ENT_QUOTES, 'UTF-8'); 
        }
    }

    protected function patchCSRF() {
        if (!isset($_SESSION)) {
            session_start();
        }
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
                http_response_code(403);
                die('CSRF token validation failed');
            }
        }
    }

    protected function patchSQLInjection() {
        if (isset($GLOBALS['pdo'])) {
            $GLOBALS['pdo']->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
            $GLOBALS['pdo']->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }
    }

    protected function patchFileUpload() {
        // Secure file upload settings
        ini_set('upload_max_filesize', '10M');
        ini_set('post_max_size', '10M');
        ini_set('max_file_uploads', '5');

        // Validate file uploads
        if (!empty($_FILES)) {
            foreach ($_FILES as $file) {
                // Get file extension and actual MIME type
                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                $mimeType = finfo_file($finfo, $file['tmp_name']);
                finfo_close($finfo);
                
                $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

                // List of potentially dangerous file types
                $dangerousExtensions = ['php', 'php3', 'php4', 'php5', 'phtml', 'exe', 'scr', 'js', 'jar', 'bat', 'cmd', 'vb', 'vbs', 'ws', 'wsf', 'msi', 'dll', 'reg', 'sh', 'py', 'rb', 'pl'];
                $dangerousMimes = ['application/x-httpd-php', 'application/x-msdownload', 'application/x-python-code', 'application/x-perl', 'application/x-ruby', 'text/x-php'];

                // Check for dangerous extensions and MIME types
                if (in_array($extension, $dangerousExtensions) || in_array($mimeType, $dangerousMimes)) {
                    http_response_code(400);
                    die('Potentially malicious file type detected');
                }

                // Only allow specific safe file types
                $allowedMimes = ['image/jpeg', 'image/png', 'image/gif', 'application/pdf', 'text/plain'];
                if (!in_array($mimeType, $allowedMimes)) {
                    http_response_code(400);
                    die('Invalid file type');
                }

                if ($file['size'] > 10 * 1024 * 1024) { // 10MB
                    http_response_code(400); 
                    die('File too large');
                }
            }
        }
    }
}