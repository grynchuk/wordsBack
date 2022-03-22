<?php

declare(strict_types=1);

namespace app\modelCollections;

use yii\base\Arrayable;
use yii\base\BaseObject;
use app\models\ModelInterface;
use app\modelCollections\ModelCollectionInterface;


abstract class AbstractModelCollection extends BaseObject implements ModelCollectionInterface, Arrayable
{
    private $models = [];

    public function getModels(): array
    {
        return array_values($this->models);
    }


    public function getKeys(): array
    {
        return array_map(fn ($key) => (string) $key, array_keys($this->models));
    }


    public function getCount(): int
    {
        return count($this->getModels());
    }


    protected function attachModel(ModelInterface $model): void
    {
        $this->models[$model->getKeyValue()] = $model;
    }


    public function getByKey(string $key): ?ModelInterface
    {
        return $this->models[$key] ?? null;
    }


    public function serializeFields(array $fields, bool $recursive = true): array
    {
        $rawData = [];
        foreach ($this->getModels() as $model) {
            $rawData[] = $model->serializeFields($fields, $recursive);
        }

        return $rawData;
    }

    public function isEmpty(): bool
    {
        return $this->getCount() === 0;
    }


    public function convertFromIterable(iterable $iterable): ModelCollectionInterface
    {
        foreach ($iterable as $item) {
            $this->attach($item);
        }
        return $this;
    }

    public function fields(): array
    {
        /** @noinspection LoopWhichDoesNotLoopInspection */
        foreach ($this->models as $model) {
            return $model->fields();
        }
        return [];
    }

    public function toArray(array $fields = [], array $expand = [], $recursive = true): array
    {
        return $this->serializeFields($this->fields());
    }

    public function extraFields(): array
    {
        return [];
    }

    public function isValid(): bool
    {
        foreach ($this->getModels() as $model) {
            /** @var ModelInterface $model */
            if ($model->getErrorSummary(false)) {
                return false;
            }
        }
        return true;
    }

    public function walkAttached(callable $handler): ModelCollectionInterface
    {
        foreach ($this->getModels() as $model) {
            $handler($model);
        }
        return $this;
    }

    public function filter(callable $filter): ModelCollectionInterface
    {
        $this->models = array_filter(
            $this->models,
            $filter
        );
        return $this;
    }

    public function removeByKey(string $key): bool
    {
        if (!in_array($key, $this->getKeys())) {
            return false;
        }

        unset($this->models[$key]);
        return true;
    }

    public function diffTo(AbstractModelCollection $collection): ModelCollectionInterface
    {
        $diffKeys = array_map(
            fn ($key) => (string) $key,
            array_diff($this->getKeys(), $collection->getKeys())
        );
        return $this->filter(
            fn (ModelInterface $model) => in_array($model->getKeyValue(), $diffKeys, true)
        );
    }
}
