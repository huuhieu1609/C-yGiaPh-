<?php
$host = '127.0.0.1';
$port = 3306;
$db = 'be_caygiapha';
$user = 'root';
$pass = '';
try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$db;charset=utf8mb4", $user, $pass, [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION]);
    $stmt = $pdo->query('SELECT id, name, email, vai_tro, is_doi_tac FROM nguoi_dungs ORDER BY id');
    foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $r) {
        echo sprintf("%s | %s | %s | %s | %s\n", $r['id'], $r['name'] ?? '', $r['email'] ?? '', $r['vai_tro'] ?? '', $r['is_doi_tac'] ?? '');
    }
} catch (PDOException $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
