<?php
function ConnectDB(){
    try{
        $host = '127.0.0.1:3307';
        $db = 'TestTask';
        $user = 'root';
        $pass = '';
        $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
        return $pdo;
    }
    catch(Exception $e){
        echo "Connection failed: " . $e->getMessage();
    }
}
