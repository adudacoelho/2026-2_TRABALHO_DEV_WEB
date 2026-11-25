<?php

namespace App\Models;

use CodeIgniter\Model;

class FranquiaModel extends Model
{
    protected $table         = 'franquias';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $allowedFields = ['nome'];
    protected $useTimestamps = true;

    protected $validationRules = [
        'id'   => 'permit_empty|is_natural_no_zero',
        'nome' => 'required|min_length[2]|max_length[150]|is_unique[franquias.nome,id,{id}]',
    ];

    protected $validationMessages = [
        'nome' => [
            'required'   => 'Informe o nome da franquia.',
            'min_length' => 'O nome deve ter pelo menos 2 caracteres.',
            'max_length' => 'O nome deve ter no máximo 150 caracteres.',
            'is_unique'  => 'Já existe uma franquia com esse nome.',
        ],
    ];

    /**
     * Lista as franquias em ordem alfabética, com busca opcional por nome.
     */
    public function listar(?string $busca = null): array
    {
        if ($busca !== null && $busca !== '') {
            $this->like('nome', $busca);
        }

        return $this->orderBy('nome', 'ASC')->findAll();
    }

    /**
     * Indica se a franquia já possui lojas vinculadas.
     */
    public function possuiDependencias(int $id): bool
    {
        return $this->db->table('lojas')->where('franquia_id', $id)->countAllResults() > 0;
    }
}
