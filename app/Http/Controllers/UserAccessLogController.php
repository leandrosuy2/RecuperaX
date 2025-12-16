<?php

namespace App\Http\Controllers;

use App\Models\UserAccessLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserAccessLogController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        // TODO: Adicionar middleware de permissão administrativa
    }

    public function index(Request $request)
    {
        $query = UserAccessLog::with(['user']);

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('ip_address')) {
            $query->where('ip_address', 'like', "%{$request->ip_address}%");
        }

        if ($request->filled('data_inicio')) {
            $query->whereDate('timestamp', '>=', $request->data_inicio);
        }

        if ($request->filled('data_fim')) {
            $query->whereDate('timestamp', '<=', $request->data_fim);
        }

        if ($request->filled('path')) {
            $query->where('path', 'like', "%{$request->path}%");
        }

        if ($request->filled('method')) {
            $query->where('method', $request->method);
        }

        $logs = $query->latest('timestamp')->paginate(50);
        $users = User::all();

        // Estatísticas
        $totalAcessos = UserAccessLog::count();
        $acessosHoje = UserAccessLog::whereDate('timestamp', today())->count();
        $acessosPorUsuario = UserAccessLog::select('user_id', DB::raw('count(*) as total'))
            ->groupBy('user_id')
            ->orderBy('total', 'desc')
            ->limit(10)
            ->get();

        return view('logs.index', compact('logs', 'users', 'totalAcessos', 'acessosHoje', 'acessosPorUsuario'));
    }

    public function exportarCsv(Request $request)
    {
        // TODO: Implementar exportação CSV
        return redirect()->back()->with('error', 'Exportação CSV será implementada em breve');
    }

    public function exportarExcel(Request $request)
    {
        // TODO: Implementar exportação Excel
        return redirect()->back()->with('error', 'Exportação Excel será implementada em breve');
    }
}
