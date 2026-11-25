<?php

namespace App\Models;

use CodeIgniter\Model;

class LojaModel extends Model
{
    protected $table         = 'lojas';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $allowedFields = ['nome', 'endereco', 'franquia_id'];
    protected $useTimestamps = true;

    protected $validationRules = [
        'id'          => 'permit_empty|is_natural_no_zero',
        'nome'        => 'required|min_length[2]|max_length[150]',
        'endereco'    => 'permit_empty|max_length[255]',
        'franquia_id' => 'required|is_natural_no_zero|is_not_unique[franquias.id]',
    ];

    protected $validationMessages = [
        'nome' => [
            'required'   => 'Informe o nome da loja.',
            'min_length' => 'O nome deve ter pelo menos 2 caracteres.',
            'max_length' => 'O nome deve ter no máximo 150 caracteres.',
        ],
        'endereco' => [
            'max_length' => 'O endereço deve ter no máximo 255 caracteres.',
        ],
        'franquia_id' => [
            'required'           => 'Informe a franquia.',
            'is_natural_no_zero' => 'A franquia informada é inválida.',
            'is_not_unique'      => 'A franquia informada não existe.',
        ],
    ];

    /**
     * Lista as lojas em ordem alfabética, com busca opcional por nome, franquia ou endereço.
     */
    public function listar(?string $busca = null): array
    {
        $this->select('lojas.*, franquias.nome AS franquia_nome')
            ->join('franquias', 'franquias.id = lojas.franquia_id', 'left');

        if ($busca !== null && $busca !== '') {
            $this->groupStart()
                ->like('lojas.nome', $busca)
                ->orLike('franquias.nome', $busca)
                ->orLike('lojas.endereco', $busca)
                ->groupEnd();
        }

        return $this->orderBy('lojas.nome', 'ASC')->findAll();
    }

    /**
     * Indica se a loja já possui unidades, estoque, vendas ou movimentações.
     */
    public function possuiDependencias(int $id): bool
    {
        return $this->db->table('unidades')->where('loja_id', $id)->countAllResults() > 0
            || $this->db->table('estoque_lojas')->where('loja_id', $id)->countAllResults() > 0
            || $this->db->table('venda_produtos')->where('loja_id', $id)->countAllResults() > 0
            || $this->db->table('estoque_movimentos')->where('loja_id', $id)->countAllResults() > 0;
    }
}
