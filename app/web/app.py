from flask import Flask, render_template, request
from app.core.ai import get_ai_response

app = Flask(__name__)

@app.route('/')
def index():
    return render_template('index.html')

@app.route('/chat', methods=['POST'])
def chat():
    prompt = request.form['prompt']
    response = get_ai_response(prompt)
    return response

if __name__ == '__main__':
    app.run(debug=True)
