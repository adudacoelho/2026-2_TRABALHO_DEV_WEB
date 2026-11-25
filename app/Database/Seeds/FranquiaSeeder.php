<?php

namespace App\Database\Seeds;

use App\Models\FranquiaModel;
use CodeIgniter\Database\Seeder;

class FranquiaSeeder extends Seeder
{
    public function run()
    {
        $model = new FranquiaModel();

        // Só popula se a tabela estiver vazia, assim o seeder pode rodar várias vezes.
        if ($model->countAllResults() > 0) {
            return;
        }

        foreach (['Matriz', 'Zona Norte', 'Zona Sul'] as $nome) {
            $model->insert(['nome' => $nome]);
        }
    }
}
