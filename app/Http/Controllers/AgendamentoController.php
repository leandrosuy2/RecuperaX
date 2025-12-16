<?php

namespace App\Http\Controllers;

use App\Models\Agendamento;
use App\Models\Devedor;
use App\Models\Empresa;
use Illuminate\Http\Request;

class AgendamentoController extends Controller
{
    public function index(Request $request)
    {
        // Usar select específico para evitar carregar dados desnecessários
        $query = Agendamento::with([
            'devedor:id,nome,razao_social,cpf,cnpj',
            'empresa:id,razao_social,nome_fantasia'
        ]);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('data_retorno')) {
            $query->whereDate('data_retorno', $request->data_retorno);
        }

        if ($request->filled('devedor_id')) {
            $query->where('devedor_id', $request->devedor_id);
        }

        $agendamentos = $query->orderBy('data_retorno', 'asc')->paginate(20);
        
        // Carregar apenas campos necessários e limitar resultados
        $devedores = Devedor::select('id', 'nome', 'razao_social', 'cpf', 'cnpj', 'tipo_pessoa')
            ->orderBy('nome')
            ->orderBy('razao_social')
            ->limit(1000)
            ->get();
        
        $empresas = Empresa::where('status_empresa', true)
            ->select('id', 'razao_social', 'nome_fantasia')
            ->orderBy('razao_social')
            ->get();

        return view('agendamentos.index', compact('agendamentos', 'devedores', 'empresas'));
    }

    public function create(Request $request)
    {
        $devedor = null;
        if ($request->filled('devedor_id')) {
            $devedor = Devedor::select('id', 'nome', 'razao_social', 'cpf', 'cnpj', 'tipo_pessoa', 'empresa_id')
                ->findOrFail($request->devedor_id);
        }

        // Carregar apenas campos necessários e limitar resultados
        $devedores = Devedor::select('id', 'nome', 'razao_social', 'cpf', 'cnpj', 'tipo_pessoa')
            ->orderBy('nome')
            ->orderBy('razao_social')
            ->limit(1000)
            ->get();
        
        $empresas = Empresa::where('status_empresa', true)
            ->select('id', 'razao_social', 'nome_fantasia')
            ->orderBy('razao_social')
            ->get();

        return view('agendamentos.create', compact('devedor', 'devedores', 'empresas'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'devedor_id' => 'required|exists:devedores,id',
            'empresa_id' => 'required|exists:core_empresa,id',
            'data_retorno' => 'required|date',
            'assunto' => 'required|string|max:1000',
            'operador' => 'nullable|string|max:255',
            'telefone' => 'nullable|string|max:20',
            'acordo_id' => 'nullable|integer',
        ]);

        $validated['data_abertura'] = now();
        $validated['status'] = 'Pendente';

        $agendamento = Agendamento::create($validated);

        return redirect()->route('agendamentos.show', $agendamento)
            ->with('success', 'Agendamento criado com sucesso!');
    }

    public function show(Agendamento $agendamento)
    {
        $agendamento->load([
            'devedor:id,nome,razao_social,cpf,cnpj,tipo_pessoa',
            'empresa:id,razao_social,nome_fantasia'
        ]);
        return view('agendamentos.show', compact('agendamento'));
    }

    public function edit(Agendamento $agendamento)
    {
        // Carregar apenas campos necessários e limitar resultados
        $devedores = Devedor::select('id', 'nome', 'razao_social', 'cpf', 'cnpj', 'tipo_pessoa')
            ->orderBy('nome')
            ->orderBy('razao_social')
            ->limit(1000)
            ->get();
        
        $empresas = Empresa::where('status_empresa', true)
            ->select('id', 'razao_social', 'nome_fantasia')
            ->orderBy('razao_social')
            ->get();
        
        return view('agendamentos.edit', compact('agendamento', 'devedores', 'empresas'));
    }

    public function update(Request $request, Agendamento $agendamento)
    {
        $validated = $request->validate([
            'devedor_id' => 'required|exists:devedores,id',
            'empresa_id' => 'required|exists:core_empresa,id',
            'data_retorno' => 'required|date',
            'assunto' => 'required|string|max:1000',
            'operador' => 'nullable|string|max:255',
            'telefone' => 'nullable|string|max:20',
            'acordo_id' => 'nullable|integer',
        ]);

        $agendamento->update($validated);

        return redirect()->route('agendamentos.show', $agendamento)
            ->with('success', 'Agendamento atualizado com sucesso!');
    }

    public function destroy(Agendamento $agendamento)
    {
        $agendamento->delete();

        return redirect()->route('agendamentos.index')
            ->with('success', 'Agendamento excluído com sucesso!');
    }

    public function finalizar(Agendamento $agendamento)
    {
        $agendamento->status = 'Finalizado';
        $agendamento->save();

        return redirect()->back()
            ->with('success', 'Agendamento finalizado com sucesso!');
    }

    public function buscarDevedores(Request $request)
    {
        $search = $request->input('search', '');
        
        $devedores = Devedor::where('nome', 'like', "%{$search}%")
            ->orWhere('razao_social', 'like', "%{$search}%")
            ->orWhere('cpf', 'like', "%{$search}%")
            ->orWhere('cnpj', 'like', "%{$search}%")
            ->limit(10)
            ->get(['id', 'nome', 'razao_social', 'cpf', 'cnpj']);

        return response()->json($devedores);
    }
}
