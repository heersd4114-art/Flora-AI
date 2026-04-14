"""Debug script to check raw server response."""
import requests
from PIL import Image, ImageDraw
import sys

# Create simple test image
img = Image.new('RGB', (224, 224), color=(34, 139, 34))
draw = ImageDraw.Draw(img)
draw.ellipse([80, 60, 130, 110], fill=(139, 90, 43))
img.save('test_plant.jpg')

print("Sending request...")
try:
    with open('test_plant.jpg', 'rb') as f:
        resp = requests.post(
            'http://127.0.0.1:5001/predict',
            files={'image': ('test_plant.jpg', f, 'image/jpeg')},
            timeout=180
        )
    print("Status code:", resp.status_code)
    print("Response text:", repr(resp.text[:2000]))
except Exception as ex:
    print("Error:", ex)
    sys.exit(1)
