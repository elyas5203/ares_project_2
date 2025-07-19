from flask import Flask, render_template, request, jsonify
from dotenv import load_dotenv
from app.core.ai import get_ai_response
from app.core.db import get_chats, create_chat, get_messages, create_message, rename_chat

app = Flask(__name__)

load_dotenv()

@app.route('/')
def index():
    return render_template('index.html')

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
