<?php

declare(strict_types=1);

namespace app\modelCollections;

use app\models\ModelInterface;

interface ModelCollectionInterface
{
    public function attach(ModelInterface $model): void;

    public function serializeFields(array $fields, bool $recursive = true): array;

    public function isEmpty(): bool;

    public function convertFromIterable(iterable $iterable): ModelCollectionInterface;

    public function getKeys(): array;

    public function getModels(): array;

    public function isValid(): bool;

    public function walkAttached(callable $handler): ModelCollectionInterface;

    public function filter(callable $filter): ModelCollectionInterface;

    public function diffTo(AbstractModelCollection $collection): ModelCollectionInterface;
}
