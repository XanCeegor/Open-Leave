<?php

return [
    'logging' => env('LDAP_LOGGING', false),
    'connections' => [
        'default' => [
            'auto_connect' => true,
            'connection' => Adldap\Connections\Ldap::class,
            'settings' => [
                'schema' => Adldap\Schemas\ActiveDirectory::class,
                'account_prefix' => env('LDAP_ACCOUNT_PREFIX', ''),
                'account_suffix' => env('LDAP_ACCOUNT_SUFFIX', ''),
                'hosts' => explode(' ', env('LDAP_HOSTS', 'corp-dc1.corp.acme.org corp-dc2.corp.acme.org')),
                'port' => env('LDAP_PORT', '389'),
                'timeout' => env('LDAP_TIMEOUT', 5),
                'base_dn' => env('LDAP_BASEDN'),
                'username' => env('LDAP_USERNAME'),     //set to an AD admin account / Used for Importing from AD using CLI
                'password' => env('LDAP_PASSWORD'),
                'follow_referrals' => env('LDAP_FOLLOW_REFERRALS', false),
                'use_ssl' => env('LDAP_USE_SSL', false),
                'use_tls' => env('LDAP_USE_TLS', false),
            ],
        ],
    ],
];
