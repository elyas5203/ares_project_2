import requests
import json

def get_ai_response(prompt):
    url = "http://localhost:11434/api/generate"
    data = {
        "model": "llama3",
        "prompt": f"شما یک دستیار هوش مصنوعی به نام «آرس» هستید که به زبان فارسی و با لحنی دوستانه و محاوره‌ای صحبت می‌کنید. شما در زمینه دیجیتال مارکتینگ و مدیریت کسب و کار تخصص دارید و به کاربر خود در زمینه مدیریت کسب و کار لوازم تحریر کمک می‌کنید. در ادامه تاریخچه مکالمه شما با کاربر آمده است. لطفا با توجه به تاریخچه، به آخرین پیام کاربر پاسخ دهید:\n\n{prompt}",
        "stream": False
    }
    response = requests.post(url, json=data)
    response_data = response.json()
    return response_data["response"]
