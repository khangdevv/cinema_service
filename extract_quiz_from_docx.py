"""
Script to extract quiz questions from .docx files and generate Laravel seeder
Usage: python extract_quiz_from_docx.py
"""

import sys
import json
import re
from pathlib import Path

try:
    from docx import Document
except ImportError:
    print("ERROR: python-docx not installed!")
    print("Run: pip install python-docx")
    sys.exit(1)

def extract_questions_from_docx(docx_path, exam_id, exam_name):
    """Extract questions from a .docx file"""
    doc = Document(docx_path)
    questions = []
    current_question = None
    
    for para in doc.paragraphs:
        text = para.text.strip()
        if not text:
            continue
            
        # Match question pattern: "Câu 1:", "Question 1:", etc
        question_match = re.match(r'^(?:Câu|Question)\s*(\d+)[:\.]?\s*(.+)', text, re.IGNORECASE)
        if question_match:
            # Save previous question if exists
            if current_question and current_question.get('question'):
                questions.append(current_question)
            
            # Start new question
            current_question = {
                'exam_id': exam_id,
                'question': question_match.group(2).strip(),
                'option_a': '',
                'option_b': '',
                'option_c': '',
                'option_d': '',
                'correct_answer': '',
                'explanation': ''
            }
            continue
        
        if not current_question:
            continue
            
        # Match options: A., B., C., D. or A) B) C) D)
        option_match = re.match(r'^([A-Da-d])[\.\)]\s*(.+)', text)
        if option_match:
            option_letter = option_match.group(1).upper()
            option_text = option_match.group(2).strip()
            current_question[f'option_{option_letter.lower()}'] = option_text
            continue
        
        # Match correct answer: "Đáp án: A", "Answer: A", "Correct: A"
        answer_match = re.search(r'(?:Đáp án|Answer|Correct)[:\s]+([A-Da-d])', text, re.IGNORECASE)
        if answer_match:
            current_question['correct_answer'] = answer_match.group(1).upper()
            continue
        
        # Match explanation: "Giải thích:", "Explanation:"
        if re.match(r'^(?:Giải thích|Explanation)[:\.]', text, re.IGNORECASE):
            explanation_text = re.sub(r'^(?:Giải thích|Explanation)[:\.]?\s*', '', text, flags=re.IGNORECASE)
            current_question['explanation'] = explanation_text
            continue
        
        # Append to explanation if it's continuation
        if current_question.get('explanation'):
            current_question['explanation'] += ' ' + text
    
    # Don't forget last question
    if current_question and current_question.get('question'):
        questions.append(current_question)
    
    print(f"\n{'='*60}")
    print(f"Extracted from: {docx_path.name}")
    print(f"Exam: {exam_name} (ID: {exam_id})")
    print(f"Total questions: {len(questions)}")
    print(f"{'='*60}\n")
    
    return questions

def generate_seeder_code(all_questions):
    """Generate Laravel seeder PHP code"""
    php_code = """<?php

namespace Database\\Seeders;

use Illuminate\\Database\\Seeder;
use Illuminate\\Support\\Facades\\DB;

class AdditionalQuizSeeder extends Seeder
{
    public function run()
    {
        $questions = [
"""
    
    for q in all_questions:
        # Escape single quotes for PHP
        question_text = q['question'].replace("'", "\\'")
        option_a = q['option_a'].replace("'", "\\'")
        option_b = q['option_b'].replace("'", "\\'")
        option_c = q['option_c'].replace("'", "\\'")
        option_d = q['option_d'].replace("'", "\\'")
        explanation = q['explanation'].replace("'", "\\'")
        
        php_code += f"""            [
                'exam_id' => {q['exam_id']},
                'question' => '{question_text}',
                'option_a' => '{option_a}',
                'option_b' => '{option_b}',
                'option_c' => '{option_c}',
                'option_d' => '{option_d}',
                'correct_answer' => '{q['correct_answer']}',
                'explanation' => '{explanation}',
                'created_at' => now(),
                'updated_at' => now(),
            ],
"""
    
    php_code += """        ];

        DB::table('quiz_questions')->insert($questions);
        
        $this->command->info('Successfully seeded additional quiz questions!');
    }
}
"""
    
    return php_code

def main():
    base_path = Path(__file__).parent / 'document'
    
    files_to_process = [
        {
            'path': base_path / 'trắc nghiệm nguồn mở(bonus).docx',
            'exam_id': 2,
            'exam_name': 'Trắc nghiệm nguồn mở (Bonus)'
        },
        {
            'path': base_path / 'Ôn-tập-phát-triển-phần-mềm-mã-nguồn-mở(Thầy khuê).docx',
            'exam_id': 3,
            'exam_name': 'Ôn tập Thầy Khuê'
        }
    ]
    
    all_questions = []
    
    for file_info in files_to_process:
        if not file_info['path'].exists():
            print(f"WARNING: File not found: {file_info['path']}")
            continue
        
        try:
            questions = extract_questions_from_docx(
                file_info['path'],
                file_info['exam_id'],
                file_info['exam_name']
            )
            all_questions.extend(questions)
            
            # Print sample
            if questions:
                print(f"Sample question from {file_info['exam_name']}:")
                sample = questions[0]
                print(f"  Q: {sample['question'][:80]}...")
                print(f"  A: {sample['option_a'][:50]}...")
                print(f"  Correct: {sample['correct_answer']}")
                print()
        
        except Exception as e:
            print(f"ERROR processing {file_info['path'].name}: {e}")
            import traceback
            traceback.print_exc()
    
    if not all_questions:
        print("No questions extracted!")
        return
    
    # Generate seeder
    seeder_code = generate_seeder_code(all_questions)
    output_file = Path(__file__).parent / 'database' / 'seeders' / 'AdditionalQuizSeeder.php'
    output_file.parent.mkdir(parents=True, exist_ok=True)
    output_file.write_text(seeder_code, encoding='utf-8')
    
    print(f"\n{'='*60}")
    print(f"✅ Seeder generated: {output_file}")
    print(f"Total questions: {len(all_questions)}")
    print(f"\nTo seed the database, run:")
    print(f"  php artisan db:seed --class=AdditionalQuizSeeder")
    print(f"{'='*60}\n")

if __name__ == '__main__':
    main()
