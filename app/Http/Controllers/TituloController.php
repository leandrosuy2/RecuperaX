<?php

namespace App\Http\Controllers;

use App\Models\Titulo;
use App\Models\Devedor;
use App\Models\Empresa;
use App\Models\TipoDocTitulo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class TituloController extends Controller
{
    public function index(Request $request)
    {
        // Usar select específico para evitar carregar dados desnecessários
        $query = Titulo::with([
            'devedor:id,nome,razao_social,cpf,cnpj',
            'empresa:id,razao_social,nome_fantasia',
            'tipoDoc:id,name'
        ]);

        if ($request->filled('statusBaixa')) {
            $query->where('statusBaixa', $request->statusBaixa);
        }

        if ($request->filled('devedor_id')) {
            $query->where('devedor_id', $request->devedor_id);
        }

        if ($request->filled('empresa_id')) {
            $query->where('empresa_id', $request->empresa_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('num_titulo', 'like', "%{$search}%")
                  ->orWhereHas('devedor', function($q) use ($search) {
                      $q->where('nome', 'like', "%{$search}%")
                        ->orWhere('razao_social', 'like', "%{$search}%");
                  });
            });
        }

        $titulos = $query->latest('created_at')->paginate(20);
        
        // Carregar apenas campos necessários e limitar resultados para filtros
        $devedores = Devedor::select('id', 'nome', 'razao_social', 'cpf', 'cnpj')
            ->orderBy('nome')
            ->orderBy('razao_social')
            ->limit(1000)
            ->get();
        
        $empresas = Empresa::where('status_empresa', true)
            ->select('id', 'razao_social', 'nome_fantasia')
            ->orderBy('razao_social')
            ->get();

        return view('titulos.index', compact('titulos', 'devedores', 'empresas'));
    }

    public function create(Request $request)
    {
        $devedor = null;
        if ($request->filled('devedor_id')) {
            $devedor = Devedor::select('id', 'nome', 'razao_social', 'cpf', 'cnpj')->findOrFail($request->devedor_id);
        }

        // Carregar apenas campos necessários e limitar resultados
        $devedores = Devedor::select('id', 'nome', 'razao_social', 'cpf', 'cnpj')
            ->orderBy('nome')
            ->orderBy('razao_social')
            ->limit(1000)
            ->get();
        
        $empresas = Empresa::where('status_empresa', true)
            ->select('id', 'razao_social', 'nome_fantasia')
            ->orderBy('razao_social')
            ->get();
        
        $tiposDoc = TipoDocTitulo::select('id', 'name')->orderBy('name')->get();

        return view('titulos.create', compact('devedor', 'devedores', 'empresas', 'tiposDoc'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'devedor_id' => 'required|exists:devedores,id',
            'empresa_id' => 'required|exists:core_empresa,id',
            'num_titulo' => 'required|integer',
            'tipo_doc_id' => 'nullable|exists:tipo_doc_titulo,id',
            'dataEmissao' => 'required|date',
            'dataVencimento' => 'required|date',
            'valor' => 'required|numeric|min:0',
            'juros' => 'nullable|numeric|min:0',
            'operador' => 'nullable|string|max:255',
        ]);

        $titulo = Titulo::create($validated);

        return redirect()->route('titulos.show', $titulo)
            ->with('success', 'Título cadastrado com sucesso!');
    }

    /**
     * Exibe os detalhes de um título
     */
    public function show(Titulo $titulo)
    {
        $titulo->load(['devedor', 'empresa', 'tipoDoc', 'acordos.parcelas']);
        
        return view('titulos.show', compact('titulo'));
    }

    public function edit(Titulo $titulo)
    {
        // Carregar apenas campos necessários
        $devedores = Devedor::select('id', 'nome', 'razao_social', 'cpf', 'cnpj')
            ->orderBy('nome')
            ->orderBy('razao_social')
            ->limit(1000)
            ->get();
        
        $empresas = Empresa::where('status_empresa', true)
            ->select('id', 'razao_social', 'nome_fantasia')
            ->orderBy('razao_social')
            ->get();
        
        $tiposDoc = TipoDocTitulo::select('id', 'name')->orderBy('name')->get();

        return view('titulos.edit', compact('titulo', 'devedores', 'empresas', 'tiposDoc'));
    }

    public function update(Request $request, Titulo $titulo)
    {
        $validated = $request->validate([
            'devedor_id' => 'required|exists:devedores,id',
            'empresa_id' => 'required|exists:core_empresa,id',
            'num_titulo' => 'required|integer',
            'tipo_doc_id' => 'nullable|exists:tipo_doc_titulo,id',
            'dataEmissao' => 'required|date',
            'dataVencimento' => 'required|date',
            'valor' => 'required|numeric|min:0',
            'juros' => 'nullable|numeric|min:0',
            'operador' => 'nullable|string|max:255',
        ]);

        $titulo->update($validated);

        return redirect()->route('titulos.show', $titulo)
            ->with('success', 'Título atualizado com sucesso!');
    }

    public function destroy(Titulo $titulo)
    {
        if ($titulo->acordos()->count() > 0) {
            return redirect()->back()
                ->with('error', 'Não é possível excluir título com acordos vinculados.');
        }

        $titulo->delete();

        return redirect()->route('titulos.index')
            ->with('success', 'Título excluído com sucesso!');
    }

    /**
     * Finaliza o trabalho do dia para um título (atualiza ultima_acao)
     */
    public function finalizar(Request $request, Titulo $titulo)
    {
        $request->validate([
            'observacao' => 'nullable|string|max:1000',
        ]);

        $titulo->ultima_acao = now();
        if ($request->filled('observacao')) {
            // Aqui você pode salvar a observação em uma tabela de histórico ou campo específico
            // Por enquanto, apenas atualizamos a ultima_acao
        }
        $titulo->save();

        return redirect()->back()
            ->with('success', 'Título finalizado com sucesso!');
    }

    /**
     * Baixa um título (marca como quitado)
     */
    public function baixar(Request $request, Titulo $titulo)
    {
        $validated = $request->validate([
            'valor_recebido' => 'required|numeric|min:0',
            'data_baixa' => 'required|date',
            'observacao' => 'nullable|string|max:1000',
        ]);

        DB::beginTransaction();
        try {
            $titulo->statusBaixa = 2; // Quitado
            $titulo->valorRecebido = $validated['valor_recebido'];
            $titulo->data_baixa = $validated['data_baixa'];
            $titulo->ultima_acao = now();
            $titulo->save();

            DB::commit();

            return redirect()->back()
                ->with('success', 'Título baixado com sucesso!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Erro ao baixar título: ' . $e->getMessage());
        }
    }

    public function quitarParcela(Request $request, Titulo $titulo)
    {
        $validated = $request->validate([
            'valor_recebido' => 'required|numeric|min:' . ($titulo->valor ?? 0),
            'data_baixa' => 'required|date',
            'forma_pagamento' => 'required|integer',
            'comprovante' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
        ]);

        DB::beginTransaction();
        try {
            $titulo->statusBaixa = 2; // Quitado
            $titulo->valorRecebido = $validated['valor_recebido'];
            $titulo->data_baixa = $validated['data_baixa'];
            $titulo->forma_pag_Id = $validated['forma_pagamento'];
            $titulo->ultima_acao = now();
            
            if ($request->hasFile('comprovante')) {
                $titulo->comprovante = $request->file('comprovante')->store('comprovantes', 'public');
            }
            
            $titulo->save();

            DB::commit();

            return redirect()->back()
                ->with('success', 'Parcela quitada com sucesso!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Erro ao quitar parcela: ' . $e->getMessage());
        }
    }

    public function gerarPdf(Titulo $titulo)
    {
        $titulo->load(['devedor', 'empresa', 'tipoDoc', 'acordos.parcelas']);
        
        // TODO: Implementar geração de PDF usando DomPDF ou similar
        // Por enquanto retorna view
        return view('titulos.pdf', compact('titulo'));
    }

    public function gerarRecibo(Titulo $titulo)
    {
        if (!$titulo->isQuitado() && $titulo->valorRecebido <= 0) {
            return redirect()->back()
                ->with('error', 'Título não está quitado ou não possui valor recebido.');
        }

        $titulo->load(['devedor', 'empresa']);
        
        // TODO: Implementar geração de PDF do recibo
        return view('titulos.recibo', compact('titulo'));
    }

    public function anexarComprovante(Request $request, Titulo $titulo)
    {
        $validated = $request->validate([
            'comprovante' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        if ($titulo->comprovante) {
            Storage::disk('public')->delete($titulo->comprovante);
        }

        $titulo->comprovante = $request->file('comprovante')->store('comprovantes', 'public');
        $titulo->save();

        return redirect()->back()
            ->with('success', 'Comprovante anexado com sucesso!');
    }

    public function baixarComprovante(Titulo $titulo)
    {
        if (!$titulo->comprovante) {
            abort(404, 'Comprovante não encontrado');
        }

        return Storage::disk('public')->download($titulo->comprovante);
    }

    public function anexarContrato(Request $request, Titulo $titulo)
    {
        $validated = $request->validate([
            'contrato' => 'required|file|mimes:pdf|max:10240',
        ]);

        if ($titulo->contrato) {
            Storage::disk('public')->delete($titulo->contrato);
        }

        $titulo->contrato = $request->file('contrato')->store('contratos', 'public');
        $titulo->save();

        return redirect()->back()
            ->with('success', 'Contrato anexado com sucesso!');
    }

    public function baixarContrato(Titulo $titulo)
    {
        if (!$titulo->contrato) {
            abort(404, 'Contrato não encontrado');
        }

        return Storage::disk('public')->download($titulo->contrato);
    }

    public function alterarOperador(Request $request, Titulo $titulo)
    {
        $validated = $request->validate([
            'operador' => 'required|string|max:255',
        ]);

        $titulo->operador = $validated['operador'];
        $titulo->save();

        return redirect()->back()
            ->with('success', 'Operador alterado com sucesso!');
    }

    public function quitados(Request $request)
    {
        $query = Titulo::where('statusBaixa', 2)
            ->with(['devedor', 'empresa']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('num_titulo', 'like', "%{$search}%")
                  ->orWhereHas('devedor', function($q) use ($search) {
                      $q->where('nome', 'like', "%{$search}%")
                        ->orWhere('razao_social', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('data_inicio')) {
            $query->whereDate('data_baixa', '>=', $request->data_inicio);
        }

        if ($request->filled('data_fim')) {
            $query->whereDate('data_baixa', '<=', $request->data_fim);
        }

        $titulos = $query->latest('data_baixa')->paginate(20);

        return view('titulos.quitados', compact('titulos'));
    }
}
