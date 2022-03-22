<?php

declare(strict_types=1);

namespace app\repositories;

interface SearchOptionInterface
{
    public function getCacheKey(): string;
}
