<?php

declare(strict_types=1);

namespace app\components;

use RuntimeException;
use yii\base\BaseObject;
use app\models\TextModel;
use app\models\UserModel;
use app\models\ModelInterface;
use app\repositories\requestSorts\Sort;
use app\repositories\RepositoryInterface;
use app\modelCreators\ModelCreatorInterface;
use app\repositories\requestSorts\SortTypeEnum;
use app\repositories\requestSorts\SortCollection;
use app\repositories\requestConditions\ModelSearchCondition;
use app\repositories\requestConditions\ModelSearchConditionCollection;

class TextComponent extends BaseObject implements EntityStoreInterface
{
    public function __construct(
        private EntityCollectionStoreInterface $wordComponent,
        private RepositoryInterface $repository,
        private ModelCreatorInterface $textModelCreator,
        $config = []
    ) {
        parent::__construct($config);
    }

    public function createFromRaw(array $data): ModelInterface
    {
        return $this->textModelCreator->createFromRaw($data);
    }

    /**
     * @param ModelInterface|TextModel $model
     */
    public function store(ModelInterface $model): void
    {
        $this->repository->save($model);
        $this->wordComponent->storeCollection($model->getWords());
    }

    public function getByUser(UserModel $userModel): ?TextModel
    {
        $conditions = new ModelSearchConditionCollection();
        $conditions->attach(new ModelSearchCondition(['property' => 'userModel' , 'value' => $userModel ]));
        $sorts = new SortCollection();
        $sorts->attach(new Sort(['property' => 'count', 'order' => SortTypeEnum::DESC]));
        $sorts->attach(new Sort(['property' => 'word', 'order' => SortTypeEnum::ASC]));
        return $this->repository->getOneByConditions($conditions, true, $sorts);
    }

    /**
     * @param ModelInterface|TextModel $model
     */
    public function remove(ModelInterface $model): void
    {
        $this->wordComponent->removeCollection($model->getWords());
        $this->repository->removeItem($model);
    }
}