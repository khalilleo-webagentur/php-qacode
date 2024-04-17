<?php

require '../vendor/autoload.php';

use Khalilleo\QrCode\QrCode;
use Khalilleo\QrCode\QrHelper;

$qr = QrCode::getMinimumQRCode("https://www.khalilleo.com", QrHelper::QR_ERROR_CORRECT_LEVEL_L);

header("Content-type: text/xml");

print("<qrcode>");

for ($r = 0; $r < $qr->getModuleCount(); $r++) {

    print("<line>");

    for ($c = 0; $c < $qr->getModuleCount(); $c++) {
        print($qr->isDark($r, $c) ? "1" : "0");
    }

    print("</line>");
}

print("</qrcode>");
