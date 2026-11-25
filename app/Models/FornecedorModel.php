<?php

namespace App\Models;

use CodeIgniter\Model;

class FornecedorModel extends Model
{
    protected $table         = 'fornecedores';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $allowedFields = ['nome', 'descricao', 'cnpj', 'telefone', 'endereco', 'email'];
    protected $useTimestamps = true;

    protected $validationRules = [
        'id'        => 'permit_empty|is_natural_no_zero',
        'nome'      => 'required|min_length[2]|max_length[150]',
        'descricao' => 'permit_empty|max_length[255]',
        'cnpj'      => 'required|exact_length[14]|alpha_numeric|cnpj_valido|is_unique[fornecedores.cnpj,id,{id}]',
        'telefone'  => 'permit_empty|numeric|min_length[10]|max_length[11]',
        'endereco'  => 'permit_empty|max_length[255]',
        'email'     => 'permit_empty|valid_email|max_length[150]',
    ];

    protected $validationMessages = [
        'nome' => [
            'required'   => 'Informe o nome ou a razão social do fornecedor.',
            'min_length' => 'O nome deve ter pelo menos 2 caracteres.',
            'max_length' => 'O nome deve ter no máximo 150 caracteres.',
        ],
        'descricao' => [
            'max_length' => 'A descrição deve ter no máximo 255 caracteres.',
        ],
        'cnpj' => [
            'required'      => 'Informe o CNPJ.',
            'exact_length'  => 'O CNPJ deve ter 14 caracteres.',
            'alpha_numeric' => 'O CNPJ deve conter apenas letras e números.',
            'is_unique'     => 'Já existe um fornecedor com esse CNPJ.',
        ],
        'telefone' => [
            'numeric'    => 'O telefone deve conter apenas números.',
            'min_length' => 'O telefone deve ter DDD e número (10 ou 11 dígitos).',
            'max_length' => 'O telefone deve ter DDD e número (10 ou 11 dígitos).',
        ],
        'endereco' => [
            'max_length' => 'O endereço deve ter no máximo 255 caracteres.',
        ],
        'email' => [
            'valid_email' => 'Informe um e-mail válido.',
            'max_length'  => 'O e-mail deve ter no máximo 150 caracteres.',
        ],
    ];

    /**
     * Lista os fornecedores em ordem alfabética, com busca opcional por nome ou CNPJ.
     */
    public function listar(?string $busca = null): array
    {
        if ($busca !== null && $busca !== '') {
            $this->groupStart()->like('nome', $busca);

            // Só procura no CNPJ se o texto digitado parece um CNPJ
            // (números, letras, pontos, barra e traço).
            if (preg_match('/^[\dA-Za-z.\-\/\s]+$/', $busca) === 1) {
                $this->orLike('cnpj', normalizar_cnpj($busca));
            }

            $this->groupEnd();
        }

        return $this->orderBy('nome', 'ASC')->findAll();
    }
}
