<?php

declare(strict_types=1);

namespace app\modelCollectionCreators;

use yii\base\BaseObject;
use app\models\ModelInterface;
use app\modelCollections\ModelCollectionInterface;

abstract class AbstractModelCollectionCreator extends BaseObject implements ModelCollectionCreatorInterface
{

    public function createCollectionFromRaw(array $rawData): ModelCollectionInterface
    {
        $collection = $this->getCollection();
        foreach ($rawData as $rawDataItem) {
            if (is_object($rawDataItem)) {
                $collection->attach($rawDataItem);
                continue;
            }

            $collection->attach(
                $this->getItem($rawDataItem)
            );
        }

        return $collection;
    }

    abstract public function getItem(array $rawDataItems): ModelInterface;

    abstract protected function getCollection(): ModelCollectionInterface;
}
