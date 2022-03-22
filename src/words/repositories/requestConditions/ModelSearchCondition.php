<?php

declare(strict_types=1);

namespace app\repositories\requestConditions;

use yii\base\BaseObject;
use app\repositories\requestConditions\ConditionTypeEnum;

class ModelSearchCondition extends BaseObject implements ConditionInterface
{
    private string $property;
    private $value;
    private $type = ConditionTypeEnum::EQUAL;

    public function getProperty(): string
    {
        return $this->property;
    }

    public function getValue()
    {
        return $this->value;
    }

    protected function setValue($value): ConditionInterface
    {
        $this->value = $value;
        return $this;
    }

    protected function setProperty(string $property): ConditionInterface
    {
        $this->property = $property;
        return $this;
    }

    public function setType(ConditionTypeEnum $type): ConditionInterface
    {
        $this->type = $type;
        return $this;
    }


    public function getType(): ConditionTypeEnum
    {
        return $this->type;
    }

    public function getCacheKey(): string
    {
        return implode(
            '-',
            [
                $this->property,
                isset($this->type) ? $this->type->getValue() : '',
                serialize($this->value)
            ]
        );
    }
}
