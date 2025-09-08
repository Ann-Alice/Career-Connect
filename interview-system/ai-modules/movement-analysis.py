import cv2
import numpy as np
from tensorflow.keras.models import load_model
import os

class MovementAnalyzer:
    def __init__(self):
        # Load pose estimation model
        model_path = os.path.join(os.path.dirname(__file__), '../models/pose_model.h5')
        self.pose_model = load_model(model_path)
        
        # Define movement thresholds
        self.movement_threshold = 30  # pixels
        self.excessive_movement_threshold = 50  # pixels
        
    def analyze_frame(self, frame, prev_frame=None):
        if prev_frame is None:
            return None
            
        # Convert frames to grayscale
        gray = cv2.cvtColor(frame, cv2.COLOR_BGR2GRAY)
        prev_gray = cv2.cvtColor(prev_frame, cv2.COLOR_BGR2GRAY)
        
        # Calculate optical flow
        flow = cv2.calcOpticalFlowFarneback(
            prev_gray, gray, None, 0.5, 3, 15, 3, 5, 1.2, 0
        )
        
        # Calculate magnitude of movement
        magnitude = np.sqrt(flow[..., 0]**2 + flow[..., 1]**2)
        
        # Calculate average movement
        avg_movement = np.mean(magnitude)
        
        # Detect excessive movement
        excessive_movement = np.mean(magnitude > self.excessive_movement_threshold)
        
        return {
            'avg_movement': float(avg_movement),
            'excessive_movement_percentage': float(excessive_movement * 100)
        }
    
    def analyze_video(self, video_path):
        cap = cv2.VideoCapture(video_path)
        frame_results = []
        prev_frame = None
        
        while cap.isOpened():
            ret, frame = cap.read()
            if not ret:
                break
                
            if prev_frame is not None:
                results = self.analyze_frame(frame, prev_frame)
                if results:
                    frame_results.append(results)
            
            prev_frame = frame.copy()
            
        cap.release()
        
        if not frame_results:
            return {
                'avg_movement': 0,
                'excessive_movement_percentage': 0
            }
        
        # Aggregate results
        avg_movement = np.mean([r['avg_movement'] for r in frame_results])
        excessive_movement = np.mean([r['excessive_movement_percentage'] for r in frame_results])
        
        # Calculate movement score (0-100)
        movement_score = max(0, 100 - (excessive_movement * 2))
        
        return {
            'avg_movement': float(avg_movement),
            'excessive_movement_percentage': float(excessive_movement),
            'movement_score': float(movement_score)
        }

if __name__ == '__main__':
    # Test the analyzer
    analyzer = MovementAnalyzer()
    results = analyzer.analyze_video('test_video.mp4')
    print("Movement Analysis Results:", results) 