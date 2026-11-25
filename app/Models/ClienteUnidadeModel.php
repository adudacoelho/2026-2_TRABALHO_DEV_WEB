<?php

namespace App\Models;

use CodeIgniter\Model;

class ClienteUnidadeModel extends Model
{
    protected $table         = 'cliente_unidades';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $allowedFields = ['cliente_id', 'unidade_id'];
    protected $useTimestamps = true;

    protected $validationRules = [
        'id'         => 'permit_empty|is_natural_no_zero',
        'cliente_id' => 'required|is_natural_no_zero|is_not_unique[clientes.id]',
        'unidade_id' => 'required|is_natural_no_zero|is_not_unique[unidades.id]',
    ];

    protected $validationMessages = [
        'cliente_id' => [
            'required'           => 'Informe o cliente.',
            'is_natural_no_zero' => 'O cliente informado é inválido.',
            'is_not_unique'      => 'O cliente informado não existe.',
        ],
        'unidade_id' => [
            'required'           => 'Informe a unidade.',
            'is_natural_no_zero' => 'A unidade informada é inválida.',
            'is_not_unique'      => 'A unidade informada não existe.',
        ],
    ];

    /**
     * Indica se o vínculo cliente/unidade já existe.
     */
    public function vinculoExiste(int $clienteId, int $unidadeId, ?int $idIgnorar = null): bool
    {
        $builder = $this->db->table($this->table)
            ->where('cliente_id', $clienteId)
            ->where('unidade_id', $unidadeId);

        if ($idIgnorar !== null) {
            $builder->where('id !=', $idIgnorar);
        }

        return $builder->countAllResults() > 0;
    }

    /**
     * Indica se o cliente possui vínculo com a unidade dentro da loja informada.
     */
    public function clientePossuiUnidadeNaLoja(int $clienteId, int $unidadeId, int $lojaId): bool
    {
        return $this->db->table($this->table)
            ->join('unidades', 'unidades.id = cliente_unidades.unidade_id')
            ->where('cliente_unidades.cliente_id', $clienteId)
            ->where('cliente_unidades.unidade_id', $unidadeId)
            ->where('unidades.loja_id', $lojaId)
            ->countAllResults() > 0;
    }

    /**
     * Valida a combinação cliente/loja/unidade usada em uma venda.
     *
     * @return string|null Mensagem de erro, ou null quando o vínculo é válido.
     */
    public function validarVinculo(int $clienteId, int $lojaId, int $unidadeId): ?string
    {
        if ($clienteId <= 0) {
            return 'Informe o cliente.';
        }

        if ($lojaId <= 0) {
            return 'Informe a loja.';
        }

        if ($unidadeId <= 0) {
            return 'Informe a unidade.';
        }

        if ($this->db->table('clientes')->where('id', $clienteId)->countAllResults() === 0) {
            return 'O cliente informado não existe.';
        }

        if ($this->db->table('lojas')->where('id', $lojaId)->countAllResults() === 0) {
            return 'A loja informada não existe.';
        }

        $unidade = $this->db->table('unidades')
            ->select('loja_id')
            ->where('id', $unidadeId)
            ->get()
            ->getRowArray();

        if ($unidade === null) {
            return 'A unidade informada não existe.';
        }

        if ((int) $unidade['loja_id'] !== $lojaId) {
            return 'A unidade informada não pertence à loja escolhida.';
        }

        if (! $this->clientePossuiUnidadeNaLoja($clienteId, $unidadeId, $lojaId)) {
            return 'O cliente não possui cadastro nesta unidade.';
        }

        return null;
    }

    /**
     * Indica se o cliente possui algum vínculo cadastrado.
     */
    public function possuiVinculosDoCliente(int $clienteId): bool
    {
        return $this->db->table($this->table)->where('cliente_id', $clienteId)->countAllResults() > 0;
    }

    /**
     * Indica se a unidade possui algum vínculo cadastrado.
     */
    public function possuiVinculosDaUnidade(int $unidadeId): bool
    {
        return $this->db->table($this->table)->where('unidade_id', $unidadeId)->countAllResults() > 0;
    }

    /**
     * Lojas onde o cliente possui pelo menos uma unidade vinculada.
     */
    public function lojasDoCliente(int $clienteId): array
    {
        return $this->db->table('cliente_unidades')
            ->distinct()
            ->select('lojas.id, lojas.nome')
            ->join('unidades', 'unidades.id = cliente_unidades.unidade_id')
            ->join('lojas', 'lojas.id = unidades.loja_id')
            ->where('cliente_unidades.cliente_id', $clienteId)
            ->orderBy('lojas.nome', 'ASC')
            ->get()
            ->getResultArray();
    }

    /**
     * Unidades do cliente que pertencem a uma loja específica.
     */
    public function unidadesDoClienteNaLoja(int $clienteId, int $lojaId): array
    {
        return $this->db->table('cliente_unidades')
            ->select('unidades.id, unidades.identificador AS nome')
            ->join('unidades', 'unidades.id = cliente_unidades.unidade_id')
            ->where('cliente_unidades.cliente_id', $clienteId)
            ->where('unidades.loja_id', $lojaId)
            ->orderBy('unidades.identificador', 'ASC')
            ->get()
            ->getResultArray();
    }

    /**
     * Lista os vínculos com nome do cliente, unidade e loja.
     */
    public function listar(?string $busca = null): array
    {
        $this->select('cliente_unidades.*, clientes.nome AS cliente_nome, unidades.identificador AS unidade_nome, lojas.nome AS loja_nome')
            ->join('clientes', 'clientes.id = cliente_unidades.cliente_id', 'left')
            ->join('unidades', 'unidades.id = cliente_unidades.unidade_id', 'left')
            ->join('lojas', 'lojas.id = unidades.loja_id', 'left');

        if ($busca !== null && $busca !== '') {
            $this->groupStart()
                ->like('clientes.nome', $busca)
                ->orLike('unidades.identificador', $busca)
                ->orLike('lojas.nome', $busca)
                ->groupEnd();
        }

        return $this->orderBy('clientes.nome', 'ASC')
            ->orderBy('unidades.identificador', 'ASC')
            ->findAll();
    }
}
