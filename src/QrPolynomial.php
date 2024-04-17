<?php

declare(strict_types=1);

namespace Khalilleo\QrCode;

final class QrPolynomial
{
    private $num;

    public function __construct($num, $shift = 0)
    {
        $offset = 0;

        while ($offset < count($num) && $num[$offset] == 0) {
            $offset++;
        }

        $this->num = QrMath::createNumArray(count($num) - $offset + $shift);

        for ($i = 0; $i < count($num) - $offset; $i++) {
            $this->num[$i] = $num[$i + $offset];
        }
    }

    public function get($index)
    {
        return $this->num[$index];
    }

    public function getLength()
    {
        return count($this->num);
    }

    public function __toString()
    {
        return $this->toString();
    }

    public function toString()
    {
        $buffer = "";

        for ($i = 0; $i < $this->getLength(); $i++) {
            if ($i > 0) {
                $buffer .= ",";
            }
            $buffer .= $this->get($i);
        }

        return $buffer;
    }

    public function toLogString()
    {
        $buffer = "";

        for ($i = 0; $i < $this->getLength(); $i++) {
            if ($i > 0) {
                $buffer .= ",";
            }
            $buffer .= QrMath::glog($this->get($i));
        }

        return $buffer;
    }

    public function multiply(QrPolynomial $e): QrPolynomial
    {
        $num = QrMath::createNumArray($this->getLength() + $e->getLength() - 1);

        for ($i = 0; $i < $this->getLength(); $i++) {
            $vi = QrMath::glog($this->get($i));

            for ($j = 0; $j < $e->getLength(); $j++) {
                $num[$i + $j] ^= QrMath::gexp($vi + QrMath::glog($e->get($j)));
            }
        }

        return new QrPolynomial($num);
    }

    public function mod(QrPolynomial $e)
    {
        if ($this->getLength() - $e->getLength() < 0) {
            return $this;
        }

        $ratio = QrMath::glog($this->get(0)) - QrMath::glog($e->get(0));

        $num = QrMath::createNumArray($this->getLength());

        for ($i = 0; $i < $this->getLength(); $i++) {
            $num[$i] = $this->get($i);
        }

        for ($i = 0; $i < $e->getLength(); $i++) {
            $num[$i] ^= QrMath::gexp(QrMath::glog($e->get($i)) + $ratio);
        }

        $newPolynomial = new QrPolynomial($num);
        
        return $newPolynomial->mod($e);
    }
}
