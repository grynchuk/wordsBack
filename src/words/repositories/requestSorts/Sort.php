<?php

declare(strict_types=1);

namespace app\repositories\requestSorts;

use yii\base\BaseObject;

class Sort extends BaseObject implements SortInterface
{
    private string $property;
    private SortTypeEnum $order;

    public function getProperty(): string
    {
        return $this->property;
    }

    protected function setProperty(string $property): self
    {
        $this->property = $property;
        return $this;
    }

    public function getOrder(): SortTypeEnum
    {
        return $this->order;
    }

    protected function setOrder(SortTypeEnum $order): self
    {
        $this->order = $order;
        return $this;
    }

    public function getCacheKey(): string
    {
        return "property:{$this->property};order:{$this->order->getValue()}";
    }
}
