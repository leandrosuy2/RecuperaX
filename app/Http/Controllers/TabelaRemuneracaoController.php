<?php

namespace App\Http\Controllers;

use App\Models\TabelaRemuneracao;
use App\Models\TabelaRemuneracaoLista;
use Illuminate\Http\Request;

class TabelaRemuneracaoController extends Controller
{
    public function index()
    {
        $tabelas = TabelaRemuneracao::with('itens')->get();
        return view('tabelas.index', compact('tabelas'));
    }

    public function create()
    {
        return view('tabelas.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nome' => 'required|string|max:255',
        ]);

        $tabela = TabelaRemuneracao::create($validated);

        return redirect()->route('tabelas.show', $tabela)
            ->with('success', 'Tabela criada com sucesso!');
    }

    public function show(TabelaRemuneracao $tabela)
    {
        $tabela->load('itens');
        return view('tabelas.show', compact('tabela'));
    }

    public function edit(TabelaRemuneracao $tabela)
    {
        return view('tabelas.edit', compact('tabela'));
    }

    public function update(Request $request, TabelaRemuneracao $tabela)
    {
        $validated = $request->validate([
            'nome' => 'required|string|max:255',
        ]);

        $tabela->update($validated);

        return redirect()->route('tabelas.show', $tabela)
            ->with('success', 'Tabela atualizada com sucesso!');
    }

    public function destroy(TabelaRemuneracao $tabela)
    {
        if ($tabela->empresas()->count() > 0) {
            return redirect()->back()
                ->with('error', 'Não é possível excluir tabela com empresas vinculadas.');
        }

        $tabela->delete();

        return redirect()->route('tabelas.index')
            ->with('success', 'Tabela excluída com sucesso!');
    }

    public function adicionarItem(Request $request, TabelaRemuneracao $tabela)
    {
        $validated = $request->validate([
            'de_dias' => 'required|integer|min:0',
            'ate_dias' => 'required|integer|min:0|gte:de_dias',
            'percentual_remuneracao' => 'required|numeric|min:0|max:100',
        ]);

        TabelaRemuneracaoLista::create([
            'tabela_remuneracao_id' => $tabela->id,
            'de_dias' => $validated['de_dias'],
            'ate_dias' => $validated['ate_dias'],
            'percentual_remuneracao' => $validated['percentual_remuneracao'],
        ]);

        return redirect()->back()
            ->with('success', 'Item adicionado com sucesso!');
    }

    public function editarItem(Request $request, TabelaRemuneracao $tabela, TabelaRemuneracaoLista $item)
    {
        $validated = $request->validate([
            'de_dias' => 'required|integer|min:0',
            'ate_dias' => 'required|integer|min:0|gte:de_dias',
            'percentual_remuneracao' => 'required|numeric|min:0|max:100',
        ]);

        $item->update($validated);

        return redirect()->back()
            ->with('success', 'Item atualizado com sucesso!');
    }

    public function excluirItem(TabelaRemuneracao $tabela, TabelaRemuneracaoLista $item)
    {
        $item->delete();

        return redirect()->back()
            ->with('success', 'Item excluído com sucesso!');
    }
}
