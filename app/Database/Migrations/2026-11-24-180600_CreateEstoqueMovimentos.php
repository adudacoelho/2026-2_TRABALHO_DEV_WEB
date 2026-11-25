<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * RF03: histórico de entradas, saídas e ajustes de estoque.
 *
 * Cada linha é um evento imutável: a quantidade é o delta aplicado
 * (positivo = entrada, negativo = saída) e saldo_apos registra o resultado.
 */
class CreateEstoqueMovimentos extends Migration
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
                'type'     => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'produto_id' => [
                'type'     => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'venda_id' => [
                'type'     => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null'     => true,
            ],
            'tipo' => [
                'type'       => 'VARCHAR',
                'constraint' => 10,
            ],
            'origem' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
            ],
            'quantidade' => [
                'type'       => 'INT',
                'constraint' => 11,
            ],
            'saldo_apos' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
            ],
            'observacao' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
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
        $this->forge->addForeignKey('loja_id', 'lojas', 'id', 'CASCADE', 'RESTRICT');
        $this->forge->addForeignKey('produto_id', 'produtos', 'id', 'CASCADE', 'RESTRICT');
        // Assinatura: (campo, tabela, coluna, onUpdate, onDelete).
        // Ao excluir a venda, o movimento permanece no histórico com venda_id nulo.
        $this->forge->addForeignKey('venda_id', 'venda_produtos', 'id', 'CASCADE', 'SET NULL');
        $this->forge->addKey(['loja_id', 'produto_id']);
        $this->forge->createTable('estoque_movimentos');
    }

    public function down()
    {
        $this->forge->dropTable('estoque_movimentos');
    }
}
