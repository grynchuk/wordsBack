<?php

declare(strict_types=1);

namespace app\models;

class WordModel extends AbstractModel
{
    private ?int $id;
    private string $word;
    private int $count;
    private UserModel $userModel;

    public function fields(): array
    {
        return [
            'id',
            'word',
            'count',
        ];
    }

    protected function getMandatoryFields(): array
    {
        return [
            'word',
            'count',
            'userModel',
        ];
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): WordModel
    {
        $this->id = $id;
        return $this;
    }

    public function getWord(): string
    {
        return $this->word;
    }

    public function setWord(string $word): WordModel
    {
        $this->word = $word;
        return $this;
    }

    public function getCount(): int
    {
        return $this->count;
    }

    public function setCount(int $count): WordModel
    {
        $this->count = $count;
        return $this;
    }

    public function getKeyValue(): string
    {
        return (string) ($this->getId() ?? $this->getWord());
    }


    public function getUserModel(): UserModel
    {
        return $this->userModel;
    }

    public function setUserModel(UserModel $userModel): WordModel
    {
        $this->userModel = $userModel;
        return $this;
    }
}