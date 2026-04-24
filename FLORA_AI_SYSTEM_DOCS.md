# 🌿 Flora AI: Technical Documentation & System Manifesto

## 1. Overview

Flora AI is a distributed intelligence system designed for the lifecycle management, health monitoring, and growth optimization of botanical assets. Much like Git tracks changes in code, Flora AI tracks changes in biological organisms, allowing users to "version control" their gardens, diagnose "bugs" (pests/disease), and "merge" expert botanical knowledge into their daily routines.

---

## 2. The Flora Glossary (Git-to-Flora Mapping)

To understand Flora AI, you must understand its architecture:

| Git Concept | Flora Concept | Description |
|---|---|---|
| **Repository** | **Garden** | The primary collection of plants and environmental data. |
| **Commit** | **Care Log** | A recorded snapshot of a plant's state after an action (watering, pruning). |
| **Branch** | **Experiment** | A separate care routine (e.g., testing a new fertilizer on one specific cutting). |
| **Merge** | **Succession** | Applying a successful care routine from a test branch to the main garden. |
| **Clone** | **Community Share** | Downloading a proven care template from another user. |
| **Diff** | **Growth Delta** | The visual and biological difference between two points in time. |

---

## 3. Command Line Interface (CLI) Reference

### `flora init`
**Initializes a new digital garden.**
- **Description:** Creates a `.flora` metadata folder to track light levels, humidity, and species lists.
- **Flag:** `--indoor` or `--outdoor` to set base environmental parameters.

### `flora scan [image_file]`
**The core identification engine.**
- **Description:** Uses computer vision to identify species, genus, and health status.
- **Output:** Returns a **Confidence Score**, **Toxicity Level**, and **Care Requirements**.

### `flora commit -m "Message"`
**Records an intervention.**
- **Usage:** `flora commit -m "Added nitrogen-rich fertilizer and moved to south-facing window"`
- **Function:** Saves the state to the history log for future growth analysis.

### `flora status`
**The health dashboard.**
- **Description:** Lists "staged" plants (those needing attention) and "unstaged" plants (those thriving).
- **Alerts:** Highlights critical "conflicts" (e.g., root rot, spider mites).

### `flora diagnose --deep`
**AI-driven troubleshooting.**
- **Description:** Analyzes leaf discoloration or wilting patterns.
- **Deep Flag:** Triggers a soil-chemistry analysis request or weather-pattern correlation.

---

## 4. The System Prompt (The "Brain" of Flora AI)

If you are building this into an LLM (like GPT-4), use this as the **System Instruction**:

> **Role:** You are the Flora AI Core. You are a world-class botanist, horticulturalist, and environmental scientist with a technical, data-driven personality.
>
> **Objective:** Help users maintain a 100% survival rate for their botanical assets through precise, actionable advice.

### Identity Guidelines

- **Scientific Precision:** Always use the binomial nomenclature (Latin name) alongside common names (e.g., *Monstera deliciosa*).
- **Safety First:** If a plant is toxic to felines, canines, or children, you **MUST** prefix your response with a `[⚠️ TOXICITY ALERT]`.
- **The "Git" Logic:** View every user interaction as a "Care Event." If a user says "I watered it," consider that a state change.
- **Diagnostic Protocol:** When a user reports a problem, ask for:
  1. Light exposure (Foot-candles or orientation).
  2. Watering frequency and drainage.
  3. Soil type.
- **Tone:** Professional, encouraging, and highly structured. Use Markdown for all care guides.

---

## 5. Standard Workflow (The Flora Lifecycle)

### Phase 1: Staging
- User scans a new plant.
- **Action:** `flora scan`
- **Result:** AI identifies a *Ficus Lyrata*. AI suggests a "Standard Care Branch."

### Phase 2: Monitoring
- User tracks growth over 3 months.
- **Action:** `flora log --visual`
- **Result:** AI generates a "Growth Delta" showing a 15% increase in leaf surface area.

### Phase 3: Conflict Resolution
- The plant develops brown spots.
- **Action:** `flora diagnose`
- **AI Logic:** *"Conflict detected: Overwatering detected on 'Main Branch'. Recommended Fix: Revert watering frequency by 2 days and check drainage."*

### Phase 4: Pushing to Community
- User achieves a rare bloom.
- **Action:** `flora push --community`
- **Result:** The care parameters (humidity, temp, light) are uploaded as a "Golden Template" for other users.

---

## 6. Advanced AI Features

| Feature | Description |
|---|---|
| **Flora Vision (The Eye)** | Real-time AR overlay showing the "Health Bar" of a plant when viewed through a camera. |
| **Predictive Growth (The Future)** | Based on current care, Flora AI simulates what the plant will look like in 6 months, 1 year, and 5 years. |
| **Hydration Sync** | Integrates with smart soil moisture sensors to trigger flora-bot notifications when soil moisture drops below 20%. |

---

## 7. Error Codes & Troubleshooting

| Error Code | Name | Description |
|---|---|---|
| `404` | Plant Not Found | Plant not found in database. Prompt user to submit to Flora Lab for manual review. |
| `505` | Environment Conflict | User trying to grow a tropical plant in an arctic "Main Branch" without a heater. |
| `MERGE_CONFLICT` | Care Conflict | Two different users giving conflicting care advice to the same shared garden. |

---

## 8. Technology Stack

- **Frontend (Web):** PHP, HTML5, CSS3, JavaScript — served via Apache (XAMPP)
- **Frontend (Mobile):** Flutter / Dart — cross-platform Android & iOS
- **Backend API:** PHP REST API
- **AI Service:** Python (Flask) with Google Gemini AI integration
- **Database:** MySQL
- **PWA Support:** Service Worker (`sw.js`) + Web App Manifest

---

*Flora AI — Version Control for Nature. 🌱*
