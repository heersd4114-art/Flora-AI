$src = "C:\Users\Administrator\.gemini\antigravity\brain\4873868b-fb0d-4b67-a4d6-6629ad1f511e\flora_ai_icon_1770110660414.png"
Copy-Item -Path $src -Destination "d:\plant_app_flutter\android\app\src\main\res\mipmap-mdpi\ic_launcher.png" -Force
Copy-Item -Path $src -Destination "d:\plant_app_flutter\android\app\src\main\res\mipmap-hdpi\ic_launcher.png" -Force
Copy-Item -Path $src -Destination "d:\plant_app_flutter\android\app\src\main\res\mipmap-xhdpi\ic_launcher.png" -Force
Copy-Item -Path $src -Destination "d:\plant_app_flutter\android\app\src\main\res\mipmap-xxhdpi\ic_launcher.png" -Force
Copy-Item -Path $src -Destination "d:\plant_app_flutter\android\app\src\main\res\mipmap-xxxhdpi\ic_launcher.png" -Force
Write-Host "Icons updated successfully"
