
import os
import json
from PIL import Image
from google import genai
from google.genai import types

# Load .env manually
env_path = "../.env"
api_key = None
if os.path.exists(env_path):
    with open(env_path, "r") as f:
        for line in f:
            if line.strip() and not line.startswith("#"):
                key, value = line.strip().split("=", 1)
                if key == "GEMINI_API_KEY":
                    api_key = value
                    break

if not api_key or "YOUR_GEMINI_API_KEY" in api_key:
    print("Error: Invalid or missing GEMINI_API_KEY in .env")
    exit(1)

print(f"Found API Key: {api_key[:5]}...{api_key[-3:]}")

# Test Image (Tomato)
image_path = "../uploads/69815def30e81_tomato.jfif"
if not os.path.exists(image_path):
    print(f"Error: Test image not found at {image_path}")
    exit(1)

try:
    print("Connecting to Gemini...")
    client = genai.Client(api_key=api_key)
    image = Image.open(image_path)
    
    prompt = "Identify this plant and its health status."
    system_prompt = """
    You are Flora, an expert botanist.
    Return strictly VALID JSON:
    {
        "plant_name": "string",
        "disease": "string",
        "care_tips": "string",
        "search_term": "string",
        "confidence": 0.95
    }
    """
    
    print("Sending image for analysis...")
    response = client.models.generate_content(
        model='gemini-2.5-flash',
        contents=[image, prompt],
        config=types.GenerateContentConfig(
            system_instruction=system_prompt,
            temperature=0.2,
            response_mime_type='application/json'
        )
    )
    
    print("\nSuccess! Gemini Response:")
    print(response.text)

except Exception as e:
    print(f"\nVerification Failed: {e}")
