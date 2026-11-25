<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Corrige bancos criados antes do ajuste em "MigrarUnidadesClientes" e
 * "RemoverQuantidadeGlobalDeProdutos".
 *
 * O Forge do SQLite recria a tabela renomeando-a para "temp_<tabela>", e o
 * SQLite reescrevia as FKs das tabelas dependentes para apontar para a tabela
 * temporária (que logo era excluída). O resultado eram FKs quebradas
 * (estoque_lojas.produto_id -> temp_produtos, por exemplo).
 *
 * Em bancos novos this migration não faz nada. Em bancos afetados, recria
 * apenas as tabelas com FKs quebradas, preservando todos os dados.
 */
class RepararForeignKeysTemporarias extends Migration
{
    /**
     * Tabelas que podem ter sido reconstruídas pelo Forge e precisam de reparo.
     *
     * @var list<string>
     */
    private array $tabelas = [
        'cliente_unidades',
        'estoque_lojas',
        'venda_produtos',
        'estoque_movimentos',
    ];

    public function up()
    {
        if ($this->db->DBDriver !== 'SQLite3') {
            return;
        }

        if (! $this->possuiReferenciaTemporaria()) {
            return;
        }

        // Precisa ficar fora de transação: o SQLite ignora os PRAGMAs dentro dela.
        $this->db->query('PRAGMA foreign_keys = OFF');
        $this->db->query('PRAGMA legacy_alter_table = ON');

        $this->db->transBegin();

        try {
            if ($this->db->tableExists('cliente_unidades')) {
                $this->recriarClienteUnidades();
            }

            if ($this->db->tableExists('estoque_lojas')) {
                $this->recriarEstoqueLojas();
            }

            if ($this->db->tableExists('venda_produtos')) {
                $this->recriarVendaProdutos();
            }

            if ($this->db->tableExists('estoque_movimentos')) {
                $this->recriarEstoqueMovimentos();
            }

            $this->db->transCommit();
        } catch (\Throwable $e) {
            $this->db->transRollback();
            throw $e;
        } finally {
            $this->db->query('PRAGMA legacy_alter_table = OFF');
            $this->db->query('PRAGMA foreign_keys = ON');
        }
    }

    public function down()
    {
        // Migration corretiva: não há o que desfazer.
    }

    /**
     * Indica se alguma tabela referencia uma tabela temporária do Forge.
     */
    private function possuiReferenciaTemporaria(): bool
    {
        // instr() compara o texto literalmente (LIKE trataria "_" como curinga).
        $linha = $this->db->query(
            "SELECT COUNT(*) AS total FROM sqlite_master "
            . "WHERE type = 'table' AND instr(sql, 'temp_') > 0"
        )->getRowArray();

        return (int) ($linha['total'] ?? 0) > 0;
    }

    private function recriarClienteUnidades(): void
    {
        $this->db->query('DROP TABLE IF EXISTS `cliente_unidades__novo`');

        $this->db->query(
            'CREATE TABLE `cliente_unidades__novo` ('
            . '`id` INTEGER PRIMARY KEY AUTOINCREMENT, '
            . '`cliente_id` INT NOT NULL, '
            . '`unidade_id` INT NOT NULL, '
            . '`created_at` DATETIME NULL, '
            . '`updated_at` DATETIME NULL, '
            . 'CONSTRAINT `cliente_unidades_cliente_id_foreign` FOREIGN KEY (`cliente_id`) '
            . 'REFERENCES `clientes`(`id`) ON DELETE RESTRICT ON UPDATE CASCADE, '
            . 'CONSTRAINT `cliente_unidades_unidade_id_foreign` FOREIGN KEY (`unidade_id`) '
            . 'REFERENCES `unidades`(`id`) ON DELETE RESTRICT ON UPDATE CASCADE)'
        );

        $this->copiarDados('cliente_unidades', ['id', 'cliente_id', 'unidade_id', 'created_at', 'updated_at']);

        $this->db->query(
            'CREATE UNIQUE INDEX `cliente_unidades_cliente_id_unidade_id` '
            . 'ON `cliente_unidades` (`cliente_id`, `unidade_id`)'
        );
    }

    private function recriarEstoqueLojas(): void
    {
        $this->db->query('DROP TABLE IF EXISTS `estoque_lojas__novo`');

        $this->db->query(
            'CREATE TABLE `estoque_lojas__novo` ('
            . '`id` INTEGER PRIMARY KEY AUTOINCREMENT, '
            . '`loja_id` INT NOT NULL, '
            . '`produto_id` INT NOT NULL, '
            . '`quantidade` INT NOT NULL DEFAULT 0, '
            . '`created_at` DATETIME NULL, '
            . '`updated_at` DATETIME NULL, '
            . 'CONSTRAINT `estoque_lojas_loja_id_foreign` FOREIGN KEY (`loja_id`) '
            . 'REFERENCES `lojas`(`id`) ON DELETE RESTRICT ON UPDATE CASCADE, '
            . 'CONSTRAINT `estoque_lojas_produto_id_foreign` FOREIGN KEY (`produto_id`) '
            . 'REFERENCES `produtos`(`id`) ON DELETE RESTRICT ON UPDATE CASCADE)'
        );

        $this->copiarDados('estoque_lojas', ['id', 'loja_id', 'produto_id', 'quantidade', 'created_at', 'updated_at']);

        $this->db->query(
            'CREATE UNIQUE INDEX `estoque_lojas_loja_id_produto_id` '
            . 'ON `estoque_lojas` (`loja_id`, `produto_id`)'
        );
    }

