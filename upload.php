<?php
// Include the configuration file
$config = include('config.php');

// Function to format file sizes into a human-readable form
function formatFileSize($size) {
    $units = ['B', 'KB', 'MB', 'GB', 'TB'];
    $power = $size > 0 ? floor(log($size, 1024)) : 0;
    return number_format($size / pow(1024, $power), 2, '.', ',') . ' ' . $units[$power];
}

// Function to display a success message with HTML and CSS styling
function displaySuccessMessage($filename, $metadataFile, $whatsAppResult, $telegramResult) {
    // Only show messaging results if they are not empty
    $messageResults = '';
    if ($whatsAppResult || $telegramResult) {
        $messageResults = "$whatsAppResult<br>$telegramResult<br><br>";
    }
    return "
        <style>
            body {
                font-family: Arial, sans-serif;
                display: flex;
                justify-content: center;
                align-items: center;
                height: 100vh;
                background-color: #f0f0f0;
                margin: 0;
            }
            .success-message {
                background-color: #ddffdd;
                color: #006600;
                border: 1px solid #006600;
                padding: 20px;
                margin: 20px;
                width: 80%;
                max-width: 600px;
                border-radius: 5px;
                text-align: center;
                box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            }
            .action-button {
                display: inline-block;
                margin: 10px;
                padding: 10px 20px;
                background-color: #006600;
                color: #ffffff;
                text-decoration: none;
                border-radius: 5px;
                transition: background-color 0.3s;
            }
            .action-button:hover {
                background-color: #004d00;
            }
        </style>
        <div class='success-message'>
            <strong>File " . htmlspecialchars($filename, ENT_QUOTES, 'UTF-8') . " has been successfully uploaded.</strong><br>
            Metadata successfully saved in <strong>" . htmlspecialchars($metadataFile, ENT_QUOTES, 'UTF-8') . "</strong><br>
            $messageResults
            <a href='index.html' class='action-button'>Upload Again</a>
            <a href='" . htmlspecialchars($metadataFile, ENT_QUOTES, 'UTF-8') . "' download class='action-button'>Download Metadata</a>
        </div>
    ";
}

// Function to display an error message with HTML and CSS styling
function displayErrorMessage($message) {
    return "
        <style>
            body {
                font-family: Arial, sans-serif;
                display: flex;
                justify-content: center;
                align-items: center;
                height: 100vh;
                background-color: #f0f0f0;
                margin: 0;
            }
            .error-message {
                background-color: #ffdddd;
                color: #d8000c;
                border: 1px solid #d8000c;
                padding: 20px;
                margin: 20px;
                width: 80%;
                max-width: 500px;
                border-radius: 5px;
                text-align: center;
                box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            }
            .back-button {
                display: inline-block;
                margin-top: 15px;
                padding: 10px 20px;
                background-color: #d8000c;
                color: #ffffff;
                text-decoration: none;
                border-radius: 5px;
                transition: background-color 0.3s;
            }
            .back-button:hover {
                background-color: #a50000;
            }
        </style>
        <div class='error-message'>
            <strong>Error:</strong> $message
            <br><br>
            <a href='index.html' class='back-button'>Back to Upload</a>
        </div>
    ";
}

// Function to send a plain text message via WhatsApp
function sendWhatsAppMessage($message_content) {
    global $config;
    $whatsapp = $config['whatsapp'];

    $postData = [
        'api_key' => $whatsapp['api_key'],
        'sender' => $whatsapp['sender'],
        'number' => $whatsapp['number'],
        'message' => $message_content
    ];

    $ch = curl_init($whatsapp['api_url']);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

    $message_response = curl_exec($ch);
    $error = curl_errno($ch);
    curl_close($ch);

    return $error ? 'Error sending text message via WhatsApp.' : 'Text message successfully sent via WhatsApp.';
}

