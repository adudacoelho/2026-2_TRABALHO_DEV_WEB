<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class MigrarUnidadesClientes extends Migration
{
    /**
     * Transforma o texto de clientes.unidade em registros de unidades e vínculos,
     * preservando os dados já cadastrados antes de remover a coluna antiga.
     */
    public function up()
    {
        $loja = $this->db->table('lojas')->orderBy('id', 'ASC')->get()->getRowArray();
        $lojaId = $loja['id'] ?? null;

        $clientes = $this->db->table('clientes')
            ->select('id, unidade')
            ->where('unidade IS NOT NULL')
            ->where('unidade !=', '')
            ->get()
            ->getResultArray();

        $agora = date('Y-m-d H:i:s');

        foreach ($clientes as $cliente) {
            $identificador = trim($cliente['unidade']);
            if ($identificador === '') {
                continue;
            }

            $unidade = $this->db->table('unidades')
                ->where('identificador', $identificador)
                ->where('loja_id', $lojaId)
                ->get()
                ->getRowArray();

            if ($unidade === null) {
                $this->db->table('unidades')->insert([
                    'identificador' => $identificador,
                    'loja_id'       => $lojaId,
                    'created_at'    => $agora,
                    'updated_at'    => $agora,
                ]);
                $unidadeId = $this->db->insertID();
            } else {
                $unidadeId = $unidade['id'];
            }

            $vinculo = $this->db->table('cliente_unidades')
                ->where('cliente_id', $cliente['id'])
                ->where('unidade_id', $unidadeId)
                ->get()
                ->getRowArray();

            if ($vinculo === null) {
                $this->db->table('cliente_unidades')->insert([
                    'cliente_id' => $cliente['id'],
                    'unidade_id' => $unidadeId,
                    'created_at' => $agora,
                    'updated_at' => $agora,
                ]);
            }
        }

        // O Forge do SQLite recria a tabela renomeando-a para "temp_clientes".
        // Com o modo legado ligado, o SQLite não reescreve as FKs de
        // cliente_unidades/venda_produtos para apontar para a tabela temporária.
        $this->modoLegado(true);
        $this->forge->dropColumn('clientes', 'unidade');
        $this->modoLegado(false);
    }

    public function down()
    {
        $this->modoLegado(true);
        $this->forge->addColumn('clientes', [
            'unidade' => [
                'type'       => 'VARCHAR',
                'constraint' => 30,
                'null'       => true,
            ],
        ]);
        $this->modoLegado(false);

        // Sem alias de tabela: sintaxe aceita por SQLite e MySQL.
        $this->db->query(
            'UPDATE `clientes` SET `unidade` = ('
            . 'SELECT u.`identificador` FROM `cliente_unidades` cu '
            . 'JOIN `unidades` u ON u.`id` = cu.`unidade_id` '
            . 'WHERE cu.`cliente_id` = `clientes`.`id` LIMIT 1)'
        );
    }

    /**
     * Liga/desliga o comportamento legado do ALTER TABLE do SQLite, que evita
     * reescrever as FKs das outras tabelas durante a recriação da tabela.
     */
    private function modoLegado(bool $ativo): void
    {
        if ($this->db->DBDriver === 'SQLite3') {
            $this->db->query('PRAGMA legacy_alter_table = ' . ($ativo ? 'ON' : 'OFF'));
        }
    }
}
