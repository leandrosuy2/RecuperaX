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

    public function quitados_listar(Request $request)
    {
        $user = $request->user();
        $is_admin = $user && ($user->is_staff || $user->is_superuser);
        $user_name = $user ? ($user->name ?: $user->username) : '';

        // Usar Eloquent com query builder para melhor performance
        $query = DB::table('titulo as t')
            ->join('devedores as d', 'd.id', '=', 't.devedor_id')
            ->join('core_empresa as e', 'e.id', '=', 'd.empresa_id')
            ->select([
                't.data_baixa',
                't.dataVencimento',
                't.valorRecebido',
                'd.nome',
                'd.cpf',
                'd.cnpj',
                'e.nome_fantasia',
                't.idTituloRef',
                'e.operador',
                'e.supervisor'
            ])
            ->whereNotNull('t.data_baixa')
            ->whereNotNull('t.valorRecebido')
            ->where('e.status_empresa', 1);

        // Aplicar filtros de operador/supervisor para usuários não-admin
        if (!$is_admin && $user_name) {
            $query->where(function($q) use ($user_name) {
                $q->whereRaw('LOWER(e.operador) = LOWER(?)', [$user_name])
                  ->orWhereRaw('LOWER(COALESCE(e.supervisor, \'\')) = LOWER(?)', [$user_name]);
            });
        }

        // Filtro de período
        if ($request->filled('data_inicio')) {
            $query->where('t.data_baixa', '>=', $request->data_inicio);
        }

        if ($request->filled('data_fim')) {
            $query->where('t.data_baixa', '<=', $request->data_fim);
        }

        // Filtro de tipo (parcela vs quitação)
        if ($request->filled('tipo')) {
            if ($request->tipo === 'parcela') {
                $query->whereNotNull('t.idTituloRef');
            } elseif ($request->tipo === 'quitacao') {
                $query->whereNull('t.idTituloRef');
            }
        }

        // Filtros de texto
        if ($request->filled('devedor')) {
            $query->whereRaw('LOWER(d.nome) LIKE LOWER(?)', ['%' . $request->devedor . '%']);
        }

        if ($request->filled('empresa')) {
            $query->whereRaw('LOWER(e.nome_fantasia) LIKE LOWER(?)', ['%' . $request->empresa . '%']);
        }

        // Filtros de valor
        if ($request->filled('valor_min')) {
            $valor_min = str_replace(',', '.', $request->valor_min);
            if (is_numeric($valor_min)) {
                $query->where('t.valorRecebido', '>=', $valor_min);
            }
        }

        if ($request->filled('valor_max')) {
            $valor_max = str_replace(',', '.', $request->valor_max);
            if (is_numeric($valor_max)) {
                $query->where('t.valorRecebido', '<=', $valor_max);
            }
        }

        // Filtros de operador/supervisor (apenas admin)
        if ($is_admin && $request->filled('operador')) {
            $query->whereRaw('LOWER(e.operador) = LOWER(?)', [$request->operador]);
        }

        if ($is_admin && $request->filled('supervisor')) {
            $query->whereRaw('LOWER(e.supervisor) = LOWER(?)', [$request->supervisor]);
        }

        // Executar query com paginação
        $paginator = $query->orderBy('t.data_baixa', 'desc')
                           ->orderBy('t.id', 'desc')
                           ->paginate(50);

        // Mapear dados para o formato esperado pela view
        $paginator->getCollection()->transform(function ($item) {
            return [
                'data_baixa' => $item->data_baixa ? date('d/m/Y', strtotime($item->data_baixa)) : '',
                'data_vencimento' => $item->dataVencimento ? date('d/m/Y', strtotime($item->dataVencimento)) : '',
                'valor_recebido' => $item->valorRecebido ? floatval($item->valorRecebido) : 0.0,
                'nome' => $item->nome ?: '',
                'cpf' => $item->cpf ?: '',
                'cnpj' => $item->cnpj ?: '',
                'empresa' => $item->nome_fantasia ?: '',
                'idTituloRef' => $item->idTituloRef,
                'operador' => $item->operador ?: '',
                'supervisor' => $item->supervisor ?: '',
            ];
        });

        // Calcular soma total de todos os registros (não apenas da página atual)
        $soma_total_query = clone $query;
        $soma_total = $soma_total_query->sum('t.valorRecebido');

        // Carregar listas para filtros (apenas admin)
        $operadores = [];
        $supervisores = [];
        if ($is_admin) {
            $operadores = DB::select("
                SELECT DISTINCT e.operador
                FROM core_empresa e
                WHERE e.status_empresa = 1
                  AND e.operador IS NOT NULL
                  AND e.operador <> ''
                ORDER BY 1
            ");

            $supervisores = DB::select("
                SELECT DISTINCT e.supervisor
                FROM core_empresa e
                WHERE e.status_empresa = 1
                  AND e.supervisor IS NOT NULL
                  AND e.supervisor <> ''
                ORDER BY 1
            ");
        }

        return view('titulos.quitados', compact(
            'paginator',
            'soma_total',
            'is_admin',
            'user_name',
            'operadores',
            'supervisores'
        ));
    }
}
