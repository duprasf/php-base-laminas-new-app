<?php
return [
    'service_manager' => [
        'factories' => [
            'pdo' => function($sm) {
                return new \PDO(
                    'mysql:host=localhost;dbname=dbName;',
                    'username',
                    'password',
                    array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\'')
                );
            },
        ],
    ],
];
