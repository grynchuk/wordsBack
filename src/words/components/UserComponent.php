<?php

declare(strict_types=1);

namespace app\components;

use yii\base\BaseObject;
use app\models\UserModel;
use app\models\ModelInterface;
use app\valueObjects\IpAddressValueObject;
use app\modelCreators\ModelCreatorInterface;
use app\repositories\RepositoryInterface;
use app\repositories\requestConditions\ModelSearchCondition;
use app\repositories\requestConditions\ModelSearchConditionCollection;

class UserComponent extends BaseObject implements EntityStoreInterface
{
    public function __construct(
        private RepositoryInterface $repository,
        private ModelCreatorInterface $userModelCreator,
        $config = []
    ) {
        parent::__construct($config);
    }

    public function getCreateByIp(IpAddressValueObject $ip): UserModel
    {
        $conditions = new ModelSearchConditionCollection();
        $conditions->attach(new ModelSearchCondition(['property' => 'ipAddress', 'value' => (string) $ip]));

        /** @var UserModel $user */
        $user = $this->repository->getOneByConditions($conditions);

        if ($user === null) {
            $user = $this->createFromRaw(
                [
                    'id' => null,
                    'ipAddress' => $ip
                ]
            );

            $this->store($user);
        }

        return $user;
    }

    public function store(ModelInterface $model): void
    {
        $this->repository->save($model);
    }

    public function createFromRaw(array $data): ModelInterface
    {
        return $this->userModelCreator->createFromRaw($data);
    }

    public function remove(ModelInterface $model): void
    {
        $this->repository->removeItem($model);
    }
}