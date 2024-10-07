Here's the improved version of the `README.md` with corrections and formatting enhancements:

# Image Upload and Metadata Extraction Project

## Description
This project provides a web-based image upload system that extracts metadata from images. It includes features for sending metadata to WhatsApp and Telegram. The project is designed with security considerations, including configuration protection, secure file handling, and restricted directory access.

## About
This repository consists of four main files and a folder:
1. **`.htaccess`** - Security configurations and URL rewrite rules.
2. **`config.php`** - Configuration settings for WhatsApp and Telegram messaging.
3. **`index.html`** - The front-end interface for uploading images.
4. **`upload.php`** - Backend logic for handling image uploads and metadata extraction.
5. **`temp_images/`** - Folder to temporarily store uploaded images.

## Features
- **Image Upload**: Users can upload images through a user-friendly HTML form.
- **Metadata Extraction**: Automatically extracts EXIF metadata from JPEG images.
- **WhatsApp and Telegram Messaging**: Sends metadata as a text message via WhatsApp and as a document via Telegram (configurable).
- **Security**: Implements security best practices using `.htaccess` to protect sensitive files and directories.

## File Details

### 1. `.htaccess`
This file handles security settings and URL rewrites for the project. Key functions include:
- Blocking direct access to the `config.php` file.
- Protecting the `.htaccess` file itself.
- Restricting access to hidden files or files that start with a dot.
- Disabling directory listings to enhance security.
- Setting the default file to be served as `index.html`.

### 2. `config.php`
This file contains the configuration settings for WhatsApp and Telegram messaging. Sensitive information like API keys, sender details, and chat IDs are stored here. It is protected to prevent unauthorized access.

**Sample Configuration:**
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
```

### 3. `index.html`
This is the front-end HTML page where users can upload their images. It includes:
- An upload form for image selection.
- A styled button to initiate the upload process.
- A responsive design for a user-friendly interface.

### 4. `upload.php`
This file contains the core logic for handling image uploads and metadata extraction. It performs the following functions:
- Validates the uploaded image file type and size.
- Extracts EXIF metadata from JPEG images.
- Saves the metadata to a text file.
- Optionally sends the metadata to WhatsApp and Telegram using APIs.
- Displays success or error messages to the user.

### 5. `temp_images/`
This folder temporarily stores the uploaded images. It is protected by the `.htaccess` file to prevent direct access.

## Security
To ensure the security of the application, the following measures have been implemented:
- **Configuration Protection**: Direct access to sensitive files like `config.php` and `.htaccess` is blocked.
- **Hidden Files**: Access to hidden files or files that start with a dot is restricted.
- **Directory Listing Disabled**: Directory listing is disabled to prevent unauthorized access to the file structure.
- **Content Security**: Headers are set to prevent cross-site scripting (XSS) and other security vulnerabilities.

## Installation
To set up this project locally or on a server:
1. Clone the repository to your web server.
2. Ensure that the server has write permissions for the `temp_images/` folder.
3. Update the `config.php` file with your actual API keys and configuration details for WhatsApp and Telegram.
4. Access the `index.html` file through your browser to start uploading images.

## Usage
1. Open the `index.html` file in your browser.
2. Use the form to upload an image.
3. If successful, the image metadata will be displayed, and if configured, the metadata will be sent to WhatsApp and Telegram.
