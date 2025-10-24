<?php
// Update for your environment
$DB_HOST = '127.0.0.1';
$DB_NAME = 'od';
$DB_USER = 'abedod';
$DB_PASS = 'abedod';

$pdo = new PDO(
  "mysql:host={$DB_HOST};dbname={$DB_NAME};charset=utf8mb4",
  $DB_USER,
  $DB_PASS,
  [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
  ]
);
