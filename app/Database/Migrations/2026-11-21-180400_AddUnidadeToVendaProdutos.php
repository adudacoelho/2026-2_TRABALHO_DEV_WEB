<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddUnidadeToVendaProdutos extends Migration
{
    /**
     * Congela a unidade do cliente no momento da venda, para que mudanças de
     * unidade não alterem o histórico das compras já realizadas.
     */
    public function up()
    {
        // A coluna pode ter sido criada em uma tentativa anterior que falhou
        // no meio da migration (o SQLite não usa transação automática aqui).
        if (! $this->db->fieldExists('unidade_id', 'venda_produtos')) {
            $this->forge->addColumn('venda_produtos', [
                'unidade_id' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
                    'null'       => true,
                ],
            ]);
        }

        // Sem alias de tabela: sintaxe aceita por SQLite e MySQL.
        // Prefere a unidade do cliente que pertence à loja da venda;
        // se não houver, usa qualquer unidade do cliente (dados legados).
        $this->db->query(
            'UPDATE `venda_produtos` SET `unidade_id` = COALESCE('
            . '(SELECT cu.`unidade_id` FROM `cliente_unidades` cu '
            . 'JOIN `unidades` u ON u.`id` = cu.`unidade_id` '
            . 'WHERE cu.`cliente_id` = `venda_produtos`.`cliente_id` '
            . 'AND u.`loja_id` = `venda_produtos`.`loja_id` LIMIT 1), '
            . '(SELECT cu.`unidade_id` FROM `cliente_unidades` cu '
            . 'WHERE cu.`cliente_id` = `venda_produtos`.`cliente_id` LIMIT 1))'
        );

        $this->forge->addForeignKey('unidade_id', 'unidades', 'id', 'CASCADE', 'RESTRICT');
        $this->forge->processIndexes('venda_produtos');
    }

    public function down()
    {
        $this->modoLegado(true);
        $this->forge->dropForeignKey('venda_produtos', 'venda_produtos_unidade_id_foreign');
        $this->forge->dropColumn('venda_produtos', 'unidade_id');
        $this->modoLegado(false);
    }

    /**
     * Liga/desliga o comportamento legado do ALTER TABLE do SQLite.
     */
    private function modoLegado(bool $ativo): void
    {
        if ($this->db->DBDriver === 'SQLite3') {
            $this->db->query('PRAGMA legacy_alter_table = ' . ($ativo ? 'ON' : 'OFF'));
        }
    }
}
