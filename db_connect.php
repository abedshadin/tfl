<?php 
// 1. IP Blocker FIRST
include 'ip_blocker.php'; 
?>

<?php
// --- Existing Database configuration ---
$servername = "localhost";
$username = "root"; // Your DB username
$password = ""; // Your DB password
$dbname = "od"; // Your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

// --- Encryption Settings ---
define('ENCRYPTION_KEY', '@01794588580@abed@'); // Your secret key
define('ENCRYPTION_METHOD', 'AES-256-CBC');

/**
 * Encrypts an ID for URL usage (DETERMINISTIC IV - LESS SECURE, URL-SAFE).
 * @param int $id The ID to encrypt.
 * @return string|false The URL-safe encrypted string, or false on failure.
 */
function encryptId($id) {
    $key = hex2bin(hash('sha256', ENCRYPTION_KEY));
    $iv_length = openssl_cipher_iv_length(ENCRYPTION_METHOD);
    $iv = substr(hash('sha256', (string)$id . ENCRYPTION_KEY), 0, $iv_length);

    if (strlen($iv) < $iv_length) {
        $iv = str_pad($iv, $iv_length, "\0");
    } elseif (strlen($iv) > $iv_length) {
        $iv = substr($iv, 0, $iv_length);
    }

    $encrypted = openssl_encrypt((string)$id, ENCRYPTION_METHOD, $key, OPENSSL_RAW_DATA, $iv);

    if ($encrypted === false) {
        // error_log("Encryption failed: " . openssl_error_string()); // Optional: Log error
        return false;
    }

    // Combine IV and ciphertext, then use URL-safe Base64 encoding
    $data_to_encode = $iv . $encrypted;
    return strtr(base64_encode($data_to_encode), '+/', '-_'); // Replace '+' with '-' and '/' with '_'
}

/**
 * Decrypts a URL-safe encrypted ID.
 * @param string $encrypted_id_urlsafe The URL-safe encrypted string.
 * @return int|false The original ID, or false on failure/invalid input.
 */
function decryptId($encrypted_id_urlsafe) {
    if (empty($encrypted_id_urlsafe)) {
        return false;
    }

    // Convert URL-safe base64 back to standard base64
    $encrypted_id = strtr($encrypted_id_urlsafe, '-_', '+/');
    
    // Decode using strict mode to catch errors
    $data = base64_decode($encrypted_id, true);
    
    if ($data === false) {
        // error_log("Base64 decode failed for: " . $encrypted_id_urlsafe); // Optional: Log error
        return false;
    }

    $key = hex2bin(hash('sha256', ENCRYPTION_KEY));
    $iv_length = openssl_cipher_iv_length(ENCRYPTION_METHOD);

    if (strlen($data) < $iv_length) {
        // error_log("Decoded data too short."); // Optional: Log error
        return false;
    }

    $iv = substr($data, 0, $iv_length);
    $ciphertext = substr($data, $iv_length);

    // Check if ciphertext part exists
    if ($ciphertext === false || $ciphertext === '') {
         // error_log("Ciphertext extraction failed or empty."); // Optional: Log error
        return false;
    }

    $decrypted = openssl_decrypt($ciphertext, ENCRYPTION_METHOD, $key, OPENSSL_RAW_DATA, $iv);

    if ($decrypted === false) {
         // error_log("Openssl decrypt failed: " . openssl_error_string()); // Optional: Log error
        return false;
    }

    // Ensure the result is purely numeric digits before casting
    if (!ctype_digit($decrypted)) {
         // error_log("Decrypted data not numeric."); // Optional: Log error
        return false;
    }

    return (int)$decrypted;
}
?>