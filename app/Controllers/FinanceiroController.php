<?php
namespace App\Controllers;

use App\Models\FinanceiroModel;
use Exception;

class FinanceiroController {
    
    private $model;

    public function __construct() {
        // Define o fuso horário para evitar erros de data retroativa
        date_default_timezone_set('America/Sao_Paulo');
        $this->model = new FinanceiroModel();
    }

    public function indexEntradas() { require dirname(__DIR__) . '/Views/financeiro/entradas.php'; }
    public function indexSaidas() { require dirname(__DIR__) . '/Views/financeiro/saidas.php'; }
    public function indexRelatorios() { require dirname(__DIR__) . '/Views/financeiro/relatorios.php'; }
    public function indexIncongruencias() { require dirname(__DIR__) . '/Views/financeiro/incongruencias.php'; }
    public function indexCadastros() { require dirname(__DIR__) . '/Views/financeiro/cadastros.php'; }
    public function indexBI() { require dirname(__DIR__) . '/Views/financeiro/bi.php'; }

    public function dadosBI() {
        header('Content-Type: application/json');
        try {
            $tipo  = $_GET['tipo'] ?? 'mensais';
            $ini   = $_GET['inicio'] ?? date('Y-01-01');
            $fim   = $_GET['fim']    ?? date('Y-12-31');
            $ini2  = $_GET['inicio2'] ?? '';
            $fim2  = $_GET['fim2']    ?? '';

            switch ($tipo) {
                case 'mensais':
                    $dados = $this->model->biEntradasMensais($ini, $fim); break;
                case 'top_congregacoes':
                    $dados = $this->model->biTopCongregacoes($ini, $fim); break;
                case 'dizimistas_fieis':
                    $dados = $this->model->biDizimistasFields(); break;
                case 'dizimistas_fieis_cong':
                    $dados = $this->model->biDizimistasFieisPorCongregacao(); break;
                case 'top_dizimistas':
                    $dados = $this->model->biTopDizimistasPorCongregacao($ini, $fim); break;
                case 'semanais':
                    $dados = $this->model->biEntradasSemanais($ini, $fim); break;
                case 'comparacao':
                    $dados = $this->model->biComparacaoPeriodos($ini, $fim, $ini2, $fim2); break;
                default:
                    $dados = [];
            }
            echo json_encode(['status' => 'success', 'dados' => $dados]);
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function autocomplete() {
        header('Content-Type: application/json');
        try {
            $termo = $_GET['termo'] ?? '';
            $campo = $_GET['campo'] ?? 'nome';
            
            $sugestoes = $this->model->buscarSugestoes($termo, $campo);
            
            if ($campo === 'nome' || $campo === 'recebedor' || $campo === 'dados_cadastrais' || $campo === 'descricao') {
                // Retorna o objeto completo (label, extra, etc)
                echo json_encode($sugestoes);
            } else {
                // Retorna apenas a lista de strings para compatibilidade
                echo json_encode(array_column($sugestoes, 'label'));
            }
        } catch (Exception $e) { echo json_encode([]); }
    }

    /**
     * SALVAR SAÍDA - MAPEAMENTO COM AS COLUNAS ENVIADAS
     */
    public function salvarSaida() {
        header('Content-Type: application/json');
        try {
            $inputJSON = file_get_contents('php://input');
            $dados = json_decode($inputJSON, true);
            
            if (!$dados) throw new Exception("Dados inválidos no envio.");

            $dataArr = explode('/', $dados['data']);
            $sqlData = (count($dataArr) == 3) ? $dataArr[2] . '-' . $dataArr[1] . '-' . $dataArr[0] : date('Y-m-d');

            $processados = [
                'recebedor'        => trim($dados['recebedor']),
                'data'             => $sqlData,
                'valor'            => $this->limparValor($dados['valor']),
                'descricao'        => trim($dados['descricao']),
                'dados_cadastrais' => trim($dados['dados_cadastrais']),
                'tipo_saida'       => trim($dados['tipo_saida']),
                'parcela'          => trim($dados['parcela'])
            ];

            if (!empty($dados['id'])) {
                $this->model->atualizarSaida(intval($dados['id']), $processados);
                $id = $dados['id'];
            } else {
                $id = $this->model->registrarSaida($processados);
            }
            echo json_encode(['status' => 'success', 'id' => $id]);
        } catch (Exception $e) { 
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]); 
        }
    }

    public function salvarEntrada() {
        header('Content-Type: application/json');
        try {
            $dados = json_decode(file_get_contents('php://input'), true);
            $dataArr = explode('/', $dados['data']);
            $sqlData = (count($dataArr) == 3) ? $dataArr[2] . '-' . $dataArr[1] . '-' . $dataArr[0] : date('Y-m-d');
            $proc = ['nome'=>trim($dados['nome']), 'data'=>$sqlData, 'valor'=>$this->limparValor($dados['valor']), 'congregacao'=>trim($dados['congregacao']), 'tipo'=>trim($dados['tipo'])];
            if (!empty($dados['id'])) { $this->model->atualizarEntrada(intval($dados['id']), $proc); $id = $dados['id']; }
            else { $id = $this->model->registrarEntrada($proc); }
            echo json_encode(['status' => 'success', 'id' => $id]);
        } catch (Exception $e) { echo json_encode(['status' => 'error']); }
    }

    public function salvarEdicao() {
        header('Content-Type: application/json');
        try {
            $dados = json_decode(file_get_contents('php://input'), true);
            if (!$dados) throw new Exception("Dados vazios");
            $id = intval($dados['id']);
            $sqlData = $dados['data']; // O HTML5 envia YYYY-MM-DD
            $valor = $this->limparValor($dados['valor']);

            if ($dados['origem'] === 'Entrada') {
                $this->model->atualizarEntrada($id, [
                    'nome' => $dados['nome'], 'data' => $sqlData, 'valor' => $valor, 
                    'congregacao' => $dados['congregacao'], 'tipo' => $dados['tipo']
                ]);
            } else {
                // CORREÇÃO: Mapeia 'nome' vindo do modal para 'recebedor' exigido pelo Model
                $this->model->atualizarSaida($id, [
                    'recebedor' => $dados['recebedor'] ?? $dados['nome'], 
                    'data' => $sqlData, 'valor' => $valor, 
                    'descricao' => $dados['descricao'], 'tipo_saida' => $dados['tipo_saida'], 
                    'parcela' => $dados['parcela'] ?? '', 'dados_cadastrais' => $dados['dados_cadastrais'] ?? ''
                ]);
            }
            echo json_encode(['status' => 'success']);
        } catch (Exception $e) { echo json_encode(['status' => 'error', 'message' => $e->getMessage()]); }
    }

    public function aceitarIncongruencia() {
        header('Content-Type: application/json');
        try {
            $dados = json_decode(file_get_contents('php://input'), true);
            if (!$dados) throw new Exception("Dados vazios");
            $res = $this->model->aceitarIncongruencia($dados['id'], $dados['origem']);
            echo json_encode(['status' => $res ? 'success' : 'error']);
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function excluirEntrada() {
        header('Content-Type: application/json');
        try {
            $dados = json_decode(file_get_contents('php://input'), true);
            $this->model->excluirEntrada(intval($dados['id']));
            echo json_encode(['status' => 'success']);
        } catch (Exception $e) { echo json_encode(['status' => 'error']); }
    }

    public function excluirSaida() {
        header('Content-Type: application/json');
        try {
            $dados = json_decode(file_get_contents('php://input'), true);
            $this->model->excluirSaida(intval($dados['id']));
            echo json_encode(['status' => 'success']);
        } catch (Exception $e) { echo json_encode(['status' => 'error']); }
    }

    public function buscarDadosEdicao() {
        header('Content-Type: application/json');
        $id = intval($_GET['id']);
        $tabela = ($_GET['origem'] === 'Entrada') ? 'entradas' : 'saidas';
        $d = $this->model->buscarPorId($id, $tabela);
        if($d) {
            $d['data_movimento'] = $d['data'];
            echo json_encode(['status' => 'success', 'dados' => $d]);
        } else { echo json_encode(['status' => 'error']); }
    }

    public function gerarRelatorio() {
        header('Content-Type: application/json');
        $tipoRelatorio = $_GET['tipo_relatorio'] ?? 'pesquisa';
        $inicio = $_GET['inicio'] ?? ''; $fim = $_GET['fim'] ?? '';
        
        if ($tipoRelatorio === 'incongruencias') {
            $dados = $this->model->buscarIncongruencias();
            echo json_encode(['status' => 'success', 'dados' => $dados]);
        } elseif ($tipoRelatorio === 'balanco') {
            $dadosRaw = $this->model->pesquisarRelatorio($inicio, $fim, '', 'ambos', 'data', 'todas');
            $balanco = [];
            foreach ($dadosRaw as $mov) {
                $mesRef = date('m/Y', strtotime($mov['data_movimento']));
                if (!isset($balanco[$mesRef])) {
                    $balanco[$mesRef] = ['mes_ref' => $mesRef, 'entradas' => 0, 'saidas' => 0, 'saldo' => 0];
                }
                if ($mov['origem'] === 'Entrada') { $balanco[$mesRef]['entradas'] += (float)$mov['valor']; }
                else { $balanco[$mesRef]['saidas'] += (float)$mov['valor']; }
                $balanco[$mesRef]['saldo'] = $balanco[$mesRef]['entradas'] - $balanco[$mesRef]['saidas'];
            }
            echo json_encode(['status' => 'success', 'dados' => array_values($balanco)]);
        } elseif ($tipoRelatorio === 'entradas_por_data') {
            $dados = $this->model->buscarMovimentacoesPorDataCriacao($inicio, $fim);
            echo json_encode(['status' => 'success', 'dados' => $dados]);
        } else {
            $dados = $this->model->pesquisarRelatorio($inicio, $fim, $_GET['nome']??'', $_GET['filtro_tipo']??'ambos', $_GET['ordem']??'data', $_GET['congregacao']??'todas');
            echo json_encode(['status' => 'success', 'dados' => $dados]);
        }
    }

    public function relatorioSimplificado() {
        header('Content-Type: application/json');
        try {
            $ini   = $_GET['inicio'] ?? date('Y-01-01');
            $fim   = $_GET['fim']    ?? date('Y-12-31');
            $congs = [];
            if (!empty($_GET['congregacoes'])) {
                $congs = array_filter(array_map('trim', explode(',', $_GET['congregacoes'])));
            }
            $dados = $this->model->relatorioSimplificado($ini, $fim, array_values($congs));
            echo json_encode(['status' => 'success', 'dados' => $dados]);
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function listarCongregacoes() {
        header('Content-Type: application/json');
        // Ajuste: Converte o array de objetos do Model em um array simples de strings para o JS da View
        $dados = $this->model->listarCongregacoes();
        $lista = array_column($dados, 'congregacao');
        echo json_encode(['status' => 'success', 'dados' => $lista]);
    }


    private function limparValor($v) {
        $v = preg_replace('/[^0-9,]/', '', $v);
        return str_replace(',', '.', $v);
    }
}