<?php
/**
 * Front Controller - Roteador Robusto
 * Localização: gestao_igreja/public/index.php
 */

$ds = DIRECTORY_SEPARATOR;
$baseAppPath = dirname(__DIR__) . $ds . 'app';

// Captura a URL
$route = isset($_GET['url']) ? $_GET['url'] : 'dashboard';
$route = str_replace(['.', '/', '\\'], '', $route);

$viewPath = "";

switch ($route) {
    // --- PÁGINAS GERAIS ---
    case 'dashboard':
        $viewPath = $baseAppPath . $ds . 'Views' . $ds . 'dashboard.php';
        break;

    case 'membros':
        $viewPath = $baseAppPath . $ds . 'Views' . $ds . 'membros.php';
        break;

    case 'financeiro':
        $viewPath = $baseAppPath . $ds . 'Views' . $ds . 'financeiro' . $ds . 'DashboardFinanceiro.php';
        break;
        
    // --- FINANCEIRO: ENTRADAS ---
    case 'financeiro_entradas':
        require_once $baseAppPath . $ds . 'Controllers' . $ds . 'FinanceiroController.php';
        $controller = new FinanceiroController();
        $controller->indexEntradas(); 
        $viewPath = null; 
        break;

    case 'financeiro_autocomplete':
        require_once $baseAppPath . $ds . 'Controllers' . $ds . 'FinanceiroController.php';
        $controller = new FinanceiroController();
        $controller->autocomplete();
        $viewPath = null;
        break;

    case 'financeiro_salvar_entrada':
        require_once $baseAppPath . $ds . 'Controllers' . $ds . 'FinanceiroController.php';
        $controller = new FinanceiroController();
        $controller->salvarEntrada();
        $viewPath = null; 
        break;

    case 'financeiro_excluir_entrada':
        require_once $baseAppPath . $ds . 'Controllers' . $ds . 'FinanceiroController.php';
        $controller = new FinanceiroController();
        $controller->excluirEntrada();
        $viewPath = null;
        break;

    // --- FINANCEIRO: SAÍDAS ---
    case 'financeiro_saidas':
        require_once $baseAppPath . $ds . 'Controllers' . $ds . 'FinanceiroController.php';
        $controller = new FinanceiroController();
        $controller->indexSaidas(); 
        $viewPath = null;
        break;

    case 'financeiro_salvar_saida':
        require_once $baseAppPath . $ds . 'Controllers' . $ds . 'FinanceiroController.php';
        $controller = new FinanceiroController();
        $controller->salvarSaida();
        $viewPath = null;
        break;

    case 'financeiro_excluir_saida':
        require_once $baseAppPath . $ds . 'Controllers' . $ds . 'FinanceiroController.php';
        $controller = new FinanceiroController();
        $controller->excluirSaida();
        $viewPath = null;
        break;

    // --- FINANCEIRO: RELATÓRIOS, EDIÇÃO E CADASTROS ---
    case 'financeiro_relatorios':
        require_once $baseAppPath . $ds . 'Controllers' . $ds . 'FinanceiroController.php';
        $controller = new FinanceiroController();
        $controller->indexRelatorios();
        $viewPath = null;
        break;

    case 'financeiro_cadastros':
        require_once $baseAppPath . $ds . 'Controllers' . $ds . 'FinanceiroController.php';
        $controller = new FinanceiroController();
        $controller->indexCadastros();
        $viewPath = null;
        break;

    case 'api_relatorios':
        require_once $baseAppPath . $ds . 'Controllers' . $ds . 'FinanceiroController.php';
        $controller = new FinanceiroController();
        $controller->gerarRelatorio();
        $viewPath = null;
        break;

    case 'financeiro_buscar_edicao':
        require_once $baseAppPath . $ds . 'Controllers' . $ds . 'FinanceiroController.php';
        $controller = new FinanceiroController();
        $controller->buscarDadosEdicao();
        $viewPath = null;
        break;

    case 'financeiro_salvar_edicao':
        require_once $baseAppPath . $ds . 'Controllers' . $ds . 'FinanceiroController.php';
        $controller = new FinanceiroController();
        $controller->salvarEdicao();
        $viewPath = null;
        break;

    case 'financeiro_lista_congregacoes':
        require_once $baseAppPath . $ds . 'Controllers' . $ds . 'FinanceiroController.php';
        $controller = new FinanceiroController();
        $controller->listarCongregacoes(); 
        $viewPath = null; 
        break;

    // --- SISTEMA ---
    case 'ajustes':
        $viewPath = $baseAppPath . $ds . 'Views' . $ds . 'ajustes.php';
        break;

    case 'logout':
        echo "Saindo...";
        exit;
        break;

    default:
        $viewPath = $baseAppPath . $ds . 'Views' . $ds . 'dashboard.php';
        break;
}

// Renderização final
if ($viewPath && file_exists($viewPath)) {
    include_once($viewPath);
} elseif ($viewPath) {
    echo "Erro 404: Arquivo não encontrado: $viewPath";
}