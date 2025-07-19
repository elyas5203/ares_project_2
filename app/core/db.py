import sqlite3

def get_db_connection():
    conn = sqlite3.connect('data/database.db')
    conn.row_factory = sqlite3.Row
    return conn

def init_db():
    conn = get_db_connection()
    conn.execute('CREATE TABLE IF NOT EXISTS competitors (id INTEGER PRIMARY KEY AUTOINCREMENT, name TEXT NOT NULL, url TEXT NOT NULL)')
    conn.execute('CREATE TABLE IF NOT EXISTS chats (id INTEGER PRIMARY KEY AUTOINCREMENT, title TEXT NOT NULL, telegram_chat_id INTEGER)')
    conn.execute('CREATE TABLE IF NOT EXISTS messages (id INTEGER PRIMARY KEY AUTOINCREMENT, chat_id INTEGER NOT NULL, role TEXT NOT NULL, content TEXT NOT NULL, FOREIGN KEY (chat_id) REFERENCES chats (id))')
    conn.execute('CREATE TABLE IF NOT EXISTS knowledge_base (id INTEGER PRIMARY KEY AUTOINCREMENT, content TEXT NOT NULL)')
    conn.execute('CREATE TABLE IF NOT EXISTS products (id INTEGER PRIMARY KEY AUTOINCREMENT, name TEXT NOT NULL, price REAL NOT NULL, description TEXT, image TEXT)')
    conn.close()

def add_competitor(name, url):
    conn = get_db_connection()
    conn.execute('INSERT INTO competitors (name, url) VALUES (?, ?)', (name, url))
    conn.commit()
    conn.close()

def get_chats():
    conn = get_db_connection()
    chats = conn.execute('SELECT * FROM chats').fetchall()
    conn.close()
    return chats

def create_chat(title):
    conn = get_db_connection()
    cursor = conn.execute('INSERT INTO chats (title) VALUES (?)', (title,))
    chat_id = cursor.lastrowid
    conn.commit()
    conn.close()
    return get_chat(chat_id)

def get_chat(chat_id=None, telegram_chat_id=None):
    conn = get_db_connection()
    if chat_id:
        chat = conn.execute('SELECT * FROM chats WHERE id = ?', (chat_id,)).fetchone()
    elif telegram_chat_id:
        chat = conn.execute('SELECT * FROM chats WHERE telegram_chat_id = ?', (telegram_chat_id,)).fetchone()
    else:
        chat = None
    conn.close()
    return chat

def get_messages(chat_id):
    conn = get_db_connection()
    messages = conn.execute('SELECT * FROM messages WHERE chat_id = ? ORDER BY id ASC', (chat_id,)).fetchall()
    conn.close()
    return messages

def create_message(chat_id, role, content):
    conn = get_db_connection()
    conn.execute('INSERT INTO messages (chat_id, role, content) VALUES (?, ?, ?)', (chat_id, role, content))
    conn.commit()
    conn.close()

def add_knowledge(content):
    conn = get_db_connection()
    conn.execute('INSERT INTO knowledge_base (content) VALUES (?)', (content,))
    conn.commit()
    conn.close()

def get_knowledge():
    conn = get_db_connection()
    knowledge = conn.execute('SELECT * FROM knowledge_base').fetchall()
    conn.close()
    return knowledge

def rename_chat(chat_id, title):
    conn = get_db_connection()
    conn.execute('UPDATE chats SET title = ? WHERE id = ?', (title, chat_id))
    conn.commit()
    conn.close()

def add_product(name, price, description, image):
    conn = get_db_connection()
    conn.execute('INSERT INTO products (name, price, description, image) VALUES (?, ?, ?, ?)', (name, price, description, image))
    conn.commit()
    conn.close()

def get_products():
    conn = get_db_connection()
    products = conn.execute('SELECT * FROM products').fetchall()
    conn.close()
    return products

def get_competitors():
    conn = get_db_connection()
    competitors = conn.execute('SELECT * FROM competitors').fetchall()
    conn.close()
    return competitors

def get_competitor(competitor_id):
    conn = get_db_connection()
    competitor = conn.execute('SELECT * FROM competitors WHERE id = ?', (competitor_id,)).fetchone()
    conn.close()
    return competitor
