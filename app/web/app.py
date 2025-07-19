from flask import Flask, render_template, request, jsonify
from app.core.ai import get_ai_response
from app.core.db import get_chats, create_chat, get_messages, create_message

app = Flask(__name__)

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

@app.route('/chats/<int:chat_id>/messages', methods=['POST'])
def add_message(chat_id):
    prompt = request.json['prompt']
    create_message(chat_id, 'user', prompt)

    # Get message history for context
    messages = get_messages(chat_id)
    history = "\n".join([f"{m['role']}: {m['content']}" for m in messages])

    response = get_ai_response(history)
    create_message(chat_id, 'ai', response)

    # Return the new message
    new_message = get_messages(chat_id)[-1]
    return jsonify(dict(new_message))

if __name__ == '__main__':
    app.run(debug=True)
