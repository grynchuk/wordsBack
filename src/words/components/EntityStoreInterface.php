<?php

declare(strict_types=1);

namespace app\components;

use app\models\ModelInterface;

interface EntityStoreInterface
{
    public function store(ModelInterface $model): void;

    public function createFromRaw(array $data): ModelInterface;

    public function remove(ModelInterface $model): void;

}