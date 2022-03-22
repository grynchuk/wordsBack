<?php

declare(strict_types=1);

namespace app\repositories;

use yii\db\Query;
use app\models\TextModel;
use yii\db\QueryInterface;
use app\models\ModelInterface;
use app\modelCreators\ModelCreatorInterface;
use app\modelCollections\ModelCollectionInterface;

class TextRepository extends AbstractRepository
{
    public const TABLE = 'texts';

    public function __construct(
        private ModelCreatorInterface $textModelCreator,
        $config = []
    ) {
        parent::__construct($config);
    }

    protected function createModelFromRaw(array $data): ModelInterface
    {
        return $this->textModelCreator->createFromRaw($data);
    }

    protected function prepareDataGroupForModelCreator(array $data): array {
        $grouped = [];

        foreach ($data as $item) {

            if (!array_key_exists($item['userId'], $grouped)) {
                $grouped[$item['userId']] = [
                    'id' => $item['id'],
                    'text' => $item['text'],
                    'userModel' => [
                        'id' => $item['userId'],
                        'ipAddress' => $item['userIpAddress']
                    ],
                    'words' => []
                ];
            }

            $grouped[$item['userId']]['words'][] = [
                'id' =>  $item['wordId'],
                'word' => $item['wordWord'],
                'count' => $item['wordCount'],
                'userModel' => $grouped[$item['userId']]['userModel'],
            ];
        }

        return $grouped;
    }

    protected function getTableName(): string
    {
        return self::TABLE;
    }

    protected function getDataBaseFieldAliases(): array
    {
        return [
            'id' => 'id',
            'text' =>  'text',
            'userId' => 'userModel',
        ];
    }

    protected function getOrderAliases(): array
    {
        return [
          'word' => WordRepository::TABLE . '.word',
          'count' => WordRepository::TABLE . '.count',
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
                    'text' => self::TABLE . '.text',
                    'userId' => UserRepository::TABLE . '.id',
                    'userIpAddress' => UserRepository::TABLE . '.ipAddress',
                    'wordId' => WordRepository::TABLE . '.id',
                    'wordWord' => WordRepository::TABLE . '.word',
                    'wordCount' => WordRepository::TABLE . '.count',
                ]
            )
            ->from(self::TABLE)
            ->innerJoin(UserRepository::TABLE, UserRepository::TABLE . '.id = ' . self::TABLE . '.userId')
            ->innerJoin( WordRepository::TABLE, WordRepository::TABLE . '.userId = ' . self::TABLE . '.userId');
    }

    protected function isInstanceValid(ModelInterface $model): bool
    {
        return $model instanceof TextModel;
    }

    protected function isCollectionInstanceValid(ModelCollectionInterface $collection): bool
    {
        throw new \RuntimeException('Is not supported');
    }
}