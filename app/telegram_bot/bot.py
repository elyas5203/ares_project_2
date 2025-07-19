import os
import logging
import asyncio
from dotenv import load_dotenv
from telegram import Update
from telegram.ext import Application, CommandHandler, ContextTypes
from app.core.db import add_competitor, add_knowledge

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

def main() -> None:
    """Start the bot."""
    application = Application.builder().token(os.environ["TELEGRAM_BOT_TOKEN"]).build()

    application.add_handler(CommandHandler("start", start))
    application.add_handler(CommandHandler("post", post))
    application.add_handler(CommandHandler("add_competitor", add_competitor_command))
    application.add_handler(CommandHandler("add_knowledge", add_knowledge_command))

    application.run_polling()

if __name__ == "__main__":
    main()
