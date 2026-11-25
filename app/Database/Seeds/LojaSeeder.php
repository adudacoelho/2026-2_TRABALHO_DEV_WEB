<?php

namespace App\Database\Seeds;

use App\Models\FranquiaModel;
use App\Models\LojaModel;
use CodeIgniter\Database\Seeder;

class LojaSeeder extends Seeder
{
    public function run()
    {
        $model = new LojaModel();

        // Só popula se a tabela estiver vazia, assim o seeder pode rodar várias vezes.
        if ($model->countAllResults() > 0) {
            return;
        }

        $franquiaModel = new FranquiaModel();

        $lojas = [
            ['Loja Centro', 'Rua das Flores, 100', 'Matriz'],
            ['Loja Shopping', 'Avenida Brasil, 500', 'Zona Norte'],
            ['Loja Terminal', 'Rua do Porto, 25', 'Zona Sul'],
        ];

        foreach ($lojas as [$nome, $endereco, $nomeFranquia]) {
            $franquia = $franquiaModel->where('nome', $nomeFranquia)->first();

            $model->insert([
                'nome'        => $nome,
                'endereco'    => $endereco,
                'franquia_id' => $franquia['id'] ?? null,
            ]);
        }
    }
}
