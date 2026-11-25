<?php

namespace App\Models;

use CodeIgniter\Model;

class UnidadeModel extends Model
{
    protected $table         = 'unidades';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $allowedFields = ['identificador', 'loja_id'];
    protected $useTimestamps = true;

    protected $validationRules = [
        'id'            => 'permit_empty|is_natural_no_zero',
        'identificador' => 'required|min_length[2]|max_length[50]',
        'loja_id'       => 'permit_empty|is_natural_no_zero|is_not_unique[lojas.id]',
    ];

    protected $validationMessages = [
        'identificador' => [
            'required'   => 'Informe o identificador da unidade.',
            'min_length' => 'O identificador deve ter pelo menos 2 caracteres.',
            'max_length' => 'O identificador deve ter no máximo 50 caracteres.',
        ],
        'loja_id' => [
            'is_natural_no_zero' => 'A loja informada é inválida.',
            'is_not_unique'      => 'A loja informada não existe.',
        ],
    ];

    /**
     * Indica se já existe uma unidade com o mesmo identificador na loja.
     * O unique do banco é (loja_id, identificador).
     */
    public function identificadorDuplicado(int $lojaId, string $identificador, ?int $idIgnorar = null): bool
    {
        $builder = $this->db->table($this->table)
            ->where('loja_id', $lojaId)
            ->where('identificador', $identificador);

        if ($idIgnorar !== null) {
            $builder->where('id !=', $idIgnorar);
        }

        return $builder->countAllResults() > 0;
    }

    /**
     * Indica se a unidade já é usada por vínculos ou vendas.
     */
    public function possuiDependencias(int $id): bool
    {
        return $this->db->table('cliente_unidades')->where('unidade_id', $id)->countAllResults() > 0
            || $this->db->table('venda_produtos')->where('unidade_id', $id)->countAllResults() > 0;
    }

    /**
     * Lista as unidades com o nome da loja, com busca opcional.
     */
    public function listar(?string $busca = null): array
    {
        $this->select('unidades.*, lojas.nome AS loja_nome')
            ->join('lojas', 'lojas.id = unidades.loja_id', 'left');

        if ($busca !== null && $busca !== '') {
            $this->groupStart()
                ->like('unidades.identificador', $busca)
                ->orLike('lojas.nome', $busca)
                ->groupEnd();
        }

        return $this->orderBy('lojas.nome', 'ASC')
            ->orderBy('unidades.identificador', 'ASC')
            ->findAll();
    }
}
