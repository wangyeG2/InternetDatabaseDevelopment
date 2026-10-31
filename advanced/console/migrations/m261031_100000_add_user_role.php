<?php

/** Add explicit management roles without promoting existing users. Maintainer: B. */
declare(strict_types=1);

use yii\db\Migration;

class m261031_100000_add_user_role extends Migration
{
    public function safeUp(): void
    {
        $this->addColumn('{{%user}}', 'role', $this->string(16)->notNull()->defaultValue('member'));
    }

    public function safeDown(): void
    {
        $this->dropColumn('{{%user}}', 'role');
    }
}
