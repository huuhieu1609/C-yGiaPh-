<?php
$host = '127.0.0.1';
$port = 3306;
$db = 'be_caygiapha';
$user = 'root';
$pass = '';
try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$db;charset=utf8mb4", $user, $pass, [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION]);
    $stmt = $pdo->query('SELECT id, tieu_de, chi_nhanh_id, ngay_to_chuc, created_at FROM su_kiens ORDER BY ngay_to_chuc DESC');
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if (count($rows) === 0) {
        echo "(no rows)\n";
    } else {
        foreach ($rows as $r) {
            echo sprintf("%s | %s | %s | %s | %s\n", $r['id'], $r['tieu_de'], $r['chi_nhanh_id'] ?? 'NULL', $r['ngay_to_chuc'], $r['created_at']);
        }
    }
} catch (PDOException $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
