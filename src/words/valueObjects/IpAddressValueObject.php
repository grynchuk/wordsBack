<?php

declare(strict_types=1);

namespace app\valueObjects;

use RuntimeException;

class IpAddressValueObject
{
    private string $value;

    public function __construct(string $rawIp) {
        if (filter_var($rawIp, FILTER_VALIDATE_IP)) {
            $this->value = $rawIp;
        } else {
            throw new RuntimeException('Invalid ip adress');
        }
    }

    public function __toString(): string {
        return $this->value;
    }
}