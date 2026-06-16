#!/usr/bin/env python3

import requests
import sys
import json

API_KEY = "K89821879188957"  # get free key at https://ocr.space/ocrapi

def extract_text(image_path):
    with open(image_path, 'rb') as f:
        response = requests.post(
            "https://api.ocr.space/parse/image",
            files={"file": f},
            data={"apikey": API_KEY, "language": "eng"}
        )
    result = response.json()
    if result.get("ParsedResults"):
        return {"text": result["ParsedResults"][0]["ParsedText"]}
    else:
        return {"error": result.get("ErrorMessage", "No text detected")}

if __name__ == "__main__":
    if len(sys.argv) < 2:
        print(json.dumps({"error": "Please provide an image path"}))
        sys.exit(1)

    image_path = sys.argv[1]
    try:
        result = extract_text(image_path)
        print(json.dumps(result))
    except Exception as e:
        print(json.dumps({"error": str(e)}))
