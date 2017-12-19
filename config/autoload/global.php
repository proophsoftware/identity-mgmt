<?php
declare(strict_types = 1);

namespace App\Config;

use App\Api\MsgDesc;
use App\Infrastructure\Identity\IdentityDescription;
use App\Infrastructure\User\UserDescription;
use App\Infrastructure\User\UserTypeSchemaDescription;

return [
    'environment' => getenv('PROOPH_ENV')?: 'prod',
    'base_url' => getenv('BASE_URL'),
    'event_machine' => [
        'descriptions' => [
            MsgDesc::class,
            UserTypeSchemaDescription::class,
            UserDescription::class,
            IdentityDescription::class,
        ]
    ],
    'pdo' => [
        'dsn' => getenv('PDO_DSN'),
        'user' => getenv('PDO_USER'),
        'pwd' => getenv('PDO_PWD'),
    ],
    'mongo' => [
        'server' => getenv('MONGO_SERVER'),
        'db' => getenv('MONGO_DB_NAME'),
    ],
    'rabbit' => [
        'connection' => [
            'host' => getenv('RABBIT_HOST')?: 'rabbit',
            'port' => (int)getenv('RABBIT_PORT')?: 5672,
            'login' => getenv('RABBIT_USER')?: 'event-machine',
            'password' => getenv('RABBIT_PWD')?: 'event-machine',
            'vhost' => getenv('RABBIT_VHOST')?: '/event-machine',
            'persistent' => (bool)getenv('RABBIT_PERSISTENT')?: false,
            'read_timeout' => (int)getenv('RABBIT_READ_TIMEOUT')?: 1, //sec, float allowed
            'write_timeout' => (int)getenv('RABBIT_WRITE_TIMEOUT')?: 1, //sec, float allowed,
            'heartbeat' => (int)getenv('RABBIT_HEARTBEAT')?: 0,
            'verify' => false
        ],
        'ui_exchange' => getenv('RABBIT_UI_EXCHANGE')?: 'ui-exchange',
    ],
    'mail' => [
        'from' => getenv('MAIL_FROM'),
        'from_name' => getenv('MAIL_FROM_NAME'),
        'delivery_address' => getenv('MAIL_DELIVERY_ADDRESS')?: null,
        'smtp' => [
            'host' => getenv('MAIL_SMTP_HOST'),
            'port' => (int)getenv('MAIL_SMTP_PORT'),
            'username' => getenv('MAIL_SMTP_USERNAME'),
            'password' => getenv('MAIL_SMTP_PWD'),
            'ssl' => getenv('MAIL_SMTP_SSL')
        ]
    ]
];