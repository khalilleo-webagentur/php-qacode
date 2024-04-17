<?php

use Khalilleo\QrCode\QrCode;
use Khalilleo\QrCode\QrHelper;

require '../vendor/autoload.php';

$qr = QrCode::getMinimumQRCode("www.khalilleo.com", QrHelper::QR_ERROR_CORRECT_LEVEL_L);

$im = $qr->createImage(2, 4);

header("Content-type: image/gif");

imagegif($im);

imagedestroy($im);
