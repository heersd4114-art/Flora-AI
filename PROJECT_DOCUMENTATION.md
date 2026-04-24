# Flora AI - Comprehensive Project Documentation

## 🪴 Project Overview
Flora AI is an advanced, full-stack application designed to automatically identify plant species, diagnose their health, and provide curated care recommendations. The system is built with a **Flutter (Dart)** mobile application communicating over **PHP APIs** to a powerful **Python Microservice** powered by **Google Gemini 2.5 Flash**.

---

## 🏗️ Core Architecture & Enhancements

### 1. Robust AI Microservice (Python & Gemini)
The deep-learning inference backend relies on a custom `app.py` Flask server running on local port `5001`.
- **Heuristic Filtering:** We implemented custom OpenCV HSV pixel analysis that intelligently sorts real plants from invalid images (like cars, laptops, or walls), saving API tokens. It is securely tuned to allow vibrant, brightly colored flowers like Hibiscus to pass directly to the AI gracefully.
- **Header Synchronization:** The Gemini API keys are seamlessly piped via explicit `X-API-Key` headers securely embedded in the `config_api.php` pipeline to ensure zero key drops during HTTP transfer. 
- **Expanded PHP Sockets:** The PHP cURL integration limits have been heavily widened to **60 seconds**, guaranteeing the backend never times out while rendering advanced agricultural diagnoses for heavily diseased specimens.

### 2. The Flutter Mobile Gateway
The native Android interface supports a secure, real-time connection to the XAMPP database.
- **Android OS Permissions:** The Release APK features natively compiled `.xml` overrides that authorize active HTTP (`android:usesCleartextTraffic="true"`) connections directly through Windows Network Firewalls over `Port 80`.
- **Integrated Care History:** When a disease is analyzed, the app actively syncs with the MySQL database (`plant_app_db`) and groups associated agricultural products in a horizontal, scrollable UI array right inside the user's scan history.

### 3. Web Dashboard & UI Aesthetics
The Web interface and Mobile app run cohesively out of the identical XAMPP database.
- **Branding:** Replaced all outdated placeholders with a high-fidelity Botanical Green Leaf vector seamlessly scaled across 5 different Android resolution densities.
- **Ecommerce Precision:** All backend store tables, admin dashboards, and cart screens exclusively render numerical totals utilizing authentic Indian Rupee (`₹`) typography.

---

## ⚡ Network Portability (Updating Wi-Fi) 
Due to the dynamic nature of displaying the app on various networks (Coffee Shops, Libraries, University Networks), the Flutter App must constantly adapt to the Laptop's shifting IP address.

To streamline presentation workflows, we developed **`Update_Flora_App.bat`**, currently located precisely on your Desktop.

### Instructions for Presenting
1. Connect both the **Laptop** and your **Phone** to the exact same Wi-Fi connection.
2. Plug your phone into your laptop via USB Debugging.
3. Simply double-click **`Update_Flora_App.bat`**.

**The automation tool will independently:**
1. Execute a PowerShell search routine to identify your temporary IPv4 subnet.
2. Inject the dynamic IPv4 string into the Flutter `config.dart` dependencies.
3. Automatically execute `flutter build apk --release`.
4. Flush the cached Android application off your smartphone via ADB.
5. Push the freshly compiled payload directly to the phone's OS.
6. Launch Flora AI seamlessly without writing a single line of manual code.

---

## 🛠️ Technology Stack
* **Frontend:** Flutter App (Mobile), HTML/CSS/JS (Web Dashboard)
* **Backend Gateway:** PHP 8, Apache Server (XAMPP)
* **Intelligence Layer:** Python 3.11, Flask Engine, Google Gemini Vision Architecture
* **Database:** Native MySQL Server (`root` account)
