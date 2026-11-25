<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateEstoqueLojas extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'loja_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'produto_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'quantidade' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 0,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addPrimaryKey('id');
        $this->forge->addUniqueKey(['loja_id', 'produto_id']);
        $this->forge->addForeignKey('loja_id', 'lojas', 'id', 'CASCADE', 'RESTRICT');
        $this->forge->addForeignKey('produto_id', 'produtos', 'id', 'CASCADE', 'RESTRICT');
        $this->forge->createTable('estoque_lojas');
    }

    public function down()
    {
        $this->forge->dropTable('estoque_lojas');
    }
}
