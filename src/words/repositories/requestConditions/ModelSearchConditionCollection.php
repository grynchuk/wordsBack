<?php

declare(strict_types=1);

namespace app\repositories\requestConditions;

use app\repositories\SearchOptionInterface;
use app\repositories\AbstractSearchOptionCollection;
use app\modules\api\base\repositories\BaseSearchOptionCollection;

class ModelSearchConditionCollection extends AbstractSearchOptionCollection
{
    protected function canAttach(SearchOptionInterface $option): bool
    {
        return $option instanceof ConditionInterface;
    }
}
