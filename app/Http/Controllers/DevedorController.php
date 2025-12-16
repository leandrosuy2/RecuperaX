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
        // Query complexa para calcular status baseado nos títulos
        $query = DB::table('devedores as d')
            ->join('core_empresa as e', 'd.empresa_id', '=', 'e.id')
            ->leftJoin('titulo as t', 'd.id', '=', 't.devedor_id')
            ->select([
                'd.id',
                'd.nome',
                'd.cpf',
                'd.cnpj',
                'd.razao_social',
                'd.nome_fantasia',
                'd.tipo_pessoa',
                'd.telefone',
                'd.created_at',
                'e.nome_fantasia as empresa_nome',
                'e.id as empresa_id',
                DB::raw('COUNT(DISTINCT t.id) as qtd_titulos'),
                DB::raw('MIN(t.id) as titulo_id_exemplo'),
                DB::raw('CASE
                    WHEN MAX(CASE WHEN (t.statusBaixa=3 OR t.statusBaixaGeral=3) THEN 1 ELSE 0 END) = 1 THEN 3
                    WHEN MAX(CASE WHEN (t.statusBaixa=2 OR t.statusBaixaGeral=2) THEN 1 ELSE 0 END) = 1 THEN 2
                    ELSE 0
                END AS status_baixa_num')
            ])
            ->where('e.status_empresa', 1) // Apenas empresas ativadas
            ->groupBy('d.id', 'd.nome', 'd.cpf', 'd.cnpj', 'd.razao_social', 'd.nome_fantasia', 'd.tipo_pessoa', 'd.telefone', 'd.created_at', 'e.nome_fantasia', 'e.id')
            ->havingRaw('COUNT(DISTINCT t.id) > 0'); // Apenas devedores com títulos

        // Filtros
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('d.nome', 'like', "%{$search}%")
                  ->orWhere('d.razao_social', 'like', "%{$search}%")
                  ->orWhere('d.nome_fantasia', 'like', "%{$search}%")
                  ->orWhere('d.cpf', 'like', "%{$search}%")
                  ->orWhere('d.cnpj', 'like', "%{$search}%")
                  ->orWhere('d.telefone', 'like', "%{$search}%")
                  ->orWhere('e.nome_fantasia', 'like', "%{$search}%");
            });
        }

        if ($request->filled('empresa_id')) {
            $query->where('d.empresa_id', $request->empresa_id);
        }

        if ($request->filled('tipo_pessoa')) {
            $query->where('d.tipo_pessoa', $request->tipo_pessoa);
        }

        if ($request->filled('status')) {
            $statusMap = [
                'pendente' => 0,
                'quitado' => 2,
                'negociado' => 3
            ];

            if (isset($statusMap[$request->status])) {
                $statusNum = $statusMap[$request->status];
                if ($statusNum == 0) {
                    $query->havingRaw('CASE
                        WHEN MAX(CASE WHEN (t.statusBaixa=3 OR t.statusBaixaGeral=3) THEN 1 ELSE 0 END) = 1 THEN 3
                        WHEN MAX(CASE WHEN (t.statusBaixa=2 OR t.statusBaixaGeral=2) THEN 1 ELSE 0 END) = 1 THEN 2
                        ELSE 0
                    END = 0');
                } elseif ($statusNum == 2) {
                    $query->havingRaw('CASE
                        WHEN MAX(CASE WHEN (t.statusBaixa=3 OR t.statusBaixaGeral=3) THEN 1 ELSE 0 END) = 1 THEN 3
                        WHEN MAX(CASE WHEN (t.statusBaixa=2 OR t.statusBaixaGeral=2) THEN 1 ELSE 0 END) = 1 THEN 2
                        ELSE 0
                    END = 2');
                } elseif ($statusNum == 3) {
                    $query->havingRaw('CASE
                        WHEN MAX(CASE WHEN (t.statusBaixa=3 OR t.statusBaixaGeral=3) THEN 1 ELSE 0 END) = 1 THEN 3
                        WHEN MAX(CASE WHEN (t.statusBaixa=2 OR t.statusBaixaGeral=2) THEN 1 ELSE 0 END) = 1 THEN 2
                        ELSE 0
                    END = 3');
                }
            }
        }

        // Paginação
        $devedores = $query->orderBy('d.id', 'desc')->paginate(10);

        // Calcular totais corretos baseado no status dos títulos
        // Primeiro, vamos criar as queries base com os filtros aplicados
        $baseQueryNegociado = DB::table('devedores as d')
            ->join('core_empresa as e', 'd.empresa_id', '=', 'e.id')
            ->where('e.status_empresa', 1);

        $baseQueryQuitado = DB::table('devedores as d')
            ->join('core_empresa as e', 'd.empresa_id', '=', 'e.id')
            ->where('e.status_empresa', 1);

        $baseQueryPendente = DB::table('devedores as d')
            ->join('core_empresa as e', 'd.empresa_id', '=', 'e.id')
            ->where('e.status_empresa', 1);

        // Aplicar os mesmos filtros da query principal
        if ($request->filled('empresa_id')) {
            $baseQueryNegociado->where('d.empresa_id', $request->empresa_id);
            $baseQueryQuitado->where('d.empresa_id', $request->empresa_id);
            $baseQueryPendente->where('d.empresa_id', $request->empresa_id);
        }

        if ($request->filled('tipo_pessoa')) {
            $baseQueryNegociado->where('d.tipo_pessoa', $request->tipo_pessoa);
            $baseQueryQuitado->where('d.tipo_pessoa', $request->tipo_pessoa);
            $baseQueryPendente->where('d.tipo_pessoa', $request->tipo_pessoa);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $baseQueryNegociado->where(function($q) use ($search) {
                $q->where('d.nome', 'like', "%{$search}%")
                  ->orWhere('d.razao_social', 'like', "%{$search}%")
                  ->orWhere('d.nome_fantasia', 'like', "%{$search}%")
                  ->orWhere('d.cpf', 'like', "%{$search}%")
                  ->orWhere('d.cnpj', 'like', "%{$search}%")
                  ->orWhere('d.telefone', 'like', "%{$search}%")
                  ->orWhere('e.nome_fantasia', 'like', "%{$search}%");
            });
            $baseQueryQuitado->where(function($q) use ($search) {
                $q->where('d.nome', 'like', "%{$search}%")
                  ->orWhere('d.razao_social', 'like', "%{$search}%")
                  ->orWhere('d.nome_fantasia', 'like', "%{$search}%")
                  ->orWhere('d.cpf', 'like', "%{$search}%")
                  ->orWhere('d.cnpj', 'like', "%{$search}%")
                  ->orWhere('d.telefone', 'like', "%{$search}%")
                  ->orWhere('e.nome_fantasia', 'like', "%{$search}%");
            });
            $baseQueryPendente->where(function($q) use ($search) {
                $q->where('d.nome', 'like', "%{$search}%")
                  ->orWhere('d.razao_social', 'like', "%{$search}%")
                  ->orWhere('d.nome_fantasia', 'like', "%{$search}%")
                  ->orWhere('d.cpf', 'like', "%{$search}%")
                  ->orWhere('d.cnpj', 'like', "%{$search}%")
                  ->orWhere('d.telefone', 'like', "%{$search}%")
                  ->orWhere('e.nome_fantasia', 'like', "%{$search}%");
            });
        }

        // Agora calcular os totais com as condições específicas de status
        $totaisNegociado = (clone $baseQueryNegociado)
            ->whereExists(function($sub) {
                $sub->select(DB::raw(1))
                    ->from('titulo as t')
                    ->whereRaw('t.devedor_id = d.id')
                    ->where('t.statusBaixa', 3);
            })
            ->distinct('d.id')
            ->count('d.id');

        $totaisQuitado = (clone $baseQueryQuitado)
            ->whereExists(function($sub) {
                $sub->select(DB::raw(1))
                    ->from('titulo as t')
                    ->whereRaw('t.devedor_id = d.id')
                    ->where('t.statusBaixa', 2);
            })
            ->whereNotExists(function($sub) {
                $sub->select(DB::raw(1))
                    ->from('titulo as t')
                    ->whereRaw('t.devedor_id = d.id')
                    ->where('t.statusBaixa', 3);
            })
            ->distinct('d.id')
            ->count('d.id');

        $totaisPendente = (clone $baseQueryPendente)
            ->whereNotExists(function($sub) {
                $sub->select(DB::raw(1))
                    ->from('titulo as t')
                    ->whereRaw('t.devedor_id = d.id')
                    ->whereIn('t.statusBaixa', [2, 3]);
            })
            ->whereExists(function($sub) {
                $sub->select(DB::raw(1))
                    ->from('titulo as t')
                    ->whereRaw('t.devedor_id = d.id');
            })
            ->distinct('d.id')
            ->count('d.id');

        $totalGeral = $totaisNegociado + $totaisQuitado + $totaisPendente;

        $totais = collect([
            (object)['status_txt' => 'pendente', 'qtd' => $totaisPendente],
            (object)['status_txt' => 'negociado', 'qtd' => $totaisNegociado],
            (object)['status_txt' => 'quitado', 'qtd' => $totaisQuitado]
        ]);

        $totaisFormatados = [
            'pendente' => $totais->where('status_txt', 'pendente')->first()->qtd ?? 0,
            'negociado' => $totais->where('status_txt', 'negociado')->first()->qtd ?? 0,
            'quitado' => $totais->where('status_txt', 'quitado')->first()->qtd ?? 0,
            'total' => $totalGeral
        ];

        $empresas = Empresa::where('status_empresa', true)->get();

        return view('devedores.index', compact('devedores', 'empresas', 'totaisFormatados'));
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

    public function refazer(Request $request, Devedor $devedor)
    {
        try {
            DB::beginTransaction();

            // Buscar todos os títulos do devedor
            $titulos = $devedor->titulos;

            foreach ($titulos as $titulo) {
                // Resetar status de baixa
                $titulo->update([
                    'status_baixa' => 0,
                    'status_baixa_geral' => 0,
                    'data_baixa' => null,
                    'forma_pag_id' => null,
                    'valor_recebido' => 0,
                    'protocolo' => null,
                    'protocolo_gerado' => null,
                    'codigo_protocolo' => null,
                    'comprovante' => null
                ]);

                // Excluir acordos relacionados
                $titulo->acordos()->delete();

                // Excluir parcelamentos relacionados
                $titulo->parcelamentos()->delete();
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Devedor {$devedor->nome} foi refeito para status PENDENTE"
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Erro ao refazer devedor: ' . $e->getMessage()
            ], 500);
        }
    }

    public function consultarApi(Request $request)
    {
        // Placeholder - implementar integração com API externa
        return response()->json([
            'success' => false,
            'message' => 'Funcionalidade de consulta API será implementada'
        ]);
    }

    public function importar(Request $request)
    {
        // Placeholder - implementar importação de Excel
        return response()->json([
            'success' => false,
            'message' => 'Funcionalidade de importação será implementada'
        ]);
    }

    public function baixarModelo(Request $request)
    {
        // Placeholder - implementar download de modelo Excel
        return response()->json([
            'success' => false,
            'message' => 'Funcionalidade de download de modelo será implementada'
        ]);
    }

    public function excluirTodos(Request $request)
    {
        // Placeholder - implementar exclusão em massa com filtros
        return response()->json([
            'success' => false,
            'message' => 'Funcionalidade de exclusão em massa será implementada'
        ]);
    }
}
