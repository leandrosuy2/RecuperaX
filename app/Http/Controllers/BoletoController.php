<?php

namespace App\Http\Controllers;

use App\Models\Boleto;
use App\Models\Empresa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BoletoController extends Controller
{
    public function index(Request $request)
    {
        $query = Boleto::with(['empresa']);

        if ($request->filled('empresa_id')) {
            $query->where('empresa_id', $request->empresa_id);
        }

        if ($request->filled('situacao')) {
            $query->where('situacao', $request->situacao);
        }

        if ($request->filled('data_emissao_inicio')) {
            $query->whereDate('data_emissao', '>=', $request->data_emissao_inicio);
        }

        if ($request->filled('data_emissao_fim')) {
            $query->whereDate('data_emissao', '<=', $request->data_emissao_fim);
        }

        $boletos = $query->latest('data_emissao')->paginate(20);
        $empresas = Empresa::where('status_empresa', true)->get();

        return view('boletos.index', compact('boletos', 'empresas'));
    }

    public function emitir(Request $request)
    {
        // TODO: Implementar integração com Banco Inter ou CORA
        // Por enquanto retorna view de formulário
        $empresas = Empresa::where('status_empresa', true)->get();
        return view('boletos.emitir', compact('empresas'));
    }

    public function processarEmissao(Request $request)
    {
        // TODO: Implementar lógica de emissão de boleto
        return redirect()->route('boletos.index')
            ->with('success', 'Boleto emitido com sucesso!');
    }

    public function show(Boleto $boleto)
    {
        return view('boletos.show', compact('boleto'));
    }

    public function baixarPdf(Boleto $boleto)
    {
        // TODO: Implementar download do PDF do boleto
        // Por enquanto retorna erro
        abort(404, 'PDF do boleto não disponível');
    }

    public function qrCodePix(Boleto $boleto)
    {
        if (!$boleto->pix_copia_e_cola) {
            abort(404, 'QR Code PIX não disponível');
        }

        return view('boletos.qr-code', compact('boleto'));
    }
}
