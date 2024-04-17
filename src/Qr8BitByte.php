<?php

declare(strict_types=1);

namespace Khalilleo\QrCode;

class Qr8BitByte extends QrDataAbstract
{
    public function __construct($data)
    {
        parent::__construct(QrHelper::QR_MODE_8BIT_BYTE, $data);
    }

    public function write(&$buffer)
    {
        $data = $this->getData();

        for ($i = 0; $i < strlen($data); $i++) {
            $buffer->put(ord($data[$i]), 8);
        }
    }
}
