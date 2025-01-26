<?php


use Migrations\Migration;
use Providers\PDOProvider;

require dirname(__DIR__) . '/vendor/autoload.php';

Migration::turnOnTestMode();

$connection = PDOProvider::create();
$table = $connection->execute("SHOW TABLES LIKE 'users'");

if(empty($table)) {
    $connection->execute("CREATE TABLE `users` (
      `id` bigint NOT NULL AUTO_INCREMENT,
      `full_name` varchar(255) DEFAULT NULL,
      `role` varchar(50) DEFAULT NULL,
      `efficiency` int DEFAULT NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;");
}