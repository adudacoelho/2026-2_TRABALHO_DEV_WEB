<?php

namespace App\Controllers;

use App\Models\LojaModel;
use App\Models\UnidadeModel;
use CodeIgniter\HTTP\RedirectResponse;

class Unidades extends BaseController
{
    private UnidadeModel $unidadeModel;
    private LojaModel $lojaModel;

    public function __construct()
    {
        $this->unidadeModel = new UnidadeModel();
        $this->lojaModel    = new LojaModel();
    }

    public function index(): string
    {
        $busca = trim((string) $this->request->getGet('busca'));

        return view('unidades/index', [
            'titulo'   => 'Unidades',
            'busca'    => $busca,
            'unidades' => $this->unidadeModel->listar($busca),
        ]);
    }

    public function novo(): string
    {
        return view('unidades/form', [
            'titulo'        => 'Nova unidade',
            'acao'          => site_url('unidades/salvar'),
            'unidade'       => ['identificador' => '', 'loja_id' => ''],
            'lojas'         => $this->lojaModel->orderBy('nome', 'ASC')->findAll(),
        ]);
    }

    public function salvar(): RedirectResponse
    {
        $dados = $this->dadosDoFormulario();

        $erroDuplicidade = $this->validarDuplicidade((int) $dados['loja_id'], $dados['identificador']);
        if ($erroDuplicidade !== null) {
            return redirect()->back()->withInput()->with('erros', [$erroDuplicidade]);
        }

        if (! $this->unidadeModel->insert($dados)) {
            return redirect()->back()->withInput()->with('erros', $this->unidadeModel->errors());
        }

        return redirect()->to('unidades')->with('sucesso', 'Unidade cadastrada com sucesso.');
    }

    public function editar(int $id): string|RedirectResponse
    {
        $unidade = $this->unidadeModel->find($id);

        if ($unidade === null) {
            return redirect()->to('unidades')->with('erros', ['Unidade não encontrada.']);
        }

        return view('unidades/form', [
            'titulo'  => 'Editar unidade',
            'acao'    => site_url('unidades/atualizar/' . $id),
            'unidade' => $unidade,
            'lojas'   => $this->lojaModel->orderBy('nome', 'ASC')->findAll(),
        ]);
    }

    public function atualizar(int $id): RedirectResponse
    {
        if ($this->unidadeModel->find($id) === null) {
            return redirect()->to('unidades')->with('erros', ['Unidade não encontrada.']);
        }

        // O "id" segue junto para as regras de validação do model.
        $dados = $this->dadosDoFormulario() + ['id' => $id];

        $erroDuplicidade = $this->validarDuplicidade((int) $dados['loja_id'], $dados['identificador'], $id);
        if ($erroDuplicidade !== null) {
            return redirect()->back()->withInput()->with('erros', [$erroDuplicidade]);
        }

        if (! $this->unidadeModel->update($id, $dados)) {
            return redirect()->back()->withInput()->with('erros', $this->unidadeModel->errors());
        }

        return redirect()->to('unidades')->with('sucesso', 'Unidade atualizada com sucesso.');
    }

    public function excluir(int $id): RedirectResponse
    {
        if ($this->unidadeModel->find($id) === null) {
            return redirect()->to('unidades')->with('erros', ['Unidade não encontrada.']);
        }

        if ($this->unidadeModel->possuiDependencias($id)) {
            return redirect()->to('unidades')->with('erros', [
                'Não é possível excluir: existem clientes ou vendas vinculados a esta unidade. Remova os vínculos primeiro.',
            ]);
        }

        $this->unidadeModel->delete($id);

        return redirect()->to('unidades')->with('sucesso', 'Unidade excluída com sucesso.');
    }

    /**
     * Impede duas unidades com o mesmo identificador na mesma loja.
     */
    private function validarDuplicidade(int $lojaId, string $identificador, ?int $idIgnorar = null): ?string
    {
        if ($lojaId > 0 && $this->unidadeModel->identificadorDuplicado($lojaId, $identificador, $idIgnorar)) {
            return 'Já existe uma unidade com esse identificador nesta loja.';
        }

        return null;
    }

    /**
     * Lê os campos do formulário da unidade.
     */
    private function dadosDoFormulario(): array
    {
        return [
            'identificador' => trim((string) $this->request->getPost('identificador')),
            'loja_id'       => trim((string) $this->request->getPost('loja_id')),
        ];
    }
}
