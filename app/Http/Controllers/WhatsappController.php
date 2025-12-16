<?php

namespace App\Http\Controllers;

use App\Models\Devedor;
use App\Models\Divida;
use Illuminate\Http\Request;

class WhatsappController extends Controller
{
    public function conectarPendentes()
    {
        // Buscar devedores com dívidas pendentes (não quitadas)
        $devedores = Devedor::where('status', true)
                           ->whereHas('dividas', function($query) {
                               $query->where('status_divida', '!=', 'quitada');
                           })
                           ->with(['dividas' => function($query) {
                               $query->where('status_divida', '!=', 'quitada');
                           }])
                           ->paginate(20);

        return view('whatsapp.conectar-pendentes', compact('devedores'));
    }

    public function conectarNegociados()
    {
        // Buscar devedores com acordos ativos
        $devedores = Devedor::where('status', true)
                           ->whereHas('acordos', function($query) {
                               $query->where('status', 'ativo');
                           })
                           ->with(['acordos' => function($query) {
                               $query->where('status', 'ativo');
                           }])
                           ->paginate(20);

        return view('whatsapp.conectar-negociados', compact('devedores'));
    }

    public function enviarMensagem(Request $request)
    {
        $request->validate([
            'devedor_id' => 'required|exists:devedores,id',
            'mensagem' => 'required|string|max:1000',
        ]);

        $devedor = Devedor::findOrFail($request->devedor_id);

        // Aqui seria integrada a API do WhatsApp para envio
        // Por enquanto, apenas simulamos o envio

        return redirect()->back()
                        ->with('success', 'Mensagem enviada com sucesso para ' . $devedor->nome . '!');
    }

    public function verificarConexao(Request $request)
    {
        // Verificar se o WhatsApp está conectado
        // Esta seria uma integração com a API do WhatsApp

        return response()->json([
            'conectado' => false,
            'mensagem' => 'WhatsApp não conectado. Configure a integração.'
        ]);
    }
}