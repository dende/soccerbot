<?php

use Phinx\Migration\AbstractMigration;

class BetMigration extends AbstractMigration
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
        $table = $this->table('bets');
        $table->addColumn('chat_id', 'integer')
            ->addColumn('match_id', 'integer')
            ->addColumn('home_team_goals', 'integer')
            ->addColumn('away_team_goals', 'integer')
            ->addForeignKey('chat_id', 'privatechats')
            ->addForeignKey('match_id', 'matches')
            ->addIndex(['chat_id', 'match_id'], ['unique' => true])
            ->create();
    }
}