// Function to send a document (text file) via Telegram
function sendTelegramDocument($metadataFilePath) {
    global $config;
    $telegram = $config['telegram'];

    $telegramUrl = $telegram['api_url'] . $telegram['token'] . '/sendDocument';

    // Use CURLFile to handle file uploads with cURL
    $document = new CURLFile(realpath($metadataFilePath));
    
    $payload = [
        'chat_id' => $telegram['chat_id'],
        'document' => $document,
        'caption' => 'Here is the metadata file for the uploaded image.'
    ];

    $ch = curl_init($telegramUrl);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);

    $response = curl_exec($ch);
    $error = curl_errno($ch);
    curl_close($ch);

    return $error ? 'Error sending document via Telegram.' : 'Document successfully sent via Telegram.';
}

// Define a constant to avoid direct script access
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['image'])) {
    // Set up a temporary directory to store the uploaded images
    $target_dir = "temp_images/";
    if (!is_dir($target_dir) && !mkdir($target_dir, 0777, true)) {
        exit(displayErrorMessage("Error: Unable to create directory for image uploads."));
    }

    // Sanitize and validate file input
    $original_filename = basename($_FILES['image']['name']);
    $imageFileType = strtolower(pathinfo($original_filename, PATHINFO_EXTENSION));
    $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
    $new_filename = date('d_m_Y_H_i_s') . '.' . $imageFileType;
    $target_file = $target_dir . $new_filename;

    // Check if the file type is valid
    if (!in_array($imageFileType, $allowed_types)) {
        exit(displayErrorMessage("Error: Only JPG, JPEG, PNG, and GIF files are allowed."));
    }

    // Verify that the file is an actual image
    $check = getimagesize($_FILES['image']['tmp_name']);
    if ($check === false) {
        exit(displayErrorMessage("Error: Uploaded file is not a valid image."));
    }

    // Ensure the uploaded file does not exceed the size limit (e.g., 10MB)
    if ($_FILES['image']['size'] > 10 * 1024 * 1024) {
        exit(displayErrorMessage("Error: File size exceeds the maximum limit of 10MB."));
    }

    // Move the uploaded file to the target directory securely
    if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
        $metadata_file = $target_dir . pathinfo($target_file, PATHINFO_FILENAME) . "_metadata.txt";
        
        // Extract metadata using EXIF for JPEG images only
        if (in_array($imageFileType, ['jpg', 'jpeg'])) {
            $exif = @exif_read_data($target_file, 0, true);
            if ($exif) {
                $file_handle = fopen($metadata_file, 'w');

                // Format file date and size for better readability
                $fileDateTime = isset($exif['FILE']['FileDateTime']) ? date('l, d F Y H:i:s', $exif['FILE']['FileDateTime']) : 'No data';
                $fileSize = isset($exif['FILE']['FileSize']) ? formatFileSize($exif['FILE']['FileSize']) : 'No data';

                // Write metadata to the file
                fwrite($file_handle, "Metadata EXIF for image: " . htmlspecialchars($new_filename, ENT_QUOTES, 'UTF-8') . "\n\n");
                fwrite($file_handle, "FILE.FileDateTime: $fileDateTime\n");
                fwrite($file_handle, "FILE.FileSize: $fileSize\n\n");

                $metadata_content = "FileDateTime: $fileDateTime\nFileSize: $fileSize\n"; // For WhatsApp message

                foreach ($exif as $key => $section) {
                    foreach ($section as $name => $val) {
                        $val = is_array($val) ? implode(", ", $val) : $val;
                        $metadata_line = "$key.$name: " . htmlspecialchars($val, ENT_QUOTES, 'UTF-8') . "\n";
                        fwrite($file_handle, $metadata_line);
                        $metadata_content .= $metadata_line; // Add to WhatsApp message
                    }
                }
                fclose($file_handle);

                // Check configuration and send metadata as a text message to WhatsApp and as a document to Telegram
                $whatsAppResult = $config['send_whatsapp'] ? sendWhatsAppMessage($metadata_content) : '';
                $telegramResult = $config['send_telegram'] ? sendTelegramDocument($metadata_file) : '';

                echo displaySuccessMessage($new_filename, $metadata_file, $whatsAppResult, $telegramResult);
            }
        }
    } else {
        exit(displayErrorMessage("Error: There was an issue uploading your file."));
    }
} else {
    exit(displayErrorMessage("Error: Invalid request or no file uploaded."));
}
?>
