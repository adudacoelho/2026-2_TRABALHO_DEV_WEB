<?php

namespace App\Controllers;

use App\Models\CategoriaModel;
use App\Models\ProdutoModel;
use CodeIgniter\HTTP\RedirectResponse;

class Produtos extends BaseController
{
    private ProdutoModel $produtoModel;
    private CategoriaModel $categoriaModel;

    public function __construct()
    {
        $this->produtoModel   = new ProdutoModel();
        $this->categoriaModel = new CategoriaModel();
    }

    public function index(): string
    {
        $busca = trim((string) $this->request->getGet('busca'));

        return view('produtos/index', [
            'titulo'   => 'Produtos',
            'busca'    => $busca,
            'produtos' => $this->produtoModel->listar($busca),
        ]);
    }

    public function novo(): string
    {
        return view('produtos/form', [
            'titulo'     => 'Novo produto',
            'acao'       => site_url('produtos/salvar'),
            'produto'    => ['nome' => '', 'descricao' => '', 'preco' => '', 'quantidade' => 0, 'categoria_id' => ''],
            'categorias' => $this->categoriaModel->listar(),
        ]);
    }

    public function salvar(): RedirectResponse
    {
        if (! $this->produtoModel->insert($this->dadosDoFormulario())) {
            return redirect()->back()->withInput()->with('erros', $this->produtoModel->errors());
        }

        return redirect()->to('produtos')->with('sucesso', 'Produto cadastrado com sucesso.');
    }

    public function editar(int $id): string|RedirectResponse
    {
        $produto = $this->produtoModel->find($id);

        if ($produto === null) {
            return redirect()->to('produtos')->with('erros', ['Produto não encontrado.']);
        }

        return view('produtos/form', [
            'titulo'     => 'Editar produto',
            'acao'       => site_url('produtos/atualizar/' . $id),
            'produto'    => $produto,
            'categorias' => $this->categoriaModel->listar(),
        ]);
    }

    public function atualizar(int $id): RedirectResponse
    {
        if ($this->produtoModel->find($id) === null) {
            return redirect()->to('produtos')->with('erros', ['Produto não encontrado.']);
        }

        $dados = $this->dadosDoFormulario() + ['id' => $id];

        if (! $this->produtoModel->update($id, $dados)) {
            return redirect()->back()->withInput()->with('erros', $this->produtoModel->errors());
        }

        return redirect()->to('produtos')->with('sucesso', 'Produto atualizado com sucesso.');
    }

    public function excluir(int $id): RedirectResponse
    {
        if ($this->produtoModel->find($id) === null) {
            return redirect()->to('produtos')->with('erros', ['Produto não encontrado.']);
        }

        $this->produtoModel->delete($id);

        return redirect()->to('produtos')->with('sucesso', 'Produto excluído com sucesso.');
    }

    /**
     * Lê os campos do formulário. Categoria vazia vira null (categoria é opcional).
     */
    private function dadosDoFormulario(): array
    {
        $categoria = $this->request->getPost('categoria_id');

        return [
            'nome'         => trim((string) $this->request->getPost('nome')),
            'descricao'    => trim((string) $this->request->getPost('descricao')),
            'preco'        => trim((string) $this->request->getPost('preco')),
            'quantidade'   => trim((string) $this->request->getPost('quantidade')),
            'categoria_id' => ($categoria === null || $categoria === '') ? null : $categoria,
        ];
    }
}
