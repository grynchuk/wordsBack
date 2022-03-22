<?php

declare(strict_types=1);

namespace app\modelCollectionCreators;

use app\models\ModelInterface;
use app\modelCollections\ModelCollectionInterface;

interface ModelCollectionCreatorInterface
{
    public function getItem(array $rawDataItems): ModelInterface;

    public function createCollectionFromRaw(array $rawData): ModelCollectionInterface;
}
