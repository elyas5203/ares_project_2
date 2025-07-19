import os
import logging
from telegram.ext import Updater, CommandHandler
from app.core.db import add_competitor

# Enable logging
logging.basicConfig(
    format='%(asctime)s - %(name)s - %(levelname)s - %(message)s', level=logging.INFO
)

logger = logging.getLogger(__name__)

# Define a few command handlers. These usually take the two arguments update and
# context.
def start(update, context):
    """Send a message when the command /start is issued."""
    update.message.reply_text('Hi!')

def post(update, context):
    """Post a message to a specific chat."""
    chat_id = os.environ["TELEGRAM_CHAT_ID"]
    message = " ".join(context.args)
    context.bot.send_message(chat_id=chat_id, text=message)

def add_competitor_command(update, context):
    """Add a competitor to the database."""
    name = context.args[0]
    url = context.args[1]
    add_competitor(name, url)
    update.message.reply_text(f'Competitor {name} added.')

def main() -> None:
    """Start the bot."""
    # Create the Updater and pass it your bot's token.
    updater = Updater(os.environ["TELEGRAM_BOT_TOKEN"])

    # Get the dispatcher to register handlers
    dispatcher = updater.dispatcher

    # on different commands - answer in Telegram
    dispatcher.add_handler(CommandHandler("start", start))
    dispatcher.add_handler(CommandHandler("post", post))
    dispatcher.add_handler(CommandHandler("add_competitor", add_competitor_command))

    # Start the Bot
    updater.start_polling()

    # Run the bot until you press Ctrl-C or the process receives SIGINT,
    # SIGTERM or SIGABRT. This should be used most of the time, since
    # start_polling() is non-blocking and will stop the bot gracefully.
    updater.idle()


if __name__ == '__main__':
    main()
