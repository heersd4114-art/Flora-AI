"""
Flora AI Local LLM Engine
Uses Microsoft Phi-3-mini (GGUF) - runs 100% locally on your PC.
Model cached on D: drive to save C: drive space.
Same architecture as D:/Ai_powered_financial_planing/core/local_ai_engine.py
"""
import os

# Force HuggingFace to cache model on C: drive (saves C: space)
os.environ["HF_HOME"] = "C:/xampp/htdocs/plant_app/plant_app_data/hf_cache"

llm = None


def load_llm():
    """Downloads (first run only, ~2.3 GB to D:) and loads Phi-3-mini."""
    global llm
    if llm is not None:
        return
    try:
        from huggingface_hub import hf_hub_download
        from llama_cpp import Llama
        print("Flora AI: Loading Phi-3-mini (first run downloads ~2.3 GB to D: drive)...")
        model_path = hf_hub_download(
            repo_id="microsoft/Phi-3-mini-4k-instruct-gguf",
            filename="Phi-3-mini-4k-instruct-q4.gguf"
        )
        llm = Llama(
            model_path=model_path,
            n_ctx=1024,
            n_threads=8,
            n_batch=512,
            n_gpu_layers=-1,
            verbose=False
        )
        print("Flora AI: Phi-3-mini loaded successfully!")
    except ImportError:
        print("Flora AI WARNING: pip install llama-cpp-python huggingface-hub")
        llm = None
    except Exception as e:
        print(f"Flora AI WARNING: {e}")
        llm = None


TREATMENT_DB = {
    "healthy":   ["Keep watering when soil feels dry.",
                  "Ensure 6+ hours of sunlight daily.",
                  "Feed with balanced fertilizer once a month."],
    "blight":    ["Remove and destroy all infected leaves immediately.",
                  "Apply copper-based fungicide every 7 days.",
                  "Water only at the base, never on leaves.",
                  "Improve air circulation by spacing plants apart."],
    "mildew":    ["Remove heavily infected leaves.",
                  "Spray neem oil or sulfur fungicide on all surfaces.",
                  "Water at the base only, never on leaves.",
                  "Space plants further apart to improve airflow."],
    "rot":       ["Prune all affected stems and roots.",
                  "Reduce watering frequency immediately.",
                  "Apply a systemic fungicide to the soil.",
                  "Repot in fresh, well-draining soil if possible."],
    "spot":      ["Avoid overhead watering.",
                  "Apply copper-based fungicide.",
                  "Remove and safely dispose of infected leaves.",
                  "Rotate crops next growing season."],
    "rust":      ["Isolate the plant from others immediately.",
                  "Apply sulfur-based or neem oil fungicide.",
                  "Remove all leaves with orange or brown pustules.",
                  "Avoid working with plants when they are wet."],
    "virus":     ["No chemical cure - remove and destroy the plant.",
                  "Control whiteflies and aphids on nearby plants.",
                  "Disinfect all tools with bleach solution after use.",
                  "Do NOT compost infected plant material."],
    "mosaic":    ["Remove and destroy the infected plant immediately.",
                  "Control aphids and sap-sucking insects nearby.",
                  "Disinfect tools with bleach after every use.",
                  "Plant virus-resistant varieties in future."],
    "scab":      ["Remove infected fruit and leaves promptly.",
                  "Apply fungicide at early bud-break stage.",
                  "Prune for better air circulation.",
                  "Clean up all fallen leaves at end of season."],
    "bacterial": ["Apply copper-based bactericide every 5-7 days.",
                  "Avoid overhead irrigation.",
                  "Remove infected plant parts.",
                  "Disinfect tools after every use."],
    "curl":      ["Remove and destroy affected plants.",
                  "Control whitefly populations with insecticidal soap.",
                  "Use reflective mulch to deter whiteflies.",
                  "Plant resistant varieties next season."],
    "default":   ["Isolate the plant from healthy ones.",
                  "Remove all visibly infected leaves.",
                  "Consult a local nursery with a photo for confirmation.",
                  "Monitor the plant daily for any further spread."]
}


def _get_steps(disease, is_healthy):
    """Get treatment steps from the knowledge base."""
    if is_healthy:
        return TREATMENT_DB["healthy"]
    d = disease.lower()
    for key in TREATMENT_DB:
        if key != "default" and key in d:
            return TREATMENT_DB[key]
    return TREATMENT_DB["default"]


