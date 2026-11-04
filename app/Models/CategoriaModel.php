<?php

namespace App\Models;

use CodeIgniter\Model;

class CategoriaModel extends Model
{
    protected $table         = 'categorias';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $allowedFields = ['nome'];
    protected $useTimestamps = true;

    protected $validationRules = [
        'id'   => 'permit_empty|is_natural_no_zero',
        'nome' => 'required|min_length[2]|max_length[100]|is_unique[categorias.nome,id,{id}]',
    ];

    protected $validationMessages = [
        'nome' => [
            'required'   => 'Informe o nome da categoria.',
            'min_length' => 'O nome da categoria deve ter pelo menos 2 caracteres.',
            'max_length' => 'O nome da categoria deve ter no máximo 100 caracteres.',
            'is_unique'  => 'Já existe uma categoria com esse nome.',
        ],
    ];

    /**
     * Lista as categorias em ordem alfabética, com busca opcional pelo nome.
     */
    public function listar(?string $busca = null): array
    {
        if ($busca !== null && $busca !== '') {
            $this->like('nome', $busca);
        }

        return $this->orderBy('nome', 'ASC')->findAll();
    }

    /**
     * Indica se existe algum produto usando esta categoria.
     */
    public function possuiProdutos(int $id): bool
    {
        return $this->db->table('produtos')->where('categoria_id', $id)->countAllResults() > 0;
    }
}
