<?php

declare(strict_types=1);

namespace app\modelCreators;

use app\models\ModelInterface;


interface ModelCreatorInterface
{

    public function prepareRawData(array $rawData): array;

    /**
     * @param array $rawData
     * @return ModelInterface
     */
    public function createFromRaw(array $rawData = []): ModelInterface;

    /**
     * @return ModelInterface
     */
    public function getDefaultInstance(): ModelInterface;
}
