<?php

namespace App\Http\Controllers;

use App\Models\Carteira;
use App\Models\Credor;
use App\Models\User;
use Illuminate\Http\Request;

class CarteiraController extends Controller
{

    public function index(Request $request)
    {
        $query = Carteira::with(['consultor', 'credor'])->withCount('dividas');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('nome', 'like', "%{$search}%")
                  ->orWhere('descricao', 'like', "%{$search}%");
        }

        if ($request->filled('status')) {
            if ($request->status === 'ativo') {
                $query->where('ativo', true);
            } elseif ($request->status === 'inativo') {
                $query->where('ativo', false);
            }
        }

        $carteiras = $query->latest('created_at')->paginate(20);

        return view('carteiras.index', compact('carteiras'));
    }

    public function create()
    {
        $credores = Credor::where('ativo', true)->get();
        $consultores = User::where('role', 'consultor')->get();

        return view('carteiras.create', compact('credores', 'consultores'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'credor_id' => 'nullable|exists:credores,id',
            'consultor_id' => 'required|exists:users,id',
            'dias_atraso_min' => 'nullable|integer|min:0',
            'dias_atraso_max' => 'nullable|integer|min:0|gte:dias_atraso_min',
            'valor_min' => 'nullable|numeric|min:0',
            'valor_max' => 'nullable|numeric|min:0|gte:valor_min',
            'status_filtro' => 'nullable|in:a_vencer,vencida,em_negociacao',
            'descricao' => 'nullable|string',
        ]);

        $carteira = Carteira::create($validated);

        // Sincronizar dívidas baseado nos filtros
        $carteira->sincronizarDividas();

        return redirect()->route('carteiras.show', $carteira)
            ->with('success', 'Carteira criada com sucesso!');
    }

    public function show(Carteira $carteira)
    {
        $carteira->load(['consultor', 'credor', 'dividas.credor', 'dividas.cliente']);
        
        return view('carteiras.show', compact('carteira'));
    }

    public function edit(Carteira $carteira)
    {
        $credores = Credor::where('ativo', true)->get();
        $consultores = User::where('role', 'consultor')->get();

        return view('carteiras.edit', compact('carteira', 'credores', 'consultores'));
    }

    public function update(Request $request, Carteira $carteira)
    {
        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'credor_id' => 'nullable|exists:credores,id',
            'consultor_id' => 'required|exists:users,id',
            'dias_atraso_min' => 'nullable|integer|min:0',
            'dias_atraso_max' => 'nullable|integer|min:0|gte:dias_atraso_min',
            'valor_min' => 'nullable|numeric|min:0',
            'valor_max' => 'nullable|numeric|min:0|gte:valor_min',
            'status_filtro' => 'nullable|in:a_vencer,vencida,em_negociacao',
            'descricao' => 'nullable|string',
            'ativo' => 'boolean',
        ]);

        $carteira->update($validated);

        // Re-sincronizar dívidas se filtros mudaram
        if ($request->has(['dias_atraso_min', 'dias_atraso_max', 'valor_min', 'valor_max', 'status_filtro', 'credor_id'])) {
            $carteira->sincronizarDividas();
        }

        return redirect()->route('carteiras.show', $carteira)
            ->with('success', 'Carteira atualizada com sucesso!');
    }

    public function sincronizar(Carteira $carteira)
    {
        $carteira->sincronizarDividas();

        return redirect()->back()
            ->with('success', 'Carteira sincronizada com sucesso!');
    }

    public function destroy(Carteira $carteira)
    {
        $carteira->delete();

        return redirect()->route('carteiras.index')
            ->with('success', 'Carteira excluída com sucesso!');
    }
}
