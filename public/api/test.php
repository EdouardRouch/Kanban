<?php 
    require __DIR__.'/vendor/autoload.php';

    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
    $dotenv->load();

    echo $_ENV['S3_BUCKET'];
    echo $_ENV['SECRET_KEY'];
?>