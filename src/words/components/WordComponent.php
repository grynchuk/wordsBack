<?php
declare(strict_types=1);

namespace app\components;

use yii\base\BaseObject;
use app\repositories\RepositoryInterface;
use app\modelCollections\WordModelCollection;
use app\modelCollections\ModelCollectionInterface;


class WordComponent extends BaseObject implements EntityCollectionStoreInterface
{
    public function __construct(
        private RepositoryInterface $wordRepository,
        $config = []
    ) {
        parent::__construct($config);
    }

    public function storeCollection(ModelCollectionInterface $collection): void
    {
        $this->wordRepository->saveCollection($collection);
    }

    public function removeCollection(ModelCollectionInterface $collection): void
    {
        $this->wordRepository->removeItems($collection);
    }
}