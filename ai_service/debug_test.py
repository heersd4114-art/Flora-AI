"""
Standalone test: runs the full predict logic WITHOUT Flask so we can see
the actual exception and traceback clearly.
"""
import os, sys, json, traceback
import numpy as np
from PIL import Image, ImageDraw

print("--- Test 1: flora_ai_engine imports ---")
try:
    from flora_ai_engine import load_llm, generate_plant_analysis
    print("PASS: flora_ai_engine imported OK")
except Exception as e:
    print("FAIL:", e)
    traceback.print_exc()
    sys.exit(1)

print("\n--- Test 2: load_llm ---")
try:
    load_llm()
    print("PASS: load_llm OK")
except Exception as e:
    print("FAIL:", e)
    traceback.print_exc()

print("\n--- Test 3: generate_plant_analysis (healthy) ---")
try:
    r = generate_plant_analysis("Tomato", "Healthy", 0.92, True)
    print("PASS:", r)
except Exception as e:
    print("FAIL:", e)
    traceback.print_exc()

print("\n--- Test 4: generate_plant_analysis (diseased) ---")
try:
    r = generate_plant_analysis("Tomato", "Early blight", 0.85, False)
    print("PASS:", r)
except Exception as e:
    print("FAIL:", e)
    traceback.print_exc()

print("\n--- Test 5: TensorFlow + Keras model ---")
try:
    import tensorflow as tf
    from tensorflow.keras.models import load_model
    from tensorflow.keras.preprocessing.image import img_to_array
    model = load_model("custom_model.keras")
    with open("class_indices.json") as f:
        indices = json.load(f)
    class_labels = {v: k for k, v in indices.items()}

    # Fake image
    img = Image.new('RGB', (224, 224), color=(34, 139, 34))
    draw = ImageDraw.Draw(img)
    draw.ellipse([80, 60, 130, 110], fill=(139, 90, 43))
    img_resized = img.resize((224, 224))
    x = img_to_array(img_resized)
    x = np.expand_dims(x, axis=0)
    x = x / 255.0
    preds = model.predict(x)
    predicted_index = int(np.argmax(preds))
    confidence = float(np.max(preds))
    class_name_raw = class_labels.get(predicted_index, "Unknown")
    print("PASS: Keras prediction =", class_name_raw, "confidence =", round(confidence * 100, 1), "%")
except Exception as e:
    print("FAIL:", e)
    traceback.print_exc()

print("\n--- Test 6: Full pipeline (Keras + LLM) ---")
try:
    if "___" in class_name_raw:
        parts = class_name_raw.split("___")
        plant_name = parts[0].replace("_", " ")
        disease_raw = parts[1].replace("_", " ")
        is_healthy = "healthy" in disease_raw.lower()
        disease_display = "Healthy" if is_healthy else disease_raw
    else:
        plant_name = "Unknown"
        disease_display = class_name_raw.replace("_", " ")
        is_healthy = "healthy" in disease_display.lower()

    result = generate_plant_analysis(plant_name, disease_display, confidence, is_healthy)
    print("PASS full pipeline!")
    print("  plant_name:       ", plant_name)
    print("  disease:          ", disease_display)
    print("  confidence_level: ", result["confidence_level"])
    print("  severity:         ", result["severity"])
    print("  ai_analysis:      ", result["ai_analysis"][:120], "...")
    print("  treatment_steps:  ", result["treatment_steps"])
except Exception as e:
    print("FAIL:", e)
    traceback.print_exc()

print("\nAll tests done.")
