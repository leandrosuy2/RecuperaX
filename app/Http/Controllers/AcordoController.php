<?php

namespace App\Http\Controllers;

use App\Models\Acordo;
use App\Models\Titulo;
use App\Models\Devedor;
use App\Models\Parcelamento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AcordoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $query = Acordo::with(['titulo', 'devedor', 'empresa', 'parcelas']);

        if ($request->filled('empresa_id')) {
            $query->where('empresa_id', $request->empresa_id);
        }

        if ($request->filled('devedor_id')) {
            $query->where('devedor_id', $request->devedor_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereHas('devedor', function($q2) use ($search) {
                    $q2->where('nome', 'like', "%{$search}%")
                       ->orWhere('razao_social', 'like', "%{$search}%");
                })
                ->orWhere('id', 'like', "%{$search}%");
            });
        }

        $acordos = $query->latest('created_at')->paginate(20);
        $empresas = \App\Models\Empresa::where('status_empresa', true)->get();

        return view('acordos.index', compact('acordos', 'empresas'));
    }

    public function create(Request $request)
    {
        $titulo = null;
        if ($request->filled('titulo_id')) {
            $titulo = Titulo::with(['devedor', 'empresa'])->findOrFail($request->titulo_id);
        }

        // Buscar títulos pendentes para seleção
        $titulos = Titulo::where(function($q) {
                $q->where('statusBaixa', 0)->orWhereNull('statusBaixa');
            })
            ->with(['devedor', 'empresa'])
            ->get();
        
        return view('acordos.create', compact('titulo', 'titulos'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'titulo_id' => 'required|exists:titulo,id',
            'entrada' => 'required|numeric|min:0',
            'data_entrada' => 'required|date',
            'qtde_prc' => 'required|integer|min:1|max:24',
            'venc_primeira_parcela' => 'required|date|after:today',
            'valor_por_parcela' => 'required|numeric|min:0',
            'contato' => 'nullable|string|max:255',
            'forma_pag_Id' => 'nullable|integer',
        ]);

        $titulo = Titulo::with(['devedor', 'empresa'])->findOrFail($validated['titulo_id']);

        DB::beginTransaction();
        try {
            // Calcular juros (8% ao mês, pró-rata por dia)
            $diasAtraso = Carbon::parse($titulo->dataVencimento)->diffInDays(now());
            $juros = ($titulo->valor * 0.08) * ($diasAtraso / 30);
            $valorTotalNegociacao = $validated['entrada'] + ($validated['valor_por_parcela'] * $validated['qtde_prc']);

            // Criar acordo
            $acordo = Acordo::create([
                'empresa_id' => $titulo->empresa_id,
                'devedor_id' => $titulo->devedor_id,
                'titulo_id' => $titulo->id,
                'entrada' => $validated['entrada'],
                'data_entrada' => $validated['data_entrada'],
                'qtde_prc' => $validated['qtde_prc'],
                'valor_total_negociacao' => $valorTotalNegociacao,
                'diferenca_dias' => $diasAtraso,
                'venc_primeira_parcela' => $validated['venc_primeira_parcela'],
                'valor_por_parcela' => $validated['valor_por_parcela'],
                'contato' => $validated['contato'] ?? null,
                'forma_pag_Id' => $validated['forma_pag_Id'] ?? null,
            ]);

            // Atualizar título original
            $titulo->statusBaixa = 3; // Negociado
            $titulo->valorRecebido = $validated['entrada'];
            $titulo->total_acordo = $validated['entrada'];
            $titulo->juros = $juros;
            $titulo->primeiro_vencimento = $validated['venc_primeira_parcela'];
            $titulo->forma_pag_Id = $validated['forma_pag_Id'];
            $titulo->save();

            // Criar títulos filhos (parcelas)
            $dataVencimento = Carbon::parse($validated['venc_primeira_parcela']);
            for ($i = 1; $i <= $validated['qtde_prc']; $i++) {
                $tituloFilho = Titulo::create([
                    'devedor_id' => $titulo->devedor_id,
                    'empresa_id' => $titulo->empresa_id,
                    'idTituloRef' => $titulo->id,
                    'num_titulo' => $titulo->num_titulo,
                    'tipo_doc_id' => $titulo->tipo_doc_id,
                    'dataEmissao' => $titulo->dataEmissao,
                    'dataVencimento' => $dataVencimento->copy()->addMonths($i - 1),
                    'valor' => $validated['valor_por_parcela'],
                    'statusBaixa' => 3, // Negociado
                    'qtde_parcelas' => $validated['qtde_prc'],
                    'nPrc' => $i,
                    'operador' => $titulo->operador,
                ]);

                // Criar parcela
                Parcelamento::create([
                    'acordo_id' => $acordo->id,
                    'parcela_numero' => $i,
                    'data_vencimento' => $dataVencimento->copy()->addMonths($i - 1),
                    'data_vencimento_parcela' => $dataVencimento->copy()->addMonths($i - 1),
                    'valor' => $validated['valor_por_parcela'],
                    'status' => 'PENDENTE',
                ]);
            }

            // TODO: Enviar email de negociação

            DB::commit();

            return redirect()->route('acordos.show', $acordo)
                ->with('success', 'Acordo criado com sucesso!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Erro ao criar acordo: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function show(Acordo $acordo)
    {
        $acordo->load(['titulo.devedor', 'titulo.empresa', 'devedor', 'empresa', 'parcelas']);
        
        return view('acordos.show', compact('acordo'));
    }

    public function gerarContrato(Acordo $acordo)
    {
        $acordo->load(['titulo.devedor', 'titulo.empresa', 'devedor', 'empresa', 'parcelas']);
        
        // TODO: Implementar geração de PDF do contrato usando DomPDF ou similar
        return view('acordos.contrato', compact('acordo'));
    }

    public function aprovar(Acordo $acordo)
    {
        // Marcar acordo como aprovado (se houver campo de status)
        // Por enquanto apenas redireciona
        return redirect()->back()
            ->with('success', 'Acordo aprovado com sucesso!');
    }

    public function quebrar(Acordo $acordo)
    {
        DB::beginTransaction();
        try {
            // Reverter status do título original
            if ($acordo->titulo) {
                $acordo->titulo->statusBaixa = 0; // Pendente novamente
                $acordo->titulo->valorRecebido = null;
                $acordo->titulo->total_acordo = null;
                $acordo->titulo->save();
            }

            // Marcar todas as parcelas como canceladas
            $acordo->parcelas()->update(['status' => 'CANCELADO']);

            // Marcar acordo como quebrado (se houver campo de status)
            // Por enquanto apenas registramos a ação

            DB::commit();

            return redirect()->back()
                ->with('success', 'Acordo quebrado com sucesso! O título foi revertido para pendente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Erro ao quebrar acordo: ' . $e->getMessage());
        }
    }
}
