<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateClienteUnidades extends Migration
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
            'unidade_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
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
        $this->forge->addUniqueKey(['cliente_id', 'unidade_id']);
        $this->forge->addForeignKey('cliente_id', 'clientes', 'id', 'CASCADE', 'RESTRICT');
        $this->forge->addForeignKey('unidade_id', 'unidades', 'id', 'CASCADE', 'RESTRICT');
        $this->forge->createTable('cliente_unidades');
    }

    public function down()
    {
        $this->forge->dropTable('cliente_unidades');
    }
}
