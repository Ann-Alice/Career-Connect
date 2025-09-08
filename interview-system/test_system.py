import cv2
import mediapipe as mp
import numpy as np
import speech_recognition as sr
from transformers import pipeline
import time

def test_camera():
    print("\nTesting Camera...")
    cap = cv2.VideoCapture(0)
    if not cap.isOpened():
        print("Error: Could not open camera")
        return False
    
    ret, frame = cap.read()
    if not ret:
        print("Error: Could not read frame")
        return False
    
    print("Camera test successful!")
    cap.release()
    return True

def test_facial_analysis():
    print("\nTesting Facial Analysis...")
    mp_face_mesh = mp.solutions.face_mesh
    face_mesh = mp_face_mesh.FaceMesh(
        max_num_faces=1,
        refine_landmarks=True,
        min_detection_confidence=0.5,
        min_tracking_confidence=0.5
    )
    
    cap = cv2.VideoCapture(0)
    if not cap.isOpened():
        print("Error: Could not open camera")
        return False
    
    # Capture for 3 seconds
    start_time = time.time()
    while time.time() - start_time < 3:
        ret, frame = cap.read()
        if not ret:
            continue
            
        # Convert to RGB
        rgb_frame = cv2.cvtColor(frame, cv2.COLOR_BGR2RGB)
        results = face_mesh.process(rgb_frame)
        
        if results.multi_face_landmarks:
            print("Face detected successfully!")
            cap.release()
            return True
    
    print("No face detected during test")
    cap.release()
    return False

def test_speech_recognition():
    print("\nTesting Speech Recognition...")
    recognizer = sr.Recognizer()
    
    print("Please speak something for 5 seconds...")
    with sr.Microphone() as source:
        print("Adjusting for ambient noise...")
        recognizer.adjust_for_ambient_noise(source, duration=2)
        print("Listening...")
        try:
            audio = recognizer.listen(source, timeout=5)
            print("Processing speech...")
            text = recognizer.recognize_google(audio)
            print(f"Recognized text: {text}")
            return True
        except sr.WaitTimeoutError:
            print("No speech detected")
            return False
        except sr.UnknownValueError:
            print("Could not understand audio")
            return False
        except Exception as e:
            print(f"Error: {str(e)}")
            return False

def test_movement_detection():
    print("\nTesting Movement Detection...")
    mp_pose = mp.solutions.pose
    pose = mp_pose.Pose(
        min_detection_confidence=0.5,
        min_tracking_confidence=0.5
    )
    
    cap = cv2.VideoCapture(0)
    if not cap.isOpened():
        print("Error: Could not open camera")
        return False
    
    # Capture for 3 seconds
    start_time = time.time()
    while time.time() - start_time < 3:
        ret, frame = cap.read()
        if not ret:
            continue
            
        # Convert to RGB
        rgb_frame = cv2.cvtColor(frame, cv2.COLOR_BGR2RGB)
        results = pose.process(rgb_frame)
        
        if results.pose_landmarks:
            print("Body pose detected successfully!")
            cap.release()
            return True
    
    print("No body pose detected during test")
    cap.release()
    return False

def main():
    print("Starting system tests...")
    
    # Test camera
    if not test_camera():
        print("Camera test failed. Please check your camera connection.")
        return
    
    # Test facial analysis
    if not test_facial_analysis():
        print("Facial analysis test failed. Please ensure good lighting and camera positioning.")
        return
    
    # Test speech recognition
    # if not test_speech_recognition():
    #     print("Speech recognition test failed. Please check your microphone.")
    #     return
    
    # Test movement detection
    if not test_movement_detection():
        print("Movement detection test failed. Please ensure you're visible to the camera.")
        return
    
    print("\nAll tests completed successfully!")
    print("The system is ready to use.")

if __name__ == "__main__":
    main() 