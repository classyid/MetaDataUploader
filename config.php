return [
    'send_whatsapp' => true, // Enable or disable WhatsApp messaging
    'send_telegram' => false, // Enable or disable Telegram messaging
    'whatsapp' => [
        'api_key' => 'your-api-key',
        'sender' => 'your-sender-number',
        'number' => 'receiver-number',
        'api_url' => 'https://api-url-for-whatsapp'
    ],
    'telegram' => [
        'token' => 'your-telegram-bot-token',
        'chat_id' => 'your-chat-id',
        'api_url' => 'https://api.telegram.org/bot'
    ]
];
