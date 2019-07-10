<?php
$transport = [
    'class' => 'Swift_SmtpTransport',
    'host' => 'localhost',
    'username' => 'noreply@angola.scc.com.pt',
    'password' => '7!!gby0o.I4!',
];

if ($_SERVER['HTTP_HOST'] == 'local.savoir-faire.io') {
    
    $db = [
        'class' => 'yii\db\Connection',
        'dsn' => 'mysql:host=localhost;dbname=savoir_faire',
        'username' => 'root',
        'password' => '',
        'charset' => 'utf8',
    ];
}

return [
    'components' => [
        'db' => $db,
        /*'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'viewPath' => '@common/mail',
            'useFileTransport' => false,
            'transport' => $transport,
        ],*/
    ],
];
