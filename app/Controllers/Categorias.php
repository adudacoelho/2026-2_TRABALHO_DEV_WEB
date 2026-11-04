<?php

namespace App\Controllers;

use App\Models\CategoriaModel;
use CodeIgniter\HTTP\RedirectResponse;

class Categorias extends BaseController
{
    private CategoriaModel $categoriaModel;

    public function __construct()
    {
        $this->categoriaModel = new CategoriaModel();
    }

    public function index(): string
    {
        $busca = trim((string) $this->request->getGet('busca'));

        return view('categorias/index', [
            'titulo'     => 'Categorias',
            'busca'      => $busca,
            'categorias' => $this->categoriaModel->listar($busca),
        ]);
    }

    public function nova(): string
    {
        return view('categorias/form', [
            'titulo'    => 'Nova categoria',
            'acao'      => site_url('categorias/salvar'),
            'categoria' => ['nome' => ''],
        ]);
    }

    public function salvar(): RedirectResponse
    {
        $dados = ['nome' => trim((string) $this->request->getPost('nome'))];

        if (! $this->categoriaModel->insert($dados)) {
            return redirect()->back()->withInput()->with('erros', $this->categoriaModel->errors());
        }

        return redirect()->to('categorias')->with('sucesso', 'Categoria cadastrada com sucesso.');
    }

    public function editar(int $id): string|RedirectResponse
    {
        $categoria = $this->categoriaModel->find($id);

        if ($categoria === null) {
            return redirect()->to('categorias')->with('erros', ['Categoria não encontrada.']);
        }

        return view('categorias/form', [
            'titulo'    => 'Editar categoria',
            'acao'      => site_url('categorias/atualizar/' . $id),
            'categoria' => $categoria,
        ]);
    }

    public function atualizar(int $id): RedirectResponse
    {
        if ($this->categoriaModel->find($id) === null) {
            return redirect()->to('categorias')->with('erros', ['Categoria não encontrada.']);
        }

        // O "id" vai junto para a regra is_unique ignorar o próprio registro.
        $dados = [
            'id'   => $id,
            'nome' => trim((string) $this->request->getPost('nome')),
        ];

        if (! $this->categoriaModel->update($id, $dados)) {
            return redirect()->back()->withInput()->with('erros', $this->categoriaModel->errors());
        }

        return redirect()->to('categorias')->with('sucesso', 'Categoria atualizada com sucesso.');
    }

    public function excluir(int $id): RedirectResponse
    {
        if ($this->categoriaModel->find($id) === null) {
            return redirect()->to('categorias')->with('erros', ['Categoria não encontrada.']);
        }

        if ($this->categoriaModel->possuiProdutos($id)) {
            return redirect()->to('categorias')->with('erros', [
                'Não é possível excluir: existem produtos nesta categoria. Mude ou exclua os produtos primeiro.',
            ]);
        }

        $this->categoriaModel->delete($id);

        return redirect()->to('categorias')->with('sucesso', 'Categoria excluída com sucesso.');
    }
}
