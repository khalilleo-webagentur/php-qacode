<?php

declare(strict_types=1);

namespace Khalilleo\QrCode;

abstract class QrDataAbstract
{
    private mixed $mode;
    private mixed $data;

    public function __construct($mode, $data)
    {
        $this->mode = $mode;
        $this->data = $data;
    }

    public function getMode()
    {
        return $this->mode;
    }

    public function getData()
    {
        return $this->data;
    }

    public function getLength(): int
    {
        return strlen($this->getData());
    }

    abstract protected function write(QrBitBuffer &$buffer);

    public function getLengthInBits($type)
    {
        if (1 <= $type && $type < 10) {

            // 1 - 9

            switch ($this->mode) {
                case QrHelper::QR_MODE_NUMBER:
                    return 10;
                case QrHelper::QR_MODE_ALPHA_NUM:
                    return 9;
                case QrHelper::QR_MODE_8BIT_BYTE:
                    return 8;
                case QrHelper::QR_MODE_KANJI:
                    return 8;
                default:
                    trigger_error("mode:$this->mode", E_USER_ERROR);
            }
        } else if ($type < 27) {

            // 10 - 26

            switch ($this->mode) {
                case QrHelper::QR_MODE_NUMBER:
                    return 12;
                case QrHelper::QR_MODE_ALPHA_NUM:
                    return 11;
                case QrHelper::QR_MODE_8BIT_BYTE:
                    return 16;
                case QrHelper::QR_MODE_KANJI:
                    return 10;
                default:
                    trigger_error("mode:$this->mode", E_USER_ERROR);
            }
        } else if ($type < 41) {

            // 27 - 40

            switch ($this->mode) {
                case QrHelper::QR_MODE_NUMBER:
                    return 14;
                case QrHelper::QR_MODE_ALPHA_NUM:
                    return 13;
                case QrHelper::QR_MODE_8BIT_BYTE:
                    return 16;
                case QrHelper::QR_MODE_KANJI:
                    return 12;
                default:
                    trigger_error("mode:$this->mode", E_USER_ERROR);
            }
        } else {
            trigger_error("mode:$this->mode", E_USER_ERROR);
        }
    }
}
