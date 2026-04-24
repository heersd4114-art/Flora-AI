<div align="center">

# 🌿 Flora AI

### *Intelligent Plant Identification & Disease Diagnosis System*

**Scan. Identify. Heal. Grow.**

[![PHP](https://img.shields.io/badge/Backend-PHP%208.x-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://php.net)
[![Flutter](https://img.shields.io/badge/Mobile-Flutter%203.x-02569B?style=for-the-badge&logo=flutter&logoColor=white)](https://flutter.dev)
[![Python](https://img.shields.io/badge/AI%20Engine-Python%203.10-3776AB?style=for-the-badge&logo=python&logoColor=white)](https://python.org)
[![MySQL](https://img.shields.io/badge/Database-MySQL%208.0-4479A1?style=for-the-badge&logo=mysql&logoColor=white)](https://mysql.com)
[![Gemini](https://img.shields.io/badge/AI-Google%20Gemini-8E75B2?style=for-the-badge&logo=google&logoColor=white)](https://ai.google.dev)
[![License](https://img.shields.io/badge/License-MIT-green?style=for-the-badge)](LICENSE)

---

> *Flora AI is a full-stack, AI-powered botanical intelligence platform that combines real-time plant identification, disease diagnosis, an integrated e-commerce store for treatments, and a delivery management system — all powered by Google Gemini Vision AI.*

</div>

---

## 📑 Table of Contents

- [✨ Features](#-features)
- [🏗️ System Architecture](#️-system-architecture)
- [🧬 Technology Stack](#-technology-stack)
- [📂 Project Structure](#-project-structure)
- [⚙️ Installation & Setup](#️-installation--setup)
- [🔌 API Reference](#-api-reference)
- [🤖 AI Engine Details](#-ai-engine-details)
- [🗄️ Database Schema](#️-database-schema)
- [👥 User Roles](#-user-roles)
- [🛣️ Roadmap](#️-roadmap)
- [🤝 Contributing](#-contributing)
- [📜 License](#-license)

---

## ✨ Features

<table>
<tr>
<td width="50%">

### 🔬 AI Plant Scanner
- **Real-time identification** of plants via camera or gallery upload
- **Disease detection** with severity assessment and confidence scoring
- **Treatment recommendations** with step-by-step care guides
- Powered by **Google Gemini 2.5 Flash** with intelligent model chaining
- **ResNet-50 local fallback** when cloud AI is unavailable

</td>
<td width="50%">

### 🛒 Integrated Marketplace
- Browse & purchase **pesticides, fertilizers, tools** based on AI diagnosis
- **Smart product linking** — AI-detected diseases auto-suggest treatments
- Full **shopping cart & checkout** with COD payment support
- **Order tracking** with real-time status updates

</td>
</tr>
<tr>
<td width="50%">

### 📱 Cross-Platform Mobile App
- **Flutter-based** native app for Android & iOS
- Beautiful **Material 3** design with dark/light themes
- **Camera integration** for instant plant scanning
- Offline-capable with **PWA support** on web

</td>
<td width="50%">

###  Admin & Delivery Portal
- **Admin dashboard** with user, product, disease & order management
- **Delivery partner system** with order assignment & tracking
- **Plant & disease database** management (CRUD operations)
- **Analytics overview** with key business metrics

</td>
</tr>
</table>

---

## 🏗️ System Architecture

```
┌─────────────────────────────────────────────────────────────────────┐
│                        FLORA AI — SYSTEM OVERVIEW                  │
├─────────────────────────────────────────────────────────────────────┤
│                                                                     │
│   ┌──────────────┐     ┌──────────────┐     ┌──────────────────┐   │
│   │   Flutter     │     │   PHP Web    │     │   Admin Panel    │   │
│   │  Mobile App   │     │   Frontend   │     │   (PHP + CSS)    │   │
│   │  (Dart 3.x)   │     │  (Bootstrap) │     │                  │   │
│   └──────┬───────┘     └──────┬───────┘     └────────┬─────────┘   │
│          │                    │                       │             │
│          └────────────┬───────┴───────────────────────┘             │
│                       │                                             │
│                       ▼                                             │
│          ┌────────────────────────┐                                 │
│          │    PHP REST API Layer  │                                 │
│          │  /api/*.php endpoints  │                                 │
│          └───────────┬────────────┘                                 │
│                      │                                              │
│          ┌───────────┴────────────┐                                 │
│          │                        │                                 │
│          ▼                        ▼                                 │
│  ┌───────────────┐    ┌─────────────────────────────────┐          │
│  │   MySQL DB    │    │   Flask AI Service (:5001)      │          │
│  │ plant_app_db  │    │  ┌─────────────────────────┐    │          │
│  │               │    │  │  Google Gemini 2.5 Flash │    │          │
│  │  12 Tables    │    │  │  (Primary AI Engine)     │    │          │
│  │  - users      │    │  └────────────┬────────────┘    │          │
│  │  - products   │    │               │                 │          │
│  │  - plants     │    │  ┌────────────▼────────────┐    │          │
│  │  - diseases   │    │  │  ResNet-50 Local Model  │    │          │
│  │  - orders     │    │  │  (Fallback Engine)      │    │          │
│  │  - cart       │    │  └─────────────────────────┘    │          │
│  │  - shipments  │    │                                 │          │
│  │  ...          │    └─────────────────────────────────┘          │
│  └───────────────┘                                                 │
│                                                                     │
└─────────────────────────────────────────────────────────────────────┘
```

---

## 🧬 Technology Stack

| Layer | Technology | Purpose |
|:---:|:---|:---|
| 📱 **Mobile** | Flutter 3.x / Dart | Cross-platform Android & iOS application |
| 🌐 **Web Frontend** | PHP + HTML5 + CSS3 + JS | Responsive web application with PWA |
| ⚙️ **Backend API** | PHP 8.x (REST) | RESTful API endpoints for all CRUD operations |
| 🤖 **AI Service** | Python 3.10 + Flask | Plant identification & disease analysis microservice |
| 🧠 **Primary AI** | Google Gemini 2.5 Flash | Vision-based plant identification with JSON output |
| 🔄 **Fallback AI** | Microsoft ResNet-50 | Local inference when Gemini is unavailable |
| 🗄️ **Database** | MySQL 8.0 | Relational database with 12 normalized tables |
| 🖥️ **Server** | Apache (XAMPP) | Local development & production web server |

---

## 📂 Project Structure

```
plant_app/
│
├── 📱 plant_app_flutter/          # Flutter mobile application
│   └── lib/
│       ├── main.dart              # App entry point
│       ├── config.dart            # API base URL configuration
│       ├── models/                # Data models (User, Product, Order)
│       ├── providers/             # State management (Provider)
│       ├── screens/               # 21 UI screens
│       │   ├── home_screen.dart          # Main dashboard
│       │   ├── scan_screen.dart          # AI plant scanner (camera/gallery)
│       │   ├── disease_history_screen.dart # Past scan results
│       │   ├── cart_screen.dart           # Shopping cart
│       │   ├── admin_dashboard.dart       # Admin panel
│       │   ├── delivery_list_screen.dart  # Delivery management
│       │   └── ...                        # 15 more screens
│       └── services/              # API communication layer
│
├── 🤖 ai_service/                 # Python AI microservice
│   ├── app.py                     # Flask server (port 5001)
│   ├── flora_ai_engine.py         # Gemini integration logic
│   ├── requirements.txt           # Python dependencies
│   └── class_indices.json         # Plant classification mapping
│
├── 🔌 api/                        # PHP REST API endpoints
│   ├── identify.php               # AI identification proxy
│   ├── login.php                  # Authentication
│   ├── register.php               # User registration
│   ├── cart.php                   # Cart CRUD operations
│   ├── products.php               # Product catalog
│   ├── orders.php                 # Order management
│   ├── get_history.php            # Scan history retrieval
│   ├── admin_products.php         # Admin product management
│   ├── partner_orders.php         # Delivery partner orders
│   └── ...                        # 10 more endpoints
│
├── 🛡️ admin/                      # Admin web dashboard
│   ├── dashboard.php              # Admin overview panel
│   ├── products.php               # Product management (CRUD)
│   ├── diseases.php               # Disease database management
│   ├── plants.php                 # Plant catalog management
│   ├── orders.php                 # Order processing
│   ├── delivery.php               # Delivery partner management
│   └── users.php                  # User administration
│
├── 🗄️ database/                   # SQL schema & migrations
│   └── setup.sql                  # Complete schema (12 tables)
│
├── 🚚 delivery/                   # Delivery partner portal
│   ├── dashboard.php              # Partner dashboard
│   └── update_status.php          # Shipment status updates
│
├── 📦 includes/                   # Shared PHP components
│   ├── navbar.php                 # Navigation bar
│   └── header_pwa.php            # PWA meta headers
│
├── 🎨 assests/                    # Static assets
│   ├── css/                       # Stylesheets
│   └── images/                    # Product & UI images
│
├── 🌐 Web Pages (Root)            # Customer-facing web pages
│   ├── index.php                  # Landing page
│   ├── login.php                  # Login page
│   ├── register.php               # Registration page
│   ├── identify.php               # AI scanner page
│   ├── store.php                  # Product store
│   ├── cart.php                   # Shopping cart
│   ├── checkout.php               # Order checkout
│   ├── dashboard.php              # User dashboard
│   ├── disease.php                # Disease info page
│   └── profile.php                # User profile
│
├── config.php                     # Database configuration
├── manifest.json                  # PWA manifest
├── sw.js                          # Service Worker
└── FLORA_AI_SYSTEM_DOCS.md       # Technical system manifesto
```

---

## ⚙️ Installation & Setup

### Prerequisites

| Tool | Version | Purpose |
|:---|:---|:---|
| [XAMPP](https://www.apachefriends.org/) | 8.x+ | Apache + MySQL + PHP |
| [Python](https://python.org) | 3.10+ | AI service runtime |
| [Flutter](https://flutter.dev) | 3.x+ | Mobile app development |
| [Git](https://git-scm.com) | Latest | Version control |

### 🔧 Step 1 — Clone the Repository

```bash
git clone https://github.com/heersd4114-art/Flora-AI.git
cd Flora-AI
```

### 🗄️ Step 2 — Database Setup

1. Start **XAMPP** → Enable **Apache** and **MySQL**
2. Open **phpMyAdmin** → `http://localhost/phpmyadmin`
3. Import the database schema:

```sql
-- Import database/setup.sql via phpMyAdmin
-- Or run directly:
mysql -u root < database/setup.sql
```

### 🤖 Step 3 — AI Service Setup

```bash
# Navigate to AI service directory
cd ai_service

# Create virtual environment
python -m venv venv
venv\Scripts\activate        # Windows
# source venv/bin/activate   # Linux/Mac

# Install dependencies
pip install -r requirements.txt

# Start the AI server
python app.py
# → Running on http://127.0.0.1:5001
```

### 🔑 Step 4 — Configure API Key

Create a `.env` file in the project root:

```env
GEMINI_API_KEY=your_google_gemini_api_key_here
```

> 💡 Get your free API key at [Google AI Studio](https://aistudio.google.com/apikey)

### 📱 Step 5 — Flutter Mobile App

```bash
cd plant_app_flutter

# Update the API base URL in lib/config.dart
# Set it to your machine's local IP (e.g., http://192.168.1.x)

flutter pub get
flutter run
```

### 🌐 Step 6 — Access the Web App

```
Customer Portal:  http://localhost/plant_app/
Admin Dashboard:  http://localhost/plant_app/admin/
Delivery Portal:  http://localhost/plant_app/delivery/dashboard.php
AI Health Check:  http://localhost:5001/
```

---

## 🔌 API Reference

### 🔐 Authentication

| Method | Endpoint | Description |
|:---:|:---|:---|
| `POST` | `/api/login.php` | User login (returns user object) |
| `POST` | `/api/register.php` | New user registration |

### 🔬 AI Identification

| Method | Endpoint | Description |
|:---:|:---|:---|
| `POST` | `/api/identify.php` | Upload image → AI identification result |

**Request:** `multipart/form-data` with `image` field  
**Response:**
```json
{
  "plant_name": "Monstera deliciosa",
  "display_name": "Monstera deliciosa - Healthy",
  "disease": "Healthy",
  "confidence": 0.94,
  "confidence_level": "High",
  "is_healthy": true,
  "severity": "None",
  "ai_analysis": "This is a healthy Monstera deliciosa...",
  "treatment_steps": ["Maintain current care routine..."],
  "care_tips": "Bright indirect light, water weekly...",
  "method": "Google gemini-2.5-flash Vision API"
}
```

### 🛒 E-Commerce

| Method | Endpoint | Description |
|:---:|:---|:---|
| `GET` | `/api/products.php` | List all products |
| `POST` | `/api/cart.php` | Add / update / remove cart items |
| `GET` | `/api/orders.php?user_id={id}` | Get user's order history |
| `POST` | `/api/orders.php` | Place a new order |

### 📊 Admin APIs

| Method | Endpoint | Description |
|:---:|:---|:---|
| `GET` | `/api/admin_dashboard_api.php` | Dashboard statistics |
| `GET/POST` | `/api/admin_products.php` | Product CRUD management |
| `GET` | `/api/api_users.php` | List all users |
| `GET` | `/api/admin_orders.php` | List all orders |

### 📜 Scan History

| Method | Endpoint | Description |
|:---:|:---|:---|
| `GET` | `/api/get_history.php?user_id={id}` | Get past scan results |
| `DELETE` | `/api/delete_history.php` | Delete a scan record |

---

## 🤖 AI Engine Details

Flora AI uses a **multi-tier AI architecture** for maximum reliability:

```
Image Upload
     │
     ▼
┌─────────────────────┐
│  Gemini 2.5 Flash   │ ◄── Primary Engine (Cloud)
│  (High Accuracy)    │     Temperature: 0.2
└─────────┬───────────┘     Response: Strict JSON
          │
     On Failure
          │
          ▼
┌─────────────────────┐
│  Gemini 2.5 Flash   │ ◄── Secondary Model (Auto-failover)
│       Lite           │
└─────────┬───────────┘
          │
     On Failure
          │
          ▼
┌─────────────────────┐
│   ResNet-50 Local   │ ◄── Offline Fallback
│  (microsoft/resnet)  │     No internet needed
└─────────────────────┘
```

### AI Response Fields

| Field | Type | Description |
|:---|:---:|:---|
| `plant_name` | `string` | Scientific/common name of the plant |
| `disease` | `string` | Detected disease or "Healthy" |
| `confidence` | `float` | AI confidence score (0.0 – 1.0) |
| `confidence_level` | `string` | Human-readable: Low / Medium / High |
| `is_healthy` | `bool` | Quick health status flag |
| `severity` | `string` | Disease severity: None / Mild / Moderate / Severe |
| `ai_analysis` | `string` | Detailed botanical analysis paragraph |
| `treatment_steps` | `array` | Step-by-step treatment instructions |
| `care_tips` | `string` | Ongoing care recommendations |

---

## 🗄️ Database Schema

Flora AI uses a **normalized MySQL schema** with **12 interconnected tables**:

```
┌──────────┐     ┌──────────────┐     ┌──────────────────┐
│  users   │────▶│    orders     │────▶│   order_items    │
│          │     │              │     │                  │
│ user_id  │     │ order_id     │     │ order_id (FK)    │
│ name     │     │ user_id (FK) │     │ product_id (FK)  │
│ email    │     │ total_amount │     │ quantity         │
│ role     │     │ status       │     │ price_at_purchase│
└────┬─────┘     └──────────────┘     └──────────────────┘
     │
     │           ┌──────────────┐     ┌──────────────────┐
     ├──────────▶│    cart       │────▶│    products      │
     │           │ user_id (FK) │     │ product_id       │
     │           │ product_id   │     │ name / type      │
     │           │ quantity     │     │ price / stock    │
     │           └──────────────┘     └────────┬─────────┘
     │                                         │
     │           ┌──────────────┐     ┌────────▼─────────┐
     ├──────────▶│plant_history │     │disease_treatments│
     │           │ plant_name   │     │ disease_id (FK)  │
     │           │ disease      │     │ product_id (FK)  │
     │           │ ai_analysis  │     └────────┬─────────┘
     │           └──────────────┘              │
     │                                ┌────────▼─────────┐
     │           ┌──────────────┐     │    diseases       │
     └──────────▶│delivery_     │     │ disease_id       │
                 │ partners     │     │ symptoms         │
                 │ partner_id   │     │ care_tips        │
                 │ vehicle_type │     └──────────────────┘
                 └──────┬───────┘
                        │         ┌──────────────────┐
                        └────────▶│   shipments      │
                                  │ order_id (FK)    │
                                  │ partner_id (FK)  │
                                  │ status / tracking│
                                  └──────────────────┘
```

**Additional Tables:** `plants`, `categories`

---

## 👥 User Roles

| Role | Access | Capabilities |
|:---:|:---|:---|
| 🌱 **Customer** | Web + Mobile App | Scan plants, browse store, manage cart, place orders, view history |
| 🛡️ **Admin** | Admin Dashboard | Full CRUD on users, products, diseases, plants, orders & delivery partners |
| 🚚 **Delivery Partner** | Delivery Portal | View assigned orders, update shipment status, manage availability |

---

## 🛣️ Roadmap

- [x] AI-powered plant identification with Gemini Vision
- [x] Disease detection with treatment recommendations
- [x] E-commerce store with cart & checkout
- [x] Multi-role user system (Customer / Admin / Delivery)
- [x] Flutter cross-platform mobile app
- [x] PWA support for web application
- [x] Delivery management system with shipment tracking
- [x] Multi-model AI failover (Gemini → ResNet)
- [ ] Push notifications for order updates
- [ ] Plant growth tracking with periodic scans
- [ ] Community plant sharing & care templates
- [ ] Integration with IoT soil moisture sensors
- [ ] Multi-language support (Hindi, Marathi, Tamil)

---

## 🤝 Contributing

Contributions are welcome! Follow these steps:

```bash
# 1. Fork the repository
# 2. Create your feature branch
git checkout -b feature/amazing-feature

# 3. Commit your changes
git commit -m "Add amazing feature"

# 4. Push to the branch
git push origin feature/amazing-feature

# 5. Open a Pull Request
```

---

## 📜 License

This project is licensed under the **MIT License** — see the [LICENSE](LICENSE) file for details.

---

<div align="center">

### 🌱 Flora AI — *Version Control for Nature*

**Built with 💚 by [heersd4114-art](https://github.com/heersd4114-art)**

*Scan a leaf today. Save a plant tomorrow.*

---

![Made with Love](https://img.shields.io/badge/Made%20With-💚-22c55e?style=for-the-badge)
![Powered by Gemini](https://img.shields.io/badge/Powered%20By-Google%20Gemini-8E75B2?style=for-the-badge&logo=google)
![Built with Flutter](https://img.shields.io/badge/Built%20With-Flutter-02569B?style=for-the-badge&logo=flutter)

</div>
