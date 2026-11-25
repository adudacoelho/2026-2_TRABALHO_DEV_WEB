<?php

namespace App\Models;

use CodeIgniter\Model;

class EstoqueLojaModel extends Model
{
    private EstoqueMovimentoModel $estoqueMovimentoModel;

    protected $table         = 'estoque_lojas';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $allowedFields = ['loja_id', 'produto_id', 'quantidade'];
    protected $useTimestamps = true;

    protected $validationRules = [
        'id'         => 'permit_empty|is_natural_no_zero',
        'loja_id'    => 'required|is_natural_no_zero|is_not_unique[lojas.id]',
        'produto_id' => 'required|is_natural_no_zero|is_not_unique[produtos.id]',
        'quantidade' => 'required|integer|greater_than[-100000]|less_than[100000]',
    ];

    protected $validationMessages = [
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
        'quantidade' => [
            'required'     => 'Informe a quantidade.',
            'integer'      => 'A quantidade deve ser um número inteiro.',
            'greater_than' => 'A quantidade não pode ser menor que -100.000.',
            'less_than'    => 'A quantidade deve ser menor que 100.000.',
        ],
    ];

    public function __construct()
    {
        parent::__construct();

        $this->estoqueMovimentoModel = new EstoqueMovimentoModel();
    }

    /**
     * Lista o estoque por loja com os nomes da loja e do produto.
     */
    public function listar(?string $busca = null): array
    {
        $this->select('estoque_lojas.*, lojas.nome AS loja_nome, produtos.nome AS produto_nome')
            ->join('lojas', 'lojas.id = estoque_lojas.loja_id', 'left')
            ->join('produtos', 'produtos.id = estoque_lojas.produto_id', 'left');

        if ($busca !== null && $busca !== '') {
            $this->groupStart()
                ->like('lojas.nome', $busca)
                ->orLike('produtos.nome', $busca)
                ->groupEnd();
        }

        return $this->orderBy('lojas.nome', 'ASC')
            ->orderBy('produtos.nome', 'ASC')
            ->findAll();
    }

    /**
     * Indica se a dupla loja/produto já está cadastrada.
     */
    public function parDuplicado(int $lojaId, int $produtoId, ?int $idIgnorar = null): bool
    {
        $builder = $this->db->table($this->table)
            ->where('loja_id', $lojaId)
            ->where('produto_id', $produtoId);

        if ($idIgnorar !== null) {
            $builder->where('id !=', $idIgnorar);
        }

        return $builder->countAllResults() > 0;
    }

    /**
     * Aplica uma movimentação de estoque na loja (delta negativo = baixa,
     * delta positivo = devolução) e registra o evento no histórico.
     * Saldo negativo é permitido e representa uma venda sem entrada correspondente.
     *
     * Se ainda não existe registro para a dupla loja/produto, ele é criado
     * já com o saldo resultante, para que a venda nunca seja perdida.
     */
    public function movimentar(
        int $lojaId,
        int $produtoId,
        int $delta,
        string $origem = 'ajuste',
        ?int $vendaId = null,
        ?string $observacao = null
    ): bool {
        if ($delta === 0) {
            return true;
        }

        $agora = date('Y-m-d H:i:s');

        // Insere a linha (se ainda não existir) de forma atômica. O valor começa
        // em zero e o delta é aplicado no UPDATE abaixo, evitando que duas
        // requisições simultâneas criem a mesma dupla loja/produto.
        $inseriu = $this->db->table($this->table)->ignore(true)->insert([
            'loja_id'    => $lojaId,
            'produto_id' => $produtoId,
            'quantidade' => 0,
            'created_at' => $agora,
            'updated_at' => $agora,
        ]);

        if (! $inseriu) {
            return false;
        }

        $atualizou = $this->db->table($this->table)
            ->where('loja_id', $lojaId)
            ->where('produto_id', $produtoId)
            ->set('quantidade', 'quantidade + ' . $delta, false)
            ->set('updated_at', $agora)
            ->update();

        if (! $atualizou) {
            return false;
        }

        return $this->estoqueMovimentoModel->registrar($lojaId, $produtoId, $delta, $origem, $vendaId, $observacao);
    }

    /**
     * Cadastra um estoque e registra a entrada inicial no histórico.
     */
    public function registrarEntrada(array $dados): bool
    {
        $this->db->transStart();

        if (! $this->insert($dados)) {
            $this->db->transRollback();

            return false;
        }

        $registrado = $this->estoqueMovimentoModel->registrar(
            (int) $dados['loja_id'],
            (int) $dados['produto_id'],
            (int) $dados['quantidade'],
            'ajuste',
            null,
            'Cadastro de estoque'
        );

        if (! $registrado) {
            $this->db->transRollback();

            return false;
        }

        $this->db->transComplete();

        return $this->db->transStatus() !== false;
    }

    /**
     * Ajusta um estoque e registra a diferença no histórico.
     * Se a loja ou o produto mudou, baixa o par antigo e entra no novo.
     */
    public function revisarEstoque(int $id, array $dados): bool
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

        if ($lojaAntiga !== $lojaNova || $produtoAntigo !== $produtoNovo) {
            $removeu = $this->estoqueMovimentoModel->registrar($lojaAntiga, $produtoAntigo, -$quantidadeAntiga, 'ajuste', null, 'Ajuste manual: mudança de loja/produto');
            $entrou  = $this->estoqueMovimentoModel->registrar($lojaNova, $produtoNovo, $quantidadeNova, 'ajuste', null, 'Ajuste manual: mudança de loja/produto');

            $registrado = $removeu && $entrou;
        } elseif ($quantidadeAntiga !== $quantidadeNova) {
            $registrado = $this->estoqueMovimentoModel->registrar(
                $lojaNova,
                $produtoNovo,
                $quantidadeNova - $quantidadeAntiga,
                'ajuste',
                null,
                'Ajuste manual'
            );
        } else {
            $registrado = true;
        }

        if (! $registrado) {
            $this->db->transRollback();

            return false;
        }

        $this->db->transComplete();

        return $this->db->transStatus() !== false;
    }

    /**
     * Exclui um estoque e registra a saída correspondente no histórico.
     */
    public function removerEstoque(int $id): bool
    {
        $estoque = $this->find($id);

        if ($estoque === null) {
            return false;
        }

        $this->db->transStart();

        if (! $this->delete($id)) {
            $this->db->transRollback();

            return false;
        }

        $registrado = $this->estoqueMovimentoModel->registrar(
            (int) $estoque['loja_id'],
            (int) $estoque['produto_id'],
            - (int) $estoque['quantidade'],
            'ajuste',
            null,
            'Exclusão de estoque'
        );

        if (! $registrado) {
            $this->db->transRollback();

            return false;
        }

        $this->db->transComplete();

        return $this->db->transStatus() !== false;
    }
}
