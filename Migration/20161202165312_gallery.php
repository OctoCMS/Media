<?php

use Phinx\Migration\AbstractMigration;

class Gallery extends AbstractMigration
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
        $table = $this->table('gallery');
        $table->addColumn('parent_id', 'integer', ['null' => true, 'default' => null]);
        $table->addColumn('title', 'string', ['length' => 250, 'null' => false]);
        $table->addColumn('description', 'text', ['null' => true, 'default' => null]);
        $table->addColumn('sort_order', 'integer', ['null' => false, 'default' => 0]);
        $table->addColumn('hidden', 'integer', ['length' => \Phinx\Db\Adapter\MysqlAdapter::INT_TINY, 'null' => false, 'default' => 0]);
        $table->addColumn('slug', 'string', ['length' => 500, 'null' => true, 'default' => null]);
        $table->addForeignKey('parent_id', 'gallery', 'id', ['delete' => 'SET_NULL', 'update' => 'CASCADE']);
        $table->create();

        $table = $this->table('gallery_image', ['id' => false]);
        $table->addColumn('gallery_id', 'integer', ['null' => false]);
        $table->addColumn('image_id', 'char', ['length' => 32, 'null' => false]);
        $table->addColumn('sort_order', 'integer', ['null' => false, 'default' => 9999]);
        $table->addIndex(['gallery_id', 'image_id'], ['unique' => true]);
        $table->addForeignKey('gallery_id', 'gallery', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE']);
        $table->addForeignKey('image_id', 'file', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE']);
        $table->create();
    }
}
