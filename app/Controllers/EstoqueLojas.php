<?php

namespace App\Controllers;

use App\Models\EstoqueLojaModel;
use App\Models\LojaModel;
use App\Models\ProdutoModel;
use CodeIgniter\HTTP\RedirectResponse;

class EstoqueLojas extends BaseController
{
    private EstoqueLojaModel $estoqueLojaModel;
    private LojaModel $lojaModel;
    private ProdutoModel $produtoModel;

    public function __construct()
    {
        $this->estoqueLojaModel = new EstoqueLojaModel();
        $this->lojaModel        = new LojaModel();
        $this->produtoModel     = new ProdutoModel();
    }

    public function index(): string
    {
        $busca = trim((string) $this->request->getGet('busca'));

        return view('estoque_lojas/index', [
            'titulo'   => 'Estoque por loja',
            'busca'    => $busca,
            'estoques' => $this->estoqueLojaModel->listar($busca),
        ]);
    }

    public function novo(): string
    {
        return view('estoque_lojas/form', [
            'titulo'   => 'Novo estoque',
            'acao'     => site_url('estoque-lojas/salvar'),
            'estoque'  => ['loja_id' => '', 'produto_id' => '', 'quantidade' => ''],
            'lojas'    => $this->lojaModel->orderBy('nome', 'ASC')->findAll(),
            'produtos' => $this->produtoModel->orderBy('nome', 'ASC')->findAll(),
        ]);
    }

    public function salvar(): RedirectResponse
    {
        $dados = $this->dadosDoFormulario();

        if ($this->estoqueLojaModel->parDuplicado((int) $dados['loja_id'], (int) $dados['produto_id'])) {
            return redirect()->back()->withInput()
                ->with('erros', ['Já existe estoque cadastrado para esta loja e produto.']);
        }

        if (! $this->estoqueLojaModel->registrarEntrada($dados)) {
            return redirect()->back()->withInput()->with('erros', $this->estoqueLojaModel->errors());
        }

        return redirect()->to('estoque-lojas')->with('sucesso', 'Estoque cadastrado com sucesso.');
    }

    public function editar(int $id): string|RedirectResponse
    {
        $estoque = $this->estoqueLojaModel->find($id);

        if ($estoque === null) {
            return redirect()->to('estoque-lojas')->with('erros', ['Estoque não encontrado.']);
        }

        return view('estoque_lojas/form', [
            'titulo'   => 'Editar estoque',
            'acao'     => site_url('estoque-lojas/atualizar/' . $id),
            'estoque'  => $estoque,
            'lojas'    => $this->lojaModel->orderBy('nome', 'ASC')->findAll(),
            'produtos' => $this->produtoModel->orderBy('nome', 'ASC')->findAll(),
        ]);
    }

    public function atualizar(int $id): RedirectResponse
    {
        if ($this->estoqueLojaModel->find($id) === null) {
            return redirect()->to('estoque-lojas')->with('erros', ['Estoque não encontrado.']);
        }

        $dados = $this->dadosDoFormulario();

        if ($this->estoqueLojaModel->parDuplicado((int) $dados['loja_id'], (int) $dados['produto_id'], $id)) {
            return redirect()->back()->withInput()
                ->with('erros', ['Já existe estoque cadastrado para esta loja e produto.']);
        }

        // O "id" segue junto para as regras de validação do model.
        $dados += ['id' => $id];

        if (! $this->estoqueLojaModel->revisarEstoque($id, $dados)) {
            return redirect()->back()->withInput()->with('erros', $this->estoqueLojaModel->errors());
        }

        return redirect()->to('estoque-lojas')->with('sucesso', 'Estoque atualizado com sucesso.');
    }

    public function excluir(int $id): RedirectResponse
    {
        if ($this->estoqueLojaModel->find($id) === null) {
            return redirect()->to('estoque-lojas')->with('erros', ['Estoque não encontrado.']);
        }

        if (! $this->estoqueLojaModel->removerEstoque($id)) {
            return redirect()->to('estoque-lojas')->with('erros', ['Não foi possível excluir o estoque e registrar a movimentação.']);
        }

        return redirect()->to('estoque-lojas')->with('sucesso', 'Estoque excluído e movimentação registrada com sucesso.');
    }

    /**
     * Lê os campos do formulário de estoque por loja.
     */
    private function dadosDoFormulario(): array
    {
        return [
            'loja_id'    => trim((string) $this->request->getPost('loja_id')),
            'produto_id' => trim((string) $this->request->getPost('produto_id')),
            'quantidade' => trim((string) $this->request->getPost('quantidade')),
        ];
    }
}
