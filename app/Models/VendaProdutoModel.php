<?php

namespace App\Models;

use App\Models\EstoqueLojaModel;
use CodeIgniter\Model;

class VendaProdutoModel extends Model
{
    private EstoqueLojaModel $estoqueLojaModel;

    protected $table         = 'venda_produtos';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $allowedFields = ['cliente_id', 'loja_id', 'produto_id', 'unidade_id', 'data', 'quantidade', 'preco_unitario'];
    protected $useTimestamps = true;

    protected $validationRules = [
        'id'             => 'permit_empty|is_natural_no_zero',
        'cliente_id'     => 'required|is_natural_no_zero|is_not_unique[clientes.id]',
        'loja_id'        => 'required|is_natural_no_zero|is_not_unique[lojas.id]',
        'produto_id'     => 'required|is_natural_no_zero|is_not_unique[produtos.id]',
        'unidade_id'     => 'required|is_natural_no_zero|is_not_unique[unidades.id]',
        'data'           => 'required|valid_date[Y-m-d H:i:s]',
        'quantidade'     => 'required|is_natural_no_zero|less_than[100000]',
        'preco_unitario' => 'required|decimal|greater_than[0]|less_than[100000]',
    ];

    protected $validationMessages = [
        'cliente_id' => [
            'required'           => 'Informe o cliente.',
            'is_natural_no_zero' => 'O cliente informado é inválido.',
            'is_not_unique'      => 'O cliente informado não existe.',
        ],
        'loja_id' => [
            'required'           => 'Informe a loja.',
            'is_natural_no_zero' => 'A loja informada é inválida.',
            'is_not_unique'      => 'A loja informada não existe.',
        ],
        'produto_id' => [
            'required'           => 'Informe o produto.',
            'is_natural_no_zero' => 'O produto informado é inválido.',
            'is_not_unique'      => 'O produto informado não existe.',
        ],
        'unidade_id' => [
            'required'           => 'Informe a unidade.',
            'is_natural_no_zero' => 'A unidade informada é inválida.',
            'is_not_unique'      => 'A unidade informada não existe.',
        ],
        'data' => [
            'required'    => 'Informe a data da venda.',
            'valid_date'  => 'Informe uma data válida.',
        ],
        'quantidade' => [
            'required'           => 'Informe a quantidade vendida.',
            'is_natural_no_zero' => 'A quantidade deve ser um número inteiro maior que zero.',
            'less_than'          => 'A quantidade deve ser menor que 100.000.',
        ],
        'preco_unitario' => [
            'required'     => 'Informe o preço unitário.',
            'decimal'      => 'Informe um preço válido, por exemplo 4.50.',
            'greater_than' => 'O preço unitário deve ser maior que zero.',
            'less_than'    => 'O preço unitário deve ser menor que 100.000.',
        ],
    ];

    public function __construct()
    {
        parent::__construct();

        $this->estoqueLojaModel = new EstoqueLojaModel();
    }

    /**
     * Grava a venda e dá a baixa no estoque da loja na mesma transação.
     */
    public function registrarVenda(array $dados): bool
    {
        $this->db->transStart();

        if (! $this->insert($dados)) {
            $this->db->transRollback();

            return false;
        }

        $movimentou = $this->estoqueLojaModel->movimentar(
            (int) $dados['loja_id'],
            (int) $dados['produto_id'],
            - (int) $dados['quantidade'],
            'venda',
            (int) $this->insertID(),
            'Baixa por venda'
        );

        if (! $movimentou) {
            $this->db->transRollback();
            $this->errors = ['Não foi possível baixar o estoque da venda.'];

            return false;
        }

        $this->db->transComplete();

        return $this->db->transStatus() !== false;
    }

    /**
     * Revisa a venda e ajusta o estoque apenas pela diferença.
     * Se a loja ou o produto mudou, devolve no antigo e baixa no novo.
     */
    public function revisarVenda(int $id, array $dados): bool
    {
        $anterior = $this->find($id);

        if ($anterior === null) {
            return false;
        }

        $this->db->transStart();

        if (! $this->update($id, $dados)) {
            $this->db->transRollback();

            return false;
        }

        $lojaAntiga       = (int) $anterior['loja_id'];
        $produtoAntigo    = (int) $anterior['produto_id'];
        $quantidadeAntiga = (int) $anterior['quantidade'];
        $lojaNova         = (int) $dados['loja_id'];
        $produtoNovo      = (int) $dados['produto_id'];
        $quantidadeNova   = (int) $dados['quantidade'];

        if ($lojaAntiga === $lojaNova && $produtoAntigo === $produtoNovo) {
            // Positivo devolve a diferença; negativo dá baixa da diferença.
            $movimentou = $this->estoqueLojaModel->movimentar(
                $lojaNova,
                $produtoNovo,
                $quantidadeAntiga - $quantidadeNova,
                'venda',
                $id,
                'Ajuste por edição de venda'
            );
        } else {
            // A venda saiu de uma loja/produto e foi para outro.
            $devolveu = $this->estoqueLojaModel->movimentar($lojaAntiga, $produtoAntigo, $quantidadeAntiga, 'venda', $id, 'Ajuste por edição de venda');
            $baixou   = $this->estoqueLojaModel->movimentar($lojaNova, $produtoNovo, -$quantidadeNova, 'venda', $id, 'Ajuste por edição de venda');

            $movimentou = $devolveu && $baixou;
        }

        if (! $movimentou) {
            $this->db->transRollback();
            $this->errors = ['Não foi possível ajustar o estoque da venda.'];

            return false;
        }

        $this->db->transComplete();

        return $this->db->transStatus() !== false;
    }

    /**
     * Exclui a venda e devolve a quantidade ao estoque da loja.
     */
    public function removerVenda(int $id): bool
    {
        $venda = $this->find($id);

        if ($venda === null) {
            return false;
        }

        $this->db->transStart();

        if (! $this->delete($id)) {
            $this->db->transRollback();

            return false;
        }

        $movimentou = $this->estoqueLojaModel->movimentar(
            (int) $venda['loja_id'],
            (int) $venda['produto_id'],
            (int) $venda['quantidade'],
            'estorno',
            null,
            'Estorno da venda #' . $id
        );

        if (! $movimentou) {
            $this->db->transRollback();
            $this->errors = ['Não foi possível devolver o estoque da venda.'];

            return false;
        }

        $this->db->transComplete();

        return $this->db->transStatus() !== false;
    }

    /**
     * Lista as vendas com os nomes de cliente, loja e produto, com busca opcional.
     */
    public function listar(?string $busca = null): array
    {
        $this->select('venda_produtos.*, clientes.nome AS cliente_nome, lojas.nome AS loja_nome, produtos.nome AS produto_nome, unidades.identificador AS unidade_nome')
            ->join('clientes', 'clientes.id = venda_produtos.cliente_id', 'left')
            ->join('lojas', 'lojas.id = venda_produtos.loja_id', 'left')
            ->join('produtos', 'produtos.id = venda_produtos.produto_id', 'left')
            ->join('unidades', 'unidades.id = venda_produtos.unidade_id', 'left');

        if ($busca !== null && $busca !== '') {
            $this->groupStart()
                ->like('clientes.nome', $busca)
                ->orLike('lojas.nome', $busca)
                ->orLike('produtos.nome', $busca)
                ->groupEnd();
        }

        return $this->orderBy('venda_produtos.data', 'DESC')
            ->orderBy('venda_produtos.id', 'DESC')
            ->findAll();
    }
}
