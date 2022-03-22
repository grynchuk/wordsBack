<?php

use yii\db\Migration;

/**
 * Class m220317_140053_init_db
 */
class m220317_140053_init_db extends Migration
{
    private const WORD_TABLE = 'words';
    private const USER_TABLE = 'users';
    private const TEXT_TABLE = 'texts';

    public function safeUp()
    {
        $this->createTable(
            self::WORD_TABLE,
            [
                'id' => $this->primaryKey()->unsigned(),
                'word' => $this->string(50)->notNull(),
                'count' => $this->integer()->unsigned() ,
                'userId' => $this->integer()->unsigned(),
            ]
        );

        $this->createIndex(
            'userIdWord',
            self::WORD_TABLE,
            ['userId', 'word'],
            true
        );

        $this->createTable(
            self::USER_TABLE,
            [
                'id' => $this->primaryKey()->unsigned(),
                'ipAddress' => $this->string(39)->notNull()->unique(),
            ]
        );

        $this->createTable(
            self::TEXT_TABLE,
            [
                'id' => $this->primaryKey()->unsigned(),
                'text' => $this->string(250),
                'userId' => $this->integer()->unsigned()->unique(),
            ]
        );
    }


    public function safeDown()
    {
        $this->dropTable(self::WORD_TABLE);
        $this->dropTable(self::USER_TABLE);
        $this->dropTable(self::TEXT_TABLE);
    }
}
