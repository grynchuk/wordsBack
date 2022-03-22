<?php

declare(strict_types=1);

namespace app\repositories;

use yii\db\Connection;
use app\models\ModelInterface;
use app\repositories\requestSorts\SortCollection;
use app\modelCollections\ModelCollectionInterface;
use app\repositories\requestConditions\ModelSearchConditionCollection;

interface RepositoryInterface
{
    public function getDatabaseComponent(): Connection;

    public function convertFromIterable(iterable $data): ModelCollectionInterface;

    public function createCollectionFromRaw(iterable $data): ModelCollectionInterface;

    public function save(ModelInterface $model): void;

    public function saveCollection(ModelCollectionInterface $collection): void;

    public function getOneByConditions(
        ModelSearchConditionCollection $modelSearchConditionCollection,
        bool $isGrouped = false,
        ?SortCollection $sort = null
    ): ?ModelInterface;

    public function getAllByCondition(
        ?ModelSearchConditionCollection $modelSearchConditionCollection = null,
        ?SortCollection $sort = null
    ): iterable;

    public function removeItems(ModelCollectionInterface $collection): void;

    public function removeItem(ModelInterface $model): void;
}
