<?php

namespace App\Http\Controllers;

use App\Models\Acordo;
use App\Models\Divida;
use App\Models\Pagamento;
use App\Services\PicPayService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class PagamentoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        $query = Pagamento::with(['divida', 'acordo', 'cliente']);

        if ($user->isConsultor()) {
            $query->where('consultor_id', $user->id);
        } elseif ($user->isCredor()) {
            $query->whereHas('divida', function($q) use ($user) {
                $q->where('credor_id', $user->credor_id);
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('numero_transacao', 'like', "%{$search}%")
                  ->orWhereHas('cliente', function($q) use ($search) {
                      $q->where('nome', 'like', "%{$search}%");
                  });
            });
        }

        $pagamentos = $query->latest('data_pagamento')->paginate(20);

        return view('pagamentos.index', compact('pagamentos'));
    }

    public function create(Request $request)
    {
        $divida = null;
        $acordo = null;

        if ($request->filled('divida_id')) {
            $divida = Divida::findOrFail($request->divida_id);
        }

        if ($request->filled('acordo_id')) {
            $acordo = Acordo::findOrFail($request->acordo_id);
            $divida = $acordo->divida;
        }

        return view('pagamentos.create', compact('divida', 'acordo'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'divida_id' => 'nullable|exists:dividas,id',
            'acordo_id' => 'nullable|exists:acordos,id',
            'valor' => 'required|numeric|min:0.01',
            'data_pagamento' => 'required|date',
            'forma_pagamento' => 'required|in:dinheiro,pix,boleto,transferencia,cartao_credito,cartao_debito,cheque,picpay',
            'numero_parcela' => 'nullable|integer|min:1',
            'observacoes' => 'nullable|string',
            'usar_picpay' => 'nullable|boolean',
        ]);

        if (!$validated['divida_id'] && !$validated['acordo_id']) {
            return redirect()->back()
                ->with('error', 'É necessário informar uma dívida ou acordo.');
        }

        $divida = null;
        $acordo = null;

        if ($validated['acordo_id']) {
            $acordo = Acordo::findOrFail($validated['acordo_id']);
            $divida = $acordo->divida;
        } else {
            $divida = Divida::findOrFail($validated['divida_id']);
        }

        $user = Auth::user();

        // Verificar permissão
        if ($user->isConsultor() && $divida->consultor_id !== $user->id) {
            abort(403);
        }

        $pagamento = Pagamento::create([
            'divida_id' => $divida->id,
            'acordo_id' => $acordo?->id,
            'cliente_id' => $divida->cliente_id,
            'consultor_id' => $divida->consultor_id ?? $user->id,
            'numero_transacao' => 'PAG-' . strtoupper(Str::random(10)),
            'valor' => $validated['valor'],
            'data_pagamento' => $validated['data_pagamento'],
            'forma_pagamento' => $validated['forma_pagamento'],
            'numero_parcela' => $validated['numero_parcela'],
            'status' => 'pendente',
            'observacoes' => $validated['observacoes'],
        ]);

        // Se for pagamento de acordo, calcular data de vencimento da parcela
        if ($acordo && $validated['numero_parcela']) {
            $mesesAdicionar = $validated['numero_parcela'] - 1;
            $dataVencimento = $acordo->data_primeira_parcela->copy()->addMonths($mesesAdicionar);
            $dataVencimento->day = $acordo->dia_vencimento;
            $pagamento->data_vencimento_parcela = $dataVencimento;
            $pagamento->save();
        }

        // Se for pagamento via PicPay, criar pagamento na API
        if ($validated['forma_pagamento'] === 'picpay' || $request->boolean('usar_picpay')) {
            return $this->criarPagamentoPicPay($pagamento, $divida);
        }

        return redirect()->route('pagamentos.show', $pagamento)
            ->with('success', 'Pagamento registrado com sucesso!');
    }

    public function show(Pagamento $pagamento)
    {
        $pagamento->load(['divida.credor', 'acordo', 'cliente', 'consultor']);
        
        return view('pagamentos.show', compact('pagamento'));
    }

    public function confirmar(Pagamento $pagamento)
    {
        $user = Auth::user();
        
        if (!$user->canViewAllDividas() && $user->isConsultor() && $pagamento->consultor_id !== $user->id) {
            abort(403);
        }

        $pagamento->confirmar();

        return redirect()->route('pagamentos.show', $pagamento)
            ->with('success', 'Pagamento confirmado com sucesso!');
    }

    public function cancelar(Pagamento $pagamento)
    {
        if ($pagamento->status === 'confirmado') {
            return redirect()->back()
                ->with('error', 'Não é possível cancelar pagamento já confirmado.');
        }

        $pagamento->status = 'cancelado';
        $pagamento->save();

        return redirect()->route('pagamentos.show', $pagamento)
            ->with('success', 'Pagamento cancelado com sucesso!');
    }

    /**
     * Cria um pagamento via PicPay
     */
    public function criarPagamentoPicPay(Pagamento $pagamento, Divida $divida)
    {
        $picpayService = new PicPayService();
        $cliente = $pagamento->cliente;

        // Preparar dados do pagamento no formato da API de Link de Pagamento
        // Se valor for menor que R$ 5,00, usar apenas BRCODE (sem cartão)
        $paymentMethods = $pagamento->valor >= 5.00 
            ? ['BRCODE', 'CREDIT_CARD'] 
            : ['BRCODE'];
        
        $dadosPagamento = [
            'reference_id' => $pagamento->numero_transacao,
            'valor' => $pagamento->valor,
            'callback_url' => route('picpay.webhook'),
            'return_url' => route('pagamentos.show', $pagamento),
            'expires_at' => now()->addDays(30)->format('Y-m-d'),
            'charge_name' => 'Pagamento - ' . ($pagamento->divida->numero_documento ?? $pagamento->numero_transacao),
            'charge_description' => 'Pagamento de dívida via PicPay - ' . $pagamento->cliente->nome,
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
            // Usar dados já extraídos pelo serviço
            $responseData = $resultado['data'];
            $paymentUrl = $resultado['payment_url'];
            $qrcodeBase64 = $resultado['qrcode_base64'];
            
            // Extrair data de expiração da resposta ou usar padrão
            $expiresAt = null;
            $paymentLink = $responseData['payment_link'] ?? $responseData;
            
            if (isset($paymentLink['expired_at'])) {
                $expiresAt = \Carbon\Carbon::parse($paymentLink['expired_at']);
            } elseif (isset($responseData['expired_at'])) {
                $expiresAt = \Carbon\Carbon::parse($responseData['expired_at']);
            } else {
                $expiresAt = now()->addDays(30);
            }
            
            $pagamento->update([
                'picpay_reference_id' => $pagamento->numero_transacao,
                'picpay_payment_url' => $paymentUrl,
                'picpay_qrcode_base64' => $qrcodeBase64,
                'picpay_response' => $responseData,
                'picpay_expires_at' => $expiresAt,
                'forma_pagamento' => 'picpay',
            ]);

            return redirect()->route('pagamentos.picpay', $pagamento)
                ->with('success', 'Pagamento PicPay criado com sucesso!')
                ->with('show_modal', true);
        }

        return redirect()->route('pagamentos.show', $pagamento)
            ->with('error', 'Erro ao criar pagamento PicPay: ' . ($resultado['message'] ?? 'Erro desconhecido'));
    }

    /**
     * Exibe página de pagamento PicPay
     */
    public function picpay(Pagamento $pagamento)
    {
        $pagamento->load(['divida', 'acordo', 'cliente']);

        if (!$pagamento->isPicPay()) {
            return redirect()->route('pagamentos.show', $pagamento)
                ->with('error', 'Este pagamento não é via PicPay.');
        }

        return view('pagamentos.picpay', compact('pagamento'));
    }

    /**
     * Consulta status do pagamento PicPay
     */
    public function consultarPicPay(Pagamento $pagamento)
    {
        if (!$pagamento->isPicPay()) {
            return response()->json([
                'success' => false,
                'message' => 'Este pagamento não é via PicPay.',
            ], 400);
        }

        $picpayService = new PicPayService();
        $resultado = $picpayService->consultarPagamento($pagamento->picpay_reference_id);

        if ($resultado['success']) {
            $statusPicPay = $resultado['status'] ?? null;
            $statusInterno = $picpayService->mapearStatus($statusPicPay);

            // Atualizar status se mudou
            if ($statusInterno !== $pagamento->status) {
                $pagamento->status = $statusInterno;
                $pagamento->picpay_response = $resultado['data'];
                
                if ($statusInterno === 'confirmado') {
                    $pagamento->confirmar();
                } else {
                    $pagamento->save();
                }
            }

            return response()->json([
                'success' => true,
                'status' => $statusInterno,
                'status_picpay' => $statusPicPay,
                'data' => $resultado['data'],
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => $resultado['message'] ?? 'Erro ao consultar pagamento',
        ], 400);
    }

    /**
     * Cancela pagamento PicPay
     */
    public function cancelarPicPay(Pagamento $pagamento)
    {
        if (!$pagamento->isPicPay()) {
            return redirect()->back()
                ->with('error', 'Este pagamento não é via PicPay.');
        }

        if ($pagamento->status === 'confirmado') {
            return redirect()->back()
                ->with('error', 'Não é possível cancelar pagamento já confirmado.');
        }

        $picpayService = new PicPayService();
        $resultado = $picpayService->cancelarPagamento(
            $pagamento->picpay_reference_id,
            $pagamento->picpay_authorization_id
        );

        if ($resultado['success']) {
            $pagamento->status = 'cancelado';
            $pagamento->picpay_response = array_merge(
                $pagamento->picpay_response ?? [],
                ['cancellation' => $resultado['data']]
            );
            $pagamento->save();

            return redirect()->route('pagamentos.show', $pagamento)
                ->with('success', 'Pagamento PicPay cancelado com sucesso!');
        }

        return redirect()->back()
            ->with('error', 'Erro ao cancelar pagamento PicPay: ' . ($resultado['message'] ?? 'Erro desconhecido'));
    }

    /**
     * Webhook do PicPay para receber notificações
     */
    public function webhookPicPay(Request $request)
    {
        try {
            $dados = $request->all();
            
            Log::info('Webhook PicPay recebido', ['dados' => $dados]);

            $referenceId = $dados['referenceId'] ?? null;
            if (!$referenceId) {
                return response()->json(['error' => 'ReferenceId não encontrado'], 400);
            }

            // Buscar pagamento pelo reference_id
            $pagamento = Pagamento::where('picpay_reference_id', $referenceId)->first();

            if (!$pagamento) {
                Log::warning('Pagamento não encontrado para webhook PicPay', ['reference_id' => $referenceId]);
                return response()->json(['error' => 'Pagamento não encontrado'], 404);
            }

            $picpayService = new PicPayService();
            $resultado = $picpayService->processarNotificacao($dados);

            if ($resultado['success']) {
                $statusPicPay = $resultado['status'] ?? null;
                $statusInterno = $picpayService->mapearStatus($statusPicPay);

                // Atualizar pagamento
                $pagamento->picpay_authorization_id = $resultado['authorization_id'] ?? $pagamento->picpay_authorization_id;
                $pagamento->picpay_response = $resultado['data'] ?? $pagamento->picpay_response;
                $pagamento->status = $statusInterno;

                if ($statusInterno === 'confirmado') {
                    $pagamento->confirmar();
                } else {
                    $pagamento->save();
                }

                Log::info('Webhook PicPay processado com sucesso', [
                    'pagamento_id' => $pagamento->id,
                    'status' => $statusInterno,
                ]);

                return response()->json(['success' => true], 200);
            }

            Log::error('Erro ao processar webhook PicPay', [
                'resultado' => $resultado,
            ]);

            return response()->json(['error' => 'Erro ao processar notificação'], 400);
        } catch (\Exception $e) {
            Log::error('Exceção ao processar webhook PicPay', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json(['error' => 'Erro interno'], 500);
        }
    }
}
