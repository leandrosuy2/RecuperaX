<?php

namespace App\Http\Controllers;

use App\Models\Cobranca;
use App\Models\Empresa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CobrancaController extends Controller
{
    public function index(Request $request)
    {
        $query = Cobranca::with(['empresa']);

        if ($request->filled('empresa_id')) {
            $query->where('empresa_id', $request->empresa_id);
        }

        if ($request->filled('pago')) {
            $query->where('pago', $request->pago === '1');
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('id', $search)
                  ->orWhereHas('empresa', function($q) use ($search) {
                      $q->where('razao_social', 'like', "%{$search}%")
                        ->orWhere('cnpj', 'like', "%{$search}%");
                  });
            });
        }

        $cobrancas = $query->latest('data_cobranca')->paginate(20);
        $empresas = Empresa::where('status_empresa', true)->get();

        return view('cobrancas.index', compact('cobrancas', 'empresas'));
    }

    public function create()
    {
        $empresas = Empresa::where('status_empresa', true)->get();
        return view('cobrancas.create', compact('empresas'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'empresa_id' => 'required|exists:core_empresa,id',
            'data_cobranca' => 'required|date',
            'valor_comissao' => 'required|numeric|min:0',
            'tipo_anexo' => 'required|in:documento,link',
            'documento' => 'required_if:tipo_anexo,documento|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'link' => 'required_if:tipo_anexo,link|url|max:500',
        ]);

        if ($request->hasFile('documento')) {
            $validated['documento'] = $request->file('documento')->store('cobrancas', 'public');
        }

        $cobranca = Cobranca::create($validated);

        return redirect()->route('cobrancas.show', $cobranca)
            ->with('success', 'Cobrança criada com sucesso!');
    }

    public function show(Cobranca $cobranca)
    {
        $cobranca->load(['empresa']);
        return view('cobrancas.show', compact('cobranca'));
    }

    public function atualizarPago(Cobranca $cobranca)
    {
        $cobranca->pago = !$cobranca->pago;
        $cobranca->save();

        return redirect()->back()
            ->with('success', 'Status de pagamento atualizado com sucesso!');
    }

    public function baixarDocumento(Cobranca $cobranca)
    {
        if (!$cobranca->documento) {
            abort(404, 'Documento não encontrado');
        }

        return Storage::disk('public')->download($cobranca->documento);
    }
}
