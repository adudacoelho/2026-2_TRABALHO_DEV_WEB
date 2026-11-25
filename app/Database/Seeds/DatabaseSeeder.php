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
        $this->call('FranquiaSeeder');
        $this->call('LojaSeeder');
        $this->call('UnidadeSeeder');
        $this->call('ClienteUnidadeSeeder');
        $this->call('EstoqueLojaSeeder');
        $this->call('VendaProdutoSeeder');
    }
}
