import requests
import json

def get_ai_response(prompt):
    url = "http://localhost:11434/api/generate"
    data = {
        "model": "llama3",
        "prompt": f"شما یک دستیار هوش مصنوعی هستید که به زبان فارسی و به صورت محاوره‌ای و خودمونی صحبت می‌کنید. به سوال زیر پاسخ دهید: {prompt}",
        "stream": False
    }
    response = requests.post(url, json=data)
    response_data = response.json()
    return response_data["response"]
