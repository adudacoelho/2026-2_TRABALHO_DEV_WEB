<?php

namespace App\Database\Seeds;

use App\Models\CategoriaModel;
use App\Models\ProdutoModel;
use CodeIgniter\Database\Seeder;

class ProdutoSeeder extends Seeder
{
    public function run()
    {
        $produtoModel = new ProdutoModel();

        if ($produtoModel->countAllResults() > 0) {
            return;
        }

        $categoriaModel = new CategoriaModel();

        $produtos = [
            ['Barrinha de Cereal', 'Barrinha de cereal 25g', '4.50', 20, 'Barrinha'],
            ['Whey Protein 20g', 'Sachê individual', '8.90', 15, 'Whey'],
            ['Água Mineral 500ml', 'Sem gás', '3.00', 40, 'Bebidas'],
            ['Sabonete', 'Sabonete em barra 85g', '2.50', 12, 'Higiene'],
            ['Salgadinho', 'Pacote 50g', '5.00', 0, 'Lanches'],
        ];

        foreach ($produtos as [$nome, $descricao, $preco, $quantidade, $nomeCategoria]) {
            $categoria = $categoriaModel->where('nome', $nomeCategoria)->first();

            $produtoModel->insert([
                'nome'         => $nome,
                'descricao'    => $descricao,
                'preco'        => $preco,
                'quantidade'   => $quantidade,
                'categoria_id' => $categoria['id'] ?? null,
            ]);
        }
    }
}
