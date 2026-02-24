<?php

$pdo = new PDO('mysql:host=mysql;dbname=etuServices', 'user', 'password');

$redis = new Redis();
$redis->connect('redis', 6379);