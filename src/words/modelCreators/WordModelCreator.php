<?php

declare(strict_types=1);

namespace app\modelCreators;

use app\models\WordModel;
use app\models\UserModel;
use app\models\ModelInterface;

class WordModelCreator extends AbstractModelCreator
{
    public function __construct(
        private ModelCreatorInterface $userModelCreator,
        $config = []
    ) {
        parent::__construct($config);
    }

    protected function getPropertiesNames(): array {
        return [
            'id',
            'userModel',
            'word',
            'count',
        ];
    }

    protected function createInstance(array $modelRawData): ModelInterface
    {
        return new WordModel($modelRawData);
    }

    public function prepareRawData(array $rawData): array
    {
        $rawData = $this->prepareRawValues($rawData);

        $rawData['userModel'] = $rawData['userModel'] instanceof UserModel
            ? $rawData['userModel']
            : $this->userModelCreator->createFromRaw($rawData['userModel']);

        return $rawData;
    }
}