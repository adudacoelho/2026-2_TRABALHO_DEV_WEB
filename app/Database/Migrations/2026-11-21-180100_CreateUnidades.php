<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateUnidades extends Migration
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
            'identificador' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
            ],
            'loja_id' => [
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
        $this->forge->addUniqueKey(['loja_id', 'identificador']);
        $this->forge->addForeignKey('loja_id', 'lojas', 'id', 'CASCADE', 'RESTRICT');
        $this->forge->createTable('unidades');
    }

    public function down()
    {
        $this->forge->dropTable('unidades');
    }
}
