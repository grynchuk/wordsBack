<?php

declare(strict_types=1);

namespace app\models;

interface ModelInterface
{
    public function fields();

    public function serializeFields(array $fields, bool $recursive = true): array;

    public function getKeyValue(): string;

    public function getErrorSummary($showAllErrors);
}
