# AI-Powered Interview Assessment System

This system conducts automated interviews and assesses candidates based on facial expressions, movements, and answer accuracy.

## Prerequisites

- PHP 7.4 or higher
- Python 3.7 or higher
- MySQL/MariaDB
- Web server (Apache/Nginx)
- Webcam and microphone

## Enhanced Features

- **Realistic Scoring**: Multi-dimensional assessment with bias reduction
- **Comprehensive Analysis**: Speech, facial, and movement evaluation
- **Fair Assessment**: Reduced emotional and cultural bias
- **Detailed Feedback**: Actionable insights for candidates

## Installation

1. Clone the repository to your web server directory:
```bash
git clone <repository-url> interview-system
cd interview-system
```

2. Install PHP dependencies:
```bash
composer install
```

3. Install Python dependencies:
```bash
pip install -r requirements.txt
```

4. Install additional Python package for enhanced text analysis:
```bash
pip install textstat
```

5. Create the database:
```sql
CREATE DATABASE interview_system;
```

6. Configure the system:
- Copy `config.php.example` to `config.php`
- Update database credentials in `config.php`
- Update API keys in `config.php`
- Set up email configuration in `report-generator.php`

7. Download required AI models:
- Download facial landmark predictor model from dlib
- Download emotion detection model
- Download pose estimation model
- Place all models in the `models` directory

8. Set proper permissions:
```bash
chmod 755 -R .
chmod 777 -R uploads/ reports/
```

## Enhanced AI Assessment Features

### Speech Analysis Improvements
- Multi-dimensional scoring (relevance, coherence, completeness, sentiment, keywords)
- Bias reduction through balanced weighting
- Advanced NLP with readability metrics
- Realistic score distribution

### Facial Analysis Improvements
- Confidence and engagement metrics
- Professional presentation focus
- Expression balance to avoid emotional bias

### Movement Analysis Improvements
- Natural movement assessment
- Professional posture evaluation
- Balanced gesture analysis

## Testing

Run the test script to verify the enhanced analysis system:
```
http://localhost/eris/interview-system/test-enhanced-analysis.php
```

## Documentation

See `AI_ASSESSMENT_IMPROVEMENTS.md` for detailed information about the enhancements.