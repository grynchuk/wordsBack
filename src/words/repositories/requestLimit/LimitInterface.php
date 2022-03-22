<?php

declare(strict_types=1);

namespace app\repositories\requestLimit;

interface LimitInterface
{
    public function getLimit(): int;
    public function getOffset(): int;
}
