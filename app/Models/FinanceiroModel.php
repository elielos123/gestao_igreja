<?php
namespace App\Models;

use App\Config\Database;
use PDO;

class FinanceiroModel {
    private $conn;

    public function __construct() { 
        $this->conn = (new Database())->getConnection(); 
    }

    /**
     * REGISTROS DE SAÍDA
     */
    public function registrarSaida($d) {
        $sql = "INSERT INTO saidas (recebedor, data, valor, descricao, dados_cadastrais, tipo_saida, parcela) 
                VALUES (:recebedor, :data, :valor, :descricao, :dados_cadastrais, :tipo_saida, :parcela)";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            ':recebedor'        => $d['recebedor'], 
            ':data'             => $d['data'], 
            ':valor'            => $d['valor'], 
            ':descricao'        => $d['descricao'], 
            ':dados_cadastrais' => $d['dados_cadastrais'], 
            ':tipo_saida'       => $d['tipo_saida'], 
            ':parcela'          => $d['parcela']
        ]);
        return $this->conn->lastInsertId();
    }

    public function atualizarSaida($id, $d) {
        $sql = "UPDATE saidas SET recebedor=:recebedor, data=:data, valor=:valor, descricao=:descricao, 
                dados_cadastrais=:dados_cadastrais, tipo_saida=:tipo_saida, parcela=:parcela WHERE id=:id";
        return $this->conn->prepare($sql)->execute([
            ':id'               => $id,
            ':recebedor'        => $d['recebedor'],
            ':data'             => $d['data'],
            ':valor'            => $d['valor'],
            ':descricao'        => $d['descricao'],
            ':dados_cadastrais' => $d['dados_cadastrais'],
            ':tipo_saida'       => $d['tipo_saida'],
            ':parcela'          => $d['parcela']
        ]);
    }

    public function excluirSaida($id) {
        $sql = "DELETE FROM saidas WHERE id = :id";
        return $this->conn->prepare($sql)->execute([':id' => $id]);
    }

    /**
     * REGISTROS DE ENTRADA
     */
    public function registrarEntrada($d) {
        $sql = "INSERT INTO entradas (nome, data, valor, congregacao, tipo) 
                VALUES (:nome, :data, :valor, :congregacao, :tipo)";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            ':nome'        => $d['nome'],
            ':data'        => $d['data'],
            ':valor'       => $d['valor'],
            ':congregacao' => $d['congregacao'],
            ':tipo'        => $d['tipo']
        ]);
        return $this->conn->lastInsertId();
    }

    public function atualizarEntrada($id, $d) {
        $sql = "UPDATE entradas SET nome=:nome, data=:data, valor=:valor, congregacao=:congregacao, tipo=:tipo WHERE id=:id";
        return $this->conn->prepare($sql)->execute([
            ':id'          => $id,
            ':nome'        => $d['nome'],
            ':data'        => $d['data'],
            ':valor'       => $d['valor'],
            ':congregacao' => $d['congregacao'],
            ':tipo'        => $d['tipo']
        ]);
    }

    public function excluirEntrada($id) {
        $sql = "DELETE FROM entradas WHERE id = :id";
        return $this->conn->prepare($sql)->execute([':id' => $id]);
    }

    /**
     * BUSCAS E FILTROS
     */
    public function buscarPorId($id, $tabela) {
        $sql = "SELECT * FROM $tabela WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function buscarSugestoes($termo, $campo) {
        if ($campo === 'nome') {
            // Busca nomes de membros e entradas, e para cada um, tenta achar a congregação do último lançamento
            $sql = "SELECT DISTINCT 
                        u.label, 
                        COALESCE(ult_ent.congregacao, u.cong_cad) as congregacao
                    FROM (
                        SELECT nome as label, congregacao as cong_cad FROM membros
                        UNION
                        SELECT nome as label, congregacao as cong_cad FROM entradas
                    ) as u
                    LEFT JOIN (
                        SELECT e1.nome, e1.congregacao 
                        FROM entradas e1
                        INNER JOIN (
                            SELECT nome, MAX(id) as max_id FROM entradas GROUP BY nome
                        ) e2 ON e1.id = e2.max_id
                    ) as ult_ent ON u.label = ult_ent.nome
                    WHERE u.label LIKE :termo
                    LIMIT 15";
        } elseif ($campo === 'recebedor') {
            // Busca recebedores e seus dados cadastrais
            $sql = "SELECT DISTINCT recebedor as label, dados_cadastrais as extra FROM saidas WHERE recebedor LIKE :termo LIMIT 15";
        } elseif ($campo === 'dados_cadastrais') {
            // Busca dados cadastrais e seus recebedores
            $sql = "SELECT DISTINCT dados_cadastrais as label, recebedor as extra FROM saidas WHERE dados_cadastrais LIKE :termo AND dados_cadastrais <> '' LIMIT 15";
        } elseif ($campo === 'descricao') {
            // Busca descrições únicas de saídas
            $sql = "SELECT DISTINCT descricao as label, '' as extra FROM saidas WHERE descricao LIKE :termo AND descricao <> '' LIMIT 15";
        } else {
            $sql = "SELECT DISTINCT $campo as label, '' as extra FROM entradas WHERE $campo LIKE :termo 
                    UNION 
                    SELECT DISTINCT tipo_saida as label, '' as extra FROM saidas WHERE tipo_saida LIKE :termo 
                    LIMIT 10";
        }
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':termo' => "%$termo%"]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function pesquisarRelatorio($inicio, $fim, $nome, $tipo, $ordem, $congregacao) {
        $sqlEnt = "SELECT id, 'Entrada' as origem, nome as principal, data as data_movimento, valor, congregacao as info_extra, tipo as categoria FROM entradas WHERE 1=1";
        $sqlSai = "SELECT id, 'Saída' as origem, recebedor as principal, data as data_movimento, valor, descricao as info_extra, tipo_saida as categoria FROM saidas WHERE 1=1";
        
        $params = [];
        if ($inicio && $fim) {
            $sqlEnt .= " AND DATE(data) BETWEEN :inicio AND :fim";
            $sqlSai .= " AND DATE(data) BETWEEN :inicio AND :fim";
            $params[':inicio'] = $inicio;
            $params[':fim'] = $fim;
        }

        if ($nome) { 
            $sqlEnt .= " AND nome LIKE :nome"; 
            $sqlSai .= " AND recebedor LIKE :nome"; 
            $params[':nome'] = "%$nome%";
        }
        if ($congregacao !== 'todas') { 
            $sqlEnt .= " AND congregacao = :cong"; 
            $params[':cong'] = $congregacao;
        }
        
        if ($tipo === 'entradas') { $sql = $sqlEnt; } 
        elseif ($tipo === 'saidas') { $sql = $sqlSai; } 
        else { $sql = "($sqlEnt) UNION ALL ($sqlSai)"; }

        // RESTAURAÇÃO: Lógica de ordenação múltipla conforme solicitado anteriormente
        if ($ordem === 'valor') {
            $sql .= " ORDER BY valor DESC";
        } elseif ($ordem === 'nome') {
            $sql .= " ORDER BY principal ASC";
        } else {
            $sql .= " ORDER BY data_movimento DESC";
        }

        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function buscarIncongruencias() {
        // SQL para detectar outliers (valores > 2.5 * desvio padrão da categoria)
        // Usando JOIN para melhor performance em vez de subqueries correlacionadas
        
        $sqlOutliersEnt = "
            SELECT e.id, 'Entrada' as origem, e.nome as principal, e.data as data_movimento, e.valor,
            'Valor muito acima da média para esta categoria' as motivo
            FROM entradas e
            JOIN (
                SELECT tipo, AVG(valor) as media, STDDEV(valor) as desvio
                FROM entradas
                GROUP BY tipo
            ) stats ON e.tipo = stats.tipo
            WHERE e.incongruencia_aceita = 0 
              AND e.valor > (stats.media + 2.5 * stats.desvio) AND stats.desvio > 0
        ";

        $sqlOutliersSai = "
            SELECT s.id, 'Saída' as origem, s.recebedor as principal, s.data as data_movimento, s.valor,
            'Valor muito acima da média para esta categoria' as motivo
            FROM saidas s
            JOIN (
                SELECT tipo_saida, AVG(valor) as media, STDDEV(valor) as desvio
                FROM saidas
                GROUP BY tipo_saida
            ) stats ON s.tipo_saida = stats.tipo_saida
            WHERE s.incongruencia_aceita = 0
              AND s.valor > (stats.media + 2.5 * stats.desvio) AND stats.desvio > 0
        ";

        $sqlGeral = "
            SELECT id, 'Entrada' as origem, nome as principal, data as data_movimento, valor,
            CASE 
                WHEN valor <= 0 THEN 'Valor zerado ou inválido'
                WHEN nome IS NULL OR nome = '' THEN 'Nome do membro ausente'
                WHEN congregacao IS NULL OR congregacao = '' THEN 'Congregação não informada'
                WHEN tipo IS NULL OR tipo = '' THEN 'Tipo de entrada não definido'
            END as motivo
            FROM entradas 
            WHERE incongruencia_aceita = 0 AND (valor <= 0 OR nome IS NULL OR nome = '' OR congregacao IS NULL OR congregacao = '' OR tipo IS NULL OR tipo = '')
            
            UNION ALL
            
            SELECT id, 'Saída' as origem, recebedor as principal, data as data_movimento, valor,
            CASE 
                WHEN valor <= 0 THEN 'Valor zerado ou inválido'
                WHEN recebedor IS NULL OR recebedor = '' THEN 'Recebedor ausente'
                WHEN tipo_saida IS NULL OR tipo_saida = '' THEN 'Tipo de saída não definido'
            END as motivo
            FROM saidas
            WHERE incongruencia_aceita = 0 AND (valor <= 0 OR recebedor IS NULL OR recebedor = '' OR tipo_saida IS NULL OR tipo_saida = '')
            
            UNION ALL
            
            ($sqlOutliersEnt)
            
            UNION ALL
            
            ($sqlOutliersSai)
            
            ORDER BY data_movimento DESC
        ";

        return $this->conn->query($sqlGeral)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function aceitarIncongruencia($id, $origem) {
        $tabela = ($origem === 'Entrada') ? 'entradas' : 'saidas';
        $sql = "UPDATE $tabela SET incongruencia_aceita = 1 WHERE id = :id";
        return $this->conn->prepare($sql)->execute([':id' => $id]);
    }

    public function listarCongregacoes() {
        // CORREÇÃO: Garante que a lista de congregações seja única e populada para os filtros
        $sql = "SELECT DISTINCT congregacao FROM entradas WHERE congregacao IS NOT NULL AND congregacao <> '' ORDER BY congregacao ASC";
        return $this->conn->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    // ────────────────────────────────────────────────────
    //  BI QUERIES
    // ────────────────────────────────────────────────────

    /** Total de entradas por mês no período */
    public function biEntradasMensais($inicio, $fim) {
        $sql = "SELECT DATE_FORMAT(data, '%Y-%m') as mes, SUM(valor) as total
                FROM entradas
                WHERE data BETWEEN :inicio AND :fim
                GROUP BY mes ORDER BY mes ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':inicio' => $inicio, ':fim' => $fim]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /** Top 5 congregações por total de entradas */
    public function biTopCongregacoes($inicio, $fim) {
        $sql = "SELECT congregacao, SUM(valor) as total, COUNT(*) as registros
                FROM entradas
                WHERE data BETWEEN :inicio AND :fim
                  AND congregacao IS NOT NULL AND congregacao <> ''
                GROUP BY congregacao
                ORDER BY total DESC
                LIMIT 5";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':inicio' => $inicio, ':fim' => $fim]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /** Contagem de dizimistas fiéis (≥10 dízimos nos últimos 12 meses) */
    public function biDizimistasFields() {
        $sql = "SELECT COUNT(*) as total FROM (
                    SELECT nome
                    FROM entradas
                    WHERE (tipo LIKE '%dízim%' OR tipo LIKE '%dizim%')
                      AND data >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)
                    GROUP BY nome
                    HAVING COUNT(*) >= 10
                ) AS fieis";
        return $this->conn->query($sql)->fetch(PDO::FETCH_ASSOC);
    }

    /** Dizimistas fiéis agrupados por congregação (≥10 dízimos nos últimos 12 meses) */
    public function biDizimistasFieisPorCongregacao() {
        $sql = "SELECT congregacao, COUNT(*) as total FROM (
                    SELECT nome, congregacao
                    FROM entradas
                    WHERE (tipo LIKE '%dízim%' OR tipo LIKE '%dizim%')
                      AND data >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)
                      AND congregacao IS NOT NULL AND congregacao <> ''
                    GROUP BY nome, congregacao
                    HAVING COUNT(*) >= 10
                ) AS fieis
                GROUP BY congregacao
                ORDER BY total DESC";
        return $this->conn->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }


    /** Top 3 dizimistas por congregação com média */
    public function biTopDizimistasPorCongregacao($inicio, $fim) {
        $sql = "SELECT congregacao, nome, COUNT(*) as contagem, SUM(valor) as total, AVG(valor) as media
                FROM entradas
                WHERE (tipo LIKE '%dízim%' OR tipo LIKE '%dizim%')
                  AND data BETWEEN :inicio AND :fim
                  AND congregacao IS NOT NULL AND congregacao <> ''
                GROUP BY congregacao, nome
                HAVING contagem >= 1
                ORDER BY congregacao ASC, total DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':inicio' => $inicio, ':fim' => $fim]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        // Keep only top 3 per congregation
        $result = [];
        foreach ($rows as $row) {
            $c = $row['congregacao'];
            if (!isset($result[$c])) $result[$c] = [];
            if (count($result[$c]) < 3) $result[$c][] = $row;
        }
        return $result;
    }

    /** Entradas por semana do mês (1a, 2a, 3a, 4a semana) */
    public function biEntradasSemanais($inicio, $fim) {
        $sql = "SELECT
                    CASE
                        WHEN DAY(data) BETWEEN 1 AND 7   THEN '1ª Semana'
                        WHEN DAY(data) BETWEEN 8 AND 14  THEN '2ª Semana'
                        WHEN DAY(data) BETWEEN 15 AND 21 THEN '3ª Semana'
                        ELSE '4ª Semana'
                    END as semana,
                    SUM(valor) as total,
                    COUNT(*) as registros
                FROM entradas
                WHERE data BETWEEN :inicio AND :fim
                GROUP BY semana
                ORDER BY FIELD(semana, '1ª Semana','2ª Semana','3ª Semana','4ª Semana')";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':inicio' => $inicio, ':fim' => $fim]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /** Análise de período: total e comparação entre intervalos */
    public function biComparacaoPeriodos($inicio1, $fim1, $inicio2, $fim2) {
        $sql = "SELECT 'Período 1' as periodo, SUM(valor) as total, COUNT(*) as registros
                FROM entradas WHERE data BETWEEN :inicio1 AND :fim1
                UNION ALL
                SELECT 'Período 2' as periodo, SUM(valor) as total, COUNT(*) as registros
                FROM entradas WHERE data BETWEEN :inicio2 AND :fim2";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':inicio1'=>$inicio1,':fim1'=>$fim1,':inicio2'=>$inicio2,':fim2'=>$fim2]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /** Relatório Simplificado: total por congregação ordenado por valor */
    public function relatorioSimplificado($inicio, $fim, array $congregacoes = []) {
        $sql = "SELECT congregacao, SUM(valor) as total, COUNT(*) as registros
                FROM entradas
                WHERE data BETWEEN :inicio AND :fim
                  AND congregacao IS NOT NULL AND congregacao <> ''";
        $params = [':inicio' => $inicio, ':fim' => $fim];
        if (!empty($congregacoes)) {
            $placeholders = implode(',', array_map(fn($i) => ":c$i", array_keys($congregacoes)));
            $sql .= " AND congregacao IN ($placeholders)";
            foreach ($congregacoes as $i => $c) { $params[":c$i"] = $c; }
        }
        $sql .= " GROUP BY congregacao ORDER BY total DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
