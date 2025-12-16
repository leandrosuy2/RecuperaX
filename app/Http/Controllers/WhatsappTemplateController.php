<?php

namespace App\Http\Controllers;

use App\Models\TemplateMensagemWhatsapp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WhatsappTemplateController extends Controller
{
    public function index()
    {
        $templates = TemplateMensagemWhatsapp::latest()->paginate(20);
        return view('whatsapp-templates.index', compact('templates'));
    }

    public function create()
    {
        return view('whatsapp-templates.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'mensagem' => 'required|string|max:1000',
            'tipo' => 'required|in:texto,midia',
            'categoria' => 'required|in:cobranca,negociacao,informacao,outros',
        ]);

        TemplateMensagemWhatsapp::create([
            'nome' => $request->nome,
            'mensagem' => $request->mensagem,
            'tipo' => $request->tipo,
            'categoria' => $request->categoria,
            'user_id' => Auth::id(),
        ]);

        return redirect()->route('whatsapp-templates.index')
                        ->with('success', 'Modelo de mensagem criado com sucesso!');
    }

    public function show(TemplateMensagemWhatsapp $template)
    {
        return view('whatsapp-templates.show', compact('template'));
    }

    public function edit(TemplateMensagemWhatsapp $template)
    {
        return view('whatsapp-templates.edit', compact('template'));
    }

    public function update(Request $request, TemplateMensagemWhatsapp $template)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'mensagem' => 'required|string|max:1000',
            'tipo' => 'required|in:texto,midia',
            'categoria' => 'required|in:cobranca,negociacao,informacao,outros',
        ]);

        $template->update($request->all());

        return redirect()->route('whatsapp-templates.index')
                        ->with('success', 'Modelo de mensagem atualizado com sucesso!');
    }

    public function destroy(TemplateMensagemWhatsapp $template)
    {
        $template->delete();

        return redirect()->route('whatsapp-templates.index')
                        ->with('success', 'Modelo de mensagem excluído com sucesso!');
    }
}