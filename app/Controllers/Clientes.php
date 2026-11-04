<?php

namespace App\Controllers;

use App\Models\ClienteModel;
use CodeIgniter\HTTP\RedirectResponse;

class Clientes extends BaseController
{
    private ClienteModel $clienteModel;

    public function __construct()
    {
        $this->clienteModel = new ClienteModel();
    }

    public function index(): string
    {
        $busca = trim((string) $this->request->getGet('busca'));

        return view('clientes/index', [
            'titulo'   => 'Clientes',
            'busca'    => $busca,
            'clientes' => $this->clienteModel->listar($busca),
        ]);
    }

    public function novo(): string
    {
        return view('clientes/form', [
            'titulo'  => 'Novo cliente',
            'acao'    => site_url('clientes/salvar'),
            'cliente' => ['nome' => '', 'cpf' => '', 'telefone' => '', 'email' => '', 'unidade' => ''],
        ]);
    }

    public function salvar(): RedirectResponse
    {
        if (! $this->clienteModel->insert($this->dadosDoFormulario())) {
            return redirect()->back()->withInput()->with('erros', $this->clienteModel->errors());
        }

        return redirect()->to('clientes')->with('sucesso', 'Cliente cadastrado com sucesso.');
    }

    public function editar(int $id): string|RedirectResponse
    {
        $cliente = $this->clienteModel->find($id);

        if ($cliente === null) {
            return redirect()->to('clientes')->with('erros', ['Cliente não encontrado.']);
        }

        return view('clientes/form', [
            'titulo'  => 'Editar cliente',
            'acao'    => site_url('clientes/atualizar/' . $id),
            'cliente' => $cliente,
        ]);
    }

    public function atualizar(int $id): RedirectResponse
    {
        if ($this->clienteModel->find($id) === null) {
            return redirect()->to('clientes')->with('erros', ['Cliente não encontrado.']);
        }

        // O "id" vai junto para a regra is_unique ignorar o próprio registro.
        $dados = $this->dadosDoFormulario() + ['id' => $id];

        if (! $this->clienteModel->update($id, $dados)) {
            return redirect()->back()->withInput()->with('erros', $this->clienteModel->errors());
        }

        return redirect()->to('clientes')->with('sucesso', 'Cliente atualizado com sucesso.');
    }

    public function excluir(int $id): RedirectResponse
    {
        if ($this->clienteModel->find($id) === null) {
            return redirect()->to('clientes')->with('erros', ['Cliente não encontrado.']);
        }

        $this->clienteModel->delete($id);

        return redirect()->to('clientes')->with('sucesso', 'Cliente excluído com sucesso.');
    }

    /**
     * Lê os campos do formulário. CPF e telefone são salvos só com números (LGPD: só o necessário).
     */
    private function dadosDoFormulario(): array
    {
        return [
            'nome'     => trim((string) $this->request->getPost('nome')),
            'cpf'      => somente_numeros($this->request->getPost('cpf')),
            'telefone' => somente_numeros($this->request->getPost('telefone')),
            'email'    => trim((string) $this->request->getPost('email')),
            'unidade'  => trim((string) $this->request->getPost('unidade')),
        ];
    }
}
