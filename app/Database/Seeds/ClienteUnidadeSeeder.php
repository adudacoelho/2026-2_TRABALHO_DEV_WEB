<?php

namespace App\Database\Seeds;

use App\Models\ClienteModel;
use App\Models\ClienteUnidadeModel;
use App\Models\UnidadeModel;
use CodeIgniter\Database\Seeder;

class ClienteUnidadeSeeder extends Seeder
{
    public function run()
    {
        $model = new ClienteUnidadeModel();
        $clienteModel = new ClienteModel();
        $unidadeModel = new UnidadeModel();

        // Uma pessoa pode ter mais de uma unidade ativa.
        $vinculos = [
            ['Camila Souza', 'Apto 302'],
            ['Camila Souza', 'Apto 501'],
            ['Rafael Lima', 'Apto 101'],
            ['Rafael Lima', 'Box 12'],
        ];

        foreach ($vinculos as [$nomeCliente, $identificador]) {
            $cliente = $clienteModel->where('nome', $nomeCliente)->first();
            $unidade = $unidadeModel->where('identificador', $identificador)->first();

            if ($cliente === null || $unidade === null) {
                continue;
            }

            $existe = $model
                ->where('cliente_id', $cliente['id'])
                ->where('unidade_id', $unidade['id'])
                ->first();

            if ($existe === null) {
                $model->insert([
                    'cliente_id' => $cliente['id'],
                    'unidade_id' => $unidade['id'],
                ]);
            }
        }
    }
}
