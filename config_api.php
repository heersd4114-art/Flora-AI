<?php
// Securely store API keys here
// Ideally, this file should not be in public_html or should be protected
define('PLANT_ID_API_KEY', '2b10w6WxGEJlocxo1OsUEPpOO'); 

$geminiKey = '';
$envPath = __DIR__ . '/.env';
if (file_exists($envPath)) {
	$lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
	foreach ($lines as $line) {
		$line = trim($line);
		if ($line === '' || substr($line, 0, 1) === '#') {
			continue;
		}
		if (substr($line, 0, 15) === 'GEMINI_API_KEY=') {
			$geminiKey = trim(substr($line, strlen('GEMINI_API_KEY=')));
			break;
		}
	}
}

define('GEMINI_API_KEY', $geminiKey);
?>
