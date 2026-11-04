<?php

namespace App\Models;

use CodeIgniter\Model;

class ProdutoModel extends Model
{
    protected $table         = 'produtos';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $allowedFields = ['nome', 'descricao', 'preco', 'quantidade', 'categoria_id'];
    protected $useTimestamps = true;

    protected $validationRules = [
        'id'           => 'permit_empty|is_natural_no_zero',
        'nome'         => 'required|min_length[2]|max_length[100]',
        'descricao'    => 'permit_empty|max_length[255]',
        'preco'        => 'required|decimal|greater_than[0]|less_than[100000]',
        'quantidade'   => 'required|is_natural|less_than[100000]',
        'categoria_id' => 'permit_empty|is_natural_no_zero|is_not_unique[categorias.id]',
    ];

    protected $validationMessages = [
        'nome' => [
            'required'   => 'Informe o nome do produto.',
            'min_length' => 'O nome do produto deve ter pelo menos 2 caracteres.',
            'max_length' => 'O nome do produto deve ter no máximo 100 caracteres.',
        ],
        'descricao' => [
            'max_length' => 'A descrição deve ter no máximo 255 caracteres.',
        ],
        'preco' => [
            'required'     => 'Informe o preço do produto.',
            'decimal'      => 'Informe um preço válido, por exemplo 12.50.',
            'greater_than' => 'O preço deve ser maior que zero.',
            'less_than'    => 'O preço deve ser menor que 100.000.',
        ],
        'quantidade' => [
            'required'   => 'Informe a quantidade em estoque.',
            'is_natural' => 'A quantidade deve ser um número inteiro, de zero para cima.',
            'less_than'  => 'A quantidade deve ser menor que 100.000.',
        ],
        'categoria_id' => [
            'is_not_unique' => 'A categoria escolhida não existe.',
        ],
    ];

    /**
     * Lista os produtos com o nome da categoria, com busca opcional por nome ou descrição.
     */
    public function listar(?string $busca = null): array
    {
        $this->select('produtos.*, categorias.nome AS categoria_nome')
            ->join('categorias', 'categorias.id = produtos.categoria_id', 'left');

        if ($busca !== null && $busca !== '') {
            $this->groupStart()
                ->like('produtos.nome', $busca)
                ->orLike('produtos.descricao', $busca)
                ->groupEnd();
        }

        return $this->orderBy('produtos.nome', 'ASC')->findAll();
    }
}
