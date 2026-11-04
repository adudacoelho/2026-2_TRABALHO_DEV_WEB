<?php

namespace App\Models;

use CodeIgniter\Model;

class ClienteModel extends Model
{
    protected $table         = 'clientes';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $allowedFields = ['nome', 'cpf', 'telefone', 'email', 'unidade'];
    protected $useTimestamps = true;

    protected $validationRules = [
        'id'       => 'permit_empty|is_natural_no_zero',
        'nome'     => 'required|min_length[2]|max_length[150]',
        'cpf'      => 'required|numeric|exact_length[11]|is_unique[clientes.cpf,id,{id}]',
        'telefone' => 'permit_empty|numeric|min_length[10]|max_length[11]',
        'email'    => 'permit_empty|valid_email|max_length[150]',
        'unidade'  => 'required|max_length[30]',
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
        'unidade' => [
            'required'   => 'Informe a unidade (por exemplo, Apto 302).',
            'max_length' => 'A unidade deve ter no máximo 30 caracteres.',
        ],
    ];

    /**
     * Lista os clientes em ordem alfabética, com busca opcional por nome, unidade ou CPF.
     */
    public function listar(?string $busca = null): array
    {
        if ($busca !== null && $busca !== '') {
            $this->groupStart()
                ->like('nome', $busca)
                ->orLike('unidade', $busca);

            // Só procura no CPF se o texto digitado parece um CPF (números, pontos e traço).
            if (preg_match('/^[\d.\-\s]+$/', $busca)) {
                $this->orLike('cpf', preg_replace('/\D/', '', $busca));
            }

            $this->groupEnd();
        }

        return $this->orderBy('nome', 'ASC')->findAll();
    }
}
