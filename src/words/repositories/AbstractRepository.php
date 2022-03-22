<?php

declare(strict_types=1);

namespace app\repositories;

use Yii;
use LogicException;
use yii\db\Command;
use RuntimeException;
use yii\db\Expression;
use yii\db\Connection;
use DateTimeInterface;
use yii\base\BaseObject;
use yii\db\QueryInterface;
use app\models\ModelInterface;
use app\repositories\requestSorts\SortInterface;
use app\repositories\requestSorts\SortCollection;
use app\modelCollections\ModelCollectionInterface;
use app\repositories\requestConditions\ConditionTypeEnum;
use app\repositories\requestConditions\ConditionInterface;
use app\modelCollectionCreators\ModelCollectionCreatorInterface;
use app\repositories\requestConditions\ModelSearchConditionCollection;

abstract class AbstractRepository extends BaseObject implements RepositoryInterface
{
    public const MAIN_DATABASE_COMPONENT_ID = 'db';

    abstract protected function createModelFromRaw(array $data): ModelInterface;

    abstract protected function getTableName(): string;

    abstract protected function getDataBaseFieldAliases(): array;

    abstract protected function isInstanceValid(ModelInterface $model): bool;

    abstract protected function isCollectionInstanceValid(ModelCollectionInterface $collection): bool;

    abstract protected function getKeyProperty(): string;

    public function getDatabaseComponent(): Connection
    {
        return \Yii::$app->get(
            $this->getDataBaseComponentId()
        );
    }

    abstract protected function getQuery(): QueryInterface;


    protected function getDataBaseComponentId(): string
    {
        return self::MAIN_DATABASE_COMPONENT_ID;
    }

    public function saveCollection(ModelCollectionInterface $collection): void
    {
        if (!$this->isCollectionInstanceValid($collection)) {
            throw new RuntimeException('Invalid collection');
        }

        if ($collection->isEmpty()) {
            return;
        }

        $aliases = $this->getDataBaseFieldAliases();
        $columns = array_keys($aliases);
        $data = $collection->serializeFields($aliases, false);

        $query = $this->getDatabaseComponent()
            ->createCommand()
            ->batchInsert(
                $this->getTableName(),
                $columns,
                $data
            );

        $this->executeOnDuplicateUpdate($query, array_keys($aliases));
    }

    public function save(ModelInterface $model): void
    {
        if (!$this->isInstanceValid($model)) {
            throw new RuntimeException('Invalid model');
        }

        $aliases = $this->getDataBaseFieldAliases();

        if (!$aliases) {
            throw new LogicException('Failed to save');
        }
        $query = $this->getDatabaseComponent()
            ->createCommand()
            ->batchInsert(
                $this->getTableName(),
                array_keys($aliases),
                [$model->serializeFields($aliases, false)]
            );

        $id = $this->executeOnDuplicateUpdate($query, array_keys($aliases));

        if (method_exists($model, 'setId')) {
            $model->id = $model->id ?? $id;
        }
    }

    private function executeOnDuplicateUpdate(Command $command, array $cols): int
    {
        $postfix = [];

        foreach ($cols as $col) {
            $postfix[] = " `$col` = values(`$col`) ";
        }
        $query = $command->getRawSql() . ' ON DUPLICATE KEY UPDATE ' . implode(',', $postfix);

        $db = $this->getDatabaseComponent();
        $db->createCommand($query)
            ->execute();

        return (int) $db->getLastInsertID();
    }


    public function getOneByConditions(
        ModelSearchConditionCollection $modelSearchConditionCollection,
        bool $isGrouped = false,
        ?SortCollection $sort = null
    ): ?ModelInterface {
        $query = $this->getQuery();

        $modelSearchConditionCollection->walkAttached(
            fn (ConditionInterface $modelSearchCondition) => $query->andWhere(
                $this->prepareCondition($modelSearchCondition->getProperty(), $modelSearchCondition->getValue(), $modelSearchCondition->getType())
            )
        );

        if ($sort !== null) {
            $sort->walkAttached(fn(SortInterface $sort) => $query->addOrderBy($this->prepareOrderBy($sort)));
        }

        $rawData = $isGrouped
            ? $query->all($this->getDatabaseComponent())
            : $query->one($this->getDatabaseComponent());

        if ($rawData) {
            if ($preparedCollection = $this->prepareDataGroupForModelCreator($rawData)) {
                $rawData = array_values($preparedCollection)[0];
            }
            return $this->createModelFromRaw($rawData);
        }

        return null;
    }


    public function getAllByCondition(
        ?ModelSearchConditionCollection $modelSearchConditionCollection = null,
        ?SortCollection $sort = null
    ): iterable {
        $query = $this->getQuery();

        if ($modelSearchConditionCollection !== null) {
            $modelSearchConditionCollection->walkAttached(
                fn (ConditionInterface $modelSearchCondition) => $query->andWhere(
                    $this->prepareCondition($modelSearchCondition->getProperty(), $modelSearchCondition->getValue(), $modelSearchCondition->getType())
                )
            );
        }

        if ($sort !== null) {
            $sort->walkAttached(fn(SortInterface $sort) => $query->addOrderBy($this->prepareOrderBy($sort)));
        }

        $rawDataItems = $query->all($this->getDatabaseComponent());

        if ($preparedCollection = $this->prepareDataGroupForModelCreator($rawDataItems)) {
            $rawDataItems = $preparedCollection;
        }

        foreach ($rawDataItems as $rawData) {
            yield $this->createModelFromRaw($rawData);
        }
    }

    private function prepareOrderBy(SortInterface $sort): array
    {
        $aliases = $this->getOrderAliases();
        $property = $sort->getProperty();
        $rawOrder = $sort->getOrder()->value;

        $column = $aliases[$property] ?? null;

        if ($column === null) {
            throw new LogicException('alias for sort fields is not set');
        }

        return [$column => (int) $rawOrder];
    }

    /**
     * @param mixed $value
     * @return string|int
     */
    private function normalizeValue($value)
    {
        if ($value instanceof ModelCollectionInterface) {
            return $value->getKeys();
        } elseif ($value instanceof ModelInterface) {
            return $value->getKeyValue();
        } elseif ($value instanceof \UnitEnum) {
            return $value->value;
        } elseif ($value instanceof Stringable) {
            return (string) $value->value;
        } elseif ($value instanceof DateTimeInterface) {
            return $value->format('Y-m-d H:i:s');
        } elseif ($value instanceof Expression) {
            return $value;
        } elseif (is_object($value)) {
            throw new LogicException('value object should be  instance of BaseModelInterface');
        }

        return $value;
    }

    private function prepareCondition(string $property = null, $value = null, ?ConditionTypeEnum $type = null): ?array
    {
        $aliases = $this->getDataBaseFieldAliases();
        $conditionAliases = $this->getConditionAliases();
        $reversedAliases = array_flip($aliases);

        $value = $this->normalizeValue($value);
        $rawType = $this->prepareType($type, $value);

        if ($property === null) {
            $condition = null ;
        } elseif (array_key_exists($property, $reversedAliases)) {
            $condition = [ $rawType, $this->getTableName() . '.' . $reversedAliases[$property], $value] ;
        } elseif (array_key_exists($property, $conditionAliases)) {
            $condition = [ $rawType, $conditionAliases[$property], $value];
        } else {
            throw new LogicException("Failed to search by property {$property} ");
        }

        return $condition;
    }

    private function prepareType(?ConditionTypeEnum $type, $value): string
    {
        if ($type instanceof ConditionTypeEnum) {
            return $type->value;
        }

        if ($value === null) {
            return ConditionTypeEnum::IS->value;
        }

        if (is_array($value)) {
            return ConditionTypeEnum::IN->value;
        }

        return ConditionTypeEnum::EQUAL->value;
    }


    public function removeItems(ModelCollectionInterface $collection): void
    {
        if (!$this->isCollectionInstanceValid($collection)) {
            throw new RuntimeException('Invalid collection');
        }

        $key = $this->getKeyProperty();
        $rawDeleteKeys = [ $key => []];
        foreach ($collection->getModels() as $item) {
            $rawDeleteKeys[$key][] = $item->{$key};
        }

        $this->getDatabaseComponent()
            ->createCommand()
            ->delete(
                $this->getTableName(),
                $rawDeleteKeys
            )->execute();
    }

    public function removeItem(ModelInterface $model): void
    {
        if (!$this->isInstanceValid($model)) {
            throw new RuntimeException('Invalid model');
        }

        $key = $this->getKeyProperty();

        $this->getDatabaseComponent()
            ->createCommand()
            ->delete(
                $this->getTableName(),
                [ $key => $model->{$key} ]
            )->execute();
    }

    protected function getConditionAliases(): array
    {
        return [];
    }

    protected function getOrderAliases(): array
    {
        return [];
    }

    protected function containsFields(array $fields, array $data): bool
    {
        foreach ($fields as $field) {
            if (!array_key_exists($field, $data)) {
                return false;
            }
        }
        return true;
    }

    protected function prepareDataGroupForModelCreator(array $data): array
    {
        return [];
    }

    protected function getModelCollectionCreator(): ModelCollectionCreatorInterface
    {
        throw new LogicException('Repository model collection creator is not set');
    }

    public function convertFromIterable(iterable $data): ModelCollectionInterface
    {
        return $this->getModelCollectionCreator()
            ->createCollectionFromRaw([])
            ->convertFromIterable($data);
    }

    public function createCollectionFromRaw(iterable $data): ModelCollectionInterface
    {
        return $this->getModelCollectionCreator()->createCollectionFromRaw($data);
    }
}
