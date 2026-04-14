import tensorflow as tf
from tensorflow.keras.models import load_model
from tensorflow.keras.preprocessing.image import img_to_array
from PIL import Image
import numpy as np

model = load_model("c:/xampp/htdocs/plant_app/ai_service/custom_model.keras")

# Create a fake 'floor' image (brownish)
img = Image.new('RGB', (224, 224), color=(150, 100, 50))
x = img_to_array(img)
x = np.expand_dims(x, axis=0) / 255.0

preds = model.predict(x)
print("Floor Confidence:", np.max(preds))

# Fake Aloe Vera image (greenish but not in dataset)
img2 = Image.new('RGB', (224, 224), color=(50, 150, 50))
x2 = img_to_array(img2)
x2 = np.expand_dims(x2, axis=0) / 255.0

preds2 = model.predict(x2)
print("Aloe Vera Confidence:", np.max(preds2))
