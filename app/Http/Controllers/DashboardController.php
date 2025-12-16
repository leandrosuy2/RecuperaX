<?php

namespace App\Http\Controllers;

use App\Models\Acordo;
use App\Models\Devedor;
use App\Models\Titulo;
use App\Models\Parcelamento;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        
        // Query base para títulos
        $titulosQuery = Titulo::query();
        
        // Filtros por perfil (baseado em empresa)
        if ($user->credor_id) {
            // Se o usuário tem credor_id, filtra por empresa relacionada ao credor
            $titulosQuery->whereHas('empresa', function($q) use ($user) {
                // Assumindo que há uma relação entre credor e empresa
                // Ajuste conforme sua estrutura real
            });
        }

        // KPIs Principais
        // Pendentes: statusBaixa = 0 ou NULL
        $pendentes = (clone $titulosQuery)
            ->where(function($q) {
                $q->where('statusBaixa', 0)
                  ->orWhereNull('statusBaixa');
            })
            ->count();
        
        // Quitados: statusBaixa = 2
        $quitados = (clone $titulosQuery)
            ->where('statusBaixa', 2)
            ->count();
        
        // Negociados: statusBaixa = 3
        $negociados = (clone $titulosQuery)
            ->where('statusBaixa', 3)
            ->count();
        
        $totalClientes = Devedor::count();
        
        // Negociados em Atraso (títulos negociados com dataVencimento < hoje)
        // Conforme especificação: "Lista devedores com títulos negociados que estão em atraso"
        // Conta títulos com statusBaixa = 3 (Negociado) e dataVencimento < hoje
        $negociadosEmAtraso = Titulo::where('statusBaixa', 3) // Negociado
            ->where('dataVencimento', '<', now())
            ->count();
        
        // Quitados Hoje (títulos com data_baixa = hoje)
        // Conforme especificação: "Soma do valor recebido (valorRecebido) de títulos quitados hoje"
        $quitadosHoje = (clone $titulosQuery)
            ->whereDate('data_baixa', today())
            ->where('statusBaixa', 2)
            ->sum('valorRecebido');
        
        // Detalhes de Quitados Hoje
        $quitadosHojeDetalhes = (clone $titulosQuery)
            ->whereDate('data_baixa', today())
            ->where('statusBaixa', 2)
            ->with(['devedor', 'empresa'])
            ->get();
        
        // Negociados Hoje (títulos com statusBaixa=3 criados hoje)
        // Conforme especificação: "Soma do valor de todos os títulos negociados no dia atual"
        $negociadosHoje = (clone $titulosQuery)
            ->where('statusBaixa', 3)
            ->whereDate('created_at', today())
            ->sum('valor');
        
        // Detalhes de Negociados Hoje
        $negociadosHojeDetalhes = (clone $titulosQuery)
            ->where('statusBaixa', 3)
            ->whereDate('created_at', today())
            ->with(['devedor', 'empresa'])
            ->get();

        // Ranking de Operadores - Títulos Quitados
        // Filtros de data e operador
        $dataRankingInicio = $request->get('data_ranking_inicio') ? Carbon::parse($request->get('data_ranking_inicio')) : null;
        $dataRankingFim = $request->get('data_ranking_fim') ? Carbon::parse($request->get('data_ranking_fim')) : null;
        $operadorFiltro = $request->get('operador_filtro');
        
        $rankingOperadoresQuery = Titulo::select(
                DB::raw('COALESCE(operador, "Sem Operador") as operador'),
                DB::raw('count(*) as total'),
                DB::raw('sum(valorRecebido) as valor_total')
            )
            ->where('statusBaixa', 2) // Quitados
            ->when($dataRankingInicio, function($q) use ($dataRankingInicio) {
                $q->whereDate('data_baixa', '>=', $dataRankingInicio);
            })
            ->when($dataRankingFim, function($q) use ($dataRankingFim) {
                $q->whereDate('data_baixa', '<=', $dataRankingFim);
            })
            ->when($operadorFiltro, function($q) use ($operadorFiltro) {
                if ($operadorFiltro === 'Sem Operador') {
                    $q->whereNull('operador')->orWhere('operador', '');
                } else {
                    $q->where('operador', $operadorFiltro);
                }
            })
            ->groupBy(DB::raw('COALESCE(operador, "Sem Operador")'))
            ->orderBy('total', 'desc');
        
        $rankingOperadores = $rankingOperadoresQuery->get();
        
        // Calcular total recebido geral
        $totalRecebidoGeral = $rankingOperadores->sum('valor_total');

        // Busca rápida (para o formulário) - precisa ser definida antes de usar no filtro
        $buscaRapida = $request->get('busca_rapida');

        // Agenda de Trabalho do Dia - Pendentes (títulos sem ação hoje)
        // Conforme especificação: "Lista títulos pendentes que não foram trabalhados hoje"
        // Apenas empresas ativas (status_empresa=1)
        $agendaPendentesQuery = (clone $titulosQuery)
            ->where(function($q) {
                $q->where('statusBaixa', 0)
                  ->orWhereNull('statusBaixa');
            })
            ->where(function($q) {
                $q->whereDate('ultima_acao', '<', today())
                  ->orWhereNull('ultima_acao');
            })
            ->whereHas('empresa', function($q) {
                $q->where('status_empresa', true);
            })
            ->with(['devedor', 'empresa']);
        
        // Filtro de busca rápida na agenda de pendentes
        if ($buscaRapida) {
            $agendaPendentesQuery->whereHas('devedor', function($q) use ($buscaRapida) {
                $q->where('nome', 'like', "%{$buscaRapida}%")
                  ->orWhere('razao_social', 'like', "%{$buscaRapida}%")
                  ->orWhere('cpf', 'like', "%{$buscaRapida}%")
                  ->orWhere('cnpj', 'like', "%{$buscaRapida}%");
            })->orWhereHas('empresa', function($q) use ($buscaRapida) {
                $q->where('razao_social', 'like', "%{$buscaRapida}%")
                  ->orWhere('nome_fantasia', 'like', "%{$buscaRapida}%");
            });
        }
        
        $agendaPendentes = $agendaPendentesQuery->orderBy('dataVencimento', 'asc')
            ->paginate(10, ['*'], 'agenda_page');

        // Agendamentos Pendentes (do modelo Agendamento)
        $agendamentosPendentes = \App\Models\Agendamento::where('status', 'Pendente')
            ->whereDate('data_retorno', today())
            ->with(['devedor', 'empresa'])
            ->get();

        // Negociados em Atraso - Lista detalhada
        // Conforme especificação: "Lista devedores com títulos negociados que estão em atraso"
        // Agrupado por empresa e devedor
        $dataInicio = $request->get('data_inicio') ? Carbon::parse($request->get('data_inicio')) : null;
        $dataFim = $request->get('data_fim') ? Carbon::parse($request->get('data_fim')) : null;
        
        $negociadosAtrasoQuery = (clone $titulosQuery)
            ->where('statusBaixa', 3) // Negociado
            ->where('dataVencimento', '<', now())
            ->when($dataInicio, function($q) use ($dataInicio) {
                $q->where('dataVencimento', '>=', $dataInicio);
            })
            ->when($dataFim, function($q) use ($dataFim) {
                $q->where('dataVencimento', '<=', $dataFim);
            })
            ->with(['devedor', 'empresa', 'acordos.parcelas'])
            ->orderBy('dataVencimento', 'asc');
        
        $negociadosAtraso = $negociadosAtrasoQuery->paginate(10, ['*'], 'negociados_page');
        
        // Parcelamentos Atrasados
        // Conforme especificação: "Lista parcelas pendentes com vencimento <= hoje"
        // Exclui parcelas que já tiveram ação hoje
        $parcelamentosAtrasados = Parcelamento::where('status', 'PENDENTE')
            ->whereDate('data_vencimento', '<=', today())
            ->whereHas('acordo.titulo', function($q) {
                $q->whereDate('ultima_acao', '<', today())
                  ->orWhereNull('ultima_acao');
            })
            ->with(['acordo.devedor', 'acordo.empresa', 'acordo.titulo'])
            ->orderBy('data_vencimento', 'asc')
            ->limit(10)
            ->get();
        
        // Últimas Movimentações (últimos 10 acordos criados)
        // Conforme especificação: "Últimos 10 acordos criados"
        $ultimasMovimentacoes = Acordo::with(['devedor', 'titulo', 'empresa'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Busca rápida (para o formulário) - precisa ser definida antes de usar no filtro
        $buscaRapida = $request->get('busca_rapida');
        $resultadosBusca = collect();
        if ($buscaRapida) {
            $resultadosBusca = Devedor::where(function($q) use ($buscaRapida) {
                    $q->where('nome', 'like', "%{$buscaRapida}%")
                      ->orWhere('razao_social', 'like', "%{$buscaRapida}%")
                      ->orWhere('cpf', 'like', "%{$buscaRapida}%")
                      ->orWhere('cnpj', 'like', "%{$buscaRapida}%");
                })
                ->limit(5)
                ->get();
        }

        // Últimos Clientes Cadastrados (Devedores)
        $dataCadastroInicio = $request->get('data_cadastro_inicio') ? Carbon::parse($request->get('data_cadastro_inicio')) : null;
        $dataCadastroFim = $request->get('data_cadastro_fim') ? Carbon::parse($request->get('data_cadastro_fim')) : null;
        $empresaFiltro = $request->get('empresa_id');
        
        $ultimosClientesQuery = Devedor::query()
            ->when($empresaFiltro, function($q) use ($empresaFiltro) {
                $q->where('empresa_id', $empresaFiltro);
            })
            ->when($dataCadastroInicio, function($q) use ($dataCadastroInicio) {
                $q->whereDate('created_at', '>=', $dataCadastroInicio);
            })
            ->when($dataCadastroFim, function($q) use ($dataCadastroFim) {
                $q->whereDate('created_at', '<=', $dataCadastroFim);
            })
            ->with(['empresa'])
            ->orderBy('created_at', 'desc');
        
        $ultimosClientes = $ultimosClientesQuery->paginate(10, ['*'], 'clientes_page');

        // Lista de operadores para o filtro (incluindo "Sem Operador")
        $operadores = Titulo::select(DB::raw('COALESCE(operador, "Sem Operador") as operador'))
            ->distinct()
            ->orderBy('operador')
            ->pluck('operador');
        
        return view('dashboard', compact(
            'pendentes',
            'quitados',
            'negociados',
            'totalClientes',
            'negociadosEmAtraso',
            'quitadosHoje',
            'negociadosHoje',
            'quitadosHojeDetalhes',
            'negociadosHojeDetalhes',
            'rankingOperadores',
            'totalRecebidoGeral',
            'operadores',
            'agendaPendentes',
            'agendamentosPendentes',
            'negociadosAtraso',
            'ultimosClientes',
            'resultadosBusca',
            'buscaRapida',
            'parcelamentosAtrasados',
            'ultimasMovimentacoes'
        ));
    }
}
