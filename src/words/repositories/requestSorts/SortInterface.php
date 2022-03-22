<?php

declare(strict_types=1);

namespace app\repositories\requestSorts;

use app\repositories\SearchOptionInterface;

interface SortInterface extends SearchOptionInterface
{
    public function getProperty(): string;
    public function getOrder(): SortTypeEnum;
}
