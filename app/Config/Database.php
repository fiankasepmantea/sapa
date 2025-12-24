<?php

namespace Config;

use CodeIgniter\Database\Config;

/**
 * Database Configuration
 */
class Database extends Config
{
    /**
     * The directory that holds the Migrations
     * and Seeds directories.
     */
    public string $filesPath = APPPATH . 'Database' . DIRECTORY_SEPARATOR;

    /**
     * Lets you choose which connection group to
     * use if no other is specified.
     */
    public string $defaultGroup = 'postgre';

    /**
     * The default database connection for mysql.
     */
    public array $mysql = [
        'DSN'          => '',
        'hostname'     => 'localhost',
        'username'     => 'root',
        'password'     => '',
        'database'     => 'face-apis',
        'DBDriver'     => 'MySQLi',
        'DBPrefix'     => '',
        'pConnect'     => false,
        'DBDebug'      => true,
        'charset'      => 'utf8',
        'DBCollat'     => 'utf8_bin',
        'swapPre'      => '',
        'encrypt'      => false,
        'compress'     => false,
        'strictOn'     => false,
        'failover'     => [],
        'port'         => 3306,
        'numberNative' => false,
    ];

    /**
     * The default database connection for postgre.
     */
    public array $postgre = [
        'DSN'          => '',
        'hostname'     => 'localhost',
        'username'     => 'postgres',
        'password'     => 'root',
        'database'     => 'warehouse_db',
        'DBDriver'     => 'Postgre',
        'DBPrefix'     => '',
        'schema'       => 'public',
        'pConnect'     => false,
        'DBDebug'      => true,
        'charset'      => 'utf8',
        'DBCollat'     => 'utf8_bin',
        'swapPre'      => '',
        'encrypt'      => false,
        'compress'     => false,
        'strictOn'     => false,
        'failover'     => [],
        'numberNative' => false,
        'port'         => 5432,
    ];

    /**
     * This database connection is used when
     * running PHPUnit database tests.
     */
    public array $tests = [
        'DSN'         => '',
        'hostname'    => '127.0.0.1',
        'username'    => '',
        'password'    => '',
        'database'    => ':memory:',
        'DBDriver'    => 'SQLite3',
        'DBPrefix'    => 'db_',  // Needed to ensure we're working correctly with prefixes live. DO NOT REMOVE FOR CI DEVS
        'pConnect'    => false,
        'DBDebug'     => true,
        'charset'     => 'utf8',
        'DBCollat'    => 'utf8_bin',
        'swapPre'     => '',
        'encrypt'     => false,
        'compress'    => false,
        'strictOn'    => false,
        'failover'    => [],
        'port'        => 3306,
        'foreignKeys' => true,
        'busyTimeout' => 1000,
    ];

    public function __construct()
    {
        parent::__construct();

        // Ensure that we always set the database group to 'tests' if
        // we are currently running an automated test suite, so that
        // we don't overwrite live data on accident.
        if (ENVIRONMENT === 'testing') {
            $this->defaultGroup = 'tests';
        }
    }
    // public array $postgre = [
    //     'DSN'          => '',
    //     'hostname'     => 'localhost',
    //     'username'     => 'pencarian',
    //     'password'     => 'KepoBanget1029',
    //     'database'     => 'facematch',
    //     'DBDriver'     => 'Postgre',
    //     'DBPrefix'     => 'facematch.fm2023.',
    //     'pConnect'     => true,
    //     'DBDebug'      => true,
    //     'charset'      => 'utf8',
    //     'DBCollat'     => 'utf8_bin',
    //     'swapPre'      => '',
    //     'encrypt'      => false,
    //     'compress'     => false,
    //     'strictOn'     => false,
    //     'failover'     => [],
    //     'numberNative' => false,
    // ];
}
