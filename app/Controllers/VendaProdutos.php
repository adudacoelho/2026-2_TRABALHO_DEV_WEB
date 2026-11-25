<?php

namespace App\Controllers;

use App\Models\ClienteModel;
use App\Models\ClienteUnidadeModel;
use App\Models\LojaModel;
use App\Models\ProdutoModel;
use App\Models\UnidadeModel;
use App\Models\VendaProdutoModel;
use CodeIgniter\HTTP\RedirectResponse;
use CodeIgniter\HTTP\Response;

class VendaProdutos extends BaseController
{
    private VendaProdutoModel $vendaProdutoModel;
    private ClienteModel $clienteModel;
    private LojaModel $lojaModel;
    private ProdutoModel $produtoModel;
    private UnidadeModel $unidadeModel;
    private ClienteUnidadeModel $clienteUnidadeModel;

    public function __construct()
    {
        $this->vendaProdutoModel   = new VendaProdutoModel();
        $this->clienteModel        = new ClienteModel();
        $this->lojaModel           = new LojaModel();
        $this->produtoModel        = new ProdutoModel();
        $this->unidadeModel        = new UnidadeModel();
        $this->clienteUnidadeModel = new ClienteUnidadeModel();
    }

    public function index(): string
    {
        $busca = trim((string) $this->request->getGet('busca'));

        return view('venda_produtos/index', [
            'titulo' => 'Vendas',
            'busca'  => $busca,
            'vendas' => $this->vendaProdutoModel->listar($busca),
        ]);
    }

    /**
     * Opções dependentes do formulário: lojas do cliente e unidades do cliente na loja.
     */
    public function opcoes(): Response
    {
        $clienteId = (int) $this->request->getGet('cliente_id');
        $lojaId    = (int) $this->request->getGet('loja_id');

        $lojas    = $clienteId > 0 ? $this->clienteUnidadeModel->lojasDoCliente($clienteId) : [];
        $unidades = ($clienteId > 0 && $lojaId > 0)
            ? $this->clienteUnidadeModel->unidadesDoClienteNaLoja($clienteId, $lojaId)
            : [];

        return $this->response->setJSON([
            'lojas'    => $lojas,
            'unidades' => $unidades,
        ]);
    }

    public function novo(): string
    {
        return view('venda_produtos/form', [
            'titulo'   => 'Nova venda',
            'acao'     => site_url('venda-produtos/salvar'),
            'venda'    => [
                'cliente_id'     => '',
                'loja_id'        => '',
                'produto_id'     => '',
                'unidade_id'     => '',
                'data'           => date('Y-m-d H:i:s'),
                'quantidade'     => '1',
                'preco_unitario' => '',
            ],
            'clientes' => $this->clienteModel->orderBy('nome', 'ASC')->findAll(),
            'lojas'    => $this->lojaModel->orderBy('nome', 'ASC')->findAll(),
            'produtos' => $this->produtoModel->orderBy('nome', 'ASC')->findAll(),
            'unidades' => $this->unidadeModel->listar(),
        ]);
    }

    public function salvar(): RedirectResponse
    {
        $dados = $this->dadosDoFormulario();

        $erroVinculo = $this->clienteUnidadeModel->validarVinculo((int) $dados['cliente_id'], (int) $dados['loja_id'], (int) $dados['unidade_id']);
        if ($erroVinculo !== null) {
            return redirect()->back()->withInput()->with('erros', [$erroVinculo]);
        }

        if (! $this->vendaProdutoModel->registrarVenda($dados)) {
            return redirect()->back()->withInput()->with('erros', $this->vendaProdutoModel->errors());
        }

        return redirect()->to('venda-produtos')->with('sucesso', 'Venda cadastrada e estoque baixado com sucesso.');
    }

    public function editar(int $id): string|RedirectResponse
    {
        $venda = $this->vendaProdutoModel->find($id);

        if ($venda === null) {
            return redirect()->to('venda-produtos')->with('erros', ['Venda não encontrada.']);
        }

        return view('venda_produtos/form', [
            'titulo'   => 'Editar venda',
            'acao'     => site_url('venda-produtos/atualizar/' . $id),
            'venda'    => $venda,
            'clientes' => $this->clienteModel->orderBy('nome', 'ASC')->findAll(),
            'lojas'    => $this->lojaModel->orderBy('nome', 'ASC')->findAll(),
            'produtos' => $this->produtoModel->orderBy('nome', 'ASC')->findAll(),
            'unidades' => $this->unidadeModel->listar(),
        ]);
    }

    public function atualizar(int $id): RedirectResponse
    {
        if ($this->vendaProdutoModel->find($id) === null) {
            return redirect()->to('venda-produtos')->with('erros', ['Venda não encontrada.']);
        }

        $dados = $this->dadosDoFormulario();

        $erroVinculo = $this->clienteUnidadeModel->validarVinculo((int) $dados['cliente_id'], (int) $dados['loja_id'], (int) $dados['unidade_id']);
        if ($erroVinculo !== null) {
            return redirect()->back()->withInput()->with('erros', [$erroVinculo]);
        }

        // O "id" segue junto para as regras de validação do model.
        $dados += ['id' => $id];

        if (! $this->vendaProdutoModel->revisarVenda($id, $dados)) {
            return redirect()->back()->withInput()->with('erros', $this->vendaProdutoModel->errors());
        }

        return redirect()->to('venda-produtos')->with('sucesso', 'Venda atualizada e estoque ajustado com sucesso.');
    }

    public function excluir(int $id): RedirectResponse
    {
        if ($this->vendaProdutoModel->find($id) === null) {
            return redirect()->to('venda-produtos')->with('erros', ['Venda não encontrada.']);
        }

        if (! $this->vendaProdutoModel->removerVenda($id)) {
            return redirect()->to('venda-produtos')->with('erros', ['Não foi possível excluir a venda e devolver o estoque.']);
        }

        return redirect()->to('venda-produtos')->with('sucesso', 'Venda excluída e estoque devolvido com sucesso.');
    }

    /**
     * Converte o valor do input datetime-local (Y-m-d\TH:i) para Y-m-d H:i:s.
     */
    private function dataDoFormulario(): string
    {
        $data = str_replace('T', ' ', trim((string) $this->request->getPost('data')));

        if (strlen($data) === 16) {
            $data .= ':00';
        }

        return $data;
    }

    /**
     * Lê os campos do formulário de venda.
     */
    private function dadosDoFormulario(): array
    {
        return [
            'cliente_id'     => trim((string) $this->request->getPost('cliente_id')),
            'loja_id'        => trim((string) $this->request->getPost('loja_id')),
            'produto_id'     => trim((string) $this->request->getPost('produto_id')),
            'unidade_id'     => trim((string) $this->request->getPost('unidade_id')),
            'data'           => $this->dataDoFormulario(),
            'quantidade'     => trim((string) $this->request->getPost('quantidade')),
            'preco_unitario' => trim((string) $this->request->getPost('preco_unitario')),
        ];
    }
}
