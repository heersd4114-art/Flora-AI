<?php
$host = '127.0.0.1';
$port = 5001;
$timeout = 5;

$connection = @fsockopen($host, $port, $errno, $errstr, $timeout);

if (is_resource($connection)) {
    echo "<h3>AI Service is ONLINE (Port 5001 open)</h3>";
    fclose($connection);
    
    // Test Prediction Endpoint
    $ch = curl_init("http://127.0.0.1:5001/predict");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    // GET request (just to see if it responds 405 or something valid-ish instead of timeout)
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    echo "Endpoint /predict HTTP Code: " . $http_code . "<br>";
    curl_close($ch);
    
} else {
    echo "<h3>AI Service is OFFLINE</h3>";
    echo "Error: $errstr ($errno)<br>";
    echo "Please run <b>start_ai_service.bat</b>";
}
?>
