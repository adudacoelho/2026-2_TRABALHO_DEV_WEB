<?php

namespace App\Controllers;

use App\Models\FornecedorModel;
use CodeIgniter\HTTP\RedirectResponse;

class Fornecedores extends BaseController
{
    private FornecedorModel $fornecedorModel;

    public function __construct()
    {
        $this->fornecedorModel = new FornecedorModel();
    }

    public function index(): string
    {
        $busca = trim((string) $this->request->getGet('busca'));

        return view('fornecedores/index', [
            'titulo'       => 'Fornecedores',
            'busca'        => $busca,
            'fornecedores' => $this->fornecedorModel->listar($busca),
        ]);
    }

    public function novo(): string
    {
        return view('fornecedores/form', [
            'titulo'     => 'Novo fornecedor',
            'acao'       => site_url('fornecedores/salvar'),
            'fornecedor' => ['nome' => '', 'descricao' => '', 'cnpj' => '', 'telefone' => '', 'endereco' => '', 'email' => ''],
        ]);
    }

    public function salvar(): RedirectResponse
    {
        if (! $this->fornecedorModel->insert($this->dadosDoFormulario())) {
            return redirect()->back()->withInput()->with('erros', $this->fornecedorModel->errors());
        }

        return redirect()->to('fornecedores')->with('sucesso', 'Fornecedor cadastrado com sucesso.');
    }

    public function editar(int $id): string|RedirectResponse
    {
        $fornecedor = $this->fornecedorModel->find($id);

        if ($fornecedor === null) {
            return redirect()->to('fornecedores')->with('erros', ['Fornecedor não encontrado.']);
        }

        return view('fornecedores/form', [
            'titulo'     => 'Editar fornecedor',
            'acao'       => site_url('fornecedores/atualizar/' . $id),
            'fornecedor' => $fornecedor,
        ]);
    }

    public function atualizar(int $id): RedirectResponse
    {
        if ($this->fornecedorModel->find($id) === null) {
            return redirect()->to('fornecedores')->with('erros', ['Fornecedor não encontrado.']);
        }

        // O "id" vai junto para a regra is_unique ignorar o próprio registro.
        $dados = $this->dadosDoFormulario() + ['id' => $id];

        if (! $this->fornecedorModel->update($id, $dados)) {
            return redirect()->back()->withInput()->with('erros', $this->fornecedorModel->errors());
        }

        return redirect()->to('fornecedores')->with('sucesso', 'Fornecedor atualizado com sucesso.');
    }

    public function excluir(int $id): RedirectResponse
    {
        if ($this->fornecedorModel->find($id) === null) {
            return redirect()->to('fornecedores')->with('erros', ['Fornecedor não encontrado.']);
        }

        $this->fornecedorModel->delete($id);

        return redirect()->to('fornecedores')->with('sucesso', 'Fornecedor excluído com sucesso.');
    }

    /**
     * Lê os campos do formulário. CNPJ e telefone são salvos só com números.
     */
    private function dadosDoFormulario(): array
    {
        return [
            'nome'      => trim((string) $this->request->getPost('nome')),
            'descricao' => trim((string) $this->request->getPost('descricao')),
            'cnpj'      => somente_numeros($this->request->getPost('cnpj')),
            'telefone'  => somente_numeros($this->request->getPost('telefone')),
            'endereco'  => trim((string) $this->request->getPost('endereco')),
            'email'     => trim((string) $this->request->getPost('email')),
        ];
    }
}
