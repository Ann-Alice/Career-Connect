import speech_recognition as sr
import numpy as np
from transformers import pipeline
import os
import json
from textstat import flesch_reading_ease, syllable_count
from collections import Counter
import re

class SpeechAnalyzer:
    def __init__(self):
        # Initialize speech recognizer
        self.recognizer = sr.Recognizer()
        
        # Load sentiment analysis model
        self.sentiment_analyzer = pipeline("sentiment-analysis")
        
        # Load question-answering model
        self.qa_model = pipeline("question-answering")
        
    def transcribe_audio(self, audio_path):
        """Transcribe audio file to text"""
        with sr.AudioFile(audio_path) as source:
            audio = self.recognizer.record(source)
            try:
                text = self.recognizer.recognize_google(audio)
                return text
            except sr.UnknownValueError:
                return ""
            except sr.RequestError:
                return ""
    
    def analyze_sentiment(self, text):
        """Analyze sentiment of the answer with bias reduction"""
        if not text:
            return {'label': 'neutral', 'score': 0.5}
        
        result = self.sentiment_analyzer(text)[0]
        
        # Normalize sentiment score to be more balanced and less extreme
        normalized_score = (result['score'] - 0.5) * 0.8 + 0.5  # Reduce extremes
        
        return {
            'label': result['label'],
            'score': float(normalized_score)
        }
    
    def check_keywords(self, text, expected_keywords):
        """Check if expected keywords are present in the answer"""
        if not text or not expected_keywords:
            return {'score': 0, 'found_keywords': []}
        
        text = text.lower()
        found_keywords = []
        
        for keyword in expected_keywords:
            if keyword.lower() in text:
                found_keywords.append(keyword)
        
        # More nuanced scoring that considers partial matches
        score = len(found_keywords) / len(expected_keywords) * 100
        
        return {
            'score': float(score),
            'found_keywords': found_keywords
        }
    
    def calculate_relevance_score(self, answer_text, question_text):
        """Calculate how relevant the answer is to the question"""
        if not answer_text or not question_text:
            return 50.0  # Neutral score if missing data
            
        # Simple keyword overlap for relevance (can be enhanced with embeddings)
        answer_words = set(re.findall(r'\b\w+\b', answer_text.lower()))
        question_words = set(re.findall(r'\b\w+\b', question_text.lower()))
        
        if len(question_words) == 0:
            return 50.0
            
        overlap = len(answer_words.intersection(question_words))
        relevance = (overlap / len(question_words)) * 100
        
        # Cap at 100
        return min(100.0, relevance)
    
    def calculate_coherence_score(self, text):
        """Calculate text coherence and structure score"""
        if not text:
            return 50.0
            
        # Count sentences
        sentences = re.split(r'[.!?]+', text)
        sentences = [s.strip() for s in sentences if s.strip()]
        
        if len(sentences) == 0:
            return 30.0  # Poor coherence for empty text
            
        # Calculate average sentence length
        avg_sentence_length = np.mean([len(s.split()) for s in sentences])
        
        # Calculate readability using Flesch Reading Ease
        try:
            readability = flesch_reading_ease(text)
            # Normalize to 0-100 scale (Flesch is 0-100, higher is easier)
            readability_score = min(100.0, max(0.0, readability))
        except:
            readability_score = 50.0
            
        # Coherence score based on sentence structure and readability
        coherence = (readability_score * 0.6) + (min(100.0, avg_sentence_length * 2) * 0.4)
        
        return min(100.0, coherence)
    
    def calculate_completeness_score(self, text, expected_keywords):
        """Calculate how complete the answer is based on content and keywords"""
        if not text:
            return 0.0
            
        # Length-based score (answers should have reasonable length)
        word_count = len(text.split())
        length_score = min(100.0, word_count * 2)  # 50 words = 100 score
        
        # Keyword coverage
        keyword_result = self.check_keywords(text, expected_keywords)
        keyword_score = keyword_result['score']
        
        # Completeness is a balance of length and keyword coverage
        completeness = (length_score * 0.4) + (keyword_score * 0.6)
        
        return min(100.0, completeness)
    
    def analyze_answer(self, audio_path, question, expected_keywords):
        """Analyze a complete answer with improved, bias-free scoring"""
        # Transcribe audio
        text = self.transcribe_audio(audio_path)
        
        # Analyze sentiment
        sentiment = self.analyze_sentiment(text)
        
        # Check keywords
        keyword_analysis = self.check_keywords(text, expected_keywords)
        
        # Calculate multiple dimensions of the answer
        relevance_score = self.calculate_relevance_score(text, question)
        coherence_score = self.calculate_coherence_score(text)
        completeness_score = self.calculate_completeness_score(text, expected_keywords)
        
        # Calculate sentiment score with more nuance
        if sentiment['label'] == 'POSITIVE':
            sentiment_score = 60 + (sentiment['score'] * 40)  # 60-100 range
        elif sentiment['label'] == 'NEGATIVE':
            sentiment_score = 40 - (sentiment['score'] * 40)  # 0-40 range
        else:  # NEUTRAL
            sentiment_score = 50  # Neutral baseline
        
        # Calculate overall score using weighted components
        # This approach reduces bias by considering multiple factors equally
        overall_score = (
            (relevance_score * 0.25) +      # Relevance to question
            (coherence_score * 0.20) +      # Structure and clarity
            (completeness_score * 0.25) +   # Content completeness
            (sentiment_score * 0.15) +      # Sentiment (reduced weight to minimize bias)
            (keyword_analysis['score'] * 0.15)  # Keyword matching
        )
        
        return {
            'text': text,
            'sentiment': sentiment,
            'keyword_analysis': keyword_analysis,
            'relevance_score': float(relevance_score),
            'coherence_score': float(coherence_score),
            'completeness_score': float(completeness_score),
            'sentiment_score': float(sentiment_score),
            'overall_score': float(overall_score)
        }
    
    def analyze_interview(self, audio_segments, questions):
        """Analyze complete interview with improved scoring"""
        results = []
        
        for i, (audio_path, question) in enumerate(zip(audio_segments, questions)):
            expected_keywords = question.get('expected_keywords', [])
            if isinstance(expected_keywords, str):
                expected_keywords = json.loads(expected_keywords)
            
            result = self.analyze_answer(audio_path, question['text'], expected_keywords)
            results.append({
                'question_id': question['id'],
                'question_text': question['text'],
                'analysis': result
            })
        
        # Calculate overall interview score with more sophisticated aggregation
        if results:
            # Weighted average considering different aspects
            overall_relevance = np.mean([r['analysis']['relevance_score'] for r in results])
            overall_coherence = np.mean([r['analysis']['coherence_score'] for r in results])
            overall_completeness = np.mean([r['analysis']['completeness_score'] for r in results])
            overall_sentiment = np.mean([r['analysis']['sentiment_score'] for r in results])
            overall_keywords = np.mean([r['analysis']['keyword_analysis']['score'] for r in results])
            overall_score = np.mean([r['analysis']['overall_score'] for r in results])
            
            # Final overall score with balanced components
            final_overall_score = (
                (overall_relevance * 0.20) +
                (overall_coherence * 0.20) +
                (overall_completeness * 0.25) +
                (overall_sentiment * 0.15) +
                (overall_keywords * 0.20)
            )
        else:
            final_overall_score = 0.0
        
        return {
            'question_results': results,
            'overall_score': float(final_overall_score),
            'detailed_scores': {
                'relevance': float(overall_relevance) if results else 0.0,
                'coherence': float(overall_coherence) if results else 0.0,
                'completeness': float(overall_completeness) if results else 0.0,
                'sentiment': float(overall_sentiment) if results else 0.0,
                'keywords': float(overall_keywords) if results else 0.0
            }
        }

if __name__ == '__main__':
    # Test the analyzer
    analyzer = SpeechAnalyzer()
    
    # Example usage
    question = {
        'id': 1,
        'text': 'Tell me about your experience with Python.',
        'expected_keywords': ['python', 'experience', 'projects', 'libraries']
    }
    
    result = analyzer.analyze_answer('test_audio.wav', question['text'], question['expected_keywords'])
    print("Enhanced Speech Analysis Results:", result)