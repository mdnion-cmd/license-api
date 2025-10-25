<?php
header('Content-Type: text/plain');

// Your InfinityFree MySQL Details
$host = 'sql206.infinityfree.com';
$user = 'if0_40255159';
$pass = 'LEY4A7lwMjm';  // CHANGE THIS TO YOUR PASSWORD
$dbname = 'if0_40255159_plugin_licenses';

// Connect to MySQL
$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("DB_ERROR:" . $conn->connect_error);
}

// Get parameters
$action = $_GET['action'] ?? '';
$license_key = $_GET['key'] ?? '';
$server_ip = $_GET['server_ip'] ?? '';
$plugin_id = $_GET['plugin_id'] ?? 'dragon_guard';

if ($action == 'validate') {
    validate_license($conn, $license_key, $server_ip, $plugin_id);
} elseif ($action == 'add') {
    add_license($conn, $license_key, $plugin_id);
} else {
    echo "READY - Use ?action=validate or ?action=add";
}

function validate_license($conn, $license_key, $server_ip, $plugin_id) {
    $stmt = $conn->prepare("SELECT * FROM licenses WHERE license_key = ? AND plugin_id = ? AND status = 'active'");
    $stmt->bind_param("ss", $license_key, $plugin_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        echo "INVALID";
        return;
    }
    
    $license = $result->fetch_assoc();
    
    if (empty($license['used_server'])) {
        $update = $conn->prepare("UPDATE licenses SET used_server = ? WHERE license_key = ?");
        $update->bind_param("ss", $server_ip, $license_key);
        if ($update->execute()) {
            echo "ACTIVATED";
        } else {
            echo "UPDATE_FAILED";
        }
    } elseif ($license['used_server'] == $server_ip) {
        echo "VALID";
    } else {
        echo "ALREADY_USED:" . $license['used_server'];
    }
}

function add_license($conn, $license_key, $plugin_id) {
    $buyer = $_GET['buyer'] ?? 'Unknown';
    
    $stmt = $conn->prepare("INSERT INTO licenses (license_key, plugin_id, buyer_name) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $license_key, $plugin_id, $buyer);
    
    if ($stmt->execute()) {
        echo "ADDED:" . $license_key;
    } else {
        echo "ERROR:" . $stmt->error;
    }
}

$conn->close();
?>