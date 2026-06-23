<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePaymentsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'             => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'user_id'        => ['type' => 'INT', 'unsigned' => true],
            'kuliner_id'     => ['type' => 'INT', 'unsigned' => true],
            'invoice_number' => ['type' => 'VARCHAR', 'constraint' => 100, 'unique' => true],
            'amount'         => ['type' => 'DECIMAL', 'constraint' => '12,2'],
            'status'         => ['type' => 'ENUM', 'constraint' => ['pending', 'paid', 'expired', 'failed'], 'default' => 'pending'],
            'snap_token'     => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'payment_method' => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
            'created_at'     => ['type' => 'DATETIME', 'null' => true],
            'updated_at'     => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'RESTRICT');
        $this->forge->addForeignKey('kuliner_id', 'kuliner', 'id', 'CASCADE', 'RESTRICT');
        $this->forge->createTable('payments');
    }

    public function down()
    {
        $this->forge->dropTable('payments');
    }
}
