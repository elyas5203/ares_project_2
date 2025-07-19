from flask import Flask, render_template, request, jsonify
from dotenv import load_dotenv
from app.core.ai import get_ai_response
from app.core.db import get_chats, create_chat, get_messages, create_message, rename_chat, add_product, get_products, add_competitor, get_competitors

app = Flask(__name__)

load_dotenv()

@app.route('/')
def dashboard():
    return render_template('index.html')

@app.route('/chat')
def chat():
    return render_template('chat.html')

from app.core.ai import analyze_competitor_website

@app.route('/competitors', methods=['GET', 'POST'])
def competitors():
    if request.method == 'POST':
        name = request.form['name']
        url = request.form['url']
        add_competitor(name, url)

    competitors = get_competitors()
    return render_template('competitors.html', competitors=competitors)

from app.core.db import get_competitor

@app.route('/competitors/<int:competitor_id>/analyze')
def analyze_competitor_route(competitor_id):
    competitor = get_competitor(competitor_id)
    if competitor:
        result = analyze_competitor_website(competitor['url'])
        return render_template('analysis_result.html', competitor=competitor, result=result)
    return "رقیب یافت نشد."

@app.route('/products', methods=['GET', 'POST'])
def products():
    if request.method == 'POST':
        name = request.form['name']
        price = request.form['price']
        description = request.form['description']
        image = request.files['image']

        if image and allowed_file(image.filename):
            filename = secure_filename(image.filename)
            image.save(os.path.join(app.config['UPLOAD_FOLDER'], filename))
            add_product(name, price, description, filename)

            # Post to Telegram
            chat_id = os.environ["TELEGRAM_CHAT_ID"]
            message = f"محصول جدید: {name}\nقیمت: {price}\nتوضیحات: {description}"
            # I need to find a way to send the image to telegram
            # For now, I will just send the text
            # I will fix this in the next step
            # context.bot.send_message(chat_id=chat_id, text=message)

    products = get_products()
    return render_template('products.html', products=products)

@app.route('/chats', methods=['GET'])
def get_all_chats():
    chats = get_chats()
    return jsonify([dict(ix) for ix in chats])

@app.route('/chats', methods=['POST'])
def create_new_chat():
    chat = create_chat("چت جدید")
    return jsonify(dict(chat))

@app.route('/chats/<int:chat_id>/messages', methods=['GET'])
def get_all_messages(chat_id):
    messages = get_messages(chat_id)
    return jsonify([dict(ix) for ix in messages])

import os
from werkzeug.utils import secure_filename

UPLOAD_FOLDER = 'uploads'
ALLOWED_EXTENSIONS = {'txt', 'pdf', 'png', 'jpg', 'jpeg', 'gif'}

app.config['UPLOAD_FOLDER'] = UPLOAD_FOLDER

def allowed_file(filename):
    return '.' in filename and \
           filename.rsplit('.', 1)[1].lower() in ALLOWED_EXTENSIONS

@app.route('/chats/<int:chat_id>/messages', methods=['POST'])
def add_message(chat_id):
    prompt = request.form['prompt']
    file = request.files.get('file')

    if file and allowed_file(file.filename):
        filename = secure_filename(file.filename)
        file.save(os.path.join(app.config['UPLOAD_FOLDER'], filename))
        prompt += f"\n\nفایل ضمیمه شده: {filename}"

    create_message(chat_id, 'user', prompt)

    # Get message history for context
    messages = get_messages(chat_id)
    history = "\n".join([f"{m['role']}: {m['content']}" for m in messages])

    response = get_ai_response(history)
    create_message(chat_id, 'ai', response)

    # Return the new message
    new_message = get_messages(chat_id)[-1]
    return jsonify(dict(new_message))

@app.route('/chats/<int:chat_id>', methods=['PUT'])
def rename_chat_endpoint(chat_id):
    title = request.json['title']
    rename_chat(chat_id, title)
    return jsonify({'success': True})

if __name__ == '__main__':
    app.run(debug=True)
