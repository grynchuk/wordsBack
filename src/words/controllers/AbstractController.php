<?php

declare(strict_types=1);

namespace app\controllers;

use Yii;
use yii\filters\Cors;
use yii\web\Response;
use yii\rest\Controller;
use yii\filters\ContentNegotiator;

abstract class AbstractController extends Controller
{
    protected const HTTP_METHOD_GET = 'GET';
    protected const HTTP_METHOD_POST = 'POST';
    protected const HTTP_METHOD_PUT = 'PUT';
    protected const HTTP_METHOD_DELETE = 'DELETE';
    protected const HTTP_METHOD_OPTIONS = 'OPTIONS';

    public function behaviors(): array
    {
        $behaviours = parent::behaviors();
        $behaviours['corsFilter'] = [
            'class' => Cors::class,
            'cors' => [
                'Origin' => [
                    'http://localhost:4200',
                ],
                'Access-Control-Allow-Credentials' => true,
                'Access-Control-Allow-Origin' => [
                    'http://localhost:4200',
                ],
                'Access-Control-Allow-Headers' => ['Content-Type', 'Accept', 'Authorization'],
                'Access-Control-Request-Method' => ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'HEAD', 'OPTIONS'],
            ],
        ];

        $behaviours['contentNegotiator'] = [
            'class' => ContentNegotiator::class,
            'formats' => [
                'application/json' => Response::FORMAT_JSON,
            ],
        ];

        return $behaviours;
    }

    abstract protected function getAvailableMethods(): array;

    public function actionOptions(): void
    {
        Yii::$app->getResponse()->getHeaders()->set(
            'Access-Control-Allow-Methods',
            implode(', ', $this->getAvailableMethods())
        );
    }


}
