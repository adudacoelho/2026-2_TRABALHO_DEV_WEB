<?php

namespace App\Controllers;

use App\Models\FranquiaModel;
use App\Models\LojaModel;
use CodeIgniter\HTTP\RedirectResponse;

class Lojas extends BaseController
{
    private LojaModel $lojaModel;
    private FranquiaModel $franquiaModel;

    public function __construct()
    {
        $this->lojaModel      = new LojaModel();
        $this->franquiaModel  = new FranquiaModel();
    }

    public function index(): string
    {
        $busca = trim((string) $this->request->getGet('busca'));

        return view('lojas/index', [
            'titulo' => 'Lojas',
            'busca'  => $busca,
            'lojas'  => $this->lojaModel->listar($busca),
        ]);
    }

    public function novo(): string
    {
        return view('lojas/form', [
            'titulo'    => 'Nova loja',
            'acao'      => site_url('lojas/salvar'),
            'loja'      => ['nome' => '', 'endereco' => '', 'franquia_id' => ''],
            'franquias' => $this->franquiaModel->orderBy('nome', 'ASC')->findAll(),
        ]);
    }

    public function salvar(): RedirectResponse
    {
        if (! $this->lojaModel->insert($this->dadosDoFormulario())) {
            return redirect()->back()->withInput()->with('erros', $this->lojaModel->errors());
        }

        return redirect()->to('lojas')->with('sucesso', 'Loja cadastrada com sucesso.');
    }

    public function editar(int $id): string|RedirectResponse
    {
        $loja = $this->lojaModel->find($id);

        if ($loja === null) {
            return redirect()->to('lojas')->with('erros', ['Loja não encontrada.']);
        }

        return view('lojas/form', [
            'titulo'    => 'Editar loja',
            'acao'      => site_url('lojas/atualizar/' . $id),
            'loja'      => $loja,
            'franquias' => $this->franquiaModel->orderBy('nome', 'ASC')->findAll(),
        ]);
    }

    public function atualizar(int $id): RedirectResponse
    {
        if ($this->lojaModel->find($id) === null) {
            return redirect()->to('lojas')->with('erros', ['Loja não encontrada.']);
        }

        // O "id" segue junto para as regras de validação do model.
        $dados = $this->dadosDoFormulario() + ['id' => $id];

        if (! $this->lojaModel->update($id, $dados)) {
            return redirect()->back()->withInput()->with('erros', $this->lojaModel->errors());
        }

        return redirect()->to('lojas')->with('sucesso', 'Loja atualizada com sucesso.');
    }

    public function excluir(int $id): RedirectResponse
    {
        if ($this->lojaModel->find($id) === null) {
            return redirect()->to('lojas')->with('erros', ['Loja não encontrada.']);
        }

        if ($this->lojaModel->possuiDependencias($id)) {
            return redirect()->to('lojas')->with('erros', [
                'Não é possível excluir: existem unidades, estoques, vendas ou movimentações nesta loja. Remova-os primeiro.',
            ]);
        }

        $this->lojaModel->delete($id);

        return redirect()->to('lojas')->with('sucesso', 'Loja excluída com sucesso.');
    }

    /**
     * Lê os campos do formulário da loja.
     */
    private function dadosDoFormulario(): array
    {
        return [
            'nome'        => trim((string) $this->request->getPost('nome')),
            'endereco'    => trim((string) $this->request->getPost('endereco')),
            'franquia_id' => trim((string) $this->request->getPost('franquia_id')),
        ];
    }
}
