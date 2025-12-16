<?php

namespace App\Http\Controllers;

use App\Models\Empresa;
use App\Models\TabelaRemuneracao;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EmpresaController extends Controller
{
    public function index(Request $request)
    {
        $query = Empresa::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('razao_social', 'like', "%{$search}%")
                  ->orWhere('nome_fantasia', 'like', "%{$search}%")
                  ->orWhere('cnpj', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status_empresa')) {
            $query->where('status_empresa', $request->status_empresa);
        }

        $empresas = $query->with('plano')->latest('created_at')->paginate(20);
        $planos = TabelaRemuneracao::all();

        return view('empresas.index', compact('empresas', 'planos'));
    }

    public function create()
    {
        $planos = TabelaRemuneracao::all();
        return view('empresas.create', compact('planos'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'razao_social' => 'required|string|max:255',
            'nome_fantasia' => 'required|string|max:255',
            'cnpj' => 'required|string|max:18|unique:core_empresa,cnpj',
            'nome_contato' => 'nullable|string|max:255',
            'cpf_contato' => 'nullable|string|max:14',
            'banco' => 'nullable|string|max:100',
            'ie' => 'nullable|string|max:20',
            'telefone' => 'nullable|string|max:20',
            'celular' => 'nullable|string|max:20',
            'whatsapp_financeiro' => 'nullable|string|max:20',
            'operador' => 'nullable|string|max:255',
            'supervisor' => 'nullable|string|max:255',
            'gerente' => 'nullable|string|max:255',
            'plano_id' => 'nullable|exists:core_tabelaremuneracao,id',
            'cep' => 'nullable|string|max:10',
            'endereco' => 'nullable|string|max:255',
            'numero' => 'nullable|string|max:20',
            'bairro' => 'nullable|string|max:100',
            'uf' => 'nullable|string|size:2',
            'cidade' => 'nullable|string|max:100',
            'email' => 'nullable|email|max:255',
            'email_financeiro' => 'nullable|email|max:255',
            'valor_adesao' => 'nullable|string|max:100',
            'usuario' => 'nullable|string|max:100',
            'senha' => 'nullable|string|max:255',
            'nome_favorecido_pix' => 'nullable|string|max:255',
            'tipo_pix' => 'nullable|in:CPF,CNPJ,EMAIL,TELEFONE,CHAVE_ALEATORIA',
            'status_empresa' => 'boolean',
            'logo' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('logo')) {
            $validated['logo'] = $request->file('logo')->store('logos', 'public');
        }

        $empresa = Empresa::create($validated);

        return redirect()->route('empresas.show', $empresa)
            ->with('success', 'Empresa cadastrada com sucesso!');
    }

    public function show(Empresa $empresa)
    {
        $empresa->load(['plano', 'devedores', 'titulos']);
        return view('empresas.show', compact('empresa'));
    }

    public function edit(Empresa $empresa)
    {
        $planos = TabelaRemuneracao::all();
        return view('empresas.edit', compact('empresa', 'planos'));
    }

    public function update(Request $request, Empresa $empresa)
    {
        $validated = $request->validate([
            'razao_social' => 'required|string|max:255',
            'nome_fantasia' => 'required|string|max:255',
            'cnpj' => 'required|string|max:18|unique:core_empresa,cnpj,' . $empresa->id,
            'nome_contato' => 'nullable|string|max:255',
            'cpf_contato' => 'nullable|string|max:14',
            'banco' => 'nullable|string|max:100',
            'ie' => 'nullable|string|max:20',
            'telefone' => 'nullable|string|max:20',
            'celular' => 'nullable|string|max:20',
            'whatsapp_financeiro' => 'nullable|string|max:20',
            'operador' => 'nullable|string|max:255',
            'supervisor' => 'nullable|string|max:255',
            'gerente' => 'nullable|string|max:255',
            'plano_id' => 'nullable|exists:core_tabelaremuneracao,id',
            'cep' => 'nullable|string|max:10',
            'endereco' => 'nullable|string|max:255',
            'numero' => 'nullable|string|max:20',
            'bairro' => 'nullable|string|max:100',
            'uf' => 'nullable|string|size:2',
            'cidade' => 'nullable|string|max:100',
            'email' => 'nullable|email|max:255',
            'email_financeiro' => 'nullable|email|max:255',
            'valor_adesao' => 'nullable|string|max:100',
            'usuario' => 'nullable|string|max:100',
            'senha' => 'nullable|string|max:255',
            'nome_favorecido_pix' => 'nullable|string|max:255',
            'tipo_pix' => 'nullable|in:CPF,CNPJ,EMAIL,TELEFONE,CHAVE_ALEATORIA',
            'status_empresa' => 'boolean',
            'logo' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('logo')) {
            if ($empresa->logo) {
                Storage::disk('public')->delete($empresa->logo);
            }
            $validated['logo'] = $request->file('logo')->store('logos', 'public');
        }

        $empresa->update($validated);

        return redirect()->route('empresas.show', $empresa)
            ->with('success', 'Empresa atualizada com sucesso!');
    }

    public function destroy(Empresa $empresa)
    {
        if ($empresa->devedores()->count() > 0 || $empresa->titulos()->count() > 0) {
            return redirect()->route('empresas.index')
                ->with('error', 'Não é possível excluir empresa com devedores ou títulos vinculados.');
        }

        if ($empresa->logo) {
            Storage::disk('public')->delete($empresa->logo);
        }

        $empresa->delete();

        return redirect()->route('empresas.index')
            ->with('success', 'Empresa excluída com sucesso!');
    }

    public function alterarStatus(Empresa $empresa)
    {
        $empresa->status_empresa = !$empresa->status_empresa;
        $empresa->save();

        return redirect()->back()
            ->with('success', 'Status da empresa atualizado com sucesso!');
    }

    public function consultarCnpj(Request $request)
    {
        // Implementar scraping da Receita Federal
        // Por enquanto retorna JSON com dados mockados
        $cnpj = $request->input('cnpj');
        
        // TODO: Implementar scraping real com Selenium
        return response()->json([
            'success' => false,
            'message' => 'Funcionalidade de consulta CNPJ será implementada em breve'
        ]);
    }
}
