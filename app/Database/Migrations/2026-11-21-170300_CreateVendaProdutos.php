<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateVendaProdutos extends Migration
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
            'cliente_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
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
            'data' => [
                'type' => 'DATETIME',
            ],
            'quantidade' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 1,
            ],
            'preco_unitario' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
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
        $this->forge->addForeignKey('cliente_id', 'clientes', 'id', 'CASCADE', 'RESTRICT');
        $this->forge->addForeignKey('loja_id', 'lojas', 'id', 'CASCADE', 'RESTRICT');
        $this->forge->addForeignKey('produto_id', 'produtos', 'id', 'CASCADE', 'RESTRICT');
        $this->forge->createTable('venda_produtos');
    }

    public function down()
    {
        $this->forge->dropTable('venda_produtos');
    }
}
