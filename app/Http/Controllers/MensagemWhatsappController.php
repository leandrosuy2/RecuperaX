<?php

namespace App\Http\Controllers;

use App\Models\MensagemWhatsapp;
use App\Models\Devedor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MensagemWhatsappController extends Controller
{
    public function index(Request $request)
    {
        $query = MensagemWhatsapp::with(['devedor']);

        if ($request->filled('devedor_id')) {
            $query->where('devedor_id', $request->devedor_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('data_envio_inicio')) {
            $query->whereDate('data_envio', '>=', $request->data_envio_inicio);
        }

        if ($request->filled('data_envio_fim')) {
            $query->whereDate('data_envio', '<=', $request->data_envio_fim);
        }

        $mensagens = $query->latest('data_envio')->paginate(20);
        $devedores = Devedor::where('status', true)->get();

        return view('mensagens-whatsapp.index', compact('mensagens', 'devedores'));
    }

    public function create()
    {
        $devedores = Devedor::where('status', true)->get();
        return view('mensagens-whatsapp.create', compact('devedores'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'devedor_id' => 'required|exists:devedores,id',
            'mensagem' => 'required|string|max:1000',
            'tipo' => 'required|in:texto,midia',
        ]);

        MensagemWhatsapp::create([
            'devedor_id' => $request->devedor_id,
            'mensagem' => $request->mensagem,
            'tipo' => $request->tipo,
            'status' => 'pendente',
            'user_id' => Auth::id(),
            'data_envio' => now(),
        ]);

        return redirect()->route('mensagens-whatsapp.index')
                        ->with('success', 'Mensagem WhatsApp criada com sucesso!');
    }

    public function show(MensagemWhatsapp $mensagemWhatsapp)
    {
        return view('mensagens-whatsapp.show', compact('mensagemWhatsapp'));
    }

    public function edit(MensagemWhatsapp $mensagemWhatsapp)
    {
        $devedores = Devedor::where('status', true)->get();
        return view('mensagens-whatsapp.edit', compact('mensagemWhatsapp', 'devedores'));
    }

    public function update(Request $request, MensagemWhatsapp $mensagemWhatsapp)
    {
        $request->validate([
            'devedor_id' => 'required|exists:devedores,id',
            'mensagem' => 'required|string|max:1000',
            'tipo' => 'required|in:texto,midia',
            'status' => 'required|in:pendente,enviada,falhou',
        ]);

        $mensagemWhatsapp->update($request->all());

        return redirect()->route('mensagens-whatsapp.index')
                        ->with('success', 'Mensagem WhatsApp atualizada com sucesso!');
    }

    public function destroy(MensagemWhatsapp $mensagemWhatsapp)
    {
        $mensagemWhatsapp->delete();

        return redirect()->route('mensagens-whatsapp.index')
                        ->with('success', 'Mensagem WhatsApp excluída com sucesso!');
    }
}