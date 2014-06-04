<?php
/**
 * User: DnAp
 * Date: 02.04.14
 * Time: 15:19
 */

namespace DDelivery\DataBase;


use DDelivery\DDeliveryException;

class SQLite {
    public static $dbUri = '';
    private static $pdo;

    /**
     * Возвращает единственный экземпляр PDO SQLite
     * @return \PDO
     * @throws \DDelivery\DDeliveryException
     */
    public static function getPDO() {
        if(!self::$dbUri)
            throw new DDeliveryException('SQLite::dbUri is empty');
        if ( empty(self::$pdo) ) {

            self::$pdo = new \PDO('mysql:host=localhost;dbname=c1insales', 'c1dba', 'OH2AgbFiU',
                array(\PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
            //self::$pdo->exec('PRAGMA journal_mode=WAL;');
        }

        return self::$pdo;
    }
} 