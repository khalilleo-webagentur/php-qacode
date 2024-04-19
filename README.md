## PHP-QR-Code

PHP library for QR Code Generator.

### Requirements

* PHP `^8.1`

### Installation

`composer require khalilleo-webagentur/php-qrcode`

### Usage

```php

// sample HTML

<?php

require 'vendor/autoload.php';

use Khalilleo\QrCode\QrCode;
use Khalilleo\QrCode\QrHelper;

$qr = new QRCode();

// QrHelper::QR_ERROR_CORRECT_LEVEL_L  (7%)
// QrHelper::QR_ERROR_CORRECT_LEVEL_M : (15%)
// QrHelper::QR_ERROR_CORRECT_LEVEL_Q : (25%)
// QrHelper::QR_ERROR_CORRECT_LEVEL_H : (30%)

$qr->setErrorCorrectLevel(QrHelper::QR_ERROR_CORRECT_LEVEL_L);
$qr->setTypeNumber(4);

$anyString = "https://www.khalilleo.com";

$qr->addData($anyString);
$qr->make();
$qr->printHTML();

echo "<br><br>";

$qr = QrCode::getMinimumQRCode($anyString, QrHelper::QR_ERROR_CORRECT_LEVEL_L);
$qr->printHTML();
```

```php

// sample image

<?php

use Khalilleo\QrCode\QrCode;
use Khalilleo\QrCode\QrHelper;

require 'vendor/autoload.php';

$qr = QrCode::getMinimumQRCode("https://www.khalilleo.com", QrHelper::QR_ERROR_CORRECT_LEVEL_L);

$im = $qr->createImage(2, 4);

header("Content-type: image/gif");
imagegif($im);

imagedestroy($im);

```

```php
<?php

// sample XML

require 'vendor/autoload.php';

use Khalilleo\QrCode\QrCode;
use Khalilleo\QrCode\QrHelper;

$qr = QrCode::getMinimumQRCode("https://www.khalilleo.com", QrHelper::QR_ERROR_CORRECT_LEVEL_L);

header("Content-type: text/xml");

print("<qrcode>");

for ($r = 0; $r < $qr->getModuleCount(); $r++) {
   
   print("<line>");
    
    for ($c = 0; $c < $qr->getModuleCount(); $c++) {
        print($qr->isDark($r, $c)? "1" : "0");
    }

    print("</line>");
}

print("</qrcode>");

```

### Credit

* [Kazuhiko Arase](http://www.d-project.com/)


### Copyright

This project is licensed under the [MIT](http://www.opensource.org/licenses/mit-license.php) License.
