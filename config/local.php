<?php
// Uncomment to enable debug mode. Recommended for development.
define('YII_DEBUG', true);

// Uncomment to enable dev environment. Recommended for development
define('YII_ENV', 'dev');
function d($var) {
    var_dump($var);
}
function dd($var) {
    die(var_dump($var));
}

return [
    'components' => [
        'db' => [
            'dsn' => 'mysql:host=localhost;dbname=mydb',
            'username' => 'myuser',
            'password' => 'mysecret',
        ],
        'request' => [
            'cookieValidationKey' => 'PdXWDAfV5-gPJJWRar5sEN71DN0JcDRV',
        ],
    ],
];
