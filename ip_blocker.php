<?php

// --- Configuration ---
// Add the specific IP addresses you want to allow access from.
$allowed_ips = [
    '192.168.31.1', // Example private IP
    '203.0.113.5',   // Example public IP
    '::1',           // IPv6 loopback (for local testing)
    '127.0.0.1',
    '45.251.56.173'
          // IPv4 loopback (for local testing)
    // Add more allowed IPs here
];

// --- Get Visitor's IP ---
// Using REMOTE_ADDR is generally the most reliable for direct connections.
// Be cautious with HTTP_X_FORWARDED_FOR or HTTP_CLIENT_IP if behind a proxy,
// as they can be spoofed unless your proxy configuration is secure.
$visitor_ip = $_SERVER['REMOTE_ADDR'];

// --- Check if IP is allowed ---
if (!in_array($visitor_ip, $allowed_ips)) {
    // IP is NOT allowed
    http_response_code(403); // Set response code to "Forbidden"
    // Display a simple error message and stop script execution.
    // You could also redirect them to another page.
    die("Access Denied: Your IP address (" . htmlspecialchars($visitor_ip) . ") is not authorized to access this site."); 
}

// --- IP is allowed ---
// If the script reaches here, the IP is allowed. 
// Continue loading the rest of your page (e.g., session_start(), include 'db_connect.php', etc.)
// echo "Access Granted for IP: " . htmlspecialchars($visitor_ip); // Optional: For testing

// Now include your authentication check if you have one
// include 'auth_check.php'; 

// Include the rest of your page logic...

?>