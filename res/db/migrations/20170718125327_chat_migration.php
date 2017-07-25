<?php

use Phinx\Migration\AbstractMigration;

class ChatMigration extends AbstractMigration
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-abstractmigration-class
     *
     * The following commands can be used in this method and Phinx will
     * automatically reverse them when rolling back:
     *
     *    createTable
     *    renameTable
     *    addColumn
     *    renameColumn
     *    addIndex
     *    addForeignKey
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function change()
    {
        $table = $this->table('chats');
        $table->addColumn('chat_id', 'integer')
            ->addColumn('username', 'string')
            ->addColumn('type', 'string')
            ->addColumn('liveticker', 'boolean')
            ->addColumn('registerstatus', 'string')
            ->addColumn('betstatus', 'string')
            ->addColumn('current_bet_match_id', 'integer')
            ->addForeignKey('current_bet_match_id', 'matches')
            ->addIndex(['chat_id'], ['unique' => true])
            ->create();
    }
}
