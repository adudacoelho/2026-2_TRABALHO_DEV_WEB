<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateFornecedores extends Migration
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
                'constraint' => 150,
            ],
            'descricao' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'cnpj' => [
                'type'       => 'VARCHAR',
                'constraint' => 14,
            ],
            'telefone' => [
                'type'       => 'VARCHAR',
                'constraint' => 11,
                'null'       => true,
            ],
            'endereco' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'email' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
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
        $this->forge->addUniqueKey('cnpj');
        $this->forge->createTable('fornecedores');
    }

    public function down()
    {
        $this->forge->dropTable('fornecedores');
    }
}
