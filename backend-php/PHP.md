### Switch case để chọn định hành động => làm endpoint cho api
### Thêm sữa xóa các fields, branches, field_types, services,...

## Xử lý CORS
```
/**
 * Handle CORS (Cross-Origin Resource Sharing)
 */
function handleCORS()
{
    $allowedOrigins = [
        'http://localhost:3000',
        'http://localhost:5173',
        'http://127.0.0.1:3000',
        'http://127.0.0.1:5173',
        'http://localhost:5174',
        'http://localhost:5175',

        // Thêm domain name ở đây
    ];

    $origin = $_SERVER['HTTP_ORIGIN'] ?? '';

    if (in_array($origin, $allowedOrigins, true)) {
        header('Access-Control-Allow-Origin: ' . $origin);
    }

    header('Access-Control-Allow-Credentials: true');
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, Authorization');
    header('Access-Control-Max-Age: 86400');

    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        http_response_code(200);
        exit();
    }
}

handleCORS();

function sendJson($payload, int $status = 200)
{
    http_response_code($status);
    echo json_encode($payload, JSON_UNESCAPED_UNICODE);
    exit;
}

header('Content-Type: application/json');
```
## Hàm đọc file php.ini và kết nối Database
```
private function __construct() 
        {
            $iniPath = __DIR__ . "/php.ini";
            
            if(!file_exists($iniPath)) {
                throw new Exception("File php.ini doesn't exits!");
            }
            $ini = parse_ini_file($iniPath);
            $requireKey = ["servername", "dbname", "username", "password"];
            foreach($requireKey as $Key) {
                if(!isset($ini[$Key])){
                    throw new Exception("Missing Key: $Key in php.ini file!");
                }
            }

            $this->servername = $ini["servername"];
            $this->dbname = $ini["dbname"];
            $this->username = $ini["username"];
            $this->password = $ini["password"];

            $opt = [
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8",
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];

            try{
                $dsn = "mysql:host={$this->servername};dbname={$this->dbname};charset=utf8";
                $this->conn = new PDO($dsn, $this->username, $this->password, $opt);
            } catch(PDOException $e) {
                throw new Exception("Error! Can't connect to database: " . $e->getMessage());
            }
        }
```