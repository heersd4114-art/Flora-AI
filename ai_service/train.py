import tensorflow as tf
from tensorflow.keras.preprocessing.image import ImageDataGenerator
from tensorflow.keras.applications import MobileNetV2
from tensorflow.keras.layers import Dense, GlobalAveragePooling2D, Dropout
from tensorflow.keras.models import Model
from tensorflow.keras.optimizers import Adam
import os

# --- AUTHENTIC CUSTOM TRAINING SCRIPT ---
# To use this:
# 1. Create a folder 'dataset' inside 'ai_service'
# 2. Inside 'dataset', create subfolders for each class:
#    - dataset/Healthy
#    - dataset/Powdery_Mildew
#    - dataset/Leaf_Spot
# 3. Add images to the folders.
# 4. Run: python train.py

# Found this path after unzipping
DATASET_DIR = "C:/xampp/htdocs/plant_app/plant_app_data/dataset_raw/New Plant Diseases Dataset(Augmented)/New Plant Diseases Dataset(Augmented)/train"
IMG_SIZE = (224, 224)
BATCH_SIZE = 32
EPOCHS = 5  # Increased to 5 for better accuracy. 1 epoch is not enough!

def train_custom_model():
    if not os.path.exists(DATASET_DIR):
        print(f"ERROR: Dataset directory '{DATASET_DIR}' not found.")
        print("Please ensure the dataset is unzipped correctly.")
        return

    print(f"Found dataset at {DATASET_DIR}. Preparing data augmentation...")
    
    train_datagen = ImageDataGenerator(
        rescale=1./255,
        rotation_range=20,
        horizontal_flip=True,
        validation_split=0.2
    )

    train_generator = train_datagen.flow_from_directory(
        DATASET_DIR,
        target_size=IMG_SIZE,
        batch_size=BATCH_SIZE,
        class_mode='categorical',
        subset='training'
    )

    validation_generator = train_datagen.flow_from_directory(
        DATASET_DIR,
        target_size=IMG_SIZE,
        batch_size=BATCH_SIZE,
        class_mode='categorical',
        subset='validation'
    )

    num_classes = train_generator.num_classes
    class_indices = train_generator.class_indices
    print(f"Detected {num_classes} classes: {list(class_indices.keys())}")
    
    # Save class indices
    import json
    with open("class_indices.json", "w") as f:
        json.dump(class_indices, f)
    print("Class indices saved to 'class_indices.json'.")

    # Load Base Model (Transfer Learning)
    base_model = MobileNetV2(weights='imagenet', include_top=False, input_shape=IMG_SIZE+(3,))
    base_model.trainable = False # Freeze base layers

    # Add Custom Head
    x = base_model.output
    x = GlobalAveragePooling2D()(x)
    x = Dropout(0.2)(x)
    predictions = Dense(num_classes, activation='softmax')(x)

    model = Model(inputs=base_model.input, outputs=predictions)

    model.compile(optimizer=Adam(learning_rate=0.001),
                  loss='categorical_crossentropy',
                  metrics=['accuracy'])

    print("Starting training...")
    history = model.fit(
        train_generator,
        epochs=EPOCHS,
        validation_data=validation_generator
    )

    print("Training complete. Saving model...")
    model.save("custom_model.keras")
    print("Model saved as 'custom_model.keras'. The app.py service will now use this model automatically.")

if __name__ == "__main__":
    train_custom_model()
