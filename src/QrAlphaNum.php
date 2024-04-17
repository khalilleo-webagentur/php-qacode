<?php

declare(strict_types=1);

namespace Khalilleo\QrCode;

final class QrAlphaNum extends QrDataAbstract
{
    function __construct($data)
    {
        parent::__construct(QrHelper::QR_MODE_ALPHA_NUM, $data);
    }

    function write(&$buffer)
    {
        $i = 0;
        $c = $this->getData();

        while ($i + 1 < strlen($c)) {
            $buffer->put(QrAlphaNum::getCode(ord($c[$i])) * 45
                + QrAlphaNum::getCode(ord($c[$i + 1])), 11);
            $i += 2;
        }

        if ($i < strlen($c)) {
            $buffer->put(QrAlphaNum::getCode(ord($c[$i])), 6);
        }
    }

    static function getCode($c)
    {
        if (QrUtil::toCharCode('0') <= $c && $c <= QrUtil::toCharCode('9')) {
            return $c - QrUtil::toCharCode('0');
        } else if (QrUtil::toCharCode('A') <= $c && $c <= QrUtil::toCharCode('Z')) {
            return $c - QrUtil::toCharCode('A') + 10;
        }

        switch ($c) {
            case QrUtil::toCharCode(' '):
                return 36;
            case QrUtil::toCharCode('$'):
                return 37;
            case QrUtil::toCharCode('%'):
                return 38;
            case QrUtil::toCharCode('*'):
                return 39;
            case QrUtil::toCharCode('+'):
                return 40;
            case QrUtil::toCharCode('-'):
                return 41;
            case QrUtil::toCharCode('.'):
                return 42;
            case QrUtil::toCharCode('/'):
                return 43;
            case QrUtil::toCharCode(':'):
                return 44;
            default:
                trigger_error("illegal char : $c", E_USER_ERROR);
        }
    }
}
