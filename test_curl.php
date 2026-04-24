<?php
$local_ai_url = 'http://127.0.0.1:5001/predict';
$target_file = "C:/xampp/htdocs/plant_app/ai_service/test_leaf.jpg"; // Use the test image created earlier

$ch_local = curl_init($local_ai_url);
$cfile = new CURLFile($target_file, mime_content_type($target_file), basename($target_file));
$data_local = array('image' => $cfile);

curl_setopt($ch_local, CURLOPT_POST, 1);
curl_setopt($ch_local, CURLOPT_POSTFIELDS, $data_local);
curl_setopt($ch_local, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch_local, CURLOPT_TIMEOUT, 120); 

echo "Sending request...\n";
$result_local = curl_exec($ch_local);
$http_code = curl_getinfo($ch_local, CURLINFO_HTTP_CODE);
$error = curl_error($ch_local);
curl_close($ch_local);

echo "HTTP Code: " . $http_code . "\n";
echo "cURL Error: " . $error . "\n";
echo "Result:\n" . $result_local . "\n";
?>
