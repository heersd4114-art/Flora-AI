# Flora AI: Technical Project Report & Presentation Guide

## 1. Project Overview
Flora AI is a dual-platform (Web & Mobile) ecosystem designed to identify plant species and diagnose diseases using Advanced Vision AI. It bridges the gap between expert botanical knowledge and everyday gardening through an easy-to-use digital interface.

## 2. Technology Stack

### Frontend (User Interface)
- **Web Interface**: 
  - **Language**: HTML5, JavaScript (Vanilla), PHP (for dynamic server-side rendering).
  - **Styling**: Vanilla CSS3 with "Antigravity" premium design principles—focusing on glassmorphism, vibrant gradients, and smooth micro-animations for a premium feel.
- **Mobile Application**:
  - **Language**: **Dart** (using the **Flutter** Framework).
  - **Architecture**: Material 3 Design, handling high-performance REST API communication for real-time scans.

### Backend (Logic & Database)
- **Server**: **PHP** (running on XAMPP / Apache).
- **Database**: **MySQL (MariaDB)**. Stores user credentials, scan history, AI analysis results, and the e-commerce product catalog.
- **API Bridge**: PHP scripts acting as the secure middleman between the UI and the AI Service, managing sessions and data integrity.

### AI Engine (The Brain)
- **Primary AI**: **Google Gemini 1.5/2.5 Flash Vision**. Used for high-speed, high-precision multimodal analysis of plant images.
- **Local Fallback**: **Microsoft Phi-3-mini** (GGUF) and **ResNet-50**. These run 100% locally on the host PC via Python to ensure the system remains functional even without internet access.
- **Service Layer**: **Flask (Python)**. A specialized API server running on port 5001 that orchestrates the AI models and returns structured JSON analysis.

## 3. How everything "Sinks" (The Connection)
The system is built on a **Centralized Shared-Database Architecture**:
1. **Unified History**: When a user scans a plant on the Mobile app, the record is instantly saved to the MySQL database. When the user later logs in to the Web Dashboard, the exact same scan and AI analysis appear in their "Diagnostic History."
2. **Product Mapping**: The AI identifies a specific disease (e.g., "Powdery Mildew"). The backend logic then "sinks" this identifying term with the `products` table in MySQL to recommend specific curative products (pesticides, fertilizers) available in the store.
3. **Cross-Platform Access**: Both Flutter (Mobile) and PHP (Web) consume the same REST API endpoints, ensuring data is always synchronized across all devices in real-time.

## 4. How the AI Model Works
1. **Input Phase**: The user captures or uploads a leaf/plant image.
2. **Vision Processing**: The image is transmitted to the Flask AI Service.
3. **Multimodal Analysis**:
   - **Computer Vision**: The model analyzes pixels to detect patterns like necrosis, chlorosis, or fungal growth.
   - **Reasoning**: It applies botanical logic to determine the most likely cause based on the visual symptoms.
4. **Structured Response**: The AI returns a strict JSON object containing the Plant Name, Condition, Confidence Score, Severity Level, and specific Treatment Steps.

## 5. Potential Presentation Questions
- **Q: How do you handle "Non-Plant" images?**
  - **A**: We implemented a "Strict Rejection Heuristic" within the AI system prompt. If the model determines the subject is not a plant (e.g., a person or an object), it returns a specific "Invalid Specimen" error to prevent false positives.
- **Q: Why a Hybrid AI approach (Gemini + Local)?**
  - **A**: It provides **Zero-Gravity Reliability**. Gemini offers the most advanced global intelligence, while the local Phi-3 model ensures the system never crashes if the internet goes down.
- **Q: How is data security handled?**
  - **A**: We use a `.env` configuration system to hide API keys from the source code, and PHP sessions to ensure users can only access their own scan history.

---
**Note for Presentation**: Present this as a "Scalable Agriculture Solution" that combines High-Level Cloud AI with Local Edge Computing.
