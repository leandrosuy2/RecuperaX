<?php

namespace App\Http\Controllers;

use App\Models\Credor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class CredorController extends Controller
{

    public function index(Request $request)
    {
        $user = auth()->user();
        $query = Credor::withCount('dividas');
        
        if ($user->isCredor()) {
            $query->where('id', $user->credor_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('razao_social', 'like', "%{$search}%")
                  ->orWhere('nome_fantasia', 'like', "%{$search}%")
                  ->orWhere('cnpj', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            if ($request->status === 'ativo') {
                $query->where('ativo', true);
            } elseif ($request->status === 'inativo') {
                $query->where('ativo', false);
            }
        }

        $credores = $query->latest('created_at')->paginate(20);

        return view('credores.index', compact('credores'));
    }

    public function create()
    {
        return view('credores.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'razao_social' => 'required|string|max:255',
            'nome_fantasia' => 'nullable|string|max:255',
            'cnpj' => 'required|string|unique:credores,cnpj',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'nome_contato' => 'nullable|string|max:255',
            'cpf_contato' => 'nullable|string|max:14',
            'email' => 'nullable|email|max:255',
            'email_financeiro' => 'nullable|email|max:255',
            'telefone' => 'nullable|string|max:20',
            'celular' => 'nullable|string|max:20',
            'whatsapp_financeiro' => 'nullable|string|max:20',
            'endereco' => 'nullable|string|max:255',
            'numero' => 'nullable|string|max:20',
            'bairro' => 'nullable|string|max:100',
            'cidade' => 'nullable|string|max:100',
            'estado' => 'nullable|string|size:2',
            'cep' => 'nullable|string|max:10',
            'banco' => 'nullable|string|max:100',
            'agencia' => 'nullable|string|max:20',
            'conta' => 'nullable|string|max:30',
            'chave_pix' => 'nullable|string|max:255',
            'nome_favorecido_pix' => 'nullable|string|max:255',
            'tipo_chave_pix' => 'nullable|in:cpf,cnpj,email,telefone,aleatoria',
            'inscricao_estadual' => 'nullable|string|max:50',
            'operador' => 'nullable|string|max:255',
            'supervisor' => 'nullable|string|max:255',
            'gerente' => 'nullable|string|max:255',
            'juros_padrao' => 'required|numeric|min:0|max:100',
            'multa_padrao' => 'required|numeric|min:0|max:100',
            'desconto_maximo' => 'required|numeric|min:0|max:100',
            'implantacao' => 'nullable|numeric|min:0',
            'quantidade_parcelas' => 'nullable|integer|min:0',
            'desconto_a_vista' => 'nullable|numeric|min:0|max:100',
            'desconto_a_prazo' => 'nullable|numeric|min:0|max:100',
            'plano' => 'nullable|string|max:100',
        ]);

        // Upload do logo
        if ($request->hasFile('logo')) {
            $validated['logo'] = $request->file('logo')->store('logos', 'public');
        }

        Credor::create($validated);

        return redirect()->route('credores.index')
            ->with('success', 'Credor cadastrado com sucesso!');
    }

    public function show(Credor $credor)
    {
        $user = auth()->user();
        
        // Credor só vê seus próprios dados
        if ($user->isCredor() && $user->credor_id !== $credor->id) {
            abort(403);
        }

        $credor->load(['dividas' => function($query) {
            $query->latest('created_at')->limit(10);
        }]);

        $stats = [
            'total_dividas' => $credor->dividas()->count(),
            'dividas_ativas' => $credor->dividas()->whereIn('status', ['a_vencer', 'vencida', 'em_negociacao'])->count(),
            'valor_em_atraso' => $credor->total_em_atraso,
            'valor_recuperado' => $credor->total_recuperado,
        ];

        return view('credores.show', compact('credor', 'stats'));
    }

    public function edit(Credor $credor)
    {
        return view('credores.edit', compact('credor'));
    }

    public function update(Request $request, Credor $credor)
    {
        $validated = $request->validate([
            'razao_social' => 'required|string|max:255',
            'nome_fantasia' => 'nullable|string|max:255',
            'cnpj' => 'required|string|unique:credores,cnpj,' . $credor->id,
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'nome_contato' => 'nullable|string|max:255',
            'cpf_contato' => 'nullable|string|max:14',
            'email' => 'nullable|email|max:255',
            'email_financeiro' => 'nullable|email|max:255',
            'telefone' => 'nullable|string|max:20',
            'celular' => 'nullable|string|max:20',
            'whatsapp_financeiro' => 'nullable|string|max:20',
            'endereco' => 'nullable|string|max:255',
            'numero' => 'nullable|string|max:20',
            'bairro' => 'nullable|string|max:100',
            'cidade' => 'nullable|string|max:100',
            'estado' => 'nullable|string|size:2',
            'cep' => 'nullable|string|max:10',
            'banco' => 'nullable|string|max:100',
            'agencia' => 'nullable|string|max:20',
            'conta' => 'nullable|string|max:30',
            'chave_pix' => 'nullable|string|max:255',
            'nome_favorecido_pix' => 'nullable|string|max:255',
            'tipo_chave_pix' => 'nullable|in:cpf,cnpj,email,telefone,aleatoria',
            'inscricao_estadual' => 'nullable|string|max:50',
            'operador' => 'nullable|string|max:255',
            'supervisor' => 'nullable|string|max:255',
            'gerente' => 'nullable|string|max:255',
            'juros_padrao' => 'required|numeric|min:0|max:100',
            'multa_padrao' => 'required|numeric|min:0|max:100',
            'desconto_maximo' => 'required|numeric|min:0|max:100',
            'implantacao' => 'nullable|numeric|min:0',
            'quantidade_parcelas' => 'nullable|integer|min:0',
            'desconto_a_vista' => 'nullable|numeric|min:0|max:100',
            'desconto_a_prazo' => 'nullable|numeric|min:0|max:100',
            'plano' => 'nullable|string|max:100',
            'ativo' => 'boolean',
        ]);

        // Upload do logo
        if ($request->hasFile('logo')) {
            // Remove logo antigo se existir
            if ($credor->logo) {
                Storage::disk('public')->delete($credor->logo);
            }
            $validated['logo'] = $request->file('logo')->store('logos', 'public');
        }

        $credor->update($validated);

        return redirect()->route('credores.show', $credor)
            ->with('success', 'Credor atualizado com sucesso!');
    }

    public function destroy(Credor $credor)
    {
        if ($credor->dividas()->count() > 0) {
            return redirect()->route('credores.index')
                ->with('error', 'Não é possível excluir credor com dívidas cadastradas.');
        }

        $credor->delete();

        return redirect()->route('credores.index')
            ->with('success', 'Credor excluído com sucesso!');
    }
}
