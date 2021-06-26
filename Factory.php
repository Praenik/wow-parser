<?php

namespace Parser;

use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\Tools\Setup;

class Factory {

    private static $em;
    private static $setup;
    private static $connection;

    /**
     * @throws Exception|ORMException
     */
    public static function GetEntityManager() : EntityManager {
        if(!static::$em) {
            static::$em = EntityManager::create(static::GetConnection(), static::GetSetup());
        }
        return static::$em;
    }

    public static function GetSetup() {
        if(!static::$setup) {
            static::$setup = Setup::createAnnotationMetadataConfiguration(['entities'], false);
        }
        return static::$setup;
    }

    public static function GetConnection() {
        if(!static::$connection) {
            static::$connection = DriverManager::getConnection([
                'dbname' => getenv('DB_NAME'),
                'user' => getenv('DB_USER'),
                'password' => getenv('DB_PASS'),
                'host' => getenv('DB_HOST'),
                'charset' => 'UTF8',
                'driver' => getenv('DB_DRIVER'),
            ]);
        }
        return static::$connection;
    }
}