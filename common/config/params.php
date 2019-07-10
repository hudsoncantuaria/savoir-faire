<?php

if ($_SERVER['HTTP_HOST'] == 'bescv2.camdocs.be') {
    $params = [
        'adminEmail' => 'camdocs@camdocs.be',
        'smtpEmail' => 'noreply@bescv2.camdocs.be',
        'certificateEmail' => 'camdocs@camdocs.be',
        'registerEmail' => 'camdocs@camdocs.be',
        'contactEmail' => 'camdocs@camdocs.be',
        'user.passwordResetTokenExpire' => 3600,
        'languages' => ['en' => 'EN', 'PT' => 'PT'],
    ];
} else {
    $params = [
        'adminEmail' => 'hudson@webcomum.com',
        'smtpEmail' => 'hudson@webcomum.com',
        'certificateEmail' => 'hudson@webcomum.com',
        'registerEmail' => 'hudson@webcomum.com',
        'contactEmail' => 'hudson@webcomum.com',
        'user.passwordResetTokenExpire' => 3600,
        'languages' => ['en' => 'EN', 'pt' => 'PT'],
    ];
}
return $params;
