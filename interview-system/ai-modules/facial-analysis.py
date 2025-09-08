import cv2
import dlib
import numpy as np
from tensorflow.keras.models import load_model
import os

class FacialAnalyzer:
    def __init__(self):
        # Initialize face detector
        self.detector = dlib.get_frontal_face_detector()
        
        # Load facial landmark predictor
        predictor_path = os.path.join(os.path.dirname(__file__), '../models/shape_predictor_68_face_landmarks.dat')
        self.predictor = dlib.shape_predictor(predictor_path)
        
        # Load emotion detection model
        model_path = os.path.join(os.path.dirname(__file__), '../models/emotion_model.h5')
        self.emotion_model = load_model(model_path)
        
        # Define emotions
        self.emotions = ['angry', 'disgust', 'fear', 'happy', 'sad', 'surprise', 'neutral']
        
    def analyze_frame(self, frame):
        # Convert to grayscale
        gray = cv2.cvtColor(frame, cv2.COLOR_BGR2GRAY)
        
        # Detect faces
        faces = self.detector(gray)
        
        results = []
        for face in faces:
            # Get facial landmarks
            landmarks = self.predictor(gray, face)
            
            # Extract face region
            x, y, w, h = face.left(), face.top(), face.width(), face.height()
            face_roi = gray[y:y+h, x:x+w]
            
            # Resize for emotion detection
            face_roi = cv2.resize(face_roi, (48, 48))
            face_roi = np.expand_dims(face_roi, axis=[0, -1])
            
            # Predict emotion
            emotion_pred = self.emotion_model.predict(face_roi)[0]
            emotion_idx = np.argmax(emotion_pred)
            emotion = self.emotions[emotion_idx]
            confidence = float(emotion_pred[emotion_idx])
            
            results.append({
                'emotion': emotion,
                'confidence': confidence,
                'landmarks': [(p.x, p.y) for p in landmarks.parts()]
            })
        
        return results
    
    def analyze_video(self, video_path):
        cap = cv2.VideoCapture(video_path)
        frame_results = []
        
        while cap.isOpened():
            ret, frame = cap.read()
            if not ret:
                break
                
            results = self.analyze_frame(frame)
            frame_results.append(results)
            
        cap.release()
        
        # Aggregate results
        emotion_counts = {emotion: 0 for emotion in self.emotions}
        total_frames = len(frame_results)
        
        for frame_result in frame_results:
            if frame_result:  # If face detected
                emotion = frame_result[0]['emotion']
                emotion_counts[emotion] += 1
        
        # Calculate percentages
        emotion_percentages = {
            emotion: (count / total_frames) * 100 
            for emotion, count in emotion_counts.items()
        }
        
        return {
            'emotion_percentages': emotion_percentages,
            'frame_results': frame_results
        }

if __name__ == '__main__':
    # Test the analyzer
    analyzer = FacialAnalyzer()
    results = analyzer.analyze_video('test_video.mp4')
    print("Emotion Analysis Results:", results['emotion_percentages']) 