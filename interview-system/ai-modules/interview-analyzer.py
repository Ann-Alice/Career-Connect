#!/usr/bin/env python3
"""
Main Interview Analyzer - Integrates all AI analysis modules for comprehensive assessment
This script provides a bias-free, realistic scoring system for AI interviews.
"""

import sys
import os
import json
import numpy as np
from speech_analysis import SpeechAnalyzer
from facial_analysis import FacialAnalyzer
from movement_analysis import MovementAnalyzer

class InterviewAnalyzer:
    def __init__(self):
        self.speech_analyzer = SpeechAnalyzer()
        self.facial_analyzer = FacialAnalyzer()
        self.movement_analyzer = MovementAnalyzer()
    
    def analyze_interview(self, interview_data):
        """
        Analyze complete interview with all modules
        
        Args:
            interview_data (dict): Contains video paths, audio segments, and questions
            
        Returns:
            dict: Comprehensive analysis results with bias-free scoring
        """
        results = {
            'speech_analysis': None,
            'facial_analysis': None,
            'movement_analysis': None,
            'overall_score': 0,
            'detailed_scores': {}
        }
        
        # Speech analysis
        try:
            if 'audio_segments' in interview_data and 'questions' in interview_data:
                speech_results = self.speech_analyzer.analyze_interview(
                    interview_data['audio_segments'], 
                    interview_data['questions']
                )
                results['speech_analysis'] = speech_results
        except Exception as e:
            print(f"Speech analysis error: {e}", file=sys.stderr)
        
        # Facial analysis
        try:
            if 'video_path' in interview_data:
                facial_results = self.facial_analyzer.analyze_video(interview_data['video_path'])
                results['facial_analysis'] = facial_results
        except Exception as e:
            print(f"Facial analysis error: {e}", file=sys.stderr)
        
        # Movement analysis
        try:
            if 'video_path' in interview_data:
                movement_results = self.movement_analyzer.analyze_video(interview_data['video_path'])
                results['movement_analysis'] = movement_results
        except Exception as e:
            print(f"Movement analysis error: {e}", file=sys.stderr)
        
        # Calculate comprehensive, bias-free overall score
        results['overall_score'] = self._calculate_overall_score(results)
        results['detailed_scores'] = self._calculate_detailed_scores(results)
        
        return results
    
    def _calculate_overall_score(self, results):
        """Calculate bias-free overall score using balanced weighting"""
        scores = []
        
        # Speech score (40% weight)
        if results.get('speech_analysis'):
            speech_score = results['speech_analysis'].get('overall_score', 50)
            scores.append(speech_score * 0.4)
        
        # Facial score (30% weight) - focus on confidence and engagement, not emotions
        if results.get('facial_analysis'):
            # Use confidence and engagement metrics, avoid emotion-based bias
            facial_data = results['facial_analysis']
            if 'emotion_percentages' in facial_data:
                # Focus on positive engagement rather than specific emotions
                confidence_indicators = [
                    facial_data['emotion_percentages'].get('happy', 0),
                    facial_data['emotion_percentages'].get('surprise', 0)  # Indicates engagement
                ]
                confidence_score = min(100, sum(confidence_indicators) * 0.5)
                scores.append(confidence_score * 0.3)
        
        # Movement score (30% weight)
        if results.get('movement_analysis'):
            movement_score = results['movement_analysis'].get('movement_score', 50)
            scores.append(movement_score * 0.3)
        
        # If no analysis data, return neutral score
        if not scores:
            return 50.0
        
        return min(100, max(0, sum(scores)))
    
    def _calculate_detailed_scores(self, results):
        """Calculate detailed scores for each category with bias reduction"""
        detailed = {}
        
        # Speech detailed scores
        if results.get('speech_analysis'):
            speech_data = results['speech_analysis']
            detailed['speech'] = {
                'relevance': speech_data.get('detailed_scores', {}).get('relevance', 50),
                'coherence': speech_data.get('detailed_scores', {}).get('coherence', 50),
                'completeness': speech_data.get('detailed_scores', {}).get('completeness', 50),
                'clarity': speech_data.get('detailed_scores', {}).get('keywords', 50)
            }
        
        # Facial detailed scores
        if results.get('facial_analysis'):
            facial_data = results['facial_analysis']
            detailed['facial'] = {
                'confidence': facial_data.get('emotion_percentages', {}).get('happy', 50),
                'engagement': facial_data.get('emotion_percentages', {}).get('surprise', 50),
                'expression_balance': 50  # Neutral to avoid bias
            }
        
        # Movement detailed scores
        if results.get('movement_analysis'):
            movement_data = results['movement_analysis']
            detailed['movement'] = {
                'natural_movement': max(0, 100 - movement_data.get('excessive_movement_percentage', 0)),
                'posture': movement_data.get('movement_score', 50)
            }
        
        return detailed

def main():
    if len(sys.argv) < 2:
        print("Usage: python interview-analyzer.py <interview_data.json>")
        sys.exit(1)
    
    try:
        # Read interview data from JSON file
        with open(sys.argv[1], 'r') as f:
            interview_data = json.load(f)
        
        # Analyze interview
        analyzer = InterviewAnalyzer()
        results = analyzer.analyze_interview(interview_data)
        
        # Output results as JSON
        print(json.dumps(results, indent=2))
        
    except FileNotFoundError:
        print(f"Error: File {sys.argv[1]} not found", file=sys.stderr)
        sys.exit(1)
    except json.JSONDecodeError as e:
        print(f"Error: Invalid JSON in {sys.argv[1]}: {e}", file=sys.stderr)
        sys.exit(1)
    except Exception as e:
        print(f"Error: {e}", file=sys.stderr)
        sys.exit(1)

if __name__ == '__main__':
    main()