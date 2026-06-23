<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateKulinerTagsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'         => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'kuliner_id' => ['type' => 'INT', 'unsigned' => true],
            'tag_id'     => ['type' => 'INT', 'unsigned' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addForeignKey('kuliner_id', 'kuliner', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('tag_id', 'tags', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('kuliner_tags');
    }

    public function down()
    {
        $this->forge->dropTable('kuliner_tags');
    }
}