    private function recriarVendaProdutos(): void
    {
        $this->db->query('DROP TABLE IF EXISTS `venda_produtos__novo`');

        $this->db->query(
            'CREATE TABLE `venda_produtos__novo` ('
            . '`id` INTEGER PRIMARY KEY AUTOINCREMENT, '
            . '`cliente_id` INT NOT NULL, '
            . '`loja_id` INT NOT NULL, '
            . '`produto_id` INT NOT NULL, '
            . '`data` DATETIME NOT NULL, '
            . '`quantidade` INT NOT NULL DEFAULT 1, '
            . '`preco_unitario` DECIMAL NOT NULL, '
            . '`created_at` DATETIME NULL, '
            . '`updated_at` DATETIME NULL, '
            . '`unidade_id` INT NULL, '
            . 'CONSTRAINT `venda_produtos_cliente_id_foreign` FOREIGN KEY (`cliente_id`) '
            . 'REFERENCES `clientes`(`id`) ON DELETE RESTRICT ON UPDATE CASCADE, '
            . 'CONSTRAINT `venda_produtos_loja_id_foreign` FOREIGN KEY (`loja_id`) '
            . 'REFERENCES `lojas`(`id`) ON DELETE RESTRICT ON UPDATE CASCADE, '
            . 'CONSTRAINT `venda_produtos_produto_id_foreign` FOREIGN KEY (`produto_id`) '
            . 'REFERENCES `produtos`(`id`) ON DELETE RESTRICT ON UPDATE CASCADE, '
            . 'CONSTRAINT `venda_produtos_unidade_id_foreign` FOREIGN KEY (`unidade_id`) '
            . 'REFERENCES `unidades`(`id`) ON DELETE RESTRICT ON UPDATE CASCADE)'
        );

        $this->copiarDados('venda_produtos', [
            'id', 'cliente_id', 'loja_id', 'produto_id', 'data',
            'quantidade', 'preco_unitario', 'created_at', 'updated_at', 'unidade_id',
        ]);
    }

    private function recriarEstoqueMovimentos(): void
    {
        $this->db->query('DROP TABLE IF EXISTS `estoque_movimentos__novo`');

        $this->db->query(
            'CREATE TABLE `estoque_movimentos__novo` ('
            . '`id` INTEGER PRIMARY KEY AUTOINCREMENT, '
            . '`loja_id` INT NOT NULL, '
            . '`produto_id` INT NOT NULL, '
            . '`venda_id` INT NULL, '
            . '`tipo` VARCHAR NOT NULL, '
            . '`origem` VARCHAR NOT NULL, '
            . '`quantidade` INT NOT NULL, '
            . '`saldo_apos` INT NULL, '
            . '`observacao` VARCHAR NULL, '
            . '`created_at` DATETIME NULL, '
            . '`updated_at` DATETIME NULL, '
            . 'CONSTRAINT `estoque_movimentos_loja_id_foreign` FOREIGN KEY (`loja_id`) '
            . 'REFERENCES `lojas`(`id`) ON DELETE RESTRICT ON UPDATE CASCADE, '
            . 'CONSTRAINT `estoque_movimentos_produto_id_foreign` FOREIGN KEY (`produto_id`) '
            . 'REFERENCES `produtos`(`id`) ON DELETE RESTRICT ON UPDATE CASCADE, '
            . 'CONSTRAINT `estoque_movimentos_venda_id_foreign` FOREIGN KEY (`venda_id`) '
            . 'REFERENCES `venda_produtos`(`id`) ON DELETE SET NULL ON UPDATE CASCADE)'
        );

        $this->copiarDados('estoque_movimentos', [
            'id', 'loja_id', 'produto_id', 'venda_id', 'tipo', 'origem',
            'quantidade', 'saldo_apos', 'observacao', 'created_at', 'updated_at',
        ]);

        $this->db->query(
            'CREATE INDEX `estoque_movimentos_loja_id_produto_id` '
            . 'ON `estoque_movimentos` (`loja_id`, `produto_id`)'
        );
    }

    /**
     * Copia os dados para a tabela nova, troca as tabelas e recria os índices.
     *
     * @param list<string> $colunas
     */
    private function copiarDados(string $tabela, array $colunas): void
    {
        $lista = implode(', ', array_map(static fn (string $coluna): string => '`' . $coluna . '`', $colunas));

        $this->db->query("INSERT INTO `{$tabela}__novo` ({$lista}) SELECT {$lista} FROM `{$tabela}`");
        $this->db->query("DROP TABLE `{$tabela}`");
        $this->db->query("ALTER TABLE `{$tabela}__novo` RENAME TO `{$tabela}`");
    }
}
