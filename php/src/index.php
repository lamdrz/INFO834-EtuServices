<?php

require_once 'db.php';

$redis = new Redis();
$redis->connect('redis', 6379);