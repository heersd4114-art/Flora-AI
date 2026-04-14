
import os
import zipfile
import sys
import shutil

# CONFIG
ZIP_FILE_NAME = "dataset.zip" # Expected filename
EXTRACT_PATH = "dataset_raw"
PROCESSED_PATH = "dataset_train"

def unzip_dataset():
    if not os.path.exists(ZIP_FILE_NAME):
        print(f"ERROR: Could not find {ZIP_FILE_NAME}. Please upload it to this folder.")
        return False
        
    print(f"Found {ZIP_FILE_NAME}. Unzipping...")
    try:
        with zipfile.ZipFile(ZIP_FILE_NAME, 'r') as zip_ref:
            zip_ref.extractall(EXTRACT_PATH)
        print("Unzip successful.")
        return True
    except zipfile.BadZipFile:
        print("ERROR: The zip file is corrupted.")
        return False
    except Exception as e:
        print(f"ERROR: {e}")
        return False

if __name__ == "__main__":
    if unzip_dataset():
        print("Dataset ready for training!")
        # Trigger training here later
