"""Test Gemini model fallback chain for plant identification."""
import json
import time
from google import genai
from google.genai import types
from PIL import Image

API_KEY = "AIzaSyAQa9FwfxKSkXF4bEiOZ_wzX_q_fpK1jLY"
MODELS = ["gemini-2.5-flash", "gemini-2.0-flash", "gemini-1.5-flash"]

client = genai.Client(api_key=API_KEY)
image = Image.open("test_leaf.jpg").convert("RGB")

prompt = "You are Flora, a botanical AI. Identify the plant and return JSON with plant_name, disease, display_name, confidence, ai_analysis, treatment_steps, care_tips."

for model in MODELS:
    print(f"\nTrying {model}...")
    try:
        response = client.models.generate_content(
            model=model,
            contents=[image, "Identify this plant. Return JSON."],
            config=types.GenerateContentConfig(
                system_instruction=prompt,
                temperature=0.2,
                response_mime_type="application/json"
            )
        )
        data = json.loads(response.text)
        print(f"  SUCCESS with {model}!")
        print(f"  Plant: {data.get('plant_name')}")
        print(f"  Disease: {data.get('disease')}")
        print(f"  Confidence: {data.get('confidence')}")
        break
    except Exception as e:
        err = str(e)
        if "503" in err or "UNAVAILABLE" in err:
            print(f"  {model} is OVERLOADED (503). Trying next...")
        elif "404" in err:
            print(f"  {model} NOT FOUND. Trying next...")
        else:
            print(f"  Error: {err[:200]}")
        time.sleep(1)
else:
    print("\nAll models failed!")
