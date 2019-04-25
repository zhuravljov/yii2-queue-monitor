<?php
switch (getenv('DB_TYPE')) {
    case 'sqlite':
        return [
            'class' => \yii\db\Connection::class,
            'dsn' => 'sqlite:@runtime/yii2_queue_test.db',
            'enableSchemaCache' => true,
        ];
    case 'pgsql':
        return [
            'class' => \yii\db\Connection::class,
            'dsn' => sprintf('pgsql:host=%s;dbname=%s', getenv('POSTGRES_HOST'), getenv('POSTGRES_DB')),
            'username' => getenv('POSTGRES_USER'),
            'password' => getenv('POSTGRES_PASSWORD'),
            'charset' => 'utf8',
            'enableSchemaCache' => true,
        ];
    case 'mysql':
    default:
        return [
            'class' => \yii\db\Connection::class,
            'dsn' => sprintf('mysql:host=%s;dbname=%s', getenv('MYSQL_HOST'), getenv('MYSQL_DATABASE')),
            'username' => getenv('MYSQL_USER'),
            'password' => getenv('MYSQL_PASSWORD'),
            'charset' => 'utf8',
            'enableSchemaCache' => true,
            'attributes' => [
                PDO::MYSQL_ATTR_INIT_COMMAND => 'SET sql_mode = "STRICT_ALL_TABLES"',
            ],
        ];
}
