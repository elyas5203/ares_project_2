import sqlite3

def get_db_connection():
    conn = sqlite3.connect('data/database.db')
    conn.row_factory = sqlite3.Row
    return conn

def init_db():
    conn = get_db_connection()
    conn.execute('CREATE TABLE IF NOT EXISTS competitors (id INTEGER PRIMARY KEY AUTOINCREMENT, name TEXT NOT NULL, url TEXT NOT NULL)')
    conn.close()

def add_competitor(name, url):
    conn = get_db_connection()
    conn.execute('INSERT INTO competitors (name, url) VALUES (?, ?)', (name, url))
    conn.commit()
    conn.close()
