<?php
declare(strict_types=1);

namespace App\Database;

//либо сделать ini файл либо переделать подключение config

class ConnectionProvider
{
    static function getConnectionParams(): array
    {
        return parse_ini_file('config.ini');
    }
    static function getConnection(): \PDO
    {
        $connectionParams = ConnectionProvider::getConnectionParams();
        return new \PDO($connectionParams['dsn'], $connectionParams['username'], $connectionParams['password']);
    }
}