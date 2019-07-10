<?php
$transport = [
    'class' => 'Swift_SmtpTransport',
    'host' => 'localhost',
    'username' => 'noreply@angola.scc.com.pt',
    'password' => '7!!gby0o.I4!',
];

if ($_SERVER['HTTP_HOST'] == 'angola.scc.com.pt') {
    /*$transport = [
        'class' => 'Swift_SmtpTransport',
        'host' => 'mail.webcomum.com',
        'username' => 'luis@webcomum.com',
        'password' => 'Lp]o9*KG*5pV',
    ];*/
    
    $db = [
        'class' => 'yii\db\Connection',
        'dsn' => 'mysql:host=localhost;dbname=angolasc_db',
        'username' => 'angolasc_usr',
        'password' => 'yrV}kKpsC7_R',
        'charset' => 'utf8',
    ];
} else {

    $db = [
        'class' => 'yii\db\Connection',
        'dsn' => 'mysql:host=localhost;dbname=angdocs',
        'username' => 'root',
        'password' => '',
        'charset' => 'utf8',
    ];
}

return [
    'components' => [
        'db' => $db,
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'viewPath' => '@common/mail',
            // send all mails to a file by default. You have to set
            // 'useFileTransport' to false and configure a transport
            // for the mailer to send real emails.
            'useFileTransport' => false,
            'transport' => $transport,
        ],
    ],
];
