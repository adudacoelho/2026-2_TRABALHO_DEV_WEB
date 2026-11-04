<?php

namespace App\Database\Seeds;

use App\Models\FornecedorModel;
use CodeIgniter\Database\Seeder;

class FornecedorSeeder extends Seeder
{
    public function run()
    {
        $model = new FornecedorModel();

        if ($model->countAllResults() > 0) {
            return;
        }

        $model->insert([
            'nome'     => 'Distribuidora Boa Compra',
            'cnpj'     => '11222333000181',
            'telefone' => '27999980000',
            'endereco' => 'Rua das Flores, 100, Serra',
            'email'    => 'contato@boacompra.example.com',
        ]);

        $model->insert([
            'nome'     => 'Atacado Grande Vitória',
            'cnpj'     => '45723174000110',
            'telefone' => '2733330000',
            'endereco' => 'Av. Central, 500, Vitória',
        ]);
    }
}
