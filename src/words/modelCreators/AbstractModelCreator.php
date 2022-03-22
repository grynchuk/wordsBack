<?php

declare(strict_types=1);

namespace app\modelCreators;

use DateTime;
use DateTimeInterface;
use yii\base\BaseObject;
use app\models\ModelInterface;;

abstract class AbstractModelCreator extends BaseObject implements ModelCreatorInterface
{

    public function createFromRaw(array $rawData = []): ModelInterface
    {
        return $this->createInstance(
            $this->prepareRawData($rawData)
        );
    }

    protected function prepareRawValues(array $rawData): array
    {
        $result = [];

        foreach ($this->getPropertiesNames() as $property) {
            $result[$property] = $rawData[$property] ?? null;
        }

        return $result;
    }


    abstract protected function getPropertiesNames(): array;


    abstract protected function createInstance(array $modelRawData): ModelInterface;


    abstract public function prepareRawData(array $rawData): array;


    public function getDefaultInstance(): ModelInterface
    {
        return $this->createInstance([]);
    }
    
    protected function prepareRawDateTime($value, string $format = 'Y-m-d H:i:s'): ?DateTimeInterface
    {
        if ($value === null) {
            return null;
        }
        if ($value instanceof DateTimeInterface) {
            return $value;
        }
        return DateTime::createFromFormat($format, $value);
    }
    
    protected function prepareRawIntegerValue($value, ?int $default = null): ?int
    {
        return $value !== null ? (int)$value : $default;
    }
}
