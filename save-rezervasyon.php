<?php
// save-rezervasyon.php
//header('Content-Type: application/json');

try {
    $dbPath = __DIR__ . '/rezervasyonlar.db';
    $db = new SQLite3($dbPath);

    // Tablo oluştur (key-value yapısında)
    $db->exec('CREATE TABLE IF NOT EXISTS rezervasyonlar (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        key TEXT,
        value TEXT,
        kayit_tarihi DATETIME DEFAULT CURRENT_TIMESTAMP
    )');

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode(['error' => 'Yalnızca POST isteği kabul edilir.']);
        exit;
    }

    $data = json_decode(file_get_contents('php://input'), true);
    if (!$data || !is_array($data)) {
        http_response_code(400);
        echo json_encode(['error' => 'Geçersiz veri.']);
        exit;
    }

    // Eğer otel rezervasyonu ise
    if (isset($data['key'])) {
        $key = $data['key'];
        unset($data['key']); // key alanını JSON’dan çıkar
    }
    // Eğer balon turu rezervasyonu ise
   
    else {
        $key = 'genel'; // Belirtilmezse genel kayıt
    }

    // Geri kalan verileri JSON’a çevir
    $jsonValue = json_encode($data, JSON_UNESCAPED_UNICODE);

    $stmt = $db->prepare('INSERT INTO rezervasyonlar (key, value) VALUES (:key, :value)');
    $stmt->bindValue(':key', $key, SQLITE3_TEXT);
    $stmt->bindValue(':value', $jsonValue, SQLITE3_TEXT);

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        throw new Exception('Veritabanı kaydı başarısız.');
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Kayıt başarısız: ' . $e->getMessage()]);
} finally {
    if (isset($db)) {
        $db->close();
    }
}
?>
