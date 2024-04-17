<?php

require '../vendor/autoload.php';

use Khalilleo\QrCode\QrCode;
use Khalilleo\QrCode\QrHelper;

$qr = new QRCode();

// QrHelper::QR_ERROR_CORRECT_LEVEL_L : 7%
// QrHelper::QR_ERROR_CORRECT_LEVEL_M : 15%
// QrHelper::QR_ERROR_CORRECT_LEVEL_Q : 25%
// QrHelper::QR_ERROR_CORRECT_LEVEL_H : 30%

$qr->setErrorCorrectLevel(QrHelper::QR_ERROR_CORRECT_LEVEL_L);
$qr->setTypeNumber(4);

$anyString = "https://www.khalilleo.com";

$qr->addData($anyString);
$qr->make();
$qr->printHTML();

echo "<br><br>";

$qr = QrCode::getMinimumQRCode($anyString, QrHelper::QR_ERROR_CORRECT_LEVEL_L);
$qr->printHTML();
