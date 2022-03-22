<?php

declare(strict_types=1);

namespace app\components\textParser;

use app\models\TextModel;

interface ParserInterface
{
    public function interpret(TextModel $textModel, string $data): void;
}