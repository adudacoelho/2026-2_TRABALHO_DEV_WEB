<?php

namespace App\Database\Seeds;

use App\Models\ClienteModel;
use App\Models\LojaModel;
use App\Models\ProdutoModel;
use App\Models\UnidadeModel;
use App\Models\VendaProdutoModel;
use CodeIgniter\Database\Seeder;

class VendaProdutoSeeder extends Seeder
{
    public function run()
    {
        $model = new VendaProdutoModel();

        // Só popula se a tabela estiver vazia, assim o seeder pode rodar várias vezes.
        if ($model->countAllResults() > 0) {
            return;
        }

        $clienteModel = new ClienteModel();
        $lojaModel    = new LojaModel();
        $produtoModel = new ProdutoModel();
        $unidadeModel = new UnidadeModel();

        $vendas = [
            ['Camila Souza', 'Loja Centro', 'Apto 302', 'Barrinha de Cereal', '2026-11-20 09:35:00', 2, '4.50'],
            ['Camila Souza', 'Loja Shopping', 'Apto 501', 'Whey Protein 20g', '2026-11-21 12:10:00', 1, '8.90'],
            ['Rafael Lima', 'Loja Terminal', 'Box 12', 'Água Mineral 500ml', '2026-11-21 18:42:00', 3, '3.00'],
        ];

        foreach ($vendas as [$nomeCliente, $nomeLoja, $identificadorUnidade, $nomeProduto, $data, $quantidade, $precoUnitario]) {
            $cliente = $clienteModel->where('nome', $nomeCliente)->first();
            $loja    = $lojaModel->where('nome', $nomeLoja)->first();
            $produto = $produtoModel->where('nome', $nomeProduto)->first();
            $unidade = $unidadeModel->where('identificador', $identificadorUnidade)->first();

            // Usa o método transacional: registra a venda e já baixa o estoque da loja.
            $model->registrarVenda([
                'cliente_id'     => $cliente['id'] ?? null,
                'loja_id'        => $loja['id'] ?? null,
                'produto_id'     => $produto['id'] ?? null,
                'unidade_id'     => $unidade['id'] ?? null,
                'data'           => $data,
                'quantidade'     => $quantidade,
                'preco_unitario' => $precoUnitario,
            ]);
        }
    }
}
