<?php

declare(strict_types=1);

namespace app\repositories;

use yii\db\Query;
use app\models\WordModel;
use yii\db\QueryInterface;
use app\models\ModelInterface;
use app\modelCreators\ModelCreatorInterface;
use app\modelCollections\WordModelCollection;
use app\modelCollections\ModelCollectionInterface;

class WordRepository extends AbstractRepository
{
    public const TABLE = 'words';

    public function __construct(
        private ModelCreatorInterface $wordModelCreator,
        $config = []
    ) {
        parent::__construct($config);
    }

    protected function createModelFromRaw(array $data): ModelInterface
    {
        return $this->wordModelCreator->createFromRaw([
            'id' => $data['id'],
            'word' => $data['word'],
            'count' => $data['count'],
            'userModel' => [
                'id' => $data['userId'],
                'ipAddress' => $data['userIpAddress'],
            ]
        ]);
    }

    protected function getTableName(): string
    {
        return self::TABLE;
    }

    protected function getDataBaseFieldAliases(): array
    {
        return [
          'id' => 'id',
          'word' => 'word',
          'count' => 'count',
          'userId' => 'userModel',
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
                    'word' => self::TABLE . '.word',
                    'count' => self::TABLE . '.count',
                    'userId' => UserRepository::TABLE . '.id',
                    'userIpAddress' => UserRepository::TABLE . '.ipAddress',
                ]
            )->from(self::TABLE)
            ->join(UserRepository::TABLE, self::TABLE . '.userId = ' . UserRepository::TABLE . 'id' );
    }

    protected function isInstanceValid(ModelInterface $model): bool
    {
        return $model instanceof WordModel;
    }

    protected function isCollectionInstanceValid(ModelCollectionInterface $collection): bool
    {
        return $collection instanceof WordModelCollection;
    }
}