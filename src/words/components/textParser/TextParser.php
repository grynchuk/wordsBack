<?php

declare(strict_types=1);

namespace app\components\textParser;


use app\models\TextModel;

class TextParser extends AbstractTextParser
{
    private const PATTERN = '/\w{1,}/';

    private array $parsers;

    public function interpret(TextModel $textModel, string $data): void
    {
        if (!$textModel->getWords()->isEmpty()) {
            throw new \RuntimeException('Words already parsed');
        }

        $words = [];

        preg_match_all(self::PATTERN, $textModel->getText(),$words);

        foreach ($words[0] as $word) {
            array_map(
                fn (ParserInterface $parser) =>  $parser->interpret($textModel, $word),
                $this->parsers
            );
        }
    }

    public function add(ParserInterface $parser): void
    {
        $this->parsers[] = $parser;
    }


}