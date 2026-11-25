<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * Histórico imutável de movimentações de estoque (RF03).
 */
class EstoqueMovimentoModel extends Model
{
    protected $table         = 'estoque_movimentos';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $allowedFields = ['loja_id', 'produto_id', 'venda_id', 'tipo', 'origem', 'quantidade', 'saldo_apos', 'observacao'];
    protected $useTimestamps = true;

    protected $validationRules = [
        'loja_id'    => 'required|is_natural_no_zero|is_not_unique[lojas.id]',
        'produto_id' => 'required|is_natural_no_zero|is_not_unique[produtos.id]',
        'venda_id'   => 'permit_empty|is_natural_no_zero|is_not_unique[venda_produtos.id]',
        'tipo'       => 'required|in_list[entrada,saida]',
        'origem'     => 'required|in_list[venda,estorno,ajuste]',
        'quantidade' => 'required|integer|greater_than[-100000]|less_than[100000]',
        'saldo_apos' => 'permit_empty|integer',
        'observacao' => 'permit_empty|max_length[255]',
    ];

    protected $validationMessages = [
        'loja_id' => [
            'is_not_unique' => 'A loja do movimento não existe.',
        ],
        'produto_id' => [
            'is_not_unique' => 'O produto do movimento não existe.',
        ],
        'venda_id' => [
            'is_not_unique' => 'A venda do movimento não existe.',
        ],
        'tipo' => [
            'in_list' => 'O tipo de movimento é inválido.',
        ],
        'origem' => [
            'in_list' => 'A origem do movimento é inválida.',
        ],
        'quantidade' => [
            'integer'      => 'A quantidade movimentada deve ser um número inteiro.',
            'greater_than' => 'A quantidade movimentada não pode ser menor que -100.000.',
            'less_than'    => 'A quantidade movimentada deve ser menor que 100.000.',
        ],
        'observacao' => [
            'max_length' => 'A observação deve ter no máximo 255 caracteres.',
        ],
    ];

    /**
     * Registra um evento de estoque. O sinal de "quantidade" define o tipo:
     * positivo = entrada, negativo = saída.
     *
     * Deve ser chamado depois que o saldo de estoque_lojas já foi aplicado,
     * pois o saldo_apos é lido dessa tabela.
     */
    public function registrar(
        int $lojaId,
        int $produtoId,
        int $quantidade,
        string $origem,
        ?int $vendaId = null,
        ?string $observacao = null
    ): bool {
        // Uma movimentação zero não altera o saldo e não deve poluir o histórico.
        if ($quantidade === 0) {
            return true;
        }

        $saldo = $this->db->table('estoque_lojas')
            ->select('quantidade')
            ->where('loja_id', $lojaId)
            ->where('produto_id', $produtoId)
            ->get()
            ->getRowArray();

        $tipo = $quantidade < 0 ? 'saida' : 'entrada';

        return $this->insert([
            'loja_id'    => $lojaId,
            'produto_id' => $produtoId,
            'venda_id'   => $vendaId,
            'tipo'       => $tipo,
            'origem'     => $origem,
            'quantidade' => $quantidade,
            'saldo_apos' => $saldo === null ? 0 : (int) $saldo['quantidade'],
            'observacao' => $observacao,
        ]);
    }

    /**
     * Lista o histórico com nomes de loja e produto e filtros opcionais.
     */
    public function listar(?string $busca = null, ?string $origem = null): array
    {
        $this->select('estoque_movimentos.*, lojas.nome AS loja_nome, produtos.nome AS produto_nome')
            ->join('lojas', 'lojas.id = estoque_movimentos.loja_id', 'left')
            ->join('produtos', 'produtos.id = estoque_movimentos.produto_id', 'left');

        if ($busca !== null && $busca !== '') {
            $this->groupStart()
                ->like('lojas.nome', $busca)
                ->orLike('produtos.nome', $busca)
                ->groupEnd();
        }

        if ($origem !== null && $origem !== '') {
            $this->where('estoque_movimentos.origem', $origem);
        }

        return $this->orderBy('estoque_movimentos.created_at', 'DESC')
            ->orderBy('estoque_movimentos.id', 'DESC')
            ->findAll();
    }
}
