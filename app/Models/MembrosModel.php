<?php
namespace App\Models;

use App\Config\Database;
use PDO;

class MembrosModel {
    private $conn;

    public function __construct() {
        $this->conn = (new Database())->getConnection();
    }

    public function listarTodos() {
        $sql = "SELECT * FROM membros ORDER BY nome ASC";
        $stmt = $this->conn->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function buscarPorId($id) {
        $sql = "SELECT * FROM membros WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function salvar($dados) {
        if (isset($dados['id']) && !empty($dados['id'])) {
            return $this->atualizar($dados);
        } else {
            return $this->inserir($dados);
        }
    }

    private function inserir($d) {
        $sql = "INSERT INTO membros (nome, data_nascimento, sexo, cpf, telefone, email, endereco, data_batismo, funcao_eclesiastica, cargo_congregacional, cargo, congregacao, status) 
                VALUES (:nome, :data_nascimento, :sexo, :cpf, :telefone, :email, :endereco, :data_batismo, :funcao_eclesiastica, :cargo_congregacional, :cargo, :congregacao, :status)";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            ':nome'                 => $d['nome'],
            ':data_nascimento'      => !empty($d['data_nascimento']) ? $d['data_nascimento'] : null,
            ':sexo'                 => !empty($d['sexo']) ? $d['sexo'] : null,
            ':cpf'                  => !empty($d['cpf']) ? $d['cpf'] : null,
            ':telefone'             => !empty($d['telefone']) ? $d['telefone'] : null,
            ':email'                => !empty($d['email']) ? $d['email'] : null,
            ':endereco'             => !empty($d['endereco']) ? $d['endereco'] : null,
            ':data_batismo'         => !empty($d['data_batismo']) ? $d['data_batismo'] : null,
            ':funcao_eclesiastica'  => !empty($d['funcao_eclesiastica']) ? $d['funcao_eclesiastica'] : null,
            ':cargo_congregacional' => !empty($d['cargo_congregacional']) ? $d['cargo_congregacional'] : null,
            ':cargo'                => !empty($d['cargo']) ? $d['cargo'] : null,
            ':congregacao'          => !empty($d['congregacao']) ? $d['congregacao'] : null,
            ':status'               => !empty($d['status']) ? $d['status'] : 'Ativo'
        ]);
    }

    private function atualizar($d) {
        $sql = "UPDATE membros SET nome=:nome, data_nascimento=:data_nascimento, sexo=:sexo, cpf=:cpf, telefone=:telefone, 
                email=:email, endereco=:endereco, data_batismo=:data_batismo, funcao_eclesiastica=:funcao_eclesiastica, 
                cargo_congregacional=:cargo_congregacional, cargo=:cargo, congregacao=:congregacao, status=:status 
                WHERE id=:id";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            ':id'                   => $d['id'],
            ':nome'                 => $d['nome'],
            ':data_nascimento'      => !empty($d['data_nascimento']) ? $d['data_nascimento'] : null,
            ':sexo'                 => !empty($d['sexo']) ? $d['sexo'] : null,
            ':cpf'                  => !empty($d['cpf']) ? $d['cpf'] : null,
            ':telefone'             => !empty($d['telefone']) ? $d['telefone'] : null,
            ':email'                => !empty($d['email']) ? $d['email'] : null,
            ':endereco'             => !empty($d['endereco']) ? $d['endereco'] : null,
            ':data_batismo'         => !empty($d['data_batismo']) ? $d['data_batismo'] : null,
            ':funcao_eclesiastica'  => !empty($d['funcao_eclesiastica']) ? $d['funcao_eclesiastica'] : null,
            ':cargo_congregacional' => !empty($d['cargo_congregacional']) ? $d['cargo_congregacional'] : null,
            ':cargo'                => !empty($d['cargo']) ? $d['cargo'] : null,
            ':congregacao'          => !empty($d['congregacao']) ? $d['congregacao'] : null,
            ':status'               => !empty($d['status']) ? $d['status'] : 'Ativo'
        ]);
    }

    public function excluir($id) {
        $sql = "DELETE FROM membros WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }

    /**
     * MÉTODOS DE APOIO (LOOKUPS)
     */
    public function listarLookup($tabela) {
        $sql = "SELECT * FROM $tabela ORDER BY nome ASC";
        return $this->conn->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function salvarLookup($tabela, $nome, $id = null) {
        if ($id) {
            $sql = "UPDATE $tabela SET nome = :nome WHERE id = :id";
            $stmt = $this->conn->prepare($sql);
            return $stmt->execute([':nome' => $nome, ':id' => $id]);
        } else {
            $sql = "INSERT INTO $tabela (nome) VALUES (:nome) ON DUPLICATE KEY UPDATE nome = :nome";
            $stmt = $this->conn->prepare($sql);
            return $stmt->execute([':nome' => $nome]);
        }
    }

    public function excluirLookup($tabela, $id) {
        $sql = "DELETE FROM $tabela WHERE id = :id";
        return $this->conn->prepare($sql)->execute([':id' => $id]);
    }

    public function listarConflitos() {
        $sql = "SELECT * FROM membros_conflitos WHERE resolvido = 0 ORDER BY data_criacao DESC";
        $stmt = $this->conn->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function marcarConflitoResolvido($id) {
        $sql = "UPDATE membros_conflitos SET resolvido = 1 WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }
}
