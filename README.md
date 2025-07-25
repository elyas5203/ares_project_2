# دستیار هوش مصنوعی

این یک دستیار هوش مصنوعی برای کسب و کار لوازم تحریر شماست.

## راه اندازی

۱. **ایجاد و فعال‌سازی محیط مجازی:**
   برای جلوگیری از تداخل کتابخانه‌ها، بهتر است از یک محیط مجازی استفاده کنید.
   ```bash
   # ایجاد محیط مجازی
   python -m venv venv

   # فعال‌سازی در ویندوز
   venv\Scripts\activate

   # فعال‌سازی در مک و لینوکس
   source venv/bin/activate
   ```

۲. **نصب پیش‌نیازها:**
   ```
   pip install -r requirements.txt
   ```
۳. **ایجاد فایل `.env`:**
   یک فایل به نام `.env` ایجاد کنید و اطلاعات زیر را در آن قرار دهید:
   ```
   TELEGRAM_BOT_TOKEN="<توکن ربات تلگرام خود را اینجا وارد کنید>"
   TELEGRAM_CHAT_ID="<آیدی چت تلگرام خود را اینجا وارد کنید>"
   INSTAGRAM_USERNAME="<نام کاربری اینستاگرام خود را اینجا وارد کنید>"
   INSTAGRAM_PASSWORD="<رمز عبور اینستاگرام خود را اینجا وارد کنید>"
   ```
۴. **ایجاد پایگاه داده:**
   ```
   python -c "from app.core.db import init_db; init_db()"
   ```

## اجرای برنامه

۱. **اجرای وب اپلیکیشن:**
   ```
   python -m app.web.app
   ```
۲. **اجرای ربات تلگرام:**
   ```
   python -m app.telegram_bot.bot
   ```
