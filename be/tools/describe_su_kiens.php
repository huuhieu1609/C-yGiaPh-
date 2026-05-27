<?php
$host='127.0.0.1';$port=3306;$db='be_caygiapha';$user='root';$pass='';
try{
 $pdo=new PDO("mysql:host=$host;port=$port;dbname=$db;charset=utf8mb4",$user,$pass,[PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION]);
 $stmt=$pdo->query('DESCRIBE su_kiens');
 $rows=$stmt->fetchAll(PDO::FETCH_ASSOC);
 foreach($rows as $r) echo sprintf("%s | %s | %s | %s\n", $r['Field'],$r['Type'],$r['Null'],$r['Key']);
}catch(PDOException $e){ echo 'ERROR: '.$e->getMessage()."\n"; }
