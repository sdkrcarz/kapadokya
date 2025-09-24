<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

try {
    // Veritabanı bağlantısı
    $db_path = __DIR__ . '/rezervasyonlar.db';
    if (!file_exists($db_path)) {
        throw new Exception("Veritabanı dosyası bulunamadı: rezervasyonlar.db");
    }

    $db = new SQLite3($db_path);
    if (!$db) {
        throw new Exception("Veritabanına bağlanılamadı");
    }

    // Rezervasyonları çek
    $result = $db->query('SELECT * FROM rezervasyonlar ORDER BY kayit_tarihi DESC');
    if (!$result) {
        throw new Exception("Sorgu hatası: " . $db->lastErrorMsg());
    }

    // Kategorilere ayır
    $otel = [];
    $balon = [];
    $atv = [];

    while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
        $value = json_decode($row['value'] ?? '{}', true);
        $tur = strtolower($value['tur'] ?? 'otel');

        $item = [
            'id' => $row['id'] ?? '',
            'otel_adi' => $value['otel'] ?? $value['hotel'] ?? 'Belirtilmemiş',
            'adsoyad' => $value['adsoyad'] ?? $value['name'] ?? 'Belirtilmemiş',
            'email' => $value['email'] ?? 'Belirtilmemiş',
            'telefon' => $value['telefon'] ?? $value['phone'] ?? 'Belirtilmemiş',
            'kisi_sayisi' => $value['kisi_sayisi'] ?? $value['guests'] ?? '1',
            'giris_tarihi' => $value['giris_tarihi'] ?? $value['checkin'] ?? $value['tarih'] ?? 'Belirtilmemiş',
            'cikis_tarihi' => $value['cikis_tarihi'] ?? $value['checkout'] ?? 'Belirtilmemiş',
            'tarih' => $value['tarih'] ?? $value['date'] ?? 'Belirtilmemiş',
            'saat' => $value['saat'] ?? $value['time'] ?? 'Belirtilmemiş',
            'oda_tipi' => $value['oda_tipi'] ?? $value['roomType'] ?? 'Standart',
            'ek_talepler' => $value['ek_talepler'] ?? $value['requests'] ?? 'Yok',
            'kayit_tarihi' => $row['kayit_tarihi'] ?? 'Belirtilmemiş'
        ];

        // Türüne göre ayır
        switch ($tur) {
            case 'balon':
                $balon[] = $item;
                break;
            case 'atv':
                $atv[] = $item;
                break;
            default:
                $otel[] = $item;
        }
    }

    echo json_encode([
        'status' => 'success',
        'otel' => $otel,
        'balon' => $balon,
        'atv' => $atv,
        'toplam' => [
            'otel' => count($otel),
            'balon' => count($balon),
            'atv' => count($atv)
        ]
    ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
} finally {
    if (isset($db)) $db->close();
}
?>