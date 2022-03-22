<?php

declare(strict_types=1);

namespace app\modelCreators;

use app\models\TextModel;
use app\models\UserModel;
use app\models\ModelInterface;
use app\modelCollections\WordModelCollection;
use app\modelCollectionCreators\ModelCollectionCreatorInterface;

class TextModelCreator extends AbstractModelCreator
{
    public function __construct(
        private ModelCreatorInterface $userModelCreator,
        private ModelCollectionCreatorInterface $wordModelCollectionCreator,
        $config = []
    ) {
        parent::__construct($config);
    }

    protected function getPropertiesNames(): array
    {
        return [
            'id',
            'text',
            'userModel',
            'words',
        ];
    }

    protected function createInstance(array $modelRawData): ModelInterface
    {
        return new TextModel($modelRawData);
    }

    public function prepareRawData(array $rawData): array
    {
        $rawData = $this->prepareRawValues($rawData);

        $rawData['userModel'] = $rawData['userModel'] instanceof UserModel
            ? $rawData['userModel']
            : $this->userModelCreator->createFromRaw($rawData['userModel']);

        $rawData['words'] = $rawData['words'] instanceof WordModelCollection
            ? $rawData['words']
            : $this->wordModelCollectionCreator->createCollectionFromRaw($rawData['words']);

        return $rawData;
    }
}