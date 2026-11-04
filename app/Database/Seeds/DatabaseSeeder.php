<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call('CategoriaSeeder');
        $this->call('ProdutoSeeder');
        $this->call('FornecedorSeeder');
        $this->call('ClienteSeeder');
    }
}
