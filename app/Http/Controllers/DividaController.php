<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Credor;
use App\Models\Divida;
use App\Models\User;
use Illuminate\Http\Request;

class DividaController extends Controller
{

    public function index(Request $request)
    {
        $user = auth()->user();
        $query = Divida::with([
            'credor:id,razao_social,nome_fantasia',
            'cliente:id,nome,cpf,cnpj',
            'consultor:id,name'
        ]);

        // Filtros por perfil (só aplicar se o usuário realmente tiver essas propriedades)
        if ($user->isCredor() && $user->credor_id) {
            $query->where('credor_id', $user->credor_id);
        } elseif ($user->isConsultor()) {
            $query->where('consultor_id', $user->id);
        }

        // Filtros da busca
        if ($request->filled('credor_id')) {
            $query->where('credor_id', $request->credor_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('consultor_id')) {
            $query->where('consultor_id', $request->consultor_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('numero_documento', 'like', "%{$search}%")
                  ->orWhereHas('cliente', function($q) use ($search) {
                      $q->where('nome', 'like', "%{$search}%")
                        ->orWhere('cpf', 'like', "%{$search}%")
                        ->orWhere('cnpj', 'like', "%{$search}%");
                  });
            });
        }

        $dividas = $query->latest('created_at')->paginate(20);
        
        // Carregar apenas campos necessários
        $credores = Credor::where('ativo', true)
            ->select('id', 'razao_social', 'nome_fantasia')
            ->orderBy('razao_social')
            ->get();
        
        $consultores = User::where('role', 'consultor')
            ->select('id', 'name')
            ->orderBy('name')
            ->get();

        return view('dividas.index', compact('dividas', 'credores', 'consultores'));
    }

    public function create()
    {
        $user = auth()->user();
        
        if ($user->isCredor()) {
            $credores = Credor::where('id', $user->credor_id)->get();
        } else {
            $credores = Credor::where('ativo', true)->get();
        }
        
        $clientes = Cliente::where('ativo', true)->get();
        $consultores = User::where('role', 'consultor')->get();

        return view('dividas.create', compact('credores', 'clientes', 'consultores'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'credor_id' => 'required|exists:credores,id',
            'cliente_id' => 'required|exists:clientes,id',
            'consultor_id' => 'nullable|exists:users,id',
            'numero_documento' => 'required|string|unique:dividas,numero_documento',
            'descricao' => 'nullable|string',
            'valor_original' => 'required|numeric|min:0',
            'data_vencimento' => 'required|date',
            'data_emissao' => 'required|date',
            'juros_mensal' => 'nullable|numeric|min:0|max:100',
            'multa' => 'nullable|numeric|min:0|max:100',
            'observacoes' => 'nullable|string',
        ]);

        $credor = Credor::find($validated['credor_id']);
        
        $divida = Divida::create([
            ...$validated,
            'valor_atual' => $validated['valor_original'],
            'status' => $validated['data_vencimento'] > now() ? 'a_vencer' : 'vencida',
            'juros_mensal' => $validated['juros_mensal'] ?? $credor->juros_padrao,
            'multa' => $validated['multa'] ?? $credor->multa_padrao,
        ]);

        // Atualizar valor se já estiver vencida
        if ($divida->status === 'vencida') {
            $divida->valor_atual = $divida->calcularValorAtualizado();
            $divida->save();
        }

        // Registrar histórico
        $divida->registrarHistorico('status_alterado', 'Dívida cadastrada no sistema');

        return redirect()->route('dividas.show', $divida)
            ->with('success', 'Dívida cadastrada com sucesso!');
    }

    public function show(Divida $divida)
    {
        $user = auth()->user();
        
        // Verificar permissões
        if ($user->isCredor() && $divida->credor_id !== $user->credor_id) {
            abort(403);
        }
        
        if ($user->isConsultor() && $divida->consultor_id !== $user->id) {
            abort(403);
        }

        $divida->load(['credor', 'cliente', 'consultor', 'followups', 'acordos', 'pagamentos', 'historicoCobranca']);

        return view('dividas.show', compact('divida'));
    }

    public function edit(Divida $divida)
    {
        $credores = Credor::where('ativo', true)->get();
        $clientes = Cliente::where('ativo', true)->get();
        $consultores = User::where('role', 'consultor')->get();

        return view('dividas.edit', compact('divida', 'credores', 'clientes', 'consultores'));
    }

    public function update(Request $request, Divida $divida)
    {
        $validated = $request->validate([
            'credor_id' => 'required|exists:credores,id',
            'cliente_id' => 'required|exists:clientes,id',
            'consultor_id' => 'nullable|exists:users,id',
            'numero_documento' => 'required|string|unique:dividas,numero_documento,' . $divida->id,
            'descricao' => 'nullable|string',
            'valor_original' => 'required|numeric|min:0',
            'valor_atual' => 'required|numeric|min:0',
            'data_vencimento' => 'required|date',
            'data_emissao' => 'required|date',
            'status' => 'required|in:a_vencer,vencida,em_negociacao,quitada,cancelada',
            'juros_mensal' => 'nullable|numeric|min:0|max:100',
            'multa' => 'nullable|numeric|min:0|max:100',
            'observacoes' => 'nullable|string',
        ]);

        $dadosAnteriores = $divida->toArray();
        $divida->update($validated);
        $dadosNovos = $divida->toArray();

        // Registrar histórico se status mudou
        if ($dadosAnteriores['status'] !== $validated['status']) {
            $divida->registrarHistorico(
                'status_alterado',
                "Status alterado de {$dadosAnteriores['status']} para {$validated['status']}",
                $dadosAnteriores,
                $dadosNovos
            );
        }

        return redirect()->route('dividas.show', $divida)
            ->with('success', 'Dívida atualizada com sucesso!');
    }

    public function destroy(Divida $divida)
    {
        if ($divida->acordos()->where('status', 'ativo')->exists()) {
            return redirect()->route('dividas.index')
                ->with('error', 'Não é possível excluir dívida com acordo ativo.');
        }

        $divida->delete();

        return redirect()->route('dividas.index')
            ->with('success', 'Dívida excluída com sucesso!');
    }

    public function atualizarValor(Divida $divida)
    {
        $divida->valor_atual = $divida->calcularValorAtualizado();
        $divida->atualizarStatus();
        $divida->save();

        return redirect()->back()
            ->with('success', 'Valor atualizado com sucesso!');
    }
}
