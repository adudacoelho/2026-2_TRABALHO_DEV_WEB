<?php

namespace App\Database\Seeds;

use App\Models\LojaModel;
use App\Models\UnidadeModel;
use CodeIgniter\Database\Seeder;

class UnidadeSeeder extends Seeder
{
    public function run()
    {
        $model = new UnidadeModel();
        $lojaModel = new LojaModel();

        $unidades = [
            ['Apto 302', 'Loja Centro'],
            ['Apto 101', 'Loja Centro'],
            ['Apto 501', 'Loja Shopping'],
            ['Box 12', 'Loja Terminal'],
        ];

        // Verifica unidade por unidade para não duplicar dados já migrados.
        foreach ($unidades as [$identificador, $nomeLoja]) {
            $loja = $lojaModel->where('nome', $nomeLoja)->first();

            if ($loja === null) {
                continue;
            }

            $existe = $model
                ->where('identificador', $identificador)
                ->where('loja_id', $loja['id'])
                ->first();

            if ($existe === null) {
                $model->insert([
                    'identificador' => $identificador,
                    'loja_id'       => $loja['id'],
                ]);
            }
        }
    }
}
