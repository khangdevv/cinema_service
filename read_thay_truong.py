"""
Script to read and extract quiz questions from Đề-Ôn-Thầy-Trường-Ôn-CK.docx
"""
from docx import Document
import json
import re

def extract_questions(docx_path):
    doc = Document(docx_path)
    
    # Get all paragraphs text
    all_text = []
    for para in doc.paragraphs:
        text = para.text.strip()
        if text:
            all_text.append(text)
    
    # Print all content
    print(f"\n{'='*60}")
    print(f"Content from: {docx_path}")
    print(f"Total paragraphs: {len(all_text)}")
    print(f"{'='*60}\n")
    
    for i, text in enumerate(all_text):
        print(f"{i+1}: {text}")
    
    return all_text

if __name__ == '__main__':
    path = 'document/Đề-Ôn-Thầy-Trường-Ôn-CK.docx'
    extract_questions(path)
