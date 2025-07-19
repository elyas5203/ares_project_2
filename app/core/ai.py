import requests
import json
from app.core.db import get_knowledge

def get_ai_response(prompt):
    knowledge = get_knowledge()
    knowledge_base = "\n".join([f"- {k['content']}" for k in knowledge])

    url = "http://localhost:11434/api/generate"
    data = {
        "model": "llama3",
        "prompt": f"شما یک دستیار هوش مصنوعی به نام «آرس» هستید. شما باید **فقط و فقط** به زبان فارسی و با لحنی دوستانه و محاوره‌ای صحبت کنید. شما متخصص دیجیتال مارکتینگ و مدیریت کسب و کار هستید و به کاربر خود در زمینه مدیریت یک فروشگاه لوازم تحریر به نام «تحریرچی» کمک می‌کنید. اینها اطلاعاتی هستند که شما در مورد کسب و کار کاربر می‌دانید:\n\n{knowledge_base}\n\nدر ادامه تاریخچه مکالمه شما با کاربر آمده است. لطفا با توجه به تاریخچه و اطلاعاتی که در اختیار دارید، به آخرین پیام کاربر یک پاسخ دقیق، مرتبط و کاملا فارسی بدهید:\n\n{prompt}",
        "stream": False
    }
    response = requests.post(url, json=data)
    response_data = response.json()
    return response_data["response"]
