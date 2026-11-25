<?php

namespace App\Models;

use CodeIgniter\Model;

class ClienteModel extends Model
{
    protected $table         = 'clientes';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $allowedFields = ['nome', 'cpf', 'telefone', 'email'];
    protected $useTimestamps = true;

    protected $validationRules = [
        'id'       => 'permit_empty|is_natural_no_zero',
        'nome'     => 'required|min_length[2]|max_length[150]',
        'cpf'      => 'required|numeric|exact_length[11]|cpf_valido|is_unique[clientes.cpf,id,{id}]',
        'telefone' => 'permit_empty|numeric|min_length[10]|max_length[11]',
        'email'    => 'permit_empty|valid_email|max_length[150]',
    ];

    protected $validationMessages = [
        'nome' => [
            'required'   => 'Informe o nome do cliente.',
            'min_length' => 'O nome deve ter pelo menos 2 caracteres.',
            'max_length' => 'O nome deve ter no máximo 150 caracteres.',
        ],
        'cpf' => [
            'required'     => 'Informe o CPF.',
            'numeric'      => 'O CPF deve conter apenas números.',
            'exact_length' => 'O CPF deve ter 11 números.',
            'is_unique'    => 'Já existe um cliente com esse CPF.',
        ],
        'telefone' => [
            'numeric'    => 'O telefone deve conter apenas números.',
            'min_length' => 'O telefone deve ter DDD e número (10 ou 11 dígitos).',
            'max_length' => 'O telefone deve ter DDD e número (10 ou 11 dígitos).',
        ],
        'email' => [
            'valid_email' => 'Informe um e-mail válido.',
            'max_length'  => 'O e-mail deve ter no máximo 150 caracteres.',
        ],
    ];

    /**
     * Lista os clientes em ordem alfabética, com as unidades associadas e busca opcional.
     */
    public function listar(?string $busca = null): array
    {
        $this->select("clientes.*, GROUP_CONCAT(unidades.identificador, ', ') AS unidades_nome")
            ->join('cliente_unidades', 'cliente_unidades.cliente_id = clientes.id', 'left')
            ->join('unidades', 'unidades.id = cliente_unidades.unidade_id', 'left')
            ->groupBy('clientes.id');

        if ($busca !== null && $busca !== '') {
            $this->groupStart()
                ->like('clientes.nome', $busca)
                ->orLike('unidades.identificador', $busca);

            // Só procura no CPF se o texto digitado parece um CPF (números, pontos e traço).
            if (preg_match('/^[\d.\-\s]+$/', $busca)) {
                $this->orLike('clientes.cpf', preg_replace('/\D/', '', $busca));
            }

            $this->groupEnd();
        }

        return $this->orderBy('clientes.nome', 'ASC')->findAll();
    }

    /**
     * Indica se o cliente já possui vínculos com unidades ou vendas.
     */
    public function possuiDependencias(int $id): bool
    {
        return $this->db->table('cliente_unidades')->where('cliente_id', $id)->countAllResults() > 0
            || $this->db->table('venda_produtos')->where('cliente_id', $id)->countAllResults() > 0;
    }
}
