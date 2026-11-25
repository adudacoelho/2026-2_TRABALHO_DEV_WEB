<?php

namespace App\Controllers;

use App\Models\FranquiaModel;
use CodeIgniter\HTTP\RedirectResponse;

class Franquias extends BaseController
{
    private FranquiaModel $franquiaModel;

    public function __construct()
    {
        $this->franquiaModel = new FranquiaModel();
    }

    public function index(): string
    {
        $busca = trim((string) $this->request->getGet('busca'));

        return view('franquias/index', [
            'titulo'    => 'Franquias',
            'busca'     => $busca,
            'franquias' => $this->franquiaModel->listar($busca),
        ]);
    }

    public function novo(): string
    {
        return view('franquias/form', [
            'titulo'   => 'Nova franquia',
            'acao'     => site_url('franquias/salvar'),
            'franquia' => ['nome' => ''],
        ]);
    }

    public function salvar(): RedirectResponse
    {
        if (! $this->franquiaModel->insert($this->dadosDoFormulario())) {
            return redirect()->back()->withInput()->with('erros', $this->franquiaModel->errors());
        }

        return redirect()->to('franquias')->with('sucesso', 'Franquia cadastrada com sucesso.');
    }

    public function editar(int $id): string|RedirectResponse
    {
        $franquia = $this->franquiaModel->find($id);

        if ($franquia === null) {
            return redirect()->to('franquias')->with('erros', ['Franquia não encontrada.']);
        }

        return view('franquias/form', [
            'titulo'   => 'Editar franquia',
            'acao'     => site_url('franquias/atualizar/' . $id),
            'franquia' => $franquia,
        ]);
    }

    public function atualizar(int $id): RedirectResponse
    {
        if ($this->franquiaModel->find($id) === null) {
            return redirect()->to('franquias')->with('erros', ['Franquia não encontrada.']);
        }

        // O "id" vai junto para a regra is_unique ignorar o próprio registro.
        $dados = $this->dadosDoFormulario() + ['id' => $id];

        if (! $this->franquiaModel->update($id, $dados)) {
            return redirect()->back()->withInput()->with('erros', $this->franquiaModel->errors());
        }

        return redirect()->to('franquias')->with('sucesso', 'Franquia atualizada com sucesso.');
    }

    public function excluir(int $id): RedirectResponse
    {
        if ($this->franquiaModel->find($id) === null) {
            return redirect()->to('franquias')->with('erros', ['Franquia não encontrada.']);
        }

        if ($this->franquiaModel->possuiDependencias($id)) {
            return redirect()->to('franquias')->with('erros', [
                'Não é possível excluir: existem lojas vinculadas a esta franquia. Remova-as primeiro.',
            ]);
        }

        $this->franquiaModel->delete($id);

        return redirect()->to('franquias')->with('sucesso', 'Franquia excluída com sucesso.');
    }

    /**
     * Lê os campos do formulário da franquia.
     */
    private function dadosDoFormulario(): array
    {
        return [
            'nome' => trim((string) $this->request->getPost('nome')),
        ];
    }
}
