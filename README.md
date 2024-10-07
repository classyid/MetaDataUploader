# Image Upload and Metadata Extraction Project

## Description
This project provides a simple web-based image upload system that extracts metadata from images. It includes features for sending metadata to WhatsApp and Telegram. The project is designed with security considerations, including configuration protection, secure file handling, and restricted directory access.

## About
This repository consists of four main files and a folder:
1. `.htaccess` - Security configurations and URL rewrite rules.
2. `config.php` - Contains the configuration settings for WhatsApp and Telegram messaging.
3. `index.html` - The front-end interface for uploading images.
4. `upload.php` - Backend logic for handling image uploads and metadata extraction.
5. `temp_images/` - Folder to store uploaded images temporarily.

## Features
- **Image Upload**: Users can upload images through a user-friendly HTML form.
- **Metadata Extraction**: Automatically extracts EXIF metadata from JPEG images.
- **WhatsApp and Telegram Messaging**: Sends metadata as a text message via WhatsApp and as a document via Telegram (configurable).
- **Security**: Implements security best practices using `.htaccess` to protect sensitive files and directories.

## File Details

### 1. `.htaccess`
This file handles the security settings and URL rewrites for the project. The key functions include:
- Blocking direct access to the `config.php` file.
- Protecting the `.htaccess` file itself.
- Restricting access to any hidden files or files that start with a dot.
- Disabling directory listings to enhance security.
- Setting the default file to be served as `index.html`.

### 2. `config.php`
This file contains the configuration settings for WhatsApp and Telegram messaging. Sensitive information like API keys, sender details, and chat IDs are stored here. The file is protected to prevent unauthorized access.

**Sample Configurations:**
```php
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
