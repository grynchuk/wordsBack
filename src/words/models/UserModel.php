<?php

declare(strict_types=1);

namespace app\models;

use RuntimeException;
use app\valueObjects\IpAddressValueObject;

class UserModel extends AbstractModel
{
    private ?int $id;
    private IpAddressValueObject $ipAddress;

    public function init(): void
    {
        if (!isset($this->ipAddress)) {
            throw new RuntimeException('invalid user config');
        }
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): UserModel
    {
        $this->id = $id;
        return $this;
    }

    public function getIpAddress(): IpAddressValueObject
    {
        return $this->ipAddress;
    }

    public function setIpAddress(IpAddressValueObject $ipAddress): UserModel
    {
        $this->ipAddress = $ipAddress;
        return $this;
    }

    protected function getMandatoryFields(): array
    {
        return [
            'id',
            'ipAddress',
        ];
    }

    public function getKeyValue(): string
    {
        return (string) $this->getId();
    }
}
