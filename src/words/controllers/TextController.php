<?php

declare(strict_types=1);

namespace app\controllers;


use Yii;
use Throwable;
use yii\base\Module;
use yii\db\Transaction;
use app\models\TextModel;
use app\models\UserModel;
use app\components\TextComponent;
use app\components\UserComponent;
use app\components\textParser\WordParser;
use app\components\textParser\TextParser;
use app\valueObjects\IpAddressValueObject;

class TextController extends AbstractController
{
    public function __construct(
        string $id,
        Module $module,
        private WordParser $wordParser,
        private TextParser $textParser,
        private TextComponent $textComponent,
        private UserComponent $userComponent,
        array $config = []
    ) {
        parent::__construct($id, $module, $config);
    }

    public function actionPost(): void
    {
        /** @var Transaction $transaction */
        $transaction = \Yii::$app->get('db')->beginTransaction();

        try {
            $userModel = $this->getCurrentUser();
            $storedText = $this->textComponent->getByUser($userModel);
            $storedText !== null && $this->textComponent->remove($storedText);

            /** @var TextModel $textModel */
            $textModel = $this->textComponent->createFromRaw([
                'id' => null,
                'text' => Yii::$app->request->post('text'),
                'userModel' => $userModel,
                'words' => []
            ]);

            $this->textParser->add($this->wordParser);
            $this->textParser->interpret($textModel, $textModel->getText());

            $this->textComponent->store($textModel);

            $transaction->commit();
        } catch (Throwable $e) {
            $transaction->rollBack();
            throw $e;
        }
    }

    public function actionGet(): ?TextModel
    {
        $userModel = $this->getCurrentUser();
        return $this->textComponent->getByUser($userModel);
    }

    protected function getAvailableMethods(): array
    {
        return [
            self::HTTP_METHOD_GET,
            self::HTTP_METHOD_POST,
        ];
    }

    private function getCurrentUser(): UserModel {
        $ip = new IpAddressValueObject($_SERVER['REMOTE_ADDR']);
        return $this->userComponent->getCreateByIp($ip);
    }
}