<?php

namespace App\Controllers;

use App\Models\ClienteModel;
use App\Models\ClienteUnidadeModel;
use App\Models\UnidadeModel;
use CodeIgniter\HTTP\RedirectResponse;

class ClienteUnidades extends BaseController
{
    private ClienteUnidadeModel $clienteUnidadeModel;
    private ClienteModel $clienteModel;
    private UnidadeModel $unidadeModel;

    public function __construct()
    {
        $this->clienteUnidadeModel = new ClienteUnidadeModel();
        $this->clienteModel        = new ClienteModel();
        $this->unidadeModel        = new UnidadeModel();
    }

    public function index(): string
    {
        $busca = trim((string) $this->request->getGet('busca'));

        return view('cliente_unidades/index', [
            'titulo'  => 'Vínculos cliente e unidade',
            'busca'   => $busca,
            'vinculos' => $this->clienteUnidadeModel->listar($busca),
        ]);
    }

    public function novo(): string
    {
        return view('cliente_unidades/form', [
            'titulo'   => 'Novo vínculo',
            'acao'     => site_url('cliente-unidades/salvar'),
            'vinculo'  => ['cliente_id' => '', 'unidade_id' => ''],
            'clientes' => $this->clienteModel->orderBy('nome', 'ASC')->findAll(),
            'unidades' => $this->unidadeModel->listar(),
        ]);
    }

    public function salvar(): RedirectResponse
    {
        $dados = $this->dadosDoFormulario();

        if ($this->clienteUnidadeModel->vinculoExiste((int) $dados['cliente_id'], (int) $dados['unidade_id'])) {
            return redirect()->back()->withInput()
                ->with('erros', ['Este cliente já possui vínculo com esta unidade.']);
        }

        if (! $this->clienteUnidadeModel->insert($dados)) {
            return redirect()->back()->withInput()->with('erros', $this->clienteUnidadeModel->errors());
        }

        return redirect()->to('cliente-unidades')->with('sucesso', 'Vínculo cadastrado com sucesso.');
    }

    public function editar(int $id): string|RedirectResponse
    {
        $vinculo = $this->clienteUnidadeModel->find($id);

        if ($vinculo === null) {
            return redirect()->to('cliente-unidades')->with('erros', ['Vínculo não encontrado.']);
        }

        return view('cliente_unidades/form', [
            'titulo'   => 'Editar vínculo',
            'acao'     => site_url('cliente-unidades/atualizar/' . $id),
            'vinculo'  => $vinculo,
            'clientes' => $this->clienteModel->orderBy('nome', 'ASC')->findAll(),
            'unidades' => $this->unidadeModel->listar(),
        ]);
    }

    public function atualizar(int $id): RedirectResponse
    {
        if ($this->clienteUnidadeModel->find($id) === null) {
            return redirect()->to('cliente-unidades')->with('erros', ['Vínculo não encontrado.']);
        }

        $dados = $this->dadosDoFormulario();

        if ($this->clienteUnidadeModel->vinculoExiste((int) $dados['cliente_id'], (int) $dados['unidade_id'], $id)) {
            return redirect()->back()->withInput()
                ->with('erros', ['Este cliente já possui vínculo com esta unidade.']);
        }

        // O "id" segue junto para as regras de validação do model.
        $dados += ['id' => $id];

        if (! $this->clienteUnidadeModel->update($id, $dados)) {
            return redirect()->back()->withInput()->with('erros', $this->clienteUnidadeModel->errors());
        }

        return redirect()->to('cliente-unidades')->with('sucesso', 'Vínculo atualizado com sucesso.');
    }

    public function excluir(int $id): RedirectResponse
    {
        if ($this->clienteUnidadeModel->find($id) === null) {
            return redirect()->to('cliente-unidades')->with('erros', ['Vínculo não encontrado.']);
        }

        $this->clienteUnidadeModel->delete($id);

        return redirect()->to('cliente-unidades')->with('sucesso', 'Vínculo excluído com sucesso.');
    }

    /**
     * Lê os campos do formulário de vínculo.
     */
    private function dadosDoFormulario(): array
    {
        return [
            'cliente_id' => trim((string) $this->request->getPost('cliente_id')),
            'unidade_id' => trim((string) $this->request->getPost('unidade_id')),
        ];
    }
}
