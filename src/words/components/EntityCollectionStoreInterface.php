<?php

declare(strict_types=1);

namespace app\components;

use app\modelCollections\ModelCollectionInterface;

interface EntityCollectionStoreInterface
{
    public function storeCollection(ModelCollectionInterface $collection): void;

    public function removeCollection(ModelCollectionInterface $collection): void;
}