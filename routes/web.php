<?php

use App\Http\Controllers\AcordoController;
use App\Http\Controllers\AgendamentoController;
use App\Http\Controllers\CarteiraController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\DevedorController;
use App\Http\Controllers\DividaController;
use App\Http\Controllers\EmpresaController;
use App\Http\Controllers\FollowupController;
use App\Http\Controllers\ParcelamentoController;
use App\Http\Controllers\PagamentoController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RelatorioController;
use App\Http\Controllers\TituloController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// Rotas de autenticação
require __DIR__.'/auth.php';

// Rotas autenticadas
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Clientes - rotas específicas primeiro
    Route::middleware('role:admin,gestor,consultor')->group(function () {
        Route::get('/clientes/create', [ClienteController::class, 'create'])->name('clientes.create');
        Route::post('/clientes', [ClienteController::class, 'store'])->name('clientes.store');
        Route::get('/clientes/{cliente}/edit', [ClienteController::class, 'edit'])->name('clientes.edit');
        Route::put('/clientes/{cliente}', [ClienteController::class, 'update'])->name('clientes.update');
        Route::patch('/clientes/{cliente}', [ClienteController::class, 'update']);
        Route::delete('/clientes/{cliente}', [ClienteController::class, 'destroy'])->name('clientes.destroy');
    });
    Route::get('/clientes', [ClienteController::class, 'index'])->name('clientes.index');
    Route::get('/clientes/{cliente}', [ClienteController::class, 'show'])->name('clientes.show');

    // Dívidas - rotas específicas primeiro
    Route::middleware('role:admin,gestor,consultor')->group(function () {
        Route::get('/dividas/create', [DividaController::class, 'create'])->name('dividas.create');
        Route::post('/dividas', [DividaController::class, 'store'])->name('dividas.store');
        Route::get('/dividas/{divida}/edit', [DividaController::class, 'edit'])->name('dividas.edit');
        Route::put('/dividas/{divida}', [DividaController::class, 'update'])->name('dividas.update');
        Route::patch('/dividas/{divida}', [DividaController::class, 'update']);
        Route::delete('/dividas/{divida}', [DividaController::class, 'destroy'])->name('dividas.destroy');
    });
    Route::get('/dividas', [DividaController::class, 'index'])->name('dividas.index');
    Route::get('/dividas/{divida}', [DividaController::class, 'show'])->name('dividas.show');
    Route::post('/dividas/{divida}/atualizar-valor', [DividaController::class, 'atualizarValor'])->name('dividas.atualizar-valor');

    // Follow-ups
    Route::resource('followups', FollowupController::class);
    Route::post('/adicionar-follow-up/{devedor}', [FollowupController::class, 'adicionarFollowUp'])->name('followups.adicionar');
    Route::get('/listar-follow-ups/{devedor}', [FollowupController::class, 'listarFollowUps'])->name('followups.listar');

    // Carteiras - rotas específicas primeiro
    Route::get('/carteiras', [CarteiraController::class, 'index'])->name('carteiras.index');
    Route::get('/carteiras/{carteira}', [CarteiraController::class, 'show'])->name('carteiras.show');
    Route::middleware('role:admin,gestor')->group(function () {
        Route::get('/carteiras/create', [CarteiraController::class, 'create'])->name('carteiras.create');
        Route::post('/carteiras', [CarteiraController::class, 'store'])->name('carteiras.store');
        Route::get('/carteiras/{carteira}/edit', [CarteiraController::class, 'edit'])->name('carteiras.edit');
        Route::put('/carteiras/{carteira}', [CarteiraController::class, 'update'])->name('carteiras.update');
        Route::patch('/carteiras/{carteira}', [CarteiraController::class, 'update']);
        Route::delete('/carteiras/{carteira}', [CarteiraController::class, 'destroy'])->name('carteiras.destroy');
        Route::post('/carteiras/{carteira}/sincronizar', [CarteiraController::class, 'sincronizar'])->name('carteiras.sincronizar');
    });

    // Acordos
    Route::resource('acordos', AcordoController::class);
    Route::post('/acordos/{acordo}/aprovar', [AcordoController::class, 'aprovar'])->name('acordos.aprovar');
    Route::post('/acordos/{acordo}/quebrar', [AcordoController::class, 'quebrar'])->name('acordos.quebrar');
    Route::get('/acordos/{acordo}/gerar-contrato', [AcordoController::class, 'gerarContrato'])->name('acordos.gerar-contrato');

    // Pagamentos
    Route::resource('pagamentos', PagamentoController::class);
    Route::post('/pagamentos/{pagamento}/confirmar', [PagamentoController::class, 'confirmar'])->name('pagamentos.confirmar');
    Route::post('/pagamentos/{pagamento}/cancelar', [PagamentoController::class, 'cancelar'])->name('pagamentos.cancelar');

    // Relatórios
    Route::get('/relatorios', [RelatorioController::class, 'index'])->name('relatorios.index');
    Route::get('/relatorios/ranking-operadores', [RelatorioController::class, 'rankingOperadores'])->name('relatorios.ranking-operadores');
    Route::get('/relatorios/honorarios', [RelatorioController::class, 'honorarios'])->name('relatorios.honorarios');

    // Tabelas de Remuneração
    Route::resource('tabelas', \App\Http\Controllers\TabelaRemuneracaoController::class);
    Route::post('/tabelas/{tabela}/adicionar-item', [\App\Http\Controllers\TabelaRemuneracaoController::class, 'adicionarItem'])->name('tabelas.adicionar-item');
    Route::post('/tabelas/{tabela}/itens/{item}/editar', [\App\Http\Controllers\TabelaRemuneracaoController::class, 'editarItem'])->name('tabelas.editar-item');
    Route::delete('/tabelas/{tabela}/itens/{item}', [\App\Http\Controllers\TabelaRemuneracaoController::class, 'excluirItem'])->name('tabelas.excluir-item');

    // Devedores
    Route::resource('devedores', DevedorController::class)->parameters([
        'devedores' => 'devedor'
    ]);
    Route::post('/devedores/excluir-em-massa', [DevedorController::class, 'excluirEmMassa'])->name('devedores.excluir-em-massa');
    Route::get('/devedores/{devedor}/titulos', [DevedorController::class, 'titulos'])->name('devedores.titulos');
    Route::get('/devedores/{devedor}/adicionar-titulo', [DevedorController::class, 'adicionarTitulo'])->name('devedores.adicionar-titulo');
    Route::get('/devedores/{devedor}/editar-telefones', [DevedorController::class, 'editarTelefones'])->name('devedores.editar-telefones');
    Route::post('/devedores/{devedor}/atualizar-telefones', [DevedorController::class, 'atualizarTelefones'])->name('devedores.atualizar-telefones');
    Route::post('/devedores/{devedor}/alterar-operador', [DevedorController::class, 'alterarOperador'])->name('devedores.alterar-operador');
    Route::post('/devedores/{devedor}/alterar-consultor', [DevedorController::class, 'alterarConsultor'])->name('devedores.alterar-consultor');

    // Empresas
    Route::resource('empresas', EmpresaController::class);
    Route::post('/empresas/{empresa}/alterar-status', [EmpresaController::class, 'alterarStatus'])->name('empresas.alterar-status');
    Route::post('/empresas/consultar-cnpj', [EmpresaController::class, 'consultarCnpj'])->name('empresas.consultar-cnpj');

    // Títulos
    Route::resource('titulos', TituloController::class);
    Route::post('/titulos/{titulo}/finalizar', [TituloController::class, 'finalizar'])->name('titulos.finalizar');
    Route::post('/titulos/{titulo}/baixar', [TituloController::class, 'baixar'])->name('titulos.baixar');
    Route::post('/titulos/{titulo}/quitar-parcela', [TituloController::class, 'quitarParcela'])->name('titulos.quitar-parcela');
    Route::get('/titulos/{titulo}/gerar-pdf', [TituloController::class, 'gerarPdf'])->name('titulos.gerar-pdf');
    Route::get('/titulos/{titulo}/gerar-recibo', [TituloController::class, 'gerarRecibo'])->name('titulos.gerar-recibo');
    Route::post('/titulos/{titulo}/anexar-comprovante', [TituloController::class, 'anexarComprovante'])->name('titulos.anexar-comprovante');
    Route::get('/titulos/{titulo}/baixar-comprovante', [TituloController::class, 'baixarComprovante'])->name('titulos.baixar-comprovante');
    Route::post('/titulos/{titulo}/anexar-contrato', [TituloController::class, 'anexarContrato'])->name('titulos.anexar-contrato');
    Route::get('/titulos/{titulo}/baixar-contrato', [TituloController::class, 'baixarContrato'])->name('titulos.baixar-contrato');
    Route::post('/titulos/{titulo}/alterar-operador', [TituloController::class, 'alterarOperador'])->name('titulos.alterar-operador');
    Route::get('/titulos/quitados', [TituloController::class, 'quitados'])->name('titulos.quitados');

    // Parcelamentos
    Route::resource('parcelamentos', ParcelamentoController::class)->only(['index', 'show']);
    Route::post('/parcelamentos/{parcelamento}/pagar', [ParcelamentoController::class, 'pagar'])->name('parcelamentos.pagar');
    Route::post('/parcelamentos/{parcelamento}/anexar-comprovante', [ParcelamentoController::class, 'anexarComprovante'])->name('parcelamentos.anexar-comprovante');
    Route::get('/parcelamentos/{parcelamento}/baixar-comprovante', [ParcelamentoController::class, 'baixarComprovante'])->name('parcelamentos.baixar-comprovante');

    // Agendamentos
    Route::resource('agendamentos', AgendamentoController::class);
    Route::post('/agendamentos/{agendamento}/finalizar', [AgendamentoController::class, 'finalizar'])->name('agendamentos.finalizar');
    Route::get('/agendamentos/buscar-devedores', [AgendamentoController::class, 'buscarDevedores'])->name('agendamentos.buscar-devedores');

    // Boletos
    Route::get('/boletos', [\App\Http\Controllers\BoletoController::class, 'index'])->name('boletos.index');
    Route::get('/boletos/emitir', [\App\Http\Controllers\BoletoController::class, 'emitir'])->name('boletos.emitir');
    Route::post('/boletos/processar-emissao', [\App\Http\Controllers\BoletoController::class, 'processarEmissao'])->name('boletos.processar-emissao');
    Route::get('/boletos/{boleto}', [\App\Http\Controllers\BoletoController::class, 'show'])->name('boletos.show');
    Route::get('/boletos/{boleto}/baixar-pdf', [\App\Http\Controllers\BoletoController::class, 'baixarPdf'])->name('boletos.baixar-pdf');
    Route::get('/boletos/{boleto}/qr-code-pix', [\App\Http\Controllers\BoletoController::class, 'qrCodePix'])->name('boletos.qr-code');

    // Cobranças
    Route::resource('cobrancas', \App\Http\Controllers\CobrancaController::class);
    Route::post('/cobrancas/{cobranca}/atualizar-pago', [\App\Http\Controllers\CobrancaController::class, 'atualizarPago'])->name('cobrancas.atualizar-pago');
    Route::get('/cobrancas/{cobranca}/baixar-documento', [\App\Http\Controllers\CobrancaController::class, 'baixarDocumento'])->name('cobrancas.baixar-documento');

    // Logs de Acesso
    Route::get('/logs', [\App\Http\Controllers\UserAccessLogController::class, 'index'])->name('logs.index');
    Route::get('/logs/exportar-csv', [\App\Http\Controllers\UserAccessLogController::class, 'exportarCsv'])->name('logs.exportar-csv');
    Route::get('/logs/exportar-excel', [\App\Http\Controllers\UserAccessLogController::class, 'exportarExcel'])->name('logs.exportar-excel');
});
