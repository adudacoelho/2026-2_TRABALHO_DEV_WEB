<?php

namespace App\Database\Seeds;

use App\Models\CategoriaModel;
use CodeIgniter\Database\Seeder;

class CategoriaSeeder extends Seeder
{
    public function run()
    {
        $model = new CategoriaModel();

        // Só popula se a tabela estiver vazia, assim o seeder pode rodar várias vezes.
        if ($model->countAllResults() > 0) {
            return;
        }

        foreach (['Barrinha', 'Whey', 'Bebidas', 'Higiene', 'Lanches'] as $nome) {
            $model->insert(['nome' => $nome]);
        }
    }
}
