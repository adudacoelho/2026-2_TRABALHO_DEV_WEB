<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateProdutos extends Migration
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
            'nome' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'descricao' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'preco' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
            ],
            'quantidade' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 0,
            ],
            'categoria_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
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
        $this->forge->addForeignKey('categoria_id', 'categorias', 'id', 'CASCADE', 'RESTRICT');
        $this->forge->createTable('produtos');
    }

    public function down()
    {
        $this->forge->dropTable('produtos');
    }
}
