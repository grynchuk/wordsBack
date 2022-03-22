<?php

declare(strict_types=1);

namespace app\repositories;

interface SearchOptionCollectionInterface
{
    public function attach(SearchOptionInterface $option): void;
    public function walkAttached(callable $handler): void;
}
