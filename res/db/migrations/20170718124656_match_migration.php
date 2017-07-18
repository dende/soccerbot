<?php

use Phinx\Migration\AbstractMigration;

class MatchMigration extends AbstractMigration
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
        $table = $this->table('matches');
        $table->addColumn('home_team_id', 'integer')
            ->addColumn('away_team_id', 'integer')
            ->addColumn('home_team_goals', 'integer')
            ->addColumn('away_team_goals', 'integer')
            ->addColumn('status', 'string')
            ->addColumn('date', 'datetime')
            ->addColumn('url', 'string')
            ->addForeignKey('away_team_id', 'teams')
            ->addForeignKey('home_team_id', 'teams')
            ->create();
    }
}
