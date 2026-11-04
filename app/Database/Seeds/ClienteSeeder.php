<?php

namespace App\Database\Seeds;

use App\Models\ClienteModel;
use CodeIgniter\Database\Seeder;

class ClienteSeeder extends Seeder
{
    public function run()
    {
        $model = new ClienteModel();

        if ($model->countAllResults() > 0) {
            return;
        }

        $model->insert([
            'nome'     => 'Camila Souza',
            'cpf'      => '52998224725',
            'telefone' => '27999991111',
            'email'    => 'camila@example.com',
            'unidade'  => 'Apto 302',
        ]);

        $model->insert([
            'nome'    => 'Rafael Lima',
            'cpf'     => '11144477735',
            'unidade' => 'Apto 101',
        ]);
    }
}
