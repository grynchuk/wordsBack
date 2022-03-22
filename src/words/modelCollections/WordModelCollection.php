<?php

declare(strict_types=1);

namespace app\modelCollections;

use RuntimeException;
use app\models\WordModel;
use app\models\ModelInterface;

class WordModelCollection extends AbstractModelCollection
{
    public function attach(ModelInterface $model): void
    {
        if ($model instanceof WordModel) {
            $this->attachModel($model);
        } else {
            throw new RuntimeException('Invalid model');
        }
    }
}