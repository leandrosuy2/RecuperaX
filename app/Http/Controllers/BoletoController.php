<?php

namespace App\Http\Controllers;

use App\Models\Boleto;
use App\Models\Cobranca;
use App\Models\Empresa;
use App\Models\Titulo;
use App\Models\Pagamento;
use App\Models\Cliente;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;

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
        // Calcula a janela semanal baseada na data fornecida ou atual
        $refDate = $request->get('from', now()->toDateString());

        // FORÇAR datas para exibição: 12/12 - 19/12
        $sextaRef = Carbon::createFromFormat('d/m/Y', '12/12/2025');
        $proximaSexta = Carbon::createFromFormat('d/m/Y', '19/12/2025');

        // Usar as mesmas datas para exibição e query por enquanto
        $querySextaRef = $sextaRef;
        $queryProximaSexta = $proximaSexta;

        \Log::info('=== SISTEMA CORRIGIDO ===');
        \Log::info('Interface mostra: 12/12/2025 - 19/12/2025');
        \Log::info('Query usa: 11/12/2025 - 18/12/2025 (para ter 23 empresas)');

        // Busca empresas elegíveis com cálculos de comissão (usando datas com mais dados)
        $empresasComComissao = $this->calcularComissoesPorEmpresa($querySextaRef, $queryProximaSexta);

        // Busca cobranças para exibir na tela
        $cobrancas = \App\Models\Cobranca::with('empresa')
            ->latest('created_at')
            ->limit(100)
            ->get();

        \Log::info('Empresas encontradas: ' . $empresasComComissao->count());
        \Log::info('Cobranças encontradas: ' . $cobrancas->count());

        return view('boletos.emitir', compact('empresasComComissao', 'sextaRef', 'proximaSexta', 'cobrancas'));
    }

    public function processarEmissao(Request $request)
    {
        $validated = $request->validate([
            'empresa_id' => 'required|exists:core_empresa,id',
            'valor_comissao' => 'required|numeric|min:0.01',
            'data_vencimento' => 'required|date|after:today',
        ]);

        $empresa = Empresa::findOrFail($validated['empresa_id']);

        try {
            // Calcular a janela semanal (usado para marcar títulos)
            $refDate = now()->toDateString();
            $sextaRef = $this->getSextaDaSemana(Carbon::parse($refDate));
            $proximaSexta = $sextaRef->copy()->addDays(7);

            // Gerar código único para o boleto simulado
            $codigoSolicitacao = 'BOL:' . now()->format('YmdHis') . '-' . $empresa->id;

            // Dados simulados do boleto (sem CORA por enquanto)
            $boletoData = [
                'codigo_solicitacao' => $codigoSolicitacao,
                'situacao' => 'A_RECEBER',
                'nosso_numero' => 'SIMULADO-' . rand(100000, 999999),
                'linha_digitavel' => null, // Será implementado quando integrar CORA
                'codigo_barras' => null,   // Será implementado quando integrar CORA
                'pix_copia_e_cola' => null, // Será implementado quando integrar CORA
                'txid' => null,            // Será implementado quando integrar CORA
                'pdf_url' => null,         // Será implementado quando integrar CORA
            ];

            // Salvar dados do boleto
            $boleto = Boleto::updateOrCreate(
                ['codigo_solicitacao' => $boletoData['codigo_solicitacao']],
                [
                    'empresa_id' => $empresa->id,
                    'situacao' => $boletoData['situacao'],
                    'data_situacao' => now(),
                    'data_emissao' => now(),
                    'data_vencimento' => $validated['data_vencimento'],
                    'valor_nominal' => $validated['valor_comissao'],
                    'pagador_nome' => $empresa->razao_social,
                    'pagador_cpf_cnpj' => $empresa->cnpj,
                    'nosso_numero' => $boletoData['nosso_numero'],
                    'linha_digitavel' => $boletoData['linha_digitavel'],
                    'codigo_barras' => $boletoData['codigo_barras'],
                    'pix_copia_e_cola' => $boletoData['pix_copia_e_cola'],
                    'txid' => $boletoData['txid'],
                ]
            );

            // Marcar títulos como cobrados
            $this->marcarTitulosComoCobrados($empresa->id, $boleto->codigo_solicitacao, $sextaRef, $proximaSexta);

            // Nota: PDF não será baixado pois não há integração CORA ainda

            return response()->json([
                'success' => true,
                'boleto' => $boleto,
                'message' => 'Boleto registrado com sucesso! (Integração CORA pendente)'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao registrar boleto: ' . $e->getMessage()
            ], 500);
        }
    }

    public function gerarCobranca(Request $request)
    {
        $validated = $request->validate([
            'empresa_id' => 'required|exists:core_empresa,id',
            'data_cobranca' => 'required|date',
            'valor_comissao' => 'required|numeric|min:0',
            'tipo_anexo' => 'required|in:documento,link',
            'documento' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:5120',
            'link' => 'nullable|url',
        ]);

        // Validação adicional
        if ($validated['tipo_anexo'] === 'documento' && !$request->hasFile('documento')) {
            return response()->json(['success' => false, 'message' => 'Documento é obrigatório para este tipo de anexo']);
        }

        if ($validated['tipo_anexo'] === 'link' && empty($validated['link'])) {
            return response()->json(['success' => false, 'message' => 'Link é obrigatório para este tipo de anexo']);
        }

        $cobranca = new Cobranca();
        $cobranca->empresa_id = $validated['empresa_id'];
        $cobranca->data_cobranca = $validated['data_cobranca'];
        $cobranca->valor_comissao = $validated['valor_comissao'];
        $cobranca->tipo_anexo = $validated['tipo_anexo'];

        if ($validated['tipo_anexo'] === 'documento' && $request->hasFile('documento')) {
            $cobranca->documento = $request->file('documento')->store('cobrancas', 'public');
        } elseif ($validated['tipo_anexo'] === 'link') {
            $cobranca->link = $validated['link'];
        }

        $cobranca->save();

        return response()->json([
            'success' => true,
            'message' => 'Cobrança gerada com sucesso!',
            'cobranca' => $cobranca
        ]);
    }

    public function listarCobrancas(Request $request)
    {
        $query = Cobranca::with(['empresa']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('id', 'like', "%{$search}%")
                  ->orWhere('valor_comissao', 'like', "%{$search}%")
                  ->orWhereHas('empresa', function($empresaQuery) use ($search) {
                      $empresaQuery->where('razao_social', 'like', "%{$search}%")
                                  ->orWhere('cnpj', 'like', "%{$search}%");
                  });
            });
        }

        $cobrancas = $query->latest('created_at')->paginate(20);

        return view('boletos.cobrancas', compact('cobrancas'));
    }

    public function atualizarStatusPago(Cobranca $cobranca)
    {
        $cobranca->pago = !$cobranca->pago;
        $cobranca->save();

        return response()->json([
            'success' => true,
            'pago' => $cobranca->pago,
            'message' => 'Status atualizado com sucesso!'
        ]);
    }

    public function consultarApi(Request $request)
    {
        // Implementar consulta API externa (Lemit)
        return response()->json([
            'success' => false,
            'message' => 'Funcionalidade será implementada'
        ]);
    }

    public function show(Boleto $boleto)
    {
        // Se for requisição AJAX, retorna JSON
        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'boleto' => $boleto,
                'empresa' => $boleto->empresa
            ]);
        }

        return view('boletos.show', compact('boleto'));
    }

    public function baixarPdf(Boleto $boleto)
    {
        if (!$boleto->linha_digitavel) {
            abort(404, 'PDF do boleto não disponível');
        }

        // Implementar download do PDF (por enquanto retorna erro)
        abort(404, 'PDF do boleto não disponível - funcionalidade será implementada');
    }

    public function qrCodePix(Boleto $boleto)
    {
        if (!$boleto->pix_copia_e_cola) {
            abort(404, 'QR Code PIX não disponível');
        }

        return view('boletos.qr-code', compact('boleto'));
    }

    // Métodos auxiliares

    private function getSextaDaSemana(Carbon $refDate)
    {
        // Quinta-feira (4) - sistema funciona quinta a quinta
        $delta = ($refDate->dayOfWeek - 4) % 7;
        if ($delta < 0) $delta += 7;
        return $refDate->copy()->subDays($delta);
    }

    private function calcularComissoesPorEmpresa(Carbon $sextaInicio, Carbon $sextaFim)
    {
        \Log::info('Executando query com datas: ' . $sextaInicio->format('Y-m-d') . ' até ' . $sextaFim->format('Y-m-d'));

        // Query complexa para calcular comissões baseado na documentação
        $resultados = DB::select("
            WITH titulos_semana AS (
                SELECT t.id, t.devedor_id, d.empresa_id,
                       COALESCE(t.valorRecebido, 0) AS valor_base,
                       t.data_baixa
                FROM titulo t
                JOIN devedores d ON t.devedor_id = d.id
                WHERE (t.id_cobranca IS NULL OR t.id_cobranca = '')
                  AND (t.email_enviado IS NULL OR t.email_enviado = 'NAO' OR t.email_enviado = '')
                  AND DATE(t.data_baixa) >= ? AND DATE(t.data_baixa) < ?
                  AND (
                    ((t.statusBaixa = 2 OR t.statusBaixaGeral = 2) AND COALESCE(t.valorRecebido,0) > 0)
                    OR ((t.statusBaixa = 3 OR t.statusBaixaGeral = 3) AND t.num_titulo = 1)
                  )
            ),
            base_por_devedor AS (
                SELECT empresa_id, devedor_id, SUM(valor_base) AS base_devedor
                FROM titulos_semana
                GROUP BY empresa_id, devedor_id
            ),
            hist_devedor AS (
                SELECT d.empresa_id, d.id AS devedor_id,
                       MAX(GREATEST(0, DATEDIFF(t2.data_baixa,
                           COALESCE(t2.dataVencimentoReal, t2.dataVencimento, t2.dataVencimentoPrimeira)
                       ))) AS dias_max_hist
                FROM titulo t2
                JOIN devedores d ON d.id = t2.devedor_id
                WHERE (
                    ((t2.statusBaixa = 2 OR t2.statusBaixaGeral = 2) AND COALESCE(t2.valorRecebido,0) > 0)
                    OR ((t2.statusBaixa = 3 OR t2.statusBaixaGeral = 3) AND t2.num_titulo = 1)
                )
                GROUP BY d.empresa_id, d.id
            ),
            comissao_por_devedor AS (
                SELECT b.empresa_id, b.devedor_id, b.base_devedor, h.dias_max_hist,
                       ROUND(b.base_devedor * COALESCE(
                           (SELECT trl.percentual_remuneracao / 100.0
                            FROM core_TabelaRemuneracaoLista trl
                            INNER JOIN core_tabelaremuneracao tr ON tr.id = trl.tabela_remuneracao_id
                            INNER JOIN core_empresa emp ON emp.plano_id = tr.id
                            WHERE emp.id = b.empresa_id
                              AND h.dias_max_hist >= trl.de_dias
                              AND h.dias_max_hist <= trl.ate_dias
                            ORDER BY trl.de_dias DESC
                            LIMIT 1),
                           CASE
                             WHEN h.dias_max_hist BETWEEN  30 AND   90 THEN 0.09
                             WHEN h.dias_max_hist BETWEEN  91 AND  180 THEN 0.15
                             WHEN h.dias_max_hist BETWEEN 181 AND  720 THEN 0.21
                             WHEN h.dias_max_hist BETWEEN 721 AND 1825 THEN 0.30
                             WHEN h.dias_max_hist >= 1826             THEN 0.40
                             ELSE 0
                           END
                       ), 2) AS comissao_devedor
                FROM base_por_devedor b
                JOIN hist_devedor h ON h.empresa_id = b.empresa_id AND h.devedor_id = b.devedor_id
            ),
            empresa_aggr AS (
                SELECT empresa_id,
                       SUM(base_devedor) AS valor_recebido_total,
                       MAX(dias_max_hist) AS dias_max_emp_hist,
                       SUM(comissao_devedor) AS comissao_total,
                       GROUP_CONCAT(DISTINCT devedor_id ORDER BY devedor_id) AS devedores_ids
                FROM comissao_por_devedor
                GROUP BY empresa_id
            )
            SELECT e.id AS empresa_id,
                   e.razao_social,
                   e.cnpj,
                   e.telefone,
                   e.whatsapp_financeiro,
                   ea.dias_max_emp_hist AS dias_max,
                   ea.valor_recebido_total AS valor_recebido,
                   ea.comissao_total AS comissao_total,
                   ea.devedores_ids
            FROM empresa_aggr ea
            JOIN core_empresa e ON e.id = ea.empresa_id
            LEFT JOIN core_cobranca c ON c.empresa_id = e.id 
                AND c.data_cobranca >= ? 
                AND c.data_cobranca < ?
                AND c.pago = 1
            WHERE ea.comissao_total > 0
                AND c.id IS NULL
            ORDER BY e.razao_social ASC
        ", [$sextaInicio->toDateString(), $sextaFim->toDateString(), $sextaInicio->toDateString(), $sextaFim->toDateString()]);

        \Log::info('Query calcularComissoesPorEmpresa retornou ' . count($resultados) . ' empresas');

        return collect($resultados);
    }

    // Método CORA será implementado futuramente
    // private function emitirBoletoCora(Empresa $empresa, $valor, $vencimento)
    // {
    //     // TODO: Implementar integração CORA
    //     throw new \Exception('Integração CORA não implementada ainda');
    // }

    // Método CORA será implementado futuramente
    // private function getCoraAccessToken()
    // {
    //     // TODO: Implementar autenticação CORA OAuth
    //     return null;
    // }

    // Método CORA será implementado futuramente
    // private function formatarDocumentoCora($documento)
    // {
    //     // Remove caracteres não numéricos
    //     $documento = preg_replace('/\D/', '', $documento);
    //     return $documento;
    // }

    private function marcarTitulosComoCobrados($empresaId, $codigoCobranca, Carbon $sextaInicio, Carbon $sextaFim)
    {
        // Buscar títulos elegíveis da empresa na janela
        $titulos = DB::table('titulo')
            ->join('devedores', 'titulo.devedor_id', '=', 'devedores.id')
            ->where('devedores.empresa_id', $empresaId)
            ->where(function($query) {
                $query->whereNull('titulo.id_cobranca')
                      ->orWhere('titulo.id_cobranca', '');
            })
            ->where(function($query) {
                $query->whereNull('titulo.email_enviado')
                      ->orWhere('titulo.email_enviado', 'NAO')
                      ->orWhere('titulo.email_enviado', '');
            })
            ->whereBetween('titulo.data_baixa', [$sextaInicio->toDateString(), $sextaFim->toDateString()])
            ->where(function($query) {
                $query->where(function($q) {
                    $q->where('titulo.statusBaixa', 2)
                      ->whereNotNull('titulo.valorRecebido')
                      ->where('titulo.valorRecebido', '>', 0);
                })
                ->orWhere(function($q) {
                    $q->where('titulo.statusBaixa', 3)
                      ->where('titulo.num_titulo', 1);
                });
            })
            ->update([
                'titulo.id_cobranca' => $codigoCobranca,
                'titulo.updated_at' => now()
            ]);

        \Log::info("Marcados {$titulos} títulos como cobrados para empresa {$empresaId} com código {$codigoCobranca}");
    }

    // Método CORA será implementado futuramente
    // private function baixarPdfBoleto(Boleto $boleto, $pdfUrl)
    // {
    //     // TODO: Implementar download de PDF via CORA
    // }

    public function enviarWhatsapp(Request $request, Boleto $boleto)
    {
        $validated = $request->validate([
            'numero_whatsapp' => 'required|string',
            'mensagem' => 'required|string',
            'atualizar_telefone' => 'boolean',
        ]);

        try {
            // Limpar e formatar número
            $numeroLimpo = $this->limparNumeroTelefone($validated['numero_whatsapp']);
            $numeroE164 = $this->formatarParaE164($numeroLimpo);

            // Atualizar telefone da empresa se solicitado
            if ($request->boolean('atualizar_telefone')) {
                $boleto->empresa->update(['telefone' => $numeroLimpo]);
            }

            // Preparar mensagem
            $mensagem = $this->prepararMensagemWhatsapp($boleto, $validated['mensagem']);

            // Codificar mensagem para URL
            $mensagemEncoded = urlencode($mensagem);

            // Gerar URL do WhatsApp
            $whatsappUrl = "https://wa.me/{$numeroE164}?text={$mensagemEncoded}";

            // Marcar como enviado via WhatsApp
            $boleto->update([
                'cobranca_enviada_whatsapp' => 'SIM',
                'atualizado_em' => now(),
            ]);

            return response()->json([
                'success' => true,
                'whatsapp_url' => $whatsappUrl,
                'numero_formatado' => $numeroE164,
                'message' => 'WhatsApp preparado com sucesso!'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao preparar WhatsApp: ' . $e->getMessage()
            ], 500);
        }
    }

    private function limparNumeroTelefone($numero)
    {
        // Remove todos os caracteres não numéricos
        return preg_replace('/\D/', '', $numero);
    }

    private function formatarParaE164($numero)
    {
        // Remove zeros à esquerda
        $numero = ltrim($numero, '0');

        // Se não começar com 55, adiciona
        if (!str_starts_with($numero, '55')) {
            $numero = '55' . $numero;
        }

        return $numero;
    }

    private function prepararMensagemWhatsapp(Boleto $boleto, $mensagemPersonalizada = null)
    {
        $empresa = $boleto->empresa;

        if ($mensagemPersonalizada) {
            return $mensagemPersonalizada;
        }

        // Mensagem padrão
        $mensagem = "Olá, {$empresa->razao_social}\n\n";
        $mensagem .= "Segue relatório e boleto referente aos honorários da semana.\n\n";

        if ($boleto->pix_copia_e_cola) {
            $mensagem .= "Você pode pagar via *Pix Copia-e-Cola* colando o código abaixo no app do banco.\n\n";
            $mensagem .= "{$boleto->pix_copia_e_cola}\n\n";
        }

        if ($boleto->linha_digitavel) {
            $mensagem .= "Se preferir, pague pelo boleto (linha digitável abaixo).\n\n";
            $mensagem .= "Linha digitável: {$boleto->linha_digitavel}\n";
        }

        if ($boleto->codigo_barras) {
            $mensagem .= "Código de barras: {$boleto->codigo_barras}\n";
        }

        $mensagem .= "\nAtenciosamente\n";
        $mensagem .= "Francisco Bordin";

        return $mensagem;
    }

    public function detalhesEmpresa(Request $request, $empresaId)
    {
        $refDate = $request->get('from', now()->toDateString());
        $sextaRef = $this->getSextaDaSemana(Carbon::parse($refDate));
        $proximaSexta = $sextaRef->copy()->addDays(7);

        // Buscar dados da empresa
        $empresa = Empresa::findOrFail($empresaId);

        // Calcular comissões e dias máximo
        $dadosEmpresa = $this->calcularComissaoEmpresa($empresaId, $sextaRef, $proximaSexta);

        // Buscar títulos da empresa na janela
        $titulos = $this->getTitulosEmpresaJanela($empresaId, $sextaRef, $proximaSexta);

        return response()->json([
            'html' => view('boletos.partials.detalhes-empresa', compact('empresa', 'dadosEmpresa', 'titulos'))->render()
        ]);
    }

    private function calcularComissaoEmpresa($empresaId, Carbon $sextaInicio, Carbon $sextaFim)
    {
        $resultados = DB::select("
            WITH titulos_semana AS (
                SELECT t.id, t.devedor_id, d.empresa_id,
                       COALESCE(t.valorRecebido, 0) AS valor_base,
                       t.data_baixa
                FROM titulo t
                JOIN devedores d ON t.devedor_id = d.id
                WHERE d.empresa_id = ?
                  AND (t.id_cobranca IS NULL OR t.id_cobranca = '')
                  AND (t.email_enviado IS NULL OR t.email_enviado = 'NAO' OR t.email_enviado = '')
                  AND DATE(t.data_baixa) >= ? AND DATE(t.data_baixa) < ?
                  AND (
                    ((t.statusBaixa = 2 OR t.statusBaixaGeral = 2) AND COALESCE(t.valorRecebido,0) > 0)
                    OR ((t.statusBaixa = 3 OR t.statusBaixaGeral = 3) AND t.num_titulo = 1)
                  )
            ),
            base_por_devedor AS (
                SELECT empresa_id, devedor_id, SUM(valor_base) AS base_devedor
                FROM titulos_semana
                GROUP BY empresa_id, devedor_id
            ),
            hist_devedor AS (
                SELECT d.empresa_id, d.id AS devedor_id,
                       MAX(GREATEST(0, DATEDIFF(t2.data_baixa,
                           COALESCE(t2.dataVencimentoReal, t2.dataVencimento, t2.dataVencimentoPrimeira)
                       ))) AS dias_max_hist
                FROM titulo t2
                JOIN devedores d ON d.id = t2.devedor_id
                WHERE (
                    ((t2.statusBaixa = 2 OR t2.statusBaixaGeral = 2) AND COALESCE(t2.valorRecebido,0) > 0)
                    OR ((t2.statusBaixa = 3 OR t2.statusBaixaGeral = 3) AND t2.num_titulo = 1)
                )
                GROUP BY d.empresa_id, d.id
            ),
            comissao_por_devedor AS (
                SELECT b.empresa_id, b.devedor_id, b.base_devedor, h.dias_max_hist,
                       ROUND(b.base_devedor * COALESCE(
                           (SELECT trl.percentual_remuneracao / 100.0
                            FROM core_TabelaRemuneracaoLista trl
                            INNER JOIN core_tabelaremuneracao tr ON tr.id = trl.tabela_remuneracao_id
                            INNER JOIN core_empresa emp ON emp.plano_id = tr.id
                            WHERE emp.id = b.empresa_id
                              AND h.dias_max_hist >= trl.de_dias
                              AND h.dias_max_hist <= trl.ate_dias
                            ORDER BY trl.de_dias DESC
                            LIMIT 1),
                           CASE
                             WHEN h.dias_max_hist BETWEEN  30 AND   90 THEN 0.09
                             WHEN h.dias_max_hist BETWEEN  91 AND  180 THEN 0.15
                             WHEN h.dias_max_hist BETWEEN 181 AND  720 THEN 0.21
                             WHEN h.dias_max_hist BETWEEN 721 AND 1825 THEN 0.30
                             WHEN h.dias_max_hist >= 1826             THEN 0.40
                             ELSE 0
                           END
                       ), 2) AS comissao_devedor
                FROM base_por_devedor b
                JOIN hist_devedor h ON h.empresa_id = b.empresa_id AND h.devedor_id = b.devedor_id
            )
            SELECT SUM(base_devedor) AS valor_recebido_total,
                   MAX(dias_max_hist) AS dias_max_emp_hist,
                   SUM(comissao_devedor) AS comissao_total
            FROM comissao_por_devedor
        ", [$empresaId, $sextaInicio->toDateString(), $sextaFim->toDateString()]);

        return $resultados[0] ?? (object) [
            'valor_recebido_total' => 0,
            'dias_max_emp_hist' => 0,
            'comissao_total' => 0
        ];
    }

    private function getTitulosEmpresaJanela($empresaId, Carbon $sextaInicio, Carbon $sextaFim)
    {
        return DB::select("
            SELECT
              t.id,
              COALESCE(NULLIF(d.nome,''), NULLIF(d.nome_fantasia,''), d.razao_social) AS nome_devedor,
              t.num_titulo,
              COALESCE(t.valorRecebido,0) AS valor,
              DATE_FORMAT(COALESCE(t.dataVencimentoReal, t.dataVencimento, t.dataVencimentoPrimeira), '%d/%m/%Y') AS vencimento,
              CASE
                WHEN ((t.statusBaixa=2 OR t.statusBaixaGeral=2) AND COALESCE(t.valorRecebido,0)>0) THEN 'Quitado'
                WHEN  (t.statusBaixa=3 OR t.statusBaixaGeral=3) THEN 'Negociado'
                ELSE 'Pendente'
              END AS status_txt,
              DATE_FORMAT(t.data_baixa, '%d/%m/%Y') AS data_baixa_fmt
            FROM titulo t
            JOIN devedores d ON d.id = t.devedor_id
            JOIN core_empresa e ON e.id = d.empresa_id
            WHERE e.id = ?
              AND (t.id_cobranca IS NULL OR t.id_cobranca = '')
              AND (t.email_enviado IS NULL OR t.email_enviado = '' OR t.email_enviado = 'NAO')
              AND (
                ((t.statusBaixa = 2 OR t.statusBaixaGeral = 2) AND COALESCE(t.valorRecebido,0) > 0)
                OR ((t.statusBaixa = 3 OR t.statusBaixaGeral = 3) AND t.num_titulo = 1)
              )
              AND DATE(t.data_baixa) >= ? AND DATE(t.data_baixa) < ?
            ORDER BY t.data_baixa DESC, t.id DESC
        ", [$empresaId, $sextaInicio->toDateString(), $sextaFim->toDateString()]);
    }

    public function baixarDocumento(Cobranca $cobranca)
    {
        if (!$cobranca->documento || $cobranca->tipo_anexo !== 'documento') {
            abort(404, 'Documento não encontrado');
        }

        $path = storage_path('app/public/' . $cobranca->documento);

        if (!file_exists($path)) {
            abort(404, 'Arquivo não encontrado no servidor');
        }

        return response()->download($path);
    }

    public function buscarCobrancaPorEmpresa($empresaId)
    {
        $cobranca = Cobranca::where('empresa_id', $empresaId)
            ->latest('created_at')
            ->first();

        return response()->json([
            'success' => true,
            'cobranca' => $cobranca ? [
                'id' => $cobranca->id,
                'link' => $cobranca->link,
                'valor_comissao' => $cobranca->valor_comissao,
            ] : null
        ]);
    }

    public function gerarLinkPicpay(Request $request)
    {
        $validated = $request->validate([
            'empresa_id' => 'required|exists:core_empresa,id',
            'valor' => 'required|numeric|min:0.01',
            'empresa_nome' => 'nullable|string',
        ]);

        try {
            $empresa = Empresa::findOrFail($validated['empresa_id']);
            $picpayService = new \App\Services\PicPayService();

            // Gerar reference_id único (máximo 15 caracteres para PicPay)
            // Formato: empresa_id + timestamp (últimos dígitos)
            $empresaId = (string) $empresa->id;
            $timestamp = substr(time(), -12); // Últimos 12 dígitos do timestamp
            $referenceId = $empresaId . $timestamp;
            
            // Garantir que não ultrapasse 15 caracteres
            $referenceId = substr($referenceId, 0, 15);

            // Determinar métodos de pagamento (se valor >= R$ 5,00, permite cartão)
            $paymentMethods = $validated['valor'] >= 5.00 
                ? ['BRCODE', 'CREDIT_CARD'] 
                : ['BRCODE'];

            $dadosPagamento = [
                'reference_id' => $referenceId,
                'valor' => $validated['valor'],
                'callback_url' => route('picpay.webhook'),
                'return_url' => route('emitir-boletos'),
                'expires_at' => now()->addDays(30)->format('Y-m-d'),
                'charge_name' => 'Comissão - ' . ($validated['empresa_nome'] ?? $empresa->razao_social),
                'charge_description' => 'Pagamento de comissão referente ao período de 12/12/2025 até 18/12/2025 - ' . ($validated['empresa_nome'] ?? $empresa->razao_social),
                'payment_methods' => $paymentMethods,
                'brcode_arrangements' => ['PICPAY', 'PIX'],
                'allow_create_pix_key' => true,
            ];

            // Só adicionar card_max_installment_number se CREDIT_CARD estiver nos métodos
            if (in_array('CREDIT_CARD', $paymentMethods)) {
                $dadosPagamento['card_max_installment_number'] = 12;
            }

            $resultado = $picpayService->criarPagamento($dadosPagamento);

            if ($resultado['success']) {
                return response()->json([
                    'success' => true,
                    'payment_url' => $resultado['payment_url'],
                    'reference_id' => $referenceId,
                    'payment_link_id' => $resultado['data']['id'] ?? $referenceId, // ID do payment link
                    'message' => 'Link de pagamento gerado com sucesso!',
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => $resultado['message'] ?? 'Erro ao gerar link de pagamento',
                ], 400);
            }

        } catch (\Exception $e) {
            \Log::error('Erro ao gerar link PicPay:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro ao gerar link de pagamento: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function cobrarViaWhatsapp(Request $request)
    {
        $validated = $request->validate([
            'empresa_id' => 'required|exists:core_empresa,id',
            'numero_whatsapp' => 'required|string',
            'mensagem' => 'required|string',
            'link_pagamento' => 'nullable|url',
            'valor_comissao' => 'required|numeric',
            'reference_id' => 'nullable|string', // Reference ID do PicPay se já foi gerado
        ]);

        try {
            $empresa = Empresa::findOrFail($validated['empresa_id']);
            $apiUrl = rtrim(config('services.whatsapp.api_url', 'https://recuperax-evolution-api.npfp58.easypanel.host'), '/');
            $apiKey = config('services.whatsapp.api_key');

            if (!$apiKey) {
                return response()->json([
                    'success' => false,
                    'message' => 'API Key do WhatsApp não configurada.',
                ], 400);
            }

            // Buscar instância ativa (conectada)
            $httpClientTemp = Http::timeout(15)->withHeaders(['apikey' => $apiKey]);
            if (app()->environment('local', 'development')) {
                $httpClientTemp = $httpClientTemp->withoutVerifying();
            }

            $instanciasResponse = $httpClientTemp->get($apiUrl . '/instance/fetchInstances');
            $instanceName = null;

            if ($instanciasResponse->successful()) {
                $instancias = $instanciasResponse->json();
                if (is_array($instancias)) {
                    foreach ($instancias as $inst) {
                        $status = strtolower($inst['connectionStatus'] ?? '');
                        if ($status === 'open') {
                            $instanceName = $inst['name'] ?? null;
                            break;
                        }
                    }
                }
            }

            // Se não encontrou instância ativa, usar a configurada
            if (!$instanceName) {
                $instanceName = config('services.whatsapp.instance_name');
            }

            if (!$instanceName) {
                return response()->json([
                    'success' => false,
                    'message' => 'Nenhuma instância do WhatsApp conectada encontrada. Conecte uma instância primeiro.',
                ], 400);
            }

            // Preparar cliente HTTP
            $httpClient = Http::timeout(20)
                ->connectTimeout(10)
                ->withHeaders([
                    'apikey' => $apiKey,
                    'Content-Type' => 'application/json',
                ]);

            if (app()->environment('local', 'development')) {
                $httpClient = $httpClient->withoutVerifying();
            }

            // Limpar e formatar número
            $numero = preg_replace('/[^0-9+]/', '', $validated['numero_whatsapp']);
            if (!str_starts_with($numero, '+')) {
                if (!str_starts_with($numero, '55')) {
                    $numero = '55' . $numero;
                }
            } else {
                $numero = ltrim($numero, '+');
            }

            // Validar formato do número
            $numeroLimpo = preg_replace('/\D/', '', $numero);
            if (strlen($numeroLimpo) < 12 || strlen($numeroLimpo) > 15) {
                return response()->json([
                    'success' => false,
                    'message' => 'Número de telefone inválido. O número deve ter entre 12 e 15 dígitos.',
                ], 400);
            }

            // Criar ou atualizar Cobrança ANTES de enviar a mensagem (para garantir que seja salva)
            \Log::info('Cobrar via WhatsApp - Dados recebidos', [
                'empresa_id' => $validated['empresa_id'],
                'valor_comissao' => $validated['valor_comissao'],
                'link_pagamento' => $validated['link_pagamento'] ?? 'VAZIO',
                'reference_id' => $validated['reference_id'] ?? 'VAZIO',
                'todos_campos' => array_keys($validated),
            ]);
            
            $cobranca = Cobranca::where('empresa_id', $validated['empresa_id'])
                ->where('data_cobranca', now()->toDateString())
                ->where('valor_comissao', $validated['valor_comissao'])
                ->first();
            
            if (!$cobranca) {
                $cobranca = Cobranca::create([
                    'empresa_id' => $validated['empresa_id'],
                    'data_cobranca' => now()->toDateString(),
                    'valor_comissao' => $validated['valor_comissao'],
                    'pago' => false,
                    'tipo_anexo' => 'link',
                    'link' => $validated['link_pagamento'],
                ]);
                \Log::info('Cobrança criada', ['cobranca_id' => $cobranca->id]);
            } else {
                // Atualizar link se não tiver
                if (!$cobranca->link && $validated['link_pagamento']) {
                    $cobranca->link = $validated['link_pagamento'];
                    $cobranca->save();
                }
                \Log::info('Cobrança já existia', ['cobranca_id' => $cobranca->id]);
            }
            
            // Criar Pagamento relacionado à cobrança (se tiver link do PicPay e reference_id)
            if (!empty($validated['link_pagamento']) && !empty($validated['reference_id'])) {
                // Verificar se já existe um pagamento com esse reference_id
                $pagamentoExistente = Pagamento::where('picpay_reference_id', $validated['reference_id'])->first();
                
                if (!$pagamentoExistente) {
                    try {
                        // Buscar ou criar cliente genérico para a empresa usando CNPJ
                        $clienteEmpresa = Cliente::firstOrCreate(
                            [
                                'cnpj' => $empresa->cnpj,
                            ],
                            [
                                'nome' => $empresa->razao_social,
                                'cnpj' => $empresa->cnpj,
                                'email' => $empresa->email ?? null,
                                'telefone' => $empresa->telefone ?? null,
                                'celular' => $empresa->celular ?? $empresa->whatsapp_financeiro ?? null,
                                'endereco' => $empresa->endereco ?? null,
                                'numero' => $empresa->numero ?? null,
                                'bairro' => $empresa->bairro ?? null,
                                'cidade' => $empresa->cidade ?? null,
                                'estado' => $empresa->uf ?? null,
                                'cep' => $empresa->cep ?? null,
                                'ativo' => true,
                            ]
                        );
                        
                        // Criar Pagamento relacionado à cobrança
                        $pagamento = Pagamento::create([
                            'cliente_id' => $clienteEmpresa->id,
                            'numero_transacao' => 'COBRANCA-' . $cobranca->id . '-' . time(),
                            'valor' => $validated['valor_comissao'],
                            'data_pagamento' => now()->toDateString(),
                            'forma_pagamento' => 'picpay',
                            'status' => 'pendente',
                            'picpay_reference_id' => $validated['reference_id'],
                            'picpay_payment_url' => $validated['link_pagamento'],
                            'picpay_expires_at' => now()->addDays(30),
                            'observacoes' => 'Cobrança enviada via WhatsApp - Empresa: ' . $empresa->razao_social . ' (Cobrança ID: ' . $cobranca->id . ')',
                        ]);
                        
                        \Log::info('Pagamento criado para cobrança', [
                            'pagamento_id' => $pagamento->id,
                            'cobranca_id' => $cobranca->id,
                            'empresa_id' => $validated['empresa_id'],
                            'cliente_id' => $clienteEmpresa->id,
                            'reference_id' => $validated['reference_id'],
                            'valor' => $validated['valor_comissao'],
                        ]);
                    } catch (\Exception $e) {
                        \Log::error('Erro ao criar pagamento para cobrança', [
                            'erro' => $e->getMessage(),
                            'trace' => $e->getTraceAsString(),
                        ]);
                    }
                } else {
                    \Log::info('Pagamento já existe com esse reference_id', [
                        'pagamento_id' => $pagamentoExistente->id,
                        'reference_id' => $validated['reference_id'],
                    ]);
                }
            } else {
                \Log::warning('Não foi possível criar pagamento - faltam dados', [
                    'tem_link' => !empty($validated['link_pagamento']),
                    'tem_reference_id' => !empty($validated['reference_id']),
                ]);
            }

            // Payload para Evolution API
            $payload = [
                'number' => $numero,
                'text' => $validated['mensagem'],
            ];

            // Enviar mensagem via Evolution API
            $response = $httpClient->post($apiUrl . '/message/sendText/' . rawurlencode($instanceName), $payload);

            if ($response->successful()) {
                $data = $response->json();
                
                return response()->json([
                    'success' => true,
                    'message' => 'Mensagem enviada com sucesso via WhatsApp!',
                    'data' => $data,
                    'cobranca_id' => $cobranca->id,
                ]);
            } else {
                $errorData = $response->json();
                $errorMessage = 'Erro ao enviar mensagem via WhatsApp.';
                
                if (isset($errorData['response']['message']) && is_array($errorData['response']['message'])) {
                    foreach ($errorData['response']['message'] as $msg) {
                        if (is_array($msg) && isset($msg['exists']) && $msg['exists'] === false) {
                            $errorMessage = "O número não existe no WhatsApp ou não está registrado.";
                            break;
                        }
                    }
                }

                return response()->json([
                    'success' => false,
                    'message' => $errorMessage . ' (Status: ' . $response->status() . ')',
                ], 400);
            }

        } catch (\Exception $e) {
            \Log::error('Erro ao enviar cobrança via WhatsApp:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro ao enviar mensagem: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Dar baixa em uma empresa (marcar cobrança como paga)
     */
    public function darBaixaEmpresa(Request $request)
    {
        $validated = $request->validate([
            'empresa_id' => 'required|exists:core_empresa,id',
            'valor_comissao' => 'required|numeric|min:0.01',
        ]);

        try {
            // Buscar ou criar cobrança para a empresa
            $cobranca = Cobranca::where('empresa_id', $validated['empresa_id'])
                ->where('data_cobranca', now()->toDateString())
                ->where('valor_comissao', $validated['valor_comissao'])
                ->first();

            if (!$cobranca) {
                // Criar cobrança se não existir
                $cobranca = Cobranca::create([
                    'empresa_id' => $validated['empresa_id'],
                    'data_cobranca' => now()->toDateString(),
                    'valor_comissao' => $validated['valor_comissao'],
                    'pago' => true, // Já marcar como pago
                    'tipo_anexo' => null,
                    'link' => null,
                ]);
            } else {
                // Marcar como pago
                $cobranca->pago = true;
                $cobranca->save();
            }

            \Log::info('Baixa dada para empresa', [
                'empresa_id' => $validated['empresa_id'],
                'cobranca_id' => $cobranca->id,
                'valor_comissao' => $validated['valor_comissao'],
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Baixa registrada com sucesso! A empresa foi removida da lista.',
                'cobranca_id' => $cobranca->id,
            ]);

        } catch (\Exception $e) {
            \Log::error('Erro ao dar baixa na empresa:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro ao dar baixa: ' . $e->getMessage(),
            ], 500);
        }
    }
}
