<?php

declare(strict_types=1);

namespace app\repositories;

use LogicException;
use yii\base\BaseObject;

abstract class AbstractSearchOptionCollection extends BaseObject implements SearchOptionCollectionInterface
{
    private array $options = [];

    abstract protected function canAttach(SearchOptionInterface $option): bool;

    public function attach(SearchOptionInterface $option): void
    {
        if ($this->canAttach($option)) {
            $this->options[] = $option;
        } else {
            throw new LogicException('invalid Attachment');
        }
    }

    public function walkAttached(callable $handler): void
    {
        foreach ($this->options as $option) {
            $handler(clone $option);
        }
    }

    public function getCacheKey(): string
    {
        $key = '';
        $this->walkAttached(function (SearchOptionInterface $option) use (&$key) {
            $key .= $option->getCacheKey();
        });
        return $key;
    }
}