def _get_severity(disease, is_healthy):
    """Determine severity level from disease name."""
    if is_healthy:
        return "None"
    d = disease.lower()
    if any(w in d for w in ["blight", "rot", "virus", "mosaic", "curl"]):
        return "Severe"
    if any(w in d for w in ["mildew", "scab", "spot", "rust", "bacterial", "haunglongbing"]):
        return "Moderate"
    return "Mild"


def _fallback_analysis(plant_name, condition_str, confidence_pct, confidence_level, severity, is_healthy):
    """Template-based analysis when Phi-3 is not available."""
    if is_healthy:
        return (
            f"With {confidence_pct}% confidence ({confidence_level}), your {plant_name} "
            f"plant appears completely healthy. No signs of infection or disease were "
            f"detected in the image. Keep up with regular watering and balanced nutrition "
            f"to maintain this healthy state."
        )
    return (
        f"With {confidence_pct}% confidence ({confidence_level}), your {plant_name} plant "
        f"has been identified with {condition_str} ({severity} severity). This disease "
        f"damages the leaves and reduces the plant\'s ability to absorb sunlight and nutrients. "
        f"If left untreated, it can spread rapidly to healthy parts and nearby plants. "
        f"Act immediately by following the treatment steps below to stop the spread."
    )


# Phi-3 prompt format tags
SYS_OPEN = "<|system|>"
SYS_CLOSE = "<|end|>"
USR_OPEN = "<|user|>"
USR_CLOSE = "<|end|>"
ASST_OPEN = "<|assistant|>"

def generate_plant_analysis(plant_name, disease, confidence, is_healthy):
    """
    Main function called by app.py.
    Takes keras model prediction, generates rich analysis using Phi-3-mini LLM.
    Falls back to structured template if Phi-3 is not available.
    Returns dict with: ai_analysis, treatment_steps, severity, confidence_level
    """
    confidence_pct = round(confidence * 100, 1)
    if confidence >= 0.90:
        confidence_level = "Very High"
    elif confidence >= 0.75:
        confidence_level = "High"
    elif confidence >= 0.55:
        confidence_level = "Medium"
    else:
        confidence_level = "Low"

    severity = _get_severity(disease, is_healthy)
    steps = _get_steps(disease, is_healthy)
    condition_str = "Healthy" if is_healthy else disease

    # ── Try Phi-3-mini LLM first ──
    ai_analysis = None
    if llm is not None:
        try:
            sys_p = (
                "You are Flora AI, an expert botanist and plant disease specialist.\n"
                "Give precise, actionable advice a farmer or gardener can use right now.\n\n"
                "DETECTION DATA (use ONLY these exact values, do not invent numbers):\n"
                f"  Plant: {plant_name}\n"
                f"  Condition: {condition_str}\n"
                f"  Confidence: {confidence_pct}% ({confidence_level})\n"
                f"  Severity: {severity}\n\n"
                "RULES: Write exactly 3-4 plain English sentences, one paragraph. "
                "No bullet points, no markdown, no lists. "
                "Mention the plant name and confidence in the first sentence. "
                "Explain what this condition does to the plant in simple words. "
                "End with the single most urgent action the user must take right now."
            )
            usr_p = f"Analyze my {plant_name} plant showing {condition_str}."
            prompt = SYS_OPEN + "\n" + sys_p + SYS_CLOSE + "\n" + USR_OPEN + "\n" + usr_p + USR_CLOSE + "\n" + ASST_OPEN + "\n"
            response = llm(prompt, max_tokens=300, temperature=0.1, stop=[SYS_CLOSE, USR_OPEN])
            raw_text = response["choices"][0]["text"].strip()
            # Sanitize: replace any non-ASCII characters that cause Windows encoding errors
            ai_analysis = raw_text.encode("ascii", errors="replace").decode("ascii").replace("?", " ")
            ai_analysis = " ".join(ai_analysis.split())  # Clean extra spaces
            print(f"Flora AI LLM Analysis: {ai_analysis[:100]}...")
        except Exception as e:
            print(f"Flora AI LLM Error (using fallback): {e}")
            ai_analysis = None

    # ── Fallback if LLM not available ──
    if ai_analysis is None:
        ai_analysis = _fallback_analysis(plant_name, condition_str, confidence_pct, confidence_level, severity, is_healthy)

    return {
        "ai_analysis": ai_analysis,
        "treatment_steps": steps,
        "severity": severity,
        "confidence_level": confidence_level
    }

