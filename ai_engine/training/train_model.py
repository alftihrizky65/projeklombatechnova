import os
import pandas as pd
from sklearn.model_selection import train_test_split
from sklearn.ensemble import RandomForestClassifier
from sklearn.metrics import accuracy_score
import pickle

DATA_DIR = os.path.join(os.path.dirname(__file__), '..', 'dataset', 'bisindo')
MODEL_DIR = os.path.join(os.path.dirname(__file__), '..', 'models')
MODEL_PATH = os.path.join(MODEL_DIR, 'bisindo_model.pkl')

os.makedirs(MODEL_DIR, exist_ok=True)

print("=== BISINDO Model Trainer ===")
if not os.path.exists(DATA_DIR) or not os.listdir(DATA_DIR):
    print(f"Error: No data found in {DATA_DIR}. Please run data_collector.py first.")
    exit()

dataframes = []
for file in os.listdir(DATA_DIR):
    if file.endswith('.csv'):
        df = pd.read_csv(os.path.join(DATA_DIR, file))
        dataframes.append(df)

if not dataframes:
    print("No CSV files found.")
    exit()

full_df = pd.concat(dataframes, ignore_index=True)
print(f"Loaded {len(full_df)} total records from dataset.")

X = full_df.drop('label', axis=1)
y = full_df['label']

X_train, X_test, y_train, y_test = train_test_split(X, y, test_size=0.2, random_state=42)

print("Training Random Forest Classifier...")
clf = RandomForestClassifier(n_estimators=100, random_state=42)
clf.fit(X_train, y_train)

y_pred = clf.predict(X_test)
accuracy = accuracy_score(y_test, y_pred)
print(f"Model Accuracy on Test Set: {accuracy * 100:.2f}%")

with open(MODEL_PATH, 'wb') as f:
    pickle.dump(clf, f)

print(f"Model successfully saved to {MODEL_PATH}")
