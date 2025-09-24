<?php
$servername = "localhost";
$username = "root";
$password = ""; // XAMPP'te root şifresi genelde boş
$dbname = "kapadokya";

// MySQL bağlantısı
$conn = new mysqli($servername, $username, $password, $dbname);

// Bağlantı kontrolü
if ($conn->connect_error) {
    die("Veritabanı bağlantı hatası: " . $conn->connect_error);
}

// Karakter setini UTF-8 yap (Türkçe karakter sorunu olmasın)
$conn->set_charset("utf8");
?>