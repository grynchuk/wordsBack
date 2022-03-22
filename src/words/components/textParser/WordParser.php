<?php

declare(strict_types=1);

namespace app\components\textParser;

use RuntimeException;
use app\models\TextModel;
use app\models\WordModel;
use app\modelCreators\ModelCreatorInterface;

class WordParser extends AbstractTextParser
{
    private const MIN_LENGTH = 3;

    public function __construct(
        private ModelCreatorInterface $wordModelCreator,
        $config = []
    ) {
        parent::__construct($config);
    }

    public function interpret(TextModel $textModel, string $data): void
    {
        if (strlen($data) <= self::MIN_LENGTH) {
            return;
        }

        $word = strtolower($data);

        /** @var WordModel $wordModel */
        $wordModel = $textModel->getWords()->getByKey($word);

        if ($wordModel == null) {
            $item = $this->wordModelCreator->createFromRaw(
                [
                    'id' => null,
                    'word' => $word,
                    'userModel' => $textModel->getUserModel(),
                    'count' => 1
                ]
            );
            $textModel->getWords()->attach($item);
        } else {
            $wordModel->setCount($wordModel->getCount() + 1);
        }
    }

    public function add(ParserInterface $parser): void
    {
        throw new RuntimeException('Word is terminal expression');
    }
}