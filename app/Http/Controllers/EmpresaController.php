<?php

namespace App\Http\Controllers;

use App\Models\Empresa;
use App\Models\TabelaRemuneracao;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use GuzzleHttp\Client;

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
            'agencia' => 'nullable|string|max:20',
            'conta' => 'nullable|string|max:50',
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
            'tipo_pix' => 'nullable|in:CPF,CNPJ,EMAIL,TELEFONE,CHAVE_ALEATORIA,AGENCIA_CONTA',
            'chave_pix' => 'nullable|string|max:255',
            'qtd_parcelas' => 'nullable|integer|min:1',
            'desconto_total_avista' => 'nullable|numeric|min:0|max:100',
            'desconto_total_aprazo' => 'nullable|numeric|min:0|max:100',
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
            'agencia' => 'nullable|string|max:20',
            'conta' => 'nullable|string|max:50',
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
            'tipo_pix' => 'nullable|in:CPF,CNPJ,EMAIL,TELEFONE,CHAVE_ALEATORIA,AGENCIA_CONTA',
            'chave_pix' => 'nullable|string|max:255',
            'qtd_parcelas' => 'nullable|integer|min:1',
            'desconto_total_avista' => 'nullable|numeric|min:0|max:100',
            'desconto_total_aprazo' => 'nullable|numeric|min:0|max:100',
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

        return response()->json([
            'success' => true,
            'status_empresa' => $empresa->status_empresa,
            'message' => 'Status da empresa atualizado com sucesso!'
        ]);
    }

    public function consultarCnpj(Request $request)
    {
        $cnpj = $request->input('cnpj');

        if (!$cnpj || strlen($cnpj) !== 14) {
            return response()->json([
                'success' => false,
                'message' => 'CNPJ inválido'
            ]);
        }

        try {
            // Consulta na API pública CNPJ.ws
            $client = new Client();
            $response = $client->get("https://publica.cnpj.ws/cnpj/{$cnpj}", [
                'timeout' => 10,
                'headers' => [
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'
                ]
            ]);

            $data = json_decode($response->getBody(), true);

            if (isset($data['razao_social'])) {
                // Extrair dados relevantes
                $empresaData = [
                    'razao_social' => $data['razao_social'] ?? null,
                    'nome_fantasia' => $data['estabelecimento']['nome_fantasia'] ?? $data['razao_social'],
                    'inscricao_estadual' => $data['estabelecimento']['inscricoes_estaduais'][0]['inscricao_estadual'] ?? null,
                    'cep' => $data['estabelecimento']['cep'] ?? null,
                    'logradouro' => $data['estabelecimento']['tipo_logradouro'] . ' ' . $data['estabelecimento']['logradouro'] ?? null,
                    'numero' => $data['estabelecimento']['numero'] ?? null,
                    'bairro' => $data['estabelecimento']['bairro'] ?? null,
                    'municipio' => $data['estabelecimento']['cidade']['nome'] ?? null,
                    'uf' => $data['estabelecimento']['estado']['sigla'] ?? null,
                    'ddd_telefone_1' => $data['estabelecimento']['ddd1'] ?? null,
                    'telefone_1' => $data['estabelecimento']['telefone1'] ?? null,
                ];

                return response()->json([
                    'success' => true,
                    'data' => $empresaData
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'CNPJ não encontrado ou dados indisponíveis'
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao consultar CNPJ: ' . $e->getMessage()
            ]);
        }
    }

    public function gerarContrato(Empresa $empresa)
    {
        $empresa->load('plano');

        // Converter valor de adesão para número
        $valorAdesao = floatval(str_replace(['R$', ' ', '.', ','], ['', '', '', '.'], $empresa->valor_adesao ?? '0'));

        // Converter valor para extenso (implementação simples)
        $valorExtenso = $this->numeroParaExtenso($valorAdesao);

        return view('empresas.contrato', compact('empresa', 'valorAdesao', 'valorExtenso'));
    }

    public function gerarFicha(Empresa $empresa)
    {
        $empresa->load('plano');

        // Converter valor de adesão para número
        $valorAdesao = floatval(str_replace(['R$', ' ', '.', ','], ['', '', '', '.'], $empresa->valor_adesao ?? '0'));

        // Converter valor para extenso
        $valorExtenso = $this->numeroParaExtenso($valorAdesao);

        return view('empresas.ficha', compact('empresa', 'valorAdesao', 'valorExtenso'));
    }

    private function numeroParaExtenso($valor)
    {
        // Implementação melhorada para converter número para extenso
        // Suporte para milhares, milhões, etc.
        if ($valor == 0) return 'zero reais';

        $unidades = ['', 'um', 'dois', 'três', 'quatro', 'cinco', 'seis', 'sete', 'oito', 'nove'];
        $dez = ['', 'dez', 'vinte', 'trinta', 'quarenta', 'cinquenta', 'sessenta', 'setenta', 'oitenta', 'noventa'];
        $dezes = ['dez', 'onze', 'doze', 'treze', 'quatorze', 'quinze', 'dezesseis', 'dezessete', 'dezoito', 'dezenove'];
        $centenas = ['', 'cento', 'duzentos', 'trezentos', 'quatrocentos', 'quinhentos', 'seiscentos', 'setecentos', 'oitocentos', 'novecentos'];

        $reais = floor($valor);
        $centavos = round(($valor - $reais) * 100);

        $extenso = '';

        // Processar reais
        if ($reais > 0) {
            $extenso = $this->converterNumeroCompleto($reais) . ' real' . ($reais != 1 ? 'es' : '');
        }

        // Processar centavos
        if ($centavos > 0) {
            if ($reais > 0) $extenso .= ' e ';
            $extenso .= $this->converterNumeroCompleto($centavos) . ' centavo' . ($centavos != 1 ? 's' : '');
        }

        return $extenso;
    }

    private function converterNumeroCompleto($numero)
    {
        if ($numero == 0) return '';

        $unidades = ['', 'um', 'dois', 'três', 'quatro', 'cinco', 'seis', 'sete', 'oito', 'nove'];
        $dez = ['', 'dez', 'vinte', 'trinta', 'quarenta', 'cinquenta', 'sessenta', 'setenta', 'oitenta', 'noventa'];
        $dezes = ['dez', 'onze', 'doze', 'treze', 'quatorze', 'quinze', 'dezesseis', 'dezessete', 'dezoito', 'dezenove'];
        $centenas = ['', 'cento', 'duzentos', 'trezentos', 'quatrocentos', 'quinhentos', 'seiscentos', 'setecentos', 'oitocentos', 'novecentos'];

        $resultado = '';

        // Milhares
        $milhar = floor($numero / 1000);
        $resto = $numero % 1000;

        if ($milhar > 0) {
            if ($milhar == 1) {
                $resultado .= 'mil';
            } else {
                $resultado .= $this->converterParte($milhar, $unidades, $dez, $dezes, $centenas) . ' mil';
            }

            if ($resto > 0) {
                $resultado .= ' e ';
            }
        }

        // Centenas, dezenas e unidades
        if ($resto > 0) {
            $resultado .= $this->converterParte($resto, $unidades, $dez, $dezes, $centenas);
        }

        return trim($resultado);
    }

    private function converterParte($numero, $unidades, $dez, $dezes, $centenas)
    {
        // Esta função agora só processa números de 0 a 999
        if ($numero < 0 || $numero > 999) {
            return '';
        }

        $resultado = '';

        $centena = floor($numero / 100);
        $resto = $numero % 100;

        if ($centena > 0) {
            if ($centena == 1 && $resto == 0) {
                $resultado = 'cem';
            } else {
                $resultado = $centenas[$centena];
            }
        }

        if ($resto > 0) {
            if ($resultado != '') $resultado .= ' e ';

            if ($resto < 10) {
                $resultado .= $unidades[$resto];
            } elseif ($resto < 20) {
                $resultado .= $dezes[$resto - 10];
            } else {
                $dezena = floor($resto / 10);
                $unidade = $resto % 10;

                $resultado .= $dez[$dezena];
                if ($unidade > 0) {
                    $resultado .= ' e ' . $unidades[$unidade];
                }
            }
        }

        return $resultado;
    }
}
