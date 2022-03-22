<?php

declare(strict_types=1);

namespace app\modules\api\base\repositories\requestLimit;

use yii\base\BaseObject;
use app\repositories\SearchOptionInterface;

class Limit extends BaseObject implements LimitInterface, SearchOptionInterface
{
    private int $limit;
    private int $offset;

    public function getLimit(): int
    {
        return $this->limit;
    }

    protected function setLimit(int $limit): self
    {
        $this->limit = $limit;
        return $this;
    }

    public function getOffset(): int
    {
        return $this->offset;
    }

    protected function setOffset(int $offset): self
    {
        $this->offset = $offset;
        return $this;
    }

    public function getCacheKey(): string
    {
        return "limit:{$this->limit};offset:{$this->offset}";
    }
}
