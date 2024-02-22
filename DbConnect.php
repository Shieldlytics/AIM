<?php
$host = "pena-cloud.network";
$port = 19307;
$databaseName = "AIM";
$username = "ErnestPenaJr";
$password = "$268RedDragons";
$dsn = "mysql:host=$host;port=$port;dbname=$databaseName;charset=utf8mb4";
// $host = "tcp:guardian-dev-db.database.windows.net";
// $port = 1433;
// $databaseName = "GUARDIAN-DEV";
// $username = "GUARDIAN";
// $password = "Shieldlytics1$";
// $dsn = "pdo_sqlsrv:server=$host,$port;Database=$databaseName";



try {
    $conn = new PDO($dsn, $username, $password);
    // Set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Connected successfully"; 
} catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}

?>