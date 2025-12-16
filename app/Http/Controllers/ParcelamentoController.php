<?php

namespace App\Http\Controllers;

use App\Models\Parcelamento;
use App\Models\Acordo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ParcelamentoController extends Controller
{
    public function index(Request $request)
    {
        $query = Parcelamento::with(['acordo.devedor', 'acordo.empresa', 'acordo']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('forma_pagamento')) {
            $query->where('forma_pagamento', $request->forma_pagamento);
        }

        if ($request->filled('data_vencimento_inicio')) {
            $query->whereDate('data_vencimento', '>=', $request->data_vencimento_inicio);
        }

        if ($request->filled('data_vencimento_fim')) {
            $query->whereDate('data_vencimento', '<=', $request->data_vencimento_fim);
        }

        if ($request->filled('devedor')) {
            $query->whereHas('acordo', function($q) use ($request) {
                $q->whereHas('devedor', function($q2) use ($request) {
                    $q2->where('nome', 'like', "%{$request->devedor}%")
                       ->orWhere('razao_social', 'like', "%{$request->devedor}%");
                });
            });
        }

        $parcelamentos = $query->orderBy('data_vencimento', 'asc')->paginate(20);

        return view('parcelamentos.index', compact('parcelamentos'));
    }

    public function show(Parcelamento $parcelamento)
    {
        $parcelamento->load(['acordo.devedor', 'acordo.empresa', 'acordo.titulo']);
        return view('parcelamentos.show', compact('parcelamento'));
    }

    public function pagar(Request $request, Parcelamento $parcelamento)
    {
        if ($parcelamento->status === 'PAGO') {
            return redirect()->back()->with('error', 'Esta parcela já foi paga.');
        }

        $validated = $request->validate([
            'valor' => 'required|numeric|min:0',
            'data_pagamento' => 'required|date',
            'forma_pagamento' => 'required|in:PIX,BOLETO,DINHEIRO,CARTAO_CREDITO,CARTAO_DEBITO,CHEQUE,PAGAMENTO_LOJA',
            'comprovante' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        $parcelamento->status = 'PAGO';
        $parcelamento->data_baixa = $validated['data_pagamento'];
        $parcelamento->valor = $validated['valor'];
        $parcelamento->forma_pagamento = $validated['forma_pagamento'];

        if ($request->hasFile('comprovante')) {
            $parcelamento->comprovante = $request->file('comprovante')->store('comprovantes', 'public');
        }

        $parcelamento->save();

        // Atualizar título relacionado se necessário
        if ($parcelamento->acordo && $parcelamento->acordo->titulo) {
            $titulo = $parcelamento->acordo->titulo;
            $titulo->valorRecebido = ($titulo->valorRecebido ?? 0) + $validated['valor'];
            
            // Verificar se todas as parcelas foram pagas
            $todasPagas = $parcelamento->acordo->parcelas()->where('status', 'PAGO')->count() === 
                         $parcelamento->acordo->parcelas()->count();
            
            if ($todasPagas) {
                $titulo->statusBaixa = 2; // Quitado
                $titulo->data_baixa = now();
            }
            
            $titulo->save();
        }

        // TODO: Enviar email de quitação de parcela

        return redirect()->route('parcelamentos.show', $parcelamento)
            ->with('success', 'Parcela quitada com sucesso!');
    }

    public function anexarComprovante(Request $request, Parcelamento $parcelamento)
    {
        $validated = $request->validate([
            'comprovante' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        if ($parcelamento->comprovante) {
            Storage::disk('public')->delete($parcelamento->comprovante);
        }

        $parcelamento->comprovante = $request->file('comprovante')->store('comprovantes', 'public');
        $parcelamento->save();

        return redirect()->back()
            ->with('success', 'Comprovante anexado com sucesso!');
    }

    public function baixarComprovante(Parcelamento $parcelamento)
    {
        if (!$parcelamento->comprovante) {
            abort(404, 'Comprovante não encontrado');
        }

        return Storage::disk('public')->download($parcelamento->comprovante);
    }
}
