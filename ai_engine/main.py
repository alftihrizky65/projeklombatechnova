import cv2
import mediapipe.python.solutions.hands as mp_hands
import mediapipe.python.solutions.drawing_utils as mp_draw
import numpy as np
import base64
import json
import pickle
import os
from fastapi import FastAPI, WebSocket
from fastapi.middleware.cors import CORSMiddleware

app = FastAPI()

app.add_middleware(
    CORSMiddleware,
    allow_origins=["*"],
    allow_credentials=True,
    allow_methods=["*"],
    allow_headers=["*"],
)

hands = mp_hands.Hands(static_image_mode=False, max_num_hands=2, min_detection_confidence=0.7)

MODEL_PATH = os.path.join(os.path.dirname(__file__), 'models', 'bisindo_model.pkl')
model = None

if os.path.exists(MODEL_PATH):
    try:
        with open(MODEL_PATH, 'rb') as f:
            model = pickle.load(f)
        print("Loaded Real BISINDO Scikit-Learn Model.")
    except Exception as e:
        print(f"Error loading model: {e}")
else:
    print("WARNING: bisindo_model.pkl not found! Using fallback mock mode until trained.")

def process_frame(base64_img):
    # Decode base64 image
    img_data = base64.b64decode(base64_img.split(',')[1] if ',' in base64_img else base64_img)
    np_arr = np.frombuffer(img_data, np.uint8)
    image = cv2.imdecode(np_arr, cv2.IMREAD_COLOR)
    
    if image is None:
        return {"landmarks": [], "prediction": None}

    image_rgb = cv2.cvtColor(image, cv2.COLOR_BGR2RGB)
    results = hands.process(image_rgb)

    response = {"landmarks": [], "prediction": None}

    if results.multi_hand_landmarks:
        for hand_landmarks in results.multi_hand_landmarks:
            landmarks_list = []
            flat_landmarks = [] # for ML input
            for lm in hand_landmarks.landmark:
                landmarks_list.append({"x": lm.x, "y": lm.y, "z": lm.z})
                flat_landmarks.extend([lm.x, lm.y, lm.z])
            
            response["landmarks"].append(landmarks_list)
            
            # Predict using Real Model if exists
            if model is not None:
                try:
                    prediction = model.predict([flat_landmarks])[0]
                    response["prediction"] = prediction
                except:
                    pass
            else:
                # Fallback mock for demo safety if model isn't built yet
                # Just mock based on wrist y position randomly
                if flat_landmarks[1] < 0.5:
                    response["prediction"] = "Halo"

    return response

@app.websocket("/ws")
async def websocket_endpoint(websocket: WebSocket):
    await websocket.accept()
    try:
        while True:
            data = await websocket.receive_text()
            # data is the base64 encoded frame
            result = process_frame(data)
            await websocket.send_text(json.dumps(result))
    except Exception as e:
        print(f"WebSocket closed or error: {e}")

if __name__ == "__main__":
    import uvicorn
    uvicorn.run("main:app", host="0.0.0.0", port=8001, reload=True)
