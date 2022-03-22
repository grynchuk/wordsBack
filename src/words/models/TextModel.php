<?php

declare(strict_types=1);

namespace app\models;

use RuntimeException;
use app\modelCollections\WordModelCollection;

class TextModel extends AbstractModel
{
    private ?int $id;
    private string $text;
    private UserModel $userModel;
    private WordModelCollection $words;

    public function fields()
    {
        return [
            'id',
            'text',
            'words',
        ];
    }

    protected function getMandatoryFields(): array
    {
        return [
            'text',
            'words',
            'userModel',
        ];
    }

    public function getKeyValue(): string
    {
        return 'text';
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function setText(string $text): TextModel
    {
        $this->text = $text;
        return $this;
    }

    public function getWords(): WordModelCollection
    {
        return $this->words;
    }

    public function setWords(WordModelCollection $words): TextModel
    {
        $this->words = $words;
        return $this;
    }

    public function getUserModel(): UserModel
    {
        return $this->userModel;
    }

    public function setUserModel(UserModel $userModel): TextModel
    {
        $this->userModel = $userModel;
        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): TextModel
    {
        $this->id = $id;
        return $this;
    }

}