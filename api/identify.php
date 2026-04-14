<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

include "../config.php";
include "../config_api.php";

$response = ["status" => "error", "message" => "Unknown error"];

function is_ai_service_running($host = '127.0.0.1', $port = 5001, $timeout = 1) {
    $fp = @fsockopen($host, $port, $errno, $errstr, $timeout);
    if ($fp) {
        fclose($fp);
        return true;
    }
    return false;
}

function try_start_ai_service() {
    if (is_ai_service_running()) {
        return true;
    }

    $ai_dir = realpath(__DIR__ . '/../ai_service');
    if (!$ai_dir || !is_dir($ai_dir)) {
        return false;
    }

    $ai_dir_win = str_replace('/', '\\', $ai_dir);
    $log_file = str_replace('/', '\\', realpath(__DIR__ . '/../ai_service') . '/ai_log.txt');
    $err_file = str_replace('/', '\\', realpath(__DIR__ . '/../ai_service') . '/ai_err.txt');

    $project_start_script = str_replace('/', '\\', realpath(__DIR__ . '/../start_all_services.bat'));
    $commands = [];

    if ($project_start_script && file_exists(str_replace('\\', '/', $project_start_script))) {
        $commands[] = 'cmd /c start /B "" "' . $project_start_script . '"';
    }

    // Prefer absolute Python launcher for Windows service context.
    $venv_python = 'C:\\Users\\Administrator\\AppData\\Local\\Microsoft\\WindowsApps\\PythonSoftwareFoundation.Python.3.11_qbz5n2kfra8p0\\python.exe';
    if ($venv_python && file_exists($venv_python)) {
        $commands[] = 'cmd /c start /B "" cmd /c "cd /d ' . $ai_dir_win . ' && "' . $venv_python . '" app.py >> "' . $log_file . '" 2>> "' . $err_file . '""';
    }
    $commands[] = 'cmd /c start /B "" cmd /c "cd /d ' . $ai_dir_win . ' && python app.py >> "' . $log_file . '" 2>> "' . $err_file . '""';

    foreach ($commands as $cmd) {
        @pclose(@popen($cmd, 'r'));

        // Wait briefly for warm-up.
        for ($i = 0; $i < 16; $i++) {
            usleep(500000); // 0.5 sec
            if (is_ai_service_running()) {
                return true;
            }
        }
    }

    return false;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['image'])) {
    
    // 1. Upload logic
    $target_dir = "../uploads/";
    if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);
    
    $file_ext = strtolower(pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION));
    $new_filename = uniqid("mobile_scan_") . "." . $file_ext;
    $target_file = $target_dir . $new_filename;
    
    if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
        
        $image_url_for_app = "uploads/" . $new_filename;

        // 2. Call Local AI Service (Python Flask)
        $local_ai_url = 'http://127.0.0.1:5001/predict';

        if (!is_ai_service_running()) {
            try_start_ai_service();
        }
        
        $ch = curl_init($local_ai_url);
        // Create a CURLFile object
        $cfile = new CURLFile($target_file, mime_content_type($target_file), basename($target_file));
        $data = array('image' => $cfile);
        
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60); // Increased for AI
        
        // Securely pass API Key in Header
        $headers = [
            "X-API-Key: " . (defined('GEMINI_API_KEY') ? GEMINI_API_KEY : '')
        ];
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers); 
        
        $result = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curl_error = curl_error($ch);
        curl_close($ch);
        
        // Default / Fallback values
        $plant_name = "Unknown Plant";
        $display_name = "Unknown Plant";
        $disease_detected = "Analysis Failed";
        $care_tips = "Could not connect to AI service.";
        $care_tips_raw = "";
        $cures = [];
        $confidence = 0;
        $ai_analysis = null;
        $treatment_steps = null;
        $method = "Unknown";
        $is_not_plant = false;
        
        if ($http_code == 200 && $result) {
            $decoded = json_decode($result, true);
            
            if (isset($decoded['plant_name'])) {
                $plant_name = $decoded['plant_name'];
                $display_name = $decoded['display_name'] ?? $decoded['plant_name'];
                $disease_detected = $decoded['disease'];
                $confidence = $decoded['confidence'] ?? 0;
                $method = $decoded['method'] ?? 'AI Service';
                
                $ai_analysis = $decoded['ai_analysis'] ?? '';
                if (is_array($ai_analysis)) { $ai_analysis = implode(" ", $ai_analysis); }

                $treatment_steps = $decoded['treatment_steps'] ?? [];
                if (is_string($treatment_steps)) { $treatment_steps = [$treatment_steps]; }

                $care_tips_raw = $decoded['care_tips'] ?? '';
                if (is_array($care_tips_raw)) { $care_tips_raw = implode(" ", $care_tips_raw); }

                $plant_name_lower = strtolower(trim((string)($plant_name ?? '')));
                $disease_lower = strtolower(trim((string)($disease_detected ?? '')));
                $search_term_lower = strtolower(trim((string)($decoded['search_term'] ?? '')));

                $is_not_plant = (
                    $plant_name_lower === 'unknown object' ||
                    $plant_name_lower === 'not a plant' ||
                    $plant_name_lower === 'scan error' ||
                    $disease_lower === 'not a plant' ||
                    $disease_lower === 'invalid image' ||
                    $search_term_lower === 'not a plant' ||
                    $search_term_lower === 'invalid image'
                );
                
                // Keep legacy care_tips field but preserve original model text quality
                if ($is_not_plant) {
                    $plant_name = "Scan Error";
                    $display_name = "Scan Error";
                    $disease_detected = "Invalid Image";
                    $treatment_steps = [];
                    $care_tips_raw = "";
                    $care_tips = "No care tips or treatments available since this is not a plant. Please scan a clear plant image.";
                } else {
                    $care_tips_parts = [];
                    if (!empty($ai_analysis)) {
                        $care_tips_parts[] = $ai_analysis;
                    }
                    if (!empty($care_tips_raw)) {
                        $care_tips_parts[] = "Care Tips: " . $care_tips_raw;
                    }
                    if (!empty($treatment_steps) && is_array($treatment_steps)) {
                        $steps_text = "Treatment Steps:\n";
                        foreach ($treatment_steps as $step) {
                            $steps_text .= "- " . $step . "\n";
                        }
                        $care_tips_parts[] = trim($steps_text);
                    }
                    $care_tips = !empty($care_tips_parts) ? implode("\n\n", $care_tips_parts) : "No additional care tips available.";
                }
                
                $db_term = $decoded['search_term'] ?? $disease_detected;
                
                // 3. Find Cures in Local DB based on disease name (Limited to 2!)
                if (!$is_not_plant) {
                    $stmt = $conn->prepare("SELECT p.product_id, p.name, p.price, p.image_url FROM diseases d 
                                            JOIN disease_treatments dt ON d.disease_id = dt.disease_id 
                                            JOIN products p ON dt.product_id = p.product_id 
                                            WHERE d.name LIKE CONCAT('%', ?, '%') LIMIT 2");
                    $stmt->bind_param("s", $db_term);
                    $stmt->execute();
                    $cure_result = $stmt->get_result();
                    while ($row = $cure_result->fetch_assoc()) {
                        $cures[] = $row;
                    }

                    // Fallback: If no exact disease match exists, recommend 2 generic top-rated items
                    if (empty($cures)) {
                        $fallback = $conn->query("SELECT product_id, name, price, image_url FROM products WHERE type IN ('Pesticide', 'Fertilizer') LIMIT 2");
                        while ($f_row = $fallback->fetch_assoc()) {
                            $cures[] = $f_row;
                        }
                    }
                } else {
                    $cures = [];
                }
            } else {
                 $disease_detected = "Error: " . ($decoded['error'] ?? "Invalid Response");
            }
        } else {
             $disease_detected = "Service Offline";
             $care_tips = "HTTP: " . $http_code . " Error: " . $curl_error . " | Please ensure the AI server (app.py) is running on port 5001.";
        }

        // 4. Save to Database History (Syncs Mobile App with Web Dashboard)
        if (isset($_POST['user_id'])) {
            $steps_json = !empty($treatment_steps) ? json_encode($treatment_steps) : null;
            $stmt = $conn->prepare("INSERT INTO plant_history (user_id, plant_name, disease_detected, image_path, ai_analysis, treatment_steps, care_tips) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("issssss", $_POST['user_id'], $plant_name, $disease_detected, $image_url_for_app, $ai_analysis, $steps_json, $care_tips);
            $stmt->execute();
        }

        $response = [
            "status" => "success",
            "plant" => $plant_name,
            "display_name" => $display_name,
            "disease" => $disease_detected,
            "image_url" => $image_url_for_app,
            "cures" => $cures,
            "confidence" => $confidence,
            "care_tips" => $care_tips,
            "care_tips_raw" => $care_tips_raw,
            "ai_analysis" => $ai_analysis,
            "treatment_steps" => $treatment_steps,
            "method" => $method
        ];
        
    } else {
        $response = ["status" => "error", "message" => "File upload failed"];
    }
} else {
    $response = ["status" => "error", "message" => "No image provided"];
}

echo json_encode($response);
?>
