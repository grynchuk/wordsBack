<?php

declare(strict_types=1);

namespace app\modelCreators;

use app\models\UserModel;
use app\models\ModelInterface;
use app\valueObjects\IpAddressValueObject;

class UserModelCreator extends AbstractModelCreator
{

    protected function getPropertiesNames(): array
    {
        return [
            'id',
            'ipAddress'
        ];
    }

    protected function createInstance(array $modelRawData): ModelInterface
    {
        return new UserModel($modelRawData);
    }

    public function prepareRawData(array $rawData): array
    {
        $rawData = $this->prepareRawValues($rawData);
        $rawData['ipAddress'] = $rawData['ipAddress'] instanceof IpAddressValueObject
            ? $rawData['ipAddress']
            : new IpAddressValueObject($rawData['ipAddress']);

        return $rawData;
    }

}