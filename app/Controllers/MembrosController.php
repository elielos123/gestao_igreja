<?php
namespace App\Controllers;

use App\Models\MembrosModel;
use App\Models\FinanceiroModel;

class MembrosController {
    
    public function index() {
        LoginController::checkAuth();
        
        // --- SYNC AUTOMÁTICO DE ENTRADAS PARA MEMBROS ---
        // Pega as pessoas que deram entradas mas não estão na tabela de membros e insere-as automaticamente.
        try {
            $db = (new \App\Config\Database())->getConnection();
            $sqlSync = "INSERT INTO membros (nome, congregacao, status) 
                        SELECT e1.nome, e1.congregacao, 'Ativo' 
                        FROM entradas e1
                        INNER JOIN (
                            SELECT nome, MAX(id) as max_id FROM entradas GROUP BY nome
                        ) e2 ON e1.id = e2.max_id
                        LEFT JOIN membros m ON e1.nome = m.nome 
                        WHERE m.id IS NULL";
            $db->exec($sqlSync);
        } catch (\Exception $e) {
            // Ignora silenciosamente para não quebrar a página
        }
        // ------------------------------------------------

        $membrosModel = new MembrosModel();
        $membros = $membrosModel->listarTodos();
        
        // Buscamos as listas de apoio diretamente das tabelas cadastradas
        $congregacoes = $membrosModel->listarLookup('congregacao');
        $funcoes = $membrosModel->listarLookup('funcao_eclesiastica');
        $cargos_cong = $membrosModel->listarLookup('cargo_congregacional');
        $conflitos = $membrosModel->listarConflitos();
        
        include_once dirname(__DIR__) . '/Views/membros.php';
    }

    public function salvar() {
        LoginController::checkAuth();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $membrosModel = new MembrosModel();
            $sucesso = $membrosModel->salvar($_POST);
            
            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                header('Content-Type: application/json');
                echo json_encode(['status' => $sucesso ? 'success' : 'error']);
                exit;
            }

            header('Location: index.php?url=membros&status=' . ($sucesso ? 'sucesso' : 'erro'));
            exit;
        }
    }

    public function excluir() {
        LoginController::checkAuth();
        
        if (isset($_GET['id'])) {
            $membrosModel = new MembrosModel();
            $sucesso = $membrosModel->excluir($_GET['id']);
            
            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                header('Content-Type: application/json');
                echo json_encode(['status' => $sucesso ? 'success' : 'error']);
                exit;
            }

            header('Location: index.php?url=membros&status=' . ($sucesso ? 'excluido' : 'erro'));
            exit;
        }
    }

    /**
     * GESTÃO DE CADASTROS AUXILIARES (AJUSTES)
     */
    public function indexAjustes() {
        LoginController::checkAuth();
        $membrosModel = new MembrosModel();
        
        $congregacoes = $membrosModel->listarLookup('congregacao');
        $funcoes = $membrosModel->listarLookup('funcao_eclesiastica');
        $cargos = $membrosModel->listarLookup('cargo_congregacional');
        
        include_once dirname(__DIR__) . '/Views/ajustes.php';
    }

    public function salvarAjuste() {
        LoginController::checkAuth();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $membrosModel = new MembrosModel();
            $tabela = $_POST['tabela'];
            $nome = $_POST['nome'];
            $id = $_POST['id'] ?? null;
            $res = $membrosModel->salvarLookup($tabela, $nome, $id);

            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                header('Content-Type: application/json');
                echo json_encode(['status' => 'success']);
                exit;
            }

            header('Location: index.php?url=membros&status=sucesso');
            exit;
        }
    }

    public function excluirAjuste() {
        LoginController::checkAuth();
        if (isset($_GET['id']) && isset($_GET['tabela'])) {
            $membrosModel = new MembrosModel();
            $membrosModel->excluirLookup($_GET['tabela'], $_GET['id']);
            header('Location: index.php?url=membros&status=excluido');
            exit;
        }
    }

    public function resolverConflito() {
        LoginController::checkAuth();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $membrosModel = new MembrosModel();
            $id = $_POST['conflito_id'];
            $tipo = $_POST['tipo'];
            $nome_original = $_POST['nome_original'];
            $congs_encontradas = $_POST['congs_encontradas'] ?? '';

            if ($tipo === 'duplicidade') {
                $congregacao = $_POST['congregacao_escolhida'];
                $membrosModel->salvar(['nome' => $nome_original, 'congregacao' => $congregacao, 'status' => 'Ativo']);
            } elseif ($tipo === 'composto') {
                $nomes_input = $_POST['nomes_separados'] ?? '';
                $nomes = explode(';', $nomes_input);
                $cong_padrao = isset($_POST['congregacao_escolhida']) ? $_POST['congregacao_escolhida'] : (explode(',', $congs_encontradas)[0] ?? 'Sede');
                
                foreach ($nomes as $nome) {
                    $nome = trim($nome);
                    if (!empty($nome)) {
                        $membrosModel->salvar(['nome' => $nome, 'congregacao' => trim($cong_padrao), 'status' => 'Ativo']);
                    }
                }
            }
            
            $membrosModel->marcarConflitoResolvido($id);
            header('Location: index.php?url=membros&status=conflito_resolvido');
            exit;
        }
    }

    public function atualizarCongregacaoPorNome() {
        LoginController::checkAuth();
        header('Content-Type: application/json');
        try {
            $dados = json_decode(file_get_contents('php://input'), true);
            $nome = $dados['nome'] ?? '';
            $novaCongregacao = $dados['congregacao'] ?? '';
            
            if (empty($nome) || empty($novaCongregacao)) {
                throw new \Exception('Dados incompletos.');
            }
            
            $db = (new \App\Config\Database())->getConnection();
            $sql = "UPDATE membros SET congregacao = :cong WHERE nome = :nome";
            $stmt = $db->prepare($sql);
            $sucesso = $stmt->execute([':cong' => $novaCongregacao, ':nome' => $nome]);
            
            echo json_encode(['status' => $sucesso ? 'success' : 'error']);
        } catch (\Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
        exit;
    }
}
