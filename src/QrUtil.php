<?php

declare(strict_types=1);

namespace Khalilleo\QrCode;

final class QrUtil
{
    public static function getPatternPosition($typeNumber)
    {
        return QrHelper::QR_PATTERN_POSITION_TABLE[$typeNumber - 1];
    }

    public static function getMaxLength($typeNumber, $mode, $errorCorrectLevel)
    {
        $t = $typeNumber - 1;
        $e = 0;
        $m = 0;

        switch ($errorCorrectLevel) {
            case QrHelper::QR_ERROR_CORRECT_LEVEL_L:
                $e = 0;
                break;
            case QrHelper::QR_ERROR_CORRECT_LEVEL_M:
                $e = 1;
                break;
            case QrHelper::QR_ERROR_CORRECT_LEVEL_Q:
                $e = 2;
                break;
            case QrHelper::QR_ERROR_CORRECT_LEVEL_H:
                $e = 3;
                break;
            default:
                trigger_error("e:$errorCorrectLevel", E_USER_ERROR);
        }

        switch ($mode) {
            case QrHelper::QR_MODE_NUMBER:
                $m = 0;
                break;
            case QrHelper::QR_MODE_ALPHA_NUM:
                $m = 1;
                break;
            case QrHelper::QR_MODE_8BIT_BYTE:
                $m = 2;
                break;
            case QrHelper::QR_MODE_KANJI:
                $m = 3;
                break;
            default:
                trigger_error("m:$mode", E_USER_ERROR);
        }

        return QrHelper::QR_MAX_LENGTH[$t][$e][$m];
    }

    public static function getErrorCorrectPolynomial($errorCorrectLength)
    {
        $a = new QrPolynomial([1]);

        for ($i = 0; $i < $errorCorrectLength; $i++) {
            $a = $a->multiply(new QrPolynomial(array(1, QrMath::gexp($i))));
        }

        return $a;
    }

    public static function getMask($maskPattern, $i, $j)
    {
        switch ($maskPattern) {

            case QrHelper::QR_MASK_PATTERN000:
                return ($i + $j) % 2 == 0;
            case QrHelper::QR_MASK_PATTERN001:
                return $i % 2 == 0;
            case QrHelper::QR_MASK_PATTERN010:
                return $j % 3 == 0;
            case QrHelper::QR_MASK_PATTERN011:
                return ($i + $j) % 3 == 0;
            case QrHelper::QR_MASK_PATTERN100:
                return (floor($i / 2) + floor($j / 3)) % 2 == 0;
            case QrHelper::QR_MASK_PATTERN101:
                return ($i * $j) % 2 + ($i * $j) % 3 == 0;
            case QrHelper::QR_MASK_PATTERN110:
                return (($i * $j) % 2 + ($i * $j) % 3) % 2 == 0;
            case QrHelper::QR_MASK_PATTERN111:
                return (($i * $j) % 3 + ($i + $j) % 2) % 2 == 0;

            default:
                trigger_error("mask:$maskPattern", E_USER_ERROR);
        }
    }

    public static function getLostPoint(QrCode $qrCode): float|int
    {
        $moduleCount = $qrCode->getModuleCount();

        $lostPoint = 0;

        // LEVEL1

        for ($row = 0; $row < $moduleCount; $row++) {

            for ($col = 0; $col < $moduleCount; $col++) {

                $sameCount = 0;
                $dark = $qrCode->isDark($row, $col);

                for ($r = -1; $r <= 1; $r++) {

                    if ($row + $r < 0 || $moduleCount <= $row + $r) {
                        continue;
                    }

                    for ($c = -1; $c <= 1; $c++) {

                        if (($col + $c < 0 || $moduleCount <= $col + $c) || ($r == 0 && $c == 0)) {
                            continue;
                        }

                        if ($dark == $qrCode->isDark($row + $r, $col + $c)) {
                            $sameCount++;
                        }
                    }
                }

                if ($sameCount > 5) {
                    $lostPoint += (3 + $sameCount - 5);
                }
            }
        }

        // LEVEL2

        for ($row = 0; $row < $moduleCount - 1; $row++) {
            for ($col = 0; $col < $moduleCount - 1; $col++) {
                $count = 0;
                if ($qrCode->isDark($row,     $col)) $count++;
                if ($qrCode->isDark($row + 1, $col)) $count++;
                if ($qrCode->isDark($row,     $col + 1)) $count++;
                if ($qrCode->isDark($row + 1, $col + 1)) $count++;
                if ($count == 0 || $count == 4) {
                    $lostPoint += 3;
                }
            }
        }

        // LEVEL3

        for ($row = 0; $row < $moduleCount; $row++) {
            for ($col = 0; $col < $moduleCount - 6; $col++) {
                if (
                    $qrCode->isDark($row, $col)
                    && !$qrCode->isDark($row, $col + 1)
                    &&  $qrCode->isDark($row, $col + 2)
                    &&  $qrCode->isDark($row, $col + 3)
                    &&  $qrCode->isDark($row, $col + 4)
                    && !$qrCode->isDark($row, $col + 5)
                    &&  $qrCode->isDark($row, $col + 6)
                ) {
                    $lostPoint += 40;
                }
            }
        }

        for ($col = 0; $col < $moduleCount; $col++) {
            for ($row = 0; $row < $moduleCount - 6; $row++) {
                if (
                    $qrCode->isDark($row, $col)
                    && !$qrCode->isDark($row + 1, $col)
                    &&  $qrCode->isDark($row + 2, $col)
                    &&  $qrCode->isDark($row + 3, $col)
                    &&  $qrCode->isDark($row + 4, $col)
                    && !$qrCode->isDark($row + 5, $col)
                    &&  $qrCode->isDark($row + 6, $col)
                ) {
                    $lostPoint += 40;
                }
            }
        }

        // LEVEL4

        $darkCount = 0;

        for ($col = 0; $col < $moduleCount; $col++) {
            for ($row = 0; $row < $moduleCount; $row++) {
                if ($qrCode->isDark($row, $col)) {
                    $darkCount++;
                }
            }
        }

        $ratio = abs(100 * $darkCount / $moduleCount / $moduleCount - 50) / 5;
        $lostPoint += $ratio * 10;

        return $lostPoint;
    }

    public static function getMode($s)
    {
        if (QrUtil::isAlphaNum($s)) {

            if (QrUtil::isNumber($s)) {
                return QrHelper::QR_MODE_NUMBER;
            }

            return QrHelper::QR_MODE_ALPHA_NUM;

        } else if (QrUtil::isKanji($s)) {
            return QrHelper::QR_MODE_KANJI;
        }

        return QrHelper::QR_MODE_8BIT_BYTE;
    }

    public static function isNumber($s)
    {
        for ($i = 0; $i < strlen($s); $i++) {
            $c = ord($s[$i]);
            if (!(QrUtil::toCharCode('0') <= $c && $c <= QrUtil::toCharCode('9'))) {
                return false;
            }
        }

        return true;
    }

    public static function isAlphaNum($s)
    {
        for ($i = 0; $i < strlen($s); $i++) {
            $c = ord($s[$i]);
            if (
                !(QrUtil::toCharCode('0') <= $c && $c <= QrUtil::toCharCode('9'))
                && !(QrUtil::toCharCode('A') <= $c && $c <= QrUtil::toCharCode('Z'))
                && strpos(" $%*+-./:", $s[$i]) === false
            ) {
                return false;
            }
        }

        return true;
    }

    public static function isKanji($s)
    {
        $data = $s;

        $i = 0;

        while ($i + 1 < strlen($data)) {

            $c = ((0xff & ord($data[$i])) << 8) | (0xff & ord($data[$i + 1]));

            if (!(0x8140 <= $c && $c <= 0x9FFC) && !(0xE040 <= $c && $c <= 0xEBBF)) {
                return false;
            }

            $i += 2;
        }

        return true === $i < strlen($data);
    }

    public static function toCharCode($s)
    {
        return ord($s[0]);
    }

    public static function getBCHTypeInfo($data)
    {
        $d = $data << 10;

        while (QrUtil::getBCHDigit($d) - QrUtil::getBCHDigit(QrHelper::QR_G15) >= 0) {
            $d ^= (QrHelper::QR_G15 << (QrUtil::getBCHDigit($d) - QrUtil::getBCHDigit(QrHelper::QR_G15)));
        }

        return (($data << 10) | $d) ^ QrHelper::QR_G15_MASK;
    }

    public static function getBCHTypeNumber($data)
    {
        $d = $data << 12;

        while (QrUtil::getBCHDigit($d) - QrUtil::getBCHDigit(QrHelper::QR_G18) >= 0) {
            $d ^= (QrHelper::QR_G18 << (QrUtil::getBCHDigit($d) - QrUtil::getBCHDigit(QrHelper::QR_G18)));
        }

        return ($data << 12) | $d;
    }

    public static function getBCHDigit($data)
    {
        $digit = 0;

        while ($data != 0) {
            $digit++;
            $data >>= 1;
        }

        return $digit;
    }
}
