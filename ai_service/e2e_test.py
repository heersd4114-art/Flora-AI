"""Full end-to-end test: simulates exactly what PHP does when a user uploads a plant photo."""
import requests, sys
from PIL import Image, ImageDraw
import json

# Create a real-looking leaf image
img = Image.new('RGB', (300, 300), color=(30, 100, 30))
draw = ImageDraw.Draw(img)
# Add brown spots like a diseased leaf
draw.ellipse([80, 60, 150, 130], fill=(120, 70, 30))
draw.ellipse([160, 140, 220, 200], fill=(100, 60, 20))
draw.ellipse([50, 170, 110, 230], fill=(130, 80, 35))
img.save('test_leaf.jpg')

print("Sending image to Flora AI service...")
print("(This may take 30-60 seconds - Phi-3-mini is thinking...)")
print()

try:
    with open('test_leaf.jpg', 'rb') as f:
        resp = requests.post(
            'http://127.0.0.1:5001/predict',
            files={'image': ('test_leaf.jpg', f, 'image/jpeg')},
            timeout=120
        )
    
    print(f"HTTP Status: {resp.status_code}")
    
    if resp.status_code == 200:
        data = resp.json()
        print()
        print("=" * 55)
        print("  FLORA AI RESULT")
        print("=" * 55)
        print(f"  Plant:       {data.get('plant_name')}")
        print(f"  Disease:     {data.get('disease')}")
        conf = round(data.get('confidence', 0) * 100, 1)
        print(f"  Confidence:  {conf}% ({data.get('confidence_level')})")
        print(f"  Severity:    {data.get('severity')}")
        print(f"  Healthy:     {data.get('is_healthy')}")
        print(f"  Method:      {data.get('method')}")
        print()
        print("  AI ANALYSIS:")
        print(f"  {data.get('ai_analysis')}")
        print()
        print("  TREATMENT STEPS:")
        for i, step in enumerate(data.get('treatment_steps', []), 1):
            print(f"  {i}. {step}")
        print("=" * 55)
        print()
        print("SUCCESS - Flora AI is working correctly!")
    else:
        print(f"ERROR: HTTP {resp.status_code}")
        print(resp.text[:500])
        sys.exit(1)

except requests.exceptions.ConnectionError:
    print("FAIL: Cannot connect to port 5001. The AI service is NOT running.")
    print("Run: python app.py")
    sys.exit(1)
except requests.exceptions.Timeout:
    print("FAIL: Request timed out after 120 seconds.")
    sys.exit(1)
except Exception as e:
    print(f"FAIL: {e}")
    sys.exit(1)
