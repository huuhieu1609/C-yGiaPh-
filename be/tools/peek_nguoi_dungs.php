<?php
$host='127.0.0.1';$port=3306;$db='be_caygiapha';$user='root';$pass='';
try{
 $pdo=new PDO("mysql:host=$host;port=$port;dbname=$db;charset=utf8mb4",$user,$pass,[PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION]);
 $stmt=$pdo->query('SELECT * FROM nguoi_dungs LIMIT 5');
 $rows=$stmt->fetchAll(PDO::FETCH_ASSOC);
 if(!$rows){ echo "(no rows)\n"; exit; }
 foreach($rows as $r){ foreach($r as $k=>$v) echo "$k => ".($v===null?'NULL':$v)."\n"; echo "----\n"; }
}catch(PDOException $e){ echo 'ERROR: '.$e->getMessage()."\n"; }
