import cv2
import mediapipe.python.solutions.hands as mp_hands
import mediapipe.python.solutions.drawing_utils as mp_draw
import os
import pandas as pd
import time

hands = mp_hands.Hands(static_image_mode=False, max_num_hands=1, min_detection_confidence=0.7)

DATA_DIR = os.path.join(os.path.dirname(__file__), '..', 'dataset', 'bisindo')
os.makedirs(DATA_DIR, exist_ok=True)

print("=== BISINDO Data Collector ===")
label = input("Enter the label for this session (e.g., Halo, Terima_Kasih): ").strip()
file_path = os.path.join(DATA_DIR, f"{label}.csv")

cap = cv2.VideoCapture(0)
data = []

print("Press 's' to save a frame's landmarks.")
print("Press 'q' to quit.")

while True:
    ret, frame = cap.read()
    if not ret: break

    frame = cv2.flip(frame, 1)
    rgb_frame = cv2.cvtColor(frame, cv2.COLOR_BGR2RGB)
    results = hands.process(rgb_frame)

    if results.multi_hand_landmarks:
        for hand_landmarks in results.multi_hand_landmarks:
            mp_draw.draw_landmarks(frame, hand_landmarks, mp_hands.HAND_CONNECTIONS)
            
            # Press S to capture
            if cv2.waitKey(1) & 0xFF == ord('s'):
                row = []
                for lm in hand_landmarks.landmark:
                    row.extend([lm.x, lm.y, lm.z])
                row.append(label)
                data.append(row)
                print(f"Captured! Total collected for '{label}': {len(data)}")
                time.sleep(0.2) # debounce

    cv2.imshow('Data Collector', frame)
    if cv2.waitKey(1) & 0xFF == ord('q'):
        break

cap.release()
cv2.destroyAllWindows()

if data:
    columns = []
    for i in range(21):
        columns.extend([f'x{i}', f'y{i}', f'z{i}'])
    columns.append('label')
    
    df = pd.DataFrame(data, columns=columns)
    if os.path.exists(file_path):
        df.to_csv(file_path, mode='a', header=False, index=False)
    else:
        df.to_csv(file_path, index=False)
    print(f"Saved {len(data)} records to {file_path}")
else:
    print("No data collected.")
