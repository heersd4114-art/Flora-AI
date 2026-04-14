"""Quick test to verify Gemini API works with an image."""
from google import genai
from google.genai import types
from PIL import Image
import json

API_KEY = "AIzaSyAQa9FwfxKSkXF4bEiOZ_wzX_q_fpK1jLY"
client = genai.Client(api_key=API_KEY)
image = Image.open("test_leaf.jpg").convert("RGB")

system_prompt = "You are Flora, an expert botanical AI. Identify this plant and its health. Return STRICTLY valid JSON: {\"plant_name\": \"name\", \"disease\": \"Healthy or disease\", \"display_name\": \"name - condition\", \"confidence\": 0.95, \"ai_analysis\": \"analysis\", \"treatment_steps\": [\"step1\"], \"care_tips\": \"tips\"}"

try:
    response = client.models.generate_content(
        model="gemini-2.5-flash",
        contents=[image, "Identify this plant using JSON format."],
        config=types.GenerateContentConfig(
            system_instruction=system_prompt,
            temperature=0.2,
            response_mime_type="application/json"
        )
    )
    print("RAW RESPONSE:", response.text[:500])
    data = json.loads(response.text)
    print("PLANT:", data.get("plant_name"))
    print("DISEASE:", data.get("disease"))
    print("CONFIDENCE:", data.get("confidence"))
    print("SUCCESS - API works!")
except Exception as e:
    print(f"ERROR: {e}")
    import traceback
    traceback.print_exc()
