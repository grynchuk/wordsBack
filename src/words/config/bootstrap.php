<?php

declare(strict_types=1);


use yii\di\Instance;
use app\components\WordComponent;
use app\components\TextComponent;
use app\components\UserComponent;
use app\repositories\TextRepository;
use app\repositories\UserRepository;
use app\repositories\WordRepository;
use app\modelCreators\WordModelCreator;
use app\modelCreators\UserModelCreator;
use app\modelCreators\TextModelCreator;
use app\components\textParser\WordParser;
use app\modelCollectionCreators\WordModelCollectionCreator;

defined('YII_DEBUG') or define('YII_DEBUG', true);
defined('YII_ENV') or define('YII_ENV', 'dev');

\Yii::$container->setDefinitions(
    [
        WordModelCollectionCreator::class => [
            'class' => WordModelCollectionCreator::class,
            '__construct()' => [
                Instance::of(WordModelCreator::class)
            ]
        ],
        WordModelCreator::class => [
            'class' => WordModelCreator::class,
            '__construct()' => [
                Instance::of(UserModelCreator::class)
            ]
        ],
        TextModelCreator::class => [
            'class' => TextModelCreator::class,
            '__construct()' => [
                Instance::of(UserModelCreator::class),
                Instance::of(WordModelCollectionCreator::class),
            ]
        ],
        WordRepository::class => [
            'class' => WordRepository::class,
            '__construct()' => [
                Instance::of(WordModelCreator::class),
            ]
        ],
        TextRepository::class => [
            'class' => TextRepository::class,
            '__construct()' => [
                Instance::of(TextModelCreator::class)
            ]
        ],
        UserRepository::class => [
            'class' => UserRepository::class,
            '__construct()' => [
                Instance::of(UserModelCreator::class)
            ]
        ],
        WordComponent::class => [
            'class' => WordComponent::class,
            '__construct()' => [
                Instance::of(WordRepository::class)
            ]
        ],
        TextComponent::class => [
            'class' => TextComponent::class,
            '__construct()' => [
                Instance::of(WordComponent::class),
                Instance::of(TextRepository::class),
                Instance::of(TextModelCreator::class),
            ]
        ],
        WordParser::class => [
            'class' => WordParser::class,
            '__construct()' => [
                Instance::of(WordModelCreator::class),
            ]
        ],
        UserComponent::class => [
            'class' => UserComponent::class,
            '__construct()' => [
                Instance::of(UserRepository::class),
                Instance::of(UserModelCreator::class)],
        ]
    ]
);