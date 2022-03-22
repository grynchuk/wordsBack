<?php
declare(strict_types=1);

namespace app\modelCollectionCreators;

use app\models\ModelInterface;
use app\modelCreators\ModelCreatorInterface;
use app\modelCollections\WordModelCollection;
use app\modelCollections\ModelCollectionInterface;

class WordModelCollectionCreator extends AbstractModelCollectionCreator
{
    public function __construct(
        private ModelCreatorInterface $wordCreator,
        $config = []
    ) {
        parent::__construct($config);
    }

    public function getItem(array $rawDataItems): ModelInterface {
        return $this->wordCreator->createFromRaw($rawDataItems);
    }

    protected function getCollection(): ModelCollectionInterface {
        return new WordModelCollection();
    }
}