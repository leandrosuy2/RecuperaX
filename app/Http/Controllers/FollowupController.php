<?php

namespace App\Http\Controllers;

use App\Models\FollowUp;
use App\Models\Devedor;
use App\Models\Titulo;
use Illuminate\Http\Request;

class FollowupController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        // Usar select específico para evitar carregar dados desnecessários
        $query = FollowUp::with([
            'devedor:id,nome,razao_social,cpf,cnpj',
            'empresa:id,razao_social,nome_fantasia'
        ]);

        if ($request->filled('devedor_id')) {
            $query->where('devedor_id', $request->devedor_id);
        }

        if ($request->filled('empresa_id')) {
            $query->where('empresa_id', $request->empresa_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('texto', 'like', "%{$search}%")
                  ->orWhereHas('devedor', function($q) use ($search) {
                      $q->where('nome', 'like', "%{$search}%")
                        ->orWhere('razao_social', 'like', "%{$search}%");
                  });
            });
        }

        $followups = $query->latest('created_at')->paginate(20);
        
        // Carregar apenas campos necessários e limitar resultados para filtros
        $devedores = Devedor::select('id', 'nome', 'razao_social', 'cpf', 'cnpj', 'tipo_pessoa')
            ->orderBy('nome')
            ->orderBy('razao_social')
            ->limit(1000)
            ->get();
        
        $empresas = \App\Models\Empresa::where('status_empresa', true)
            ->select('id', 'razao_social', 'nome_fantasia')
            ->orderBy('razao_social')
            ->get();

        return view('followups.index', compact('followups', 'devedores', 'empresas'));
    }

    public function create(Request $request)
    {
        $devedor = null;
        if ($request->filled('devedor_id')) {
            $devedor = Devedor::select('id', 'nome', 'razao_social', 'cpf', 'cnpj', 'tipo_pessoa', 'empresa_id')
                ->findOrFail($request->devedor_id);
        }

        // Carregar apenas campos necessários e limitar resultados
        $devedores = Devedor::select('id', 'nome', 'razao_social', 'cpf', 'cnpj', 'tipo_pessoa')
            ->orderBy('nome')
            ->orderBy('razao_social')
            ->limit(1000)
            ->get();
        
        $empresas = \App\Models\Empresa::where('status_empresa', true)
            ->select('id', 'razao_social', 'nome_fantasia')
            ->orderBy('razao_social')
            ->get();

        return view('followups.create', compact('devedor', 'devedores', 'empresas'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'devedor_id' => 'required|exists:devedores,id',
            'empresa_id' => 'nullable|exists:core_empresa,id',
            'texto' => 'required|string|max:5000',
        ]);

        $followup = FollowUp::create([
            'devedor_id' => $validated['devedor_id'],
            'empresa_id' => $validated['empresa_id'] ?? null,
            'texto' => $validated['texto'],
            'created_at' => now(),
        ]);

        return redirect()->route('followups.show', $followup)
            ->with('success', 'Follow-up criado com sucesso!');
    }

    public function show(FollowUp $followup)
    {
        $followup->load([
            'devedor:id,nome,razao_social,cpf,cnpj,tipo_pessoa',
            'empresa:id,razao_social,nome_fantasia'
        ]);
        return view('followups.show', compact('followup'));
    }

    public function destroy(FollowUp $followup)
    {
        $followup->delete();

        return redirect()->route('followups.index')
            ->with('success', 'Follow-up excluído com sucesso!');
    }

    public function adicionarFollowUp(Request $request, Devedor $devedor)
    {
        $validated = $request->validate([
            'texto' => 'required|string|max:5000',
            'empresa_id' => 'nullable|exists:core_empresa,id',
        ]);

        FollowUp::create([
            'devedor_id' => $devedor->id,
            'empresa_id' => $validated['empresa_id'] ?? $devedor->empresa_id,
            'texto' => $validated['texto'],
            'created_at' => now(),
        ]);

        if ($request->wantsJson() || $request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Follow-up adicionado com sucesso!'
            ]);
        }

        return redirect()->route('devedores.show', $devedor)
            ->with('success', 'Follow-up adicionado com sucesso!');
    }

    public function listarFollowUps(Devedor $devedor)
    {
        $followups = FollowUp::where('devedor_id', $devedor->id)
            ->with(['empresa:id,razao_social,nome_fantasia'])
            ->latest('created_at')
            ->paginate(20);

        return view('followups.listar', compact('devedor', 'followups'));
    }
}
