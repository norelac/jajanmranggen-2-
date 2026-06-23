<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateKulinerTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'             => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'name'           => ['type' => 'VARCHAR', 'constraint' => 200],
            'slug'           => ['type' => 'VARCHAR', 'constraint' => 220, 'unique' => true],
            'description'    => ['type' => 'TEXT', 'null' => true],
            'address'        => ['type' => 'TEXT'],
            'latitude'       => ['type' => 'DECIMAL', 'constraint' => '10,8', 'null' => true],
            'longitude'      => ['type' => 'DECIMAL', 'constraint' => '11,8', 'null' => true],
            'category_id'    => ['type' => 'INT', 'unsigned' => true],
            'contributor_id' => ['type' => 'INT', 'unsigned' => true],
            'status'         => ['type' => 'ENUM', 'constraint' => ['pending', 'approved', 'rejected', 'closed_permanent'], 'default' => 'pending'],
            'average_rating' => ['type' => 'DECIMAL', 'constraint' => '3,2', 'default' => '0.00'],
            'is_promoted'    => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
            'promoted_until' => ['type' => 'DATETIME', 'null' => true],
            'created_at'     => ['type' => 'DATETIME', 'null' => true],
            'updated_at'     => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addForeignKey('category_id', 'categories', 'id', 'CASCADE', 'RESTRICT');
        $this->forge->addForeignKey('contributor_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('kuliner');
    }

    public function down()
    {
        $this->forge->dropTable('kuliner');
    }
}
