<?php

namespace App\Controllers;

use App\Models\EstoqueMovimentoModel;

/**
 * RF03: consulta ao histórico de movimentações de estoque.
 * O histórico é somente leitura: os eventos são gerados por vendas e ajustes.
 */
class EstoqueMovimentacoes extends BaseController
{
    private EstoqueMovimentoModel $estoqueMovimentoModel;

    public function __construct()
    {
        $this->estoqueMovimentoModel = new EstoqueMovimentoModel();
    }

    public function index(): string
    {
        $busca  = trim((string) $this->request->getGet('busca'));
        $origem = trim((string) $this->request->getGet('origem'));

        if (! in_array($origem, ['venda', 'estorno', 'ajuste'], true)) {
            $origem = '';
        }

        return view('estoque_movimentacoes/index', [
            'titulo'       => 'Movimentações de estoque',
            'busca'        => $busca,
            'origem'       => $origem,
            'movimentacoes' => $this->estoqueMovimentoModel->listar($busca, $origem),
        ]);
    }
}
