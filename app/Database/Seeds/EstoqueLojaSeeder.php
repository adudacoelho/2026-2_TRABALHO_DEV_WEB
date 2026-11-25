<?php

namespace App\Database\Seeds;

use App\Models\EstoqueLojaModel;
use App\Models\LojaModel;
use App\Models\ProdutoModel;
use CodeIgniter\Database\Seeder;

class EstoqueLojaSeeder extends Seeder
{
    public function run()
    {
        $model = new EstoqueLojaModel();

        // Só popula se a tabela estiver vazia, assim o seeder pode rodar várias vezes.
        if ($model->countAllResults() > 0) {
            return;
        }

        $lojas    = (new LojaModel())->orderBy('id', 'ASC')->findAll();
        $produtos = (new ProdutoModel())->orderBy('id', 'ASC')->findAll();

        // Quantidade determinística: varia por loja e por produto.
        foreach ($lojas as $i => $loja) {
            foreach ($produtos as $j => $produto) {
                // Usa o método transacional para gerar o movimento de entrada inicial.
                $model->registrarEntrada([
                    'loja_id'    => $loja['id'],
                    'produto_id' => $produto['id'],
                    'quantidade' => ($i + 1) * 5 + $j * 2,
                ]);
            }
        }
    }
}
