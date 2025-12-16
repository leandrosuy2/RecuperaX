<?php

namespace App\Http\Controllers;

use App\Models\Titulo;
use App\Models\Devedor;
use App\Models\Empresa;
use App\Models\Acordo;
use App\Models\TabelaRemuneracao;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RelatorioController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        return view('relatorios.index');
    }

    public function rankingOperadores(Request $request)
    {
        $dataInicio = $request->get('data_inicio') ? Carbon::parse($request->get('data_inicio')) : now()->startOfMonth();
        $dataFim = $request->get('data_fim') ? Carbon::parse($request->get('data_fim')) : now()->endOfMonth();

        $query = Titulo::select('operador', DB::raw('count(*) as total_negociados'), DB::raw('sum(valor) as valor_total_negociado'))
            ->where('statusBaixa', 3) // Negociado
            ->whereBetween('created_at', [$dataInicio, $dataFim])
            ->whereNotNull('operador')
            ->groupBy('operador');

        if ($request->filled('empresa_id')) {
            $query->where('empresa_id', $request->empresa_id);
        }

        $ranking = $query->get()->map(function($item) use ($dataInicio, $dataFim) {
            $quitados = Titulo::where('operador', $item->operador)
                ->where('statusBaixa', 2)
                ->whereBetween('data_baixa', [$dataInicio, $dataFim])
                ->get();

            $item->total_quitados = $quitados->count();
            $item->valor_total_quitado = $quitados->sum('valorRecebido');
            $item->taxa_conversao = $item->total_negociados > 0 
                ? round(($item->total_quitados / $item->total_negociados) * 100, 2)
                : 0;
            $item->media_valor = $item->total_negociados > 0 
                ? round($item->valor_total_negociado / $item->total_negociados, 2)
                : 0;

            return $item;
        })->sortByDesc('total_negociados')->values();

        $empresas = Empresa::where('status_empresa', true)->get();

        return view('relatorios.ranking-operadores', compact('ranking', 'empresas', 'dataInicio', 'dataFim'));
    }

    public function honorarios(Request $request)
    {
        $dataInicio = $request->get('data_inicio') ? Carbon::parse($request->get('data_inicio')) : now()->startOfMonth();
        $dataFim = $request->get('data_fim') ? Carbon::parse($request->get('data_fim')) : now()->endOfMonth();

        $query = Titulo::with(['empresa', 'devedor'])
            ->whereIn('statusBaixa', [2, 3]) // Quitado ou Negociado
            ->whereBetween('data_baixa', [$dataInicio, $dataFim]);

        if ($request->filled('empresa_id')) {
            $query->where('empresa_id', $request->empresa_id);
        }

        if ($request->filled('operador')) {
            $query->where('operador', $request->operador);
        }

        $titulos = $query->get()->map(function($titulo) {
            $diasAtraso = Carbon::parse($titulo->dataVencimento)->diffInDays(now());
            $percentual = $this->calcularPercentualRemuneracao($titulo->empresa, $diasAtraso);
            $valorBase = $titulo->valorRecebido ?? $titulo->valor;
            $honorario = ($valorBase * $percentual) / 100;

            $titulo->dias_atraso = $diasAtraso;
            $titulo->percentual_aplicado = $percentual;
            $titulo->valor_base = $valorBase;
            $titulo->honorario = $honorario;

            return $titulo;
        });

        // Agrupamento
        $porEmpresa = $titulos->groupBy('empresa_id')->map(function($grupo) {
            return [
                'empresa' => $grupo->first()->empresa,
                'total_honorarios' => $grupo->sum('honorario'),
                'quantidade' => $grupo->count(),
            ];
        });

        $porOperador = $titulos->groupBy('operador')->map(function($grupo) {
            return [
                'operador' => $grupo->first()->operador,
                'total_honorarios' => $grupo->sum('honorario'),
                'quantidade' => $grupo->count(),
            ];
        });

        $empresas = Empresa::where('status_empresa', true)->get();

        return view('relatorios.honorarios', compact('titulos', 'porEmpresa', 'porOperador', 'empresas', 'dataInicio', 'dataFim'));
    }

    private function calcularPercentualRemuneracao($empresa, $diasAtraso)
    {
        // Se empresa tem tabela de remuneração configurada
        if ($empresa && $empresa->plano) {
            $faixa = $empresa->plano->itens()
                ->where('de_dias', '<=', $diasAtraso)
                ->where('ate_dias', '>=', $diasAtraso)
                ->first();

            if ($faixa) {
                return $faixa->percentual_remuneracao;
            }
        }

        // Tabela padrão
        if ($diasAtraso >= 30 && $diasAtraso <= 90) return 9;
        if ($diasAtraso >= 91 && $diasAtraso <= 180) return 15;
        if ($diasAtraso >= 181 && $diasAtraso <= 720) return 21;
        if ($diasAtraso >= 721 && $diasAtraso <= 1825) return 30;
        if ($diasAtraso >= 1826) return 40;

        return 0;
    }
}
