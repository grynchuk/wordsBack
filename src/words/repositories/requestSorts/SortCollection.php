<?php

declare(strict_types=1);

namespace app\repositories\requestSorts;

use app\repositories\SearchOptionInterface;
use app\repositories\AbstractSearchOptionCollection;

class SortCollection extends AbstractSearchOptionCollection
{
    public function canAttach(SearchOptionInterface $option): bool
    {
        return $option instanceof SortInterface;
    }
}
