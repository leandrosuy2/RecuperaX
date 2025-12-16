<?php

namespace App\Http\Controllers;

use App\Models\Acordo;
use App\Models\Divida;
use App\Models\Pagamento;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PagamentoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $user = auth()->user();
        $query = Pagamento::with(['divida', 'acordo', 'cliente']);

        if ($user->isConsultor()) {
            $query->where('consultor_id', $user->id);
        } elseif ($user->isCredor()) {
            $query->whereHas('divida', function($q) use ($user) {
                $q->where('credor_id', $user->credor_id);
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('numero_transacao', 'like', "%{$search}%")
                  ->orWhereHas('cliente', function($q) use ($search) {
                      $q->where('nome', 'like', "%{$search}%");
                  });
            });
        }

        $pagamentos = $query->latest('data_pagamento')->paginate(20);

        return view('pagamentos.index', compact('pagamentos'));
    }

    public function create(Request $request)
    {
        $divida = null;
        $acordo = null;

        if ($request->filled('divida_id')) {
            $divida = Divida::findOrFail($request->divida_id);
        }

        if ($request->filled('acordo_id')) {
            $acordo = Acordo::findOrFail($request->acordo_id);
            $divida = $acordo->divida;
        }

        return view('pagamentos.create', compact('divida', 'acordo'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'divida_id' => 'nullable|exists:dividas,id',
            'acordo_id' => 'nullable|exists:acordos,id',
            'valor' => 'required|numeric|min:0.01',
            'data_pagamento' => 'required|date',
            'forma_pagamento' => 'required|in:dinheiro,pix,boleto,transferencia,cartao_credito,cartao_debito,cheque',
            'numero_parcela' => 'nullable|integer|min:1',
            'observacoes' => 'nullable|string',
        ]);

        if (!$validated['divida_id'] && !$validated['acordo_id']) {
            return redirect()->back()
                ->with('error', 'É necessário informar uma dívida ou acordo.');
        }

        $divida = null;
        $acordo = null;

        if ($validated['acordo_id']) {
            $acordo = Acordo::findOrFail($validated['acordo_id']);
            $divida = $acordo->divida;
        } else {
            $divida = Divida::findOrFail($validated['divida_id']);
        }

        $user = auth()->user();

        // Verificar permissão
        if ($user->isConsultor() && $divida->consultor_id !== $user->id) {
            abort(403);
        }

        $pagamento = Pagamento::create([
            'divida_id' => $divida->id,
            'acordo_id' => $acordo?->id,
            'cliente_id' => $divida->cliente_id,
            'consultor_id' => $divida->consultor_id ?? $user->id,
            'numero_transacao' => 'PAG-' . strtoupper(Str::random(10)),
            'valor' => $validated['valor'],
            'data_pagamento' => $validated['data_pagamento'],
            'forma_pagamento' => $validated['forma_pagamento'],
            'numero_parcela' => $validated['numero_parcela'],
            'status' => 'pendente',
            'observacoes' => $validated['observacoes'],
        ]);

        // Se for pagamento de acordo, calcular data de vencimento da parcela
        if ($acordo && $validated['numero_parcela']) {
            $mesesAdicionar = $validated['numero_parcela'] - 1;
            $dataVencimento = $acordo->data_primeira_parcela->copy()->addMonths($mesesAdicionar);
            $dataVencimento->day = $acordo->dia_vencimento;
            $pagamento->data_vencimento_parcela = $dataVencimento;
            $pagamento->save();
        }

        return redirect()->route('pagamentos.show', $pagamento)
            ->with('success', 'Pagamento registrado com sucesso!');
    }

    public function show(Pagamento $pagamento)
    {
        $pagamento->load(['divida.credor', 'acordo', 'cliente', 'consultor']);
        
        return view('pagamentos.show', compact('pagamento'));
    }

    public function confirmar(Pagamento $pagamento)
    {
        $user = auth()->user();
        
        if (!$user->canViewAllDividas() && $user->isConsultor() && $pagamento->consultor_id !== $user->id) {
            abort(403);
        }

        $pagamento->confirmar();

        return redirect()->route('pagamentos.show', $pagamento)
            ->with('success', 'Pagamento confirmado com sucesso!');
    }

    public function cancelar(Pagamento $pagamento)
    {
        if ($pagamento->status === 'confirmado') {
            return redirect()->back()
                ->with('error', 'Não é possível cancelar pagamento já confirmado.');
        }

        $pagamento->status = 'cancelado';
        $pagamento->save();

        return redirect()->route('pagamentos.show', $pagamento)
            ->with('success', 'Pagamento cancelado com sucesso!');
    }
}
