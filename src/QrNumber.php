<?php

declare(strict_types=1);

namespace Khalilleo\QrCode;

final class QrNumber extends QrDataAbstract
{
    public function __construct($data)
    {
        parent::__construct(QrHelper::QR_MODE_NUMBER, $data);
    }

    public function write(&$buffer)
    {
        $data = $this->getData();

        $i = 0;

        while ($i + 2 < strlen($data)) {
            $num = QrNumber::parseInt(substr($data, $i, 3));
            $buffer->put($num, 10);
            $i += 3;
        }

        if ($i < strlen($data)) {

            if (strlen($data) - $i == 1) {
                $num = QrNumber::parseInt(substr($data, $i, $i + 1));
                $buffer->put($num, 4);
            } else if (strlen($data) - $i == 2) {
                $num = QrNumber::parseInt(substr($data, $i, $i + 2));
                $buffer->put($num, 7);
            }
        }
    }

    static public function parseInt($s)
    {
        $num = 0;

        for ($i = 0; $i < strlen($s); $i++) {
            $num = $num * 10 + QrNumber::parseIntAt(ord($s[$i]));
        }

        return $num;
    }

    static public function parseIntAt($c)
    {
        if (QrUtil::toCharCode('0') <= $c && $c <= QrUtil::toCharCode('9')) {
            return $c - QrUtil::toCharCode('0');
        }

        trigger_error("illegal char : $c", E_USER_ERROR);
    }
}