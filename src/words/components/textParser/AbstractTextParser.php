<?php

declare(strict_types=1);

namespace app\components\textParser;

use yii\base\BaseObject;
use app\models\TextModel;

abstract class AbstractTextParser extends BaseObject implements ParserInterface
{
    abstract public function interpret(TextModel $textModel, string $data): void;

    abstract public function add(ParserInterface $parser): void;
}