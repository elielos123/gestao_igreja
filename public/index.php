<?php
/**
 * Front Controller - Roteador Modernizado
 * Localização: gestao_igreja/public/index.php
 */

header('Content-Type: text/html; charset=utf-8');

require_once dirname(__DIR__) . '/vendor/autoload.php';

// --- SUPORTE A HTTPS VIA PROXY (Cloudflare, etc) ---
// Resolve o erro ERR_TOO_MANY_REDIRECTS em ambientes com SSL flexível
if ((isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') || 
    (isset($_SERVER['HTTP_CF_VISITOR']) && strpos($_SERVER['HTTP_CF_VISITOR'], 'https') !== false) ||
    (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443)) {
    $_SERVER['HTTPS'] = 'on';
}

use Dotenv\Dotenv;
use App\Controllers\FinanceiroController;
use App\Controllers\LoginController;
use App\Controllers\MembrosController;
use App\Controllers\UsuarioController;

// Carrega as variáveis de ambiente
$dotenv = Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

$ds = DIRECTORY_SEPARATOR;
$baseAppPath = dirname(__DIR__) . $ds . 'app';

// --- CAPTURA DE ROTA INTELIGENTE (Compatível com Apache e Nginx) ---
$requestUri = $_SERVER['REQUEST_URI'];
$scriptName = $_SERVER['SCRIPT_NAME'];

// 1. Tenta capturar do parâmetro GET (Estilo antigo/Laragon)
$route = $_GET['url'] ?? '';

// 2. Se estiver vazio, tenta capturar da URI (Estilo moderno/Nginx)
if (empty($route)) {
    // Pega apenas o caminho da URL (sem query string)
    $path = parse_url($requestUri, PHP_URL_PATH);
    
    // Remove o scriptName (ex: /public/index.php) ou o basePath dele (/public/)
    $basePath = dirname($scriptName);
    
    // Remove as partes fixas da URL para sobrar apenas a rota
    $route = str_replace([$scriptName, $basePath], '', $path);
}

// Limpa barras e higieniza
$route = trim($route, '/');

// Se ainda estiver vazia, vai para o dashboard
if (empty($route)) {
    $route = 'dashboard';
}

// Higienização de segurança
$route = str_replace(['.', '/', '\\'], '', $route);

$viewPath = "";

// --- ROTAS PÚBLICAS (Ignoram checkAuth) ---
$publicRoutes = ['login', 'autenticar', 'verificar2fa', 'logout', 'validar_senha', 'info', 'diagnostico', 'redir_debug', 'test_headers', 'alterar_senha_view', 'alterar_senha_primeiro_acesso'];

if (in_array($route, $publicRoutes)) {
    switch ($route) {
        case 'login': (new LoginController())->index(); break;
        case 'autenticar': (new LoginController())->autenticar(); break;
        case 'verificar2fa': (new LoginController())->verificar2fa(); break;
        case 'logout': (new LoginController())->logout(); break;
        case 'validar_senha': (new LoginController())->validarSenha(); break;
        case 'info': phpinfo(); break;
        case 'diagnostico': require 'diagnostico.php'; break;
        case 'redir_debug': require 'redir_debug.php'; break;
        case 'test_headers': require 'test_headers.php'; break;
    }
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

    case 'membros_atualizar_congregacao':
        (new MembrosController())->atualizarCongregacaoPorNome();
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
        (new UsuarioController())->index();
        break;

    case 'usuarios_papeis':
        (new UsuarioController())->papeis();
        break;

    case 'usuarios_criar':
        (new UsuarioController())->criarUsuario();
        break;

    case 'usuarios_salvar_papeis':
        (new UsuarioController())->salvarUsuarioPapeis();
        break;

    case 'usuarios_salvar_papel_permissoes':
        (new UsuarioController())->salvarPapelPermissoes();
        break;

    case 'usuarios_criar_papel':
        (new UsuarioController())->criarPapel();
        break;

    case 'usuarios_atualizar_papel':
        (new UsuarioController())->atualizarPapel();
        break;

    case 'usuarios_excluir_papel':
        (new UsuarioController())->excluirPapel();
        break;

    case 'alterar_senha_view':
        (new LoginController())->viewAlterarSenha();
        break;

    case 'alterar_senha_primeiro_acesso':
        (new LoginController())->alterarSenhaPrimeiroAcesso();
        break;

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