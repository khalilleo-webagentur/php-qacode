<?php

declare(strict_types=1);

namespace Khalilleo\QrCode;

final class QrRSBlock
{
    private $totalCount;

    private $dataCount;

    public function __construct($totalCount, $dataCount)
    {
        $this->totalCount = $totalCount;
        $this->dataCount  = $dataCount;
    }

    public function getDataCount()
    {
        return $this->dataCount;
    }

    public function getTotalCount()
    {
        return $this->totalCount;
    }

    static public function getRSBlocks($typeNumber, $errorCorrectLevel)
    {

        $rsBlock = QRRSBlock::getRsBlockTable($typeNumber, $errorCorrectLevel);
        $length = count($rsBlock) / 3;

        $list = [];

        for ($i = 0; $i < $length; $i++) {

            $count = $rsBlock[$i * 3 + 0];
            $totalCount = $rsBlock[$i * 3 + 1];
            $dataCount  = $rsBlock[$i * 3 + 2];

            for ($j = 0; $j < $count; $j++) {
                $list[] = new QRRSBlock($totalCount, $dataCount);
            }
        }

        return $list;
    }

    static public function getRsBlockTable($typeNumber, $errorCorrectLevel)
    {
        switch ($errorCorrectLevel) {
            case QrHelper::QR_ERROR_CORRECT_LEVEL_L:
                return QrHelper::QR_RS_BLOCK_TABLE[($typeNumber - 1) * 4 + 0];
            case QrHelper::QR_ERROR_CORRECT_LEVEL_M:
                return QrHelper::QR_RS_BLOCK_TABLE[($typeNumber - 1) * 4 + 1];
            case QrHelper::QR_ERROR_CORRECT_LEVEL_Q:
                return QrHelper::QR_RS_BLOCK_TABLE[($typeNumber - 1) * 4 + 2];
            case QrHelper::QR_ERROR_CORRECT_LEVEL_H:
                return QrHelper::QR_RS_BLOCK_TABLE[($typeNumber - 1) * 4 + 3];
            default:
                trigger_error("tn:$typeNumber/ecl:$errorCorrectLevel", E_USER_ERROR);
        }
    }
}
