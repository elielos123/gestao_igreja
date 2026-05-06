<?php
namespace App\Controllers;

use App\Models\FinanceiroModel;
use App\Helpers\Acl;
use Exception;

class FinanceiroController {
    
    private $model;

    public function __construct() {
        // Define o fuso horário para evitar erros de data retroativa
        date_default_timezone_set('America/Sao_Paulo');
        $this->model = new FinanceiroModel();
    }

    public function indexEntradas() { Acl::check('view_financeiro'); require dirname(__DIR__) . '/Views/financeiro/entradas.php'; }
    public function indexSaidas() { Acl::check('view_financeiro'); require dirname(__DIR__) . '/Views/financeiro/saidas.php'; }
    public function indexRelatorios() { Acl::check('view_financeiro'); require dirname(__DIR__) . '/Views/financeiro/relatorios.php'; }
    public function indexIncongruencias() { Acl::check('view_financeiro'); require dirname(__DIR__) . '/Views/financeiro/incongruencias.php'; }
    public function indexCadastros() { Acl::check('manage_financeiro'); require dirname(__DIR__) . '/Views/financeiro/cadastros.php'; }
    public function indexBI() { Acl::check('view_reports'); require dirname(__DIR__) . '/Views/financeiro/bi.php'; }

    public function dadosBI() {
        Acl::check('view_reports');
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
        Acl::check('view_financeiro');
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
        Acl::check('manage_financeiro');
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
        Acl::check('manage_financeiro');
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
        Acl::check('manage_financeiro');
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
        Acl::check('manage_financeiro');
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
        Acl::check('manage_financeiro');
        header('Content-Type: application/json');
        try {
            $dados = json_decode(file_get_contents('php://input'), true);
            $this->model->excluirEntrada(intval($dados['id']));
            echo json_encode(['status' => 'success']);
        } catch (Exception $e) { echo json_encode(['status' => 'error']); }
    }

    public function excluirSaida() {
        Acl::check('manage_financeiro');
        header('Content-Type: application/json');
        try {
            $dados = json_decode(file_get_contents('php://input'), true);
            $this->model->excluirSaida(intval($dados['id']));
            echo json_encode(['status' => 'success']);
        } catch (Exception $e) { echo json_encode(['status' => 'error']); }
    }

    public function buscarDadosEdicao() {
        Acl::check('view_financeiro');
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
        Acl::check('view_financeiro');
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
        Acl::check('view_financeiro');
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


    public function exportarTxt() {
        Acl::check('view_financeiro');
        $modo = $_GET['modo'] ?? 'completo';
        $inicio = $_GET['inicio'] ?? '';
        $fim = $_GET['fim'] ?? '';
        $nome = $_GET['nome'] ?? '';
        $congregacao = $_GET['congregacao'] ?? 'todas';

        $dados = [];
        if ($modo !== 'comparativo_mensal') {
            $dados = $this->model->pesquisarRelatorio($inicio, $fim, $nome, 'entradas', 'congregacao', $congregacao);
        }

        $txt = "RELATÓRIO FINANCEIRO - MODO: " . strtoupper(str_replace('_', ' ', $modo)) . "\n";
        $txt .= "Período: " . ($inicio ? date('d/m/Y', strtotime($inicio)) : 'Início') . " até " . ($fim ? date('d/m/Y', strtotime($fim)) : 'Fim') . "\n";
        $txt .= str_repeat("-", 60) . "\n\n";

        $totalGeral = 0;

        if ($modo === 'completo') {
            $porCongregacao = [];
            foreach ($dados as $d) {
                $porCongregacao[$d['info_extra']][] = $d;
                $totalGeral += (float)$d['valor'];
            }
            foreach ($porCongregacao as $cong => $movs) {
                $txt .= "CONGREGAÇÃO: " . strtoupper($cong) . "\n";
                $subTotal = 0;
                foreach ($movs as $m) {
                    $txt .= sprintf("  %-30s | R$ %10.2f\n", $m['principal'], $m['valor']);
                    $subTotal += (float)$m['valor'];
                }
                $txt .= "  " . str_repeat("-", 45) . "\n";
                $txt .= sprintf("  SUBTOTAL %-21s | R$ %10.2f\n\n", $cong, $subTotal);
            }
            $txt .= str_repeat("=", 60) . "\n";
            $txt .= sprintf("TOTAL GERAL: %44s R$ %10.2f\n", "", $totalGeral);

        } elseif ($modo === 'total_congregacional') {
            $totais = [];
            foreach ($dados as $d) {
                if (!isset($totais[$d['info_extra']])) $totais[$d['info_extra']] = 0;
                $totais[$d['info_extra']] += (float)$d['valor'];
                $totalGeral += (float)$d['valor'];
            }
            foreach ($totais as $cong => $total) {
                $txt .= sprintf("%-45s | R$ %10.2f\n", strtoupper($cong), $total);
            }
            $txt .= str_repeat("=", 60) . "\n";
            $txt .= sprintf("TOTAL GERAL: %44s R$ %10.2f\n", "", $totalGeral);

        } elseif ($modo === 'dizimistas') {
            $porCongregacao = [];
            foreach ($dados as $d) {
                $porCongregacao[$d['info_extra']][] = $d['principal'];
            }
            foreach ($porCongregacao as $cong => $nomes) {
                $txt .= "CONGREGAÇÃO: " . strtoupper($cong) . "\n";
                $nomes = array_unique($nomes);
                sort($nomes);
                foreach ($nomes as $n) {
                    $txt .= "  - " . $n . "\n";
                }
                $txt .= "\n";
            }

        } elseif ($modo === 'comparativo_mensal') {
            $dadosComp = $this->model->buscarComparativoMensal();
            $meses = [];
            $grid = [];
            foreach ($dadosComp as $d) {
                $meses[$d['mes']] = true;
                $grid[$d['congregacao']][$d['mes']] = (float)$d['total'];
            }
            $mesesSorted = array_keys($meses);
            sort($mesesSorted);

            $txt .= sprintf("%-25s", "CONGREGAÇÃO");
            foreach ($mesesSorted as $m) { $txt .= sprintf(" | %10s", $m); }
            $txt .= "\n" . str_repeat("-", 25 + (count($mesesSorted) * 13)) . "\n";

            $totaisMes = array_fill_keys($mesesSorted, 0);
            foreach ($grid as $cong => $valores) {
                $txt .= sprintf("%-25s", strtoupper(substr($cong, 0, 25)));
                foreach ($mesesSorted as $m) {
                    $v = $valores[$m] ?? 0;
                    $txt .= sprintf(" | %10.2f", $v);
                    $totaisMes[$m] += $v;
                    $totalGeral += $v;
                }
                $txt .= "\n";
            }
            $txt .= str_repeat("=", 25 + (count($mesesSorted) * 13)) . "\n";
            $txt .= sprintf("%-25s", "TOTAL GERAL");
            foreach ($mesesSorted as $m) { $txt .= sprintf(" | %10.2f", $totaisMes[$m]); }
            $txt .= "\n";
        }

        header('Content-Type: text/plain; charset=utf-8');
        header('Content-Disposition: attachment; filename="relatorio_' . $modo . '_' . date('Ymd_His') . '.txt"');
        echo $txt;
        exit;
    }

    private function limparValor($v) {
        $v = preg_replace('/[^0-9,]/', '', $v);
        return str_replace(',', '.', $v);
    }
}