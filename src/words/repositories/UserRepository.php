<?php

declare(strict_types=1);

namespace app\repositories;

use yii\db\Query;
use app\models\UserModel;
use yii\db\QueryInterface;
use app\models\ModelInterface;
use app\modelCreators\ModelCreatorInterface;
use app\modelCollections\ModelCollectionInterface;

class UserRepository extends AbstractRepository
{
    public const TABLE = 'users';

    public function __construct(
        private ModelCreatorInterface $userModelCreator,
        $config = []
    ) {
        parent::__construct($config);
    }

    protected function createModelFromRaw(array $data): ModelInterface
    {
        return $this->userModelCreator->createFromRaw($data);
    }

    protected function getTableName(): string
    {
        return self::TABLE;
    }

    protected function getDataBaseFieldAliases(): array
    {
        return [
          'id' => 'id',
          'ipAddress' => 'ipAddress',
        ];
    }

    protected function getKeyProperty(): string
    {
        return 'id';
    }

    protected function getQuery(): QueryInterface
    {
        return (new Query())
            ->select(
                [
                    'id' => self::TABLE . '.id',
                    'ipAddress' => self::TABLE . '.ipAddress',
                ]
            )
            ->from(self::TABLE);
    }

    protected function isInstanceValid(ModelInterface $model): bool
    {
        return $model instanceof UserModel;
    }

    protected function isCollectionInstanceValid(ModelCollectionInterface $collection): bool
    {
        throw new \RuntimeException('is not Supported');
    }
}