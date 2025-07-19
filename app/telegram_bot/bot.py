import os
import logging
import asyncio
from dotenv import load_dotenv
from telegram import Update
from hazm import word_tokenize, Lemmatizer
from telegram.ext import Application, CommandHandler, MessageHandler, filters, ContextTypes
from app.core.db import add_competitor, add_knowledge, create_chat, get_chat, create_message, get_messages, get_products
from app.core.ai import get_ai_response

lemmatizer = Lemmatizer()

# Enable logging
logging.basicConfig(
    format="%(asctime)s - %(name)s - %(levelname)s - %(message)s", level=logging.INFO
)
logger = logging.getLogger(__name__)

load_dotenv()

async def start(update: Update, context: ContextTypes.DEFAULT_TYPE) -> None:
    """Send a message when the command /start is issued."""
    await update.message.reply_text("سلام! من دستیار هوش مصنوعی شما هستم.")

async def post(update: Update, context: ContextTypes.DEFAULT_TYPE) -> None:
    """Post a message to a specific chat."""
    chat_id = os.environ["TELEGRAM_CHAT_ID"]
    message = " ".join(context.args)
    await context.bot.send_message(chat_id=chat_id, text=message)

async def add_competitor_command(update: Update, context: ContextTypes.DEFAULT_TYPE) -> None:
    """Add a competitor to the database."""
    name = context.args[0]
    url = context.args[1]
    add_competitor(name, url)
    await update.message.reply_text(f"رقیب {name} با موفقیت اضافه شد.")

async def add_knowledge_command(update: Update, context: ContextTypes.DEFAULT_TYPE) -> None:
    """Add information to the knowledge base."""
    content = " ".join(context.args)
    add_knowledge(content)
    await update.message.reply_text("اطلاعات جدید با موفقیت به پایگاه دانش اضافه شد.")

async def handle_message(update: Update, context: ContextTypes.DEFAULT_TYPE) -> None:
    """Handle text messages."""
    telegram_chat_id = update.message.chat_id
    user_message = update.message.text

    tokens = word_tokenize(user_message)
    lemmas = [lemmatizer.lemmatize(token) for token in tokens]

    # Check for keywords
    if any(lemma in ["محصول", "کالا"] for lemma in lemmas) and any(lemma in ["آخرین", "جدیدترین"] for lemma in lemmas):
        products = get_products()
        if products:
            last_product = products[-1]
            await update.message.reply_text(f"آخرین محصول اضافه شده: {last_product['name']} - قیمت: {last_product['price']}")
        else:
            await update.message.reply_text("محصولی یافت نشد.")
        return

    # If no specific command is found, treat it as a normal chat message
    chat = get_chat(telegram_chat_id=telegram_chat_id)
    if not chat:
        chat = create_chat(f"Telegram Chat with {update.message.from_user.first_name}")
        conn = get_db_connection()
        conn.execute('UPDATE chats SET telegram_chat_id = ? WHERE id = ?', (telegram_chat_id, chat['id']))
        conn.commit()
        conn.close()
        chat = get_chat(chat_id=chat['id'])

    create_message(chat['id'], 'user', user_message)

    messages = get_messages(chat['id'])
    history = "\n".join([f"{m['role']}: {m['content']}" for m in messages])

    response = get_ai_response(history)
    create_message(chat['id'], 'ai', response)

    await update.message.reply_text(response)

def main() -> None:
    """Start the bot."""
    application = Application.builder().token(os.environ["TELEGRAM_BOT_TOKEN"]).job_queue(None).build()

    application.add_handler(CommandHandler("start", start))
    application.add_handler(CommandHandler("post", post))
    application.add_handler(CommandHandler("add_competitor", add_competitor_command))
    application.add_handler(CommandHandler("add_knowledge", add_knowledge_command))
    application.add_handler(MessageHandler(filters.TEXT & ~filters.COMMAND, handle_message))

    application.run_polling()

if __name__ == "__main__":
    main()
