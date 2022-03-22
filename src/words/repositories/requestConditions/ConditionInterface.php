<?php

declare(strict_types=1);

namespace app\repositories\requestConditions;

use app\repositories\SearchOptionInterface;


interface ConditionInterface extends SearchOptionInterface
{
    /**
     * @return string
     */
    public function getProperty(): string;

    /**
     * @return mixed
     */
    public function getValue();

    public function getType(): ?ConditionTypeEnum;
}
