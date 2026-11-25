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
            'cpf'      => '03544978474',
            'telefone' => '27999991111',
            'email'    => 'camila@example.com',
        ]);

        $model->insert([
            'nome' => 'Rafael Lima',
            'cpf'  => '00882570706',
            'telefone' => '27999992222',
            'email'    => 'rafael@exemplo.com',
        ]);

         $model->insert([
            'nome' => 'Fox Mulder',
            'cpf'  => '64005953921',
            'telefone' => '27999953222',
            'email'    => 'fox.mulder@fbi.com',
        ]);

         $model->insert([
            'nome' => 'Dana Scully',
            'cpf'  => '49428340316',
            'telefone' => '27999497222',
            'email'    => 'dana.scully@fbi.com',
        ]);
    }
}
