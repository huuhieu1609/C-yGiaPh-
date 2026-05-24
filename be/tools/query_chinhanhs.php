<?php
$host = '127.0.0.1';
$port = 3306;
$db = 'be_caygiapha';
$user = 'root';
$pass = '';
try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$db;charset=utf8mb4", $user, $pass, [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION]);
    $stmt = $pdo->query('SELECT id, id_nguoi_dung, ten_chi FROM chi_nhanhs ORDER BY id');
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if (count($rows) === 0) {
        echo "(no rows)\n";
    } else {
        foreach ($rows as $r) {
            echo sprintf("%s | %s | %s\n", $r['id'], $r['id_nguoi_dung'] ?? 'NULL', $r['ten_chi']);
        }
    }
} catch (PDOException $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
