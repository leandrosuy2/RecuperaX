<?php

namespace App\Http\Controllers;

use App\Models\Devedor;
use App\Models\Empresa;
use App\Models\Titulo;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class DevedorController extends Controller
{
    public function index(Request $request)
    {
        $query = Devedor::with(['empresa']);

        // Filtros
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nome', 'like', "%{$search}%")
                  ->orWhere('razao_social', 'like', "%{$search}%")
                  ->orWhere('cpf', 'like', "%{$search}%")
                  ->orWhere('cnpj', 'like', "%{$search}%");
            });
        }

        if ($request->filled('empresa_id')) {
            $query->where('empresa_id', $request->empresa_id);
        }

        if ($request->filled('tipo_pessoa')) {
            $query->where('tipo_pessoa', $request->tipo_pessoa);
        }

        if ($request->filled('status_code')) {
            $query->where('status_code', $request->status_code);
        }

        $devedores = $query->latest('created_at')->paginate(20);
        $empresas = Empresa::where('status_empresa', true)->get();

        return view('devedores.index', compact('devedores', 'empresas'));
    }

    public function create()
    {
        $empresas = Empresa::where('status_empresa', true)->get();
        return view('devedores.create', compact('empresas'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'empresa_id' => 'required|exists:core_empresa,id',
            'tipo_pessoa' => 'required|in:F,J',
            'cpf' => 'nullable|string|max:14',
            'cnpj' => 'nullable|string|max:18',
            'nome' => 'nullable|string|max:255',
            'razao_social' => 'nullable|string|max:255',
            'nome_fantasia' => 'nullable|string|max:255',
            'nome_mae' => 'nullable|string|max:255',
            'rg' => 'nullable|string|max:20',
            'nome_socio' => 'nullable|string|max:255',
            'cpf_socio' => 'nullable|string|max:14',
            'rg_socio' => 'nullable|string|max:20',
            'telefone' => 'nullable|string|max:20',
            'telefone2' => 'nullable|string|max:20',
            'telefone3' => 'nullable|string|max:20',
            'telefone4' => 'nullable|string|max:20',
            'telefone5' => 'nullable|string|max:20',
            'telefone6' => 'nullable|string|max:20',
            'telefone7' => 'nullable|string|max:20',
            'telefone8' => 'nullable|string|max:20',
            'telefone9' => 'nullable|string|max:20',
            'telefone10' => 'nullable|string|max:20',
            'email1' => 'nullable|email|max:255',
            'email2' => 'nullable|email|max:255',
            'cep' => 'nullable|string|max:10',
            'endereco' => 'nullable|string|max:255',
            'bairro' => 'nullable|string|max:100',
            'uf' => 'nullable|string|size:2',
            'cidade' => 'nullable|string|max:100',
            'observacao' => 'nullable|string',
        ]);

        $devedor = Devedor::create($validated);

        return redirect()->route('devedores.show', $devedor)
            ->with('success', 'Devedor cadastrado com sucesso!');
    }

    public function show(Devedor $devedor)
    {
        $devedor->load([
            'empresa',
            'titulos' => function($q) {
                $q->with(['empresa:id,razao_social,nome_fantasia', 'tipoDoc:id,name'])
                  ->orderBy('dataVencimento', 'asc');
            },
            'acordos.parcelas',
            'agendamentos' => function($q) {
                $q->orderBy('data_retorno', 'desc');
            },
            'followUps' => function($q) {
                $q->orderBy('created_at', 'desc');
            }
        ]);
        
        // Calcular juros automaticamente para títulos pendentes
        foreach ($devedor->titulos as $titulo) {
            if (($titulo->statusBaixa == 0 || $titulo->statusBaixa === null) && $titulo->dataVencimento) {
                // Calcular dias de atraso corretamente
                $dataVencimento = Carbon::parse($titulo->dataVencimento);
                $hoje = Carbon::now();
                
                if ($dataVencimento < $hoje) {
                    // Título vencido - calcular dias de atraso
                    // diffInDays com false retorna diferença absoluta, mas precisamos garantir positivo
                    $diasAtraso = (int) $hoje->diffInDays($dataVencimento);
                    
                    if ($titulo->valor > 0 && $diasAtraso > 0) {
                        // Calcular juros: 8% ao mês, pró-rata por dia
                        $juros = ($titulo->valor * 0.08) * ($diasAtraso / 30);
                        
                        // Atualizar sempre para garantir cálculo correto
                        $titulo->juros = max(0, round($juros, 2));
                        $titulo->dias_atraso = $diasAtraso;
                        $titulo->save();
                    }
                } else {
                    // Título não vencido ainda
                    if (($titulo->juros ?? 0) != 0 || ($titulo->dias_atraso ?? 0) != 0) {
                        $titulo->juros = 0;
                        $titulo->dias_atraso = 0;
                        $titulo->save();
                    }
                }
            }
        }
        
        // Recarregar títulos após calcular juros
        $devedor->refresh();
        $devedor->load('titulos');
        
        // Separar títulos em entrada e associados
        $titulosEntrada = $devedor->titulos->filter(function($titulo) {
            return !$titulo->idTituloRef || $titulo->idTituloRef == 0;
        });
        
        $titulosAssociados = $devedor->titulos->filter(function($titulo) {
            return $titulo->idTituloRef && $titulo->idTituloRef > 0;
        });
        
        // Calcular totais
        $totalQuitado = $devedor->titulos->filter(function($t) { return $t->statusBaixa == 2; })->sum('valorRecebido');
        $totalNegociado = $devedor->titulos->filter(function($t) { return $t->statusBaixa == 3; })->sum('valor');
        $totalPendente = $devedor->titulos->filter(function($t) { return $t->statusBaixa == 0 || $t->statusBaixa === null; })->sum('valor');
        
        // Calcular valores para proposta (apenas pendentes)
        $titulosPendentes = $devedor->titulos->filter(function($titulo) {
            return ($titulo->statusBaixa == 0 || $titulo->statusBaixa === null) && ($titulo->valor ?? 0) > 0;
        });
        
        $baseComJuros = $titulosPendentes->sum(function($titulo) {
            $valor = max(0, $titulo->valor ?? 0);
            $juros = max(0, $titulo->juros ?? 0);
            return $valor + $juros;
        });
        
        $capital = $titulosPendentes->sum(function($titulo) {
            return max(0, $titulo->valor ?? 0);
        });
        
        $jurosApurados = $titulosPendentes->sum(function($titulo) {
            return max(0, $titulo->juros ?? 0);
        });
        
        $primeiroVencimento = $titulosPendentes->min('dataVencimento');
        if ($primeiroVencimento) {
            $dataVenc = Carbon::parse($primeiroVencimento);
            $hoje = Carbon::now();
            if ($dataVenc < $hoje) {
                $diasAtrasoMax = (int) $hoje->diffInDays($dataVenc);
            } else {
                $diasAtrasoMax = 0;
            }
        } else {
            $diasAtrasoMax = 0;
        }
        
        // Buscar operador atual (do primeiro título com operador)
        $operadorAtual = $devedor->titulos->first(function($t) { return !empty($t->operador); })?->operador;
        
        // Buscar consultor (supervisor da empresa)
        $consultorAtual = $devedor->empresa?->supervisor;
        
        // Buscar lista de operadores (usuários ativos)
        $operadores = \App\Models\User::where('is_active', true)
            ->select('id', 'name')
            ->orderBy('name')
            ->get();
        
        // Preparar mensagens WhatsApp (templates básicos)
        $msgPadrao = "Olá %Nome%! Somos da %NomeCredor% e gostaríamos de negociar sua dívida.";
        $msgVencidas = "Olá %Nome%! Você possui parcelas vencidas. Entre em contato conosco para negociar.";
        $msgAVencer = "Olá %Nome%! Você possui parcelas a vencer. Entre em contato conosco.";
        $msgQuebra = "Olá %Nome%! Informamos sobre a quebra do acordo. Entre em contato conosco.";
        
        // Verificar óbito (placeholder - implementar API real)
        $obitoInfo = null; // TODO: Implementar consulta de óbito via API
        
        // Mapeamento de forma de pagamento
        $formaPagamentoMap = [
            0 => 'Pix',
            1 => 'Dinheiro',
            2 => 'Cartão de Débito',
            3 => 'Cartão de Crédito',
            4 => 'Cheque',
            5 => 'Depósito em Conta',
            6 => 'Pagamento na Loja',
            7 => 'Boleto Bancário',
            8 => 'Duplicata',
            9 => 'Recebimento pelo credor',
        ];
        
        return view('devedores.show', compact(
            'devedor',
            'titulosEntrada',
            'titulosAssociados',
            'baseComJuros',
            'capital',
            'jurosApurados',
            'primeiroVencimento',
            'diasAtrasoMax',
            'totalQuitado',
            'totalNegociado',
            'totalPendente',
            'operadorAtual',
            'consultorAtual',
            'operadores',
            'msgPadrao',
            'msgVencidas',
            'msgAVencer',
            'msgQuebra',
            'obitoInfo',
            'formaPagamentoMap'
        ));
    }

    public function edit(Devedor $devedor)
    {
        $empresas = Empresa::where('status_empresa', true)->get();
        return view('devedores.edit', compact('devedor', 'empresas'));
    }

    public function update(Request $request, Devedor $devedor)
    {
        $validated = $request->validate([
            'empresa_id' => 'required|exists:core_empresa,id',
            'tipo_pessoa' => 'required|in:F,J',
            'cpf' => 'nullable|string|max:14',
            'cnpj' => 'nullable|string|max:18',
            'nome' => 'nullable|string|max:255',
            'razao_social' => 'nullable|string|max:255',
            'nome_fantasia' => 'nullable|string|max:255',
            'nome_mae' => 'nullable|string|max:255',
            'rg' => 'nullable|string|max:20',
            'nome_socio' => 'nullable|string|max:255',
            'cpf_socio' => 'nullable|string|max:14',
            'rg_socio' => 'nullable|string|max:20',
            'telefone' => 'nullable|string|max:20',
            'telefone2' => 'nullable|string|max:20',
            'telefone3' => 'nullable|string|max:20',
            'telefone4' => 'nullable|string|max:20',
            'telefone5' => 'nullable|string|max:20',
            'telefone6' => 'nullable|string|max:20',
            'telefone7' => 'nullable|string|max:20',
            'telefone8' => 'nullable|string|max:20',
            'telefone9' => 'nullable|string|max:20',
            'telefone10' => 'nullable|string|max:20',
            'email1' => 'nullable|email|max:255',
            'email2' => 'nullable|email|max:255',
            'cep' => 'nullable|string|max:10',
            'endereco' => 'nullable|string|max:255',
            'bairro' => 'nullable|string|max:100',
            'uf' => 'nullable|string|size:2',
            'cidade' => 'nullable|string|max:100',
            'observacao' => 'nullable|string',
        ]);

        $devedor->update($validated);

        return redirect()->route('devedores.show', $devedor)
            ->with('success', 'Devedor atualizado com sucesso!');
    }

    public function destroy(Devedor $devedor)
    {
        if ($devedor->titulos()->count() > 0) {
            return redirect()->route('devedores.index')
                ->with('error', 'Não é possível excluir devedor com títulos vinculados.');
        }

        $devedor->delete();

        return redirect()->route('devedores.index')
            ->with('success', 'Devedor excluído com sucesso!');
    }

    public function excluirEmMassa(Request $request)
    {
        $ids = $request->input('ids', []);
        
        if (empty($ids)) {
            return redirect()->back()->with('error', 'Nenhum devedor selecionado.');
        }

        $count = Devedor::whereIn('id', $ids)
            ->whereDoesntHave('titulos')
            ->delete();

        return redirect()->back()
            ->with('success', "{$count} devedores excluídos com sucesso!");
    }

    public function titulos(Devedor $devedor)
    {
        $titulos = $devedor->titulos()->with(['empresa', 'tipoDoc'])->latest('created_at')->paginate(20);
        return view('devedores.titulos', compact('devedor', 'titulos'));
    }

    public function alterarOperador(Request $request, Devedor $devedor)
    {
        $validated = $request->validate([
            'operador' => 'required|string|max:255',
        ]);

        // Atualizar operador em todos os títulos do devedor
        $devedor->titulos()->update(['operador' => $validated['operador']]);

        return response()->json([
            'success' => true,
            'message' => 'Operador alterado com sucesso!'
        ]);
    }

    public function alterarConsultor(Request $request, Devedor $devedor)
    {
        $validated = $request->validate([
            'consultor' => 'required|string|max:255',
        ]);

        // Atualizar supervisor da empresa
        if ($devedor->empresa) {
            $devedor->empresa->supervisor = $validated['consultor'];
            $devedor->empresa->save();
        }

        return response()->json([
            'success' => true,
            'message' => 'Consultor alterado com sucesso!'
        ]);
    }

    public function adicionarTitulo(Devedor $devedor)
    {
        $empresas = Empresa::where('status_empresa', true)
            ->select('id', 'razao_social', 'nome_fantasia')
            ->orderBy('razao_social')
            ->get();
        
        // Carregar apenas campos necessários e limitar resultados
        $devedores = Devedor::select('id', 'nome', 'razao_social', 'cpf', 'cnpj', 'tipo_pessoa')
            ->orderBy('nome')
            ->orderBy('razao_social')
            ->limit(1000)
            ->get();
        
        $tiposDoc = \App\Models\TipoDocTitulo::select('id', 'name')->orderBy('name')->get();
        
        return view('titulos.create', compact('devedor', 'devedores', 'empresas', 'tiposDoc'));
    }

    public function editarTelefones(Devedor $devedor)
    {
        return view('devedores.editar-telefones', compact('devedor'));
    }

    public function atualizarTelefones(Request $request, Devedor $devedor)
    {
        $rules = [];
        for ($i = 1; $i <= 10; $i++) {
            $rules["telefone{$i}"] = 'nullable|string|max:20';
            $rules["telefone{$i}_valido"] = 'nullable|in:SIM,NAO,NAO VERIFICADO';
        }

        $validated = $request->validate($rules);
        $devedor->update($validated);

        return redirect()->route('devedores.show', $devedor)
            ->with('success', 'Telefones atualizados com sucesso!');
    }
}
