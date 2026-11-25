<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * O estoque passa a ter uma única fonte de verdade: estoque_lojas.
 * O "estoque global" exibido na tela de produtos é a soma por loja,
 * calculada no ProdutoModel, então a coluna antiga deixa de existir.
 */
class RemoverQuantidadeGlobalDeProdutos extends Migration
{
    public function up()
    {
        if ($this->db->fieldExists('quantidade', 'produtos')) {
            // Modo legado: ao recriar "produtos", o SQLite não deve redirecionar
            // as FKs de estoque_lojas/venda_produtos para "temp_produtos".
            $this->modoLegado(true);
            $this->forge->dropColumn('produtos', 'quantidade');
            $this->modoLegado(false);
        }
    }

    public function down()
    {
        if ($this->db->fieldExists('quantidade', 'produtos')) {
            return;
        }

        $this->modoLegado(true);
        $this->forge->addColumn('produtos', [
            'quantidade' => [
                'type'    => 'INT',
                'default' => 0,
            ],
        ]);
        $this->modoLegado(false);

        // Reconstrói o valor antigo a partir do estoque por loja.
        $this->db->query(
            'UPDATE `produtos` SET `quantidade` = COALESCE('
            . '(SELECT SUM(el.`quantidade`) FROM `estoque_lojas` el '
            . 'WHERE el.`produto_id` = `produtos`.`id`), 0)'
        );
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
