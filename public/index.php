<?php
/**
 * Front Controller - Roteador Modernizado
 * Localização: gestao_igreja/public/index.php
 */

header('Content-Type: text/html; charset=utf-8');

require_once dirname(__DIR__) . '/vendor/autoload.php';

use Dotenv\Dotenv;
use App\Controllers\FinanceiroController;
use App\Controllers\LoginController;
use App\Controllers\MembrosController;

// Carrega as variáveis de ambiente
$dotenv = Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

$ds = DIRECTORY_SEPARATOR;
$baseAppPath = dirname(__DIR__) . $ds . 'app';

// Captura a URL
$route = isset($_GET['url']) ? $_GET['url'] : 'dashboard';
$route = str_replace(['.', '/', '\\'], '', $route);

$viewPath = "";

// --- ROTAS PÚBLICAS ---
if ($route === 'login') {
    (new LoginController())->index();
    exit;
}

if ($route === 'autenticar') {
    (new LoginController())->autenticar();
    exit;
}

if ($route === 'verificar2fa') {
    (new LoginController())->verificar2fa();
    exit;
}

if ($route === 'logout') {
    (new LoginController())->logout();
    exit;
}

if ($route === 'validar_senha') {
    (new LoginController())->validarSenha();
    exit;
}

// --- PROTEÇÃO DE ROTAS (Precisa estar logado para o resto) ---
LoginController::checkAuth();

// Instancia controladores sob demanda
switch ($route) {
    // --- PÁGINAS GERAIS ---
    case 'dashboard':
        $viewPath = $baseAppPath . $ds . 'Views' . $ds . 'dashboard.php';
        break;

    // --- SEGURANÇA / 2FA ---
    case 'setup_2fa':
        (new LoginController())->setup2faView();
        exit;
    case 'get2fa_setup':
        (new LoginController())->get2faSetup();
        exit;
    case 'confirmar2fa':
        (new LoginController())->confirmar2fa();
        exit;
    case 'desativar2fa':
        (new LoginController())->desativar2fa();
        exit;

    case 'membros':
        (new MembrosController())->index();
        break;

    case 'membros_salvar':
        (new MembrosController())->salvar();
        break;

    case 'membros_excluir':
        (new MembrosController())->excluir();
        break;

    case 'membros_resolver_conflito':
        (new MembrosController())->resolverConflito();
        break;

    case 'financeiro':
        $viewPath = $baseAppPath . $ds . 'Views' . $ds . 'financeiro' . $ds . 'DashboardFinanceiro.php';
        break;
        
    // --- FINANCEIRO: ENTRADAS ---
    case 'financeiro_entradas':
        (new FinanceiroController())->indexEntradas(); 
        break;

    case 'financeiro_autocomplete':
        (new FinanceiroController())->autocomplete();
        break;

    case 'financeiro_salvar_entrada':
        (new FinanceiroController())->salvarEntrada();
        break;

    case 'financeiro_excluir_entrada':
        (new FinanceiroController())->excluirEntrada();
        break;

    // --- FINANCEIRO: SAÍDAS ---
    case 'financeiro_saidas':
        (new FinanceiroController())->indexSaidas(); 
        break;

    case 'financeiro_salvar_saida':
        (new FinanceiroController())->salvarSaida();
        break;

    case 'financeiro_excluir_saida':
        (new FinanceiroController())->excluirSaida();
        break;

    // --- FINANCEIRO: RELATÓRIOS, EDIÇÃO E CADASTROS ---
    case 'financeiro_relatorios':
        (new FinanceiroController())->indexRelatorios();
        break;

    case 'financeiro_incongruencias':
        (new FinanceiroController())->indexIncongruencias();
        break;

    case 'financeiro_aceitar_incongruencia':
        (new FinanceiroController())->aceitarIncongruencia();
        break;

    case 'financeiro_cadastros':
        (new FinanceiroController())->indexCadastros();
        break;

    case 'financeiro_bi':
        (new FinanceiroController())->indexBI();
        break;

    case 'api_bi':
        (new FinanceiroController())->dadosBI();
        break;

    case 'api_rel_simplificado':
        (new FinanceiroController())->relatorioSimplificado();
        break;

    case 'api_relatorios':
        (new FinanceiroController())->gerarRelatorio();
        break;

    case 'financeiro_buscar_edicao':
        (new FinanceiroController())->buscarDadosEdicao();
        break;

    case 'financeiro_salvar_edicao':
        (new FinanceiroController())->salvarEdicao();
        break;

    case 'financeiro_lista_congregacoes':
        (new FinanceiroController())->listarCongregacoes(); 
        break;

    // --- SISTEMA ---
    case 'ajustes':
        (new MembrosController())->indexAjustes();
        break;

    case 'ajustes_salvar':
        (new MembrosController())->salvarAjuste();
        break;

    case 'ajustes_excluir':
        (new MembrosController())->excluirAjuste();
        break;

    case 'usuarios':
        (new \App\Controllers\UsuarioController())->index();
        break;

    case 'usuarios_papeis':
        (new \App\Controllers\UsuarioController())->papeis();
        break;

    case 'usuarios_criar':
        (new UsuarioController())->criarUsuario();
        break;

    case 'usuarios_salvar_papeis':
        (new \App\Controllers\UsuarioController())->salvarUsuarioPapeis();
        break;

    case 'usuarios_salvar_papel_permissoes':
        (new \App\Controllers\UsuarioController())->salvarPapelPermissoes();
        break;

    case 'usuarios_criar_papel':
        (new \App\Controllers\UsuarioController())->criarPapel();
        break;

    case 'usuarios_atualizar_papel':
        (new \App\Controllers\UsuarioController())->atualizarPapel();
        break;

    case 'usuarios_excluir_papel':
        (new \App\Controllers\UsuarioController())->excluirPapel();
        break;

    case 'alterar_senha_view':
        (new LoginController())->viewAlterarSenha();
        break;

    case 'alterar_senha_primeiro_acesso':
        (new LoginController())->alterarSenhaPrimeiroAcesso();
        break;

    case 'logout':
        session_start();
        session_destroy();
        header("Location: index.php?url=login");
        exit;
        break;

    default:
        $viewPath = $baseAppPath . $ds . 'Views' . $ds . 'dashboard.php';
        break;
}

// Renderização final para rotas que definem $viewPath
if ($viewPath && file_exists($viewPath)) {
    include_once($viewPath);
} elseif ($viewPath) {
    echo "Erro 404: Arquivo não encontrado: $viewPath";
}