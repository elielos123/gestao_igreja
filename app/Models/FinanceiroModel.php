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
        $sql = "INSERT INTO entradas (nome, Data, valor, congregacao, tipo) 
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
        $sql = "UPDATE entradas SET nome=:nome, Data=:data, valor=:valor, congregacao=:congregacao, tipo=:tipo WHERE id=:id";
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
            // CORREÇÃO: Unifica nomes de membros (com congregação) e recebedores para o autocomplete global
            $sql = "SELECT DISTINCT nome as label, congregacao FROM membros WHERE nome LIKE :termo 
                    UNION 
                    SELECT DISTINCT nome as label, congregacao FROM entradas WHERE nome LIKE :termo 
                    UNION 
                    SELECT DISTINCT recebedor as label, '' as congregacao FROM saidas WHERE recebedor LIKE :termo 
                    LIMIT 15";
        } else {
            // CORREÇÃO: Busca tipos de entrada ou saída conforme o termo digitado
            $sql = "SELECT DISTINCT $campo as label FROM entradas WHERE $campo LIKE :termo 
                    UNION 
                    SELECT DISTINCT tipo_saida as label FROM saidas WHERE tipo_saida LIKE :termo 
                    LIMIT 10";
        }
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':termo' => "%$termo%"]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function pesquisarRelatorio($inicio, $fim, $nome, $tipo, $ordem, $congregacao) {
        $sqlEnt = "SELECT id, 'Entrada' as origem, nome as principal, Data as data_movimento, valor, congregacao as info_extra, tipo as categoria FROM entradas WHERE 1=1";
        $sqlSai = "SELECT id, 'Saída' as origem, recebedor as principal, data as data_movimento, valor, descricao as info_extra, tipo_saida as categoria FROM saidas WHERE 1=1";
        
        $params = [];
        if ($inicio && $fim) {
            $sqlEnt .= " AND DATE(Data) BETWEEN :inicio AND :fim";
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
        $sql = "SELECT id, 'Entrada' as origem, nome as principal, Data as data_movimento, 
                CASE 
                    WHEN valor <= 0 THEN 'Valor zerado ou inválido'
                    WHEN nome IS NULL OR nome = '' THEN 'Nome do membro ausente'
                    WHEN congregacao IS NULL OR congregacao = '' THEN 'Congregação não informada'
                    WHEN tipo IS NULL OR tipo = '' THEN 'Tipo de entrada não definido'
                END as motivo
                FROM entradas 
                WHERE valor <= 0 OR nome IS NULL OR nome = '' OR congregacao IS NULL OR congregacao = '' OR tipo IS NULL OR tipo = ''
                UNION ALL
                SELECT id, 'Saída' as origem, recebedor as principal, data as data_movimento,
                CASE 
                    WHEN valor <= 0 THEN 'Valor zerado ou inválido'
                    WHEN recebedor IS NULL OR recebedor = '' THEN 'Recebedor ausente'
                    WHEN tipo_saida IS NULL OR tipo_saida = '' THEN 'Tipo de saída não definido'
                END as motivo
                FROM saidas
                WHERE valor <= 0 OR recebedor IS NULL OR recebedor = '' OR tipo_saida IS NULL OR tipo_saida = ''
                ORDER BY data_movimento DESC";
        return $this->conn->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function listarCongregacoes() {
        // CORREÇÃO: Garante que a lista de congregações seja única e populada para os filtros
        $sql = "SELECT DISTINCT congregacao FROM entradas WHERE congregacao IS NOT NULL AND congregacao <> '' ORDER BY congregacao ASC";
        return $this->conn->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }
}