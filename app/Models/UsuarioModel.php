<?php
namespace app\Models;

use app\Models\Database;
use DateTime;

class UsuarioModel extends Database
{
  private $database;
  private $tabela;

  public function __construct()
  {
    $this->database = new Database();
    $this->tabela = 'usuarios';
  }

  public function buscar($params = [] , $colunas = [])
  {
    $id = $params['id'] ?? 0;
    $ids = $params['ids'] ?? [];
    $nome = $params['nome'] ?? '';
    $sobrenome = $params['sobrenome'] ?? '';
    $data_nascimento = $params['data_nascimento'] ?? '';
    $telefone = $params['telefone'] ?? '';
    $email = $params['email'] ?? '';
    $criado_inicio = $params['criado_inicio'] ?? '';
    $criado_fim = $params['criado_fim'] ?? '';

    $id = (int) $id;
    $nome = htmlspecialchars($nome);
    $sobrenome = htmlspecialchars($sobrenome);
    $email = filter_var($email, FILTER_SANITIZE_EMAIL);
    $telefone = preg_replace('/\D/', '', $telefone);
    $data_nascimento = htmlspecialchars($data_nascimento);
    $criado_inicio = htmlspecialchars($criado_inicio);
    $criado_fim = htmlspecialchars($criado_fim);

    $campo_erro = [];

    if ($data_nascimento) {
      $data_nascimento = DateTime::createFromFormat('d/m/Y', $data_nascimento);

      if (date_get_last_errors()) {
        $campo_erro[] = 'data_nascimento';
      }
      else {
        $data_nascimento = $data_nascimento->format('Y-m-d');
      }
    }

    if ($criado_inicio) {
      $criado_inicio = DateTime::createFromFormat('d/m/Y', $criado_inicio);

      if (date_get_last_errors()) {
        $campo_erro[] = 'criado_inicio';
      }
      else {
        $criado_inicio = $criado_inicio->format('Y-m-d');
      }
    }

    if ($criado_fim) {
      $criado_fim = DateTime::createFromFormat('d/m/Y', $criado_fim);

      if (date_get_last_errors()) {
        $campo_erro[] = 'criado_fim';
      }
      else {
        $criado_fim = $criado_fim->format('Y-m-d');
      }
    }

    if ($colunas) {
      foreach ($colunas as &$linha) :
        $linha = htmlspecialchars($linha);
      endforeach;

      $sql = 'SELECT ' . implode(', ', $colunas) . ' FROM ' . $this->tabela . ' AS `Usuario`';
    }
    else {
      $sql = 'SELECT * FROM ' . $this->tabela . ' AS `Usuario`';
    }

    $sql_params = [];

    // Sempre consultas individuais
    if ($id) {
      $sql .= ' WHERE id = ?';
      $sql_params[] = $id;
    }
    elseif ($ids) {
      $sql .= ' WHERE id IN (' . str_repeat('?,', count($ids) -1) . '?)';

      foreach ($ids as $linha):
        $sql_params[] = (int) $linha;
      endforeach;
    }
    elseif ($nome) {
      $sql .= ' WHERE nome LIKE ?';
      $sql_params[] = '%' . $nome . '%';
    }
    elseif ($sobrenome) {
      $sql .= ' WHERE nome LIKE ?';
      $sql_params[] = '%' . $sobrenome . '%';
    }
    elseif ($data_nascimento) {
      $sql .= ' WHERE data_nascimento = ?';
      $sql_params[] = $data_nascimento;
    }
    elseif ($telefone) {
      $sql .= ' WHERE telefone = ?';
      $sql_params[] = $telefone;
    }
    elseif ($email) {
      $sql .= ' WHERE email LIKE ?';
      $sql_params[] = $email . '%';
    }
    elseif ($criado_inicio and empty($criado_fim)) {
      $sql .= ' WHERE DATE (criado) BETWEEN ? AND CURDATE()';
      $sql_params[] = $criado_inicio;
    }
    elseif (empty($criado_inicio) and $criado_fim) {
      $sql .= ' WHERE DATE (criado) <= ?';
      $sql_params[] = $criado_fim;
    }
    elseif ($criado_inicio and $criado_fim) {
      $sql .= ' WHERE DATE (criado) BETWEEN ? AND ?';
      $sql_params[] = $criado_inicio;
      $sql_params[] = $criado_fim;
    }

    if (empty($campo_erro)) {
      $resultado = $this->database->operacoes($sql, $sql_params);

      if ($resultado !== false) {
        return $resultado;
      }
    }

    return ['erro' => 'Erro ao realizar consulta'];
  }

  public function novo($params = [])
  {
    $nome = $params['nome'] ?? '';
    $sobrenome = $params['sobrenome'] ?? '';
    $data_nascimento = $params['data_nascimento'] ?? '';
    $telefone = $params['telefone'] ?? '';
    $email = $params['email'] ?? '';
    $senha = $params['senha'] ?? '';

    $nome = htmlspecialchars($nome);
    $sobrenome = htmlspecialchars($sobrenome);
    $email = filter_var($email, FILTER_SANITIZE_EMAIL);
    $telefone = preg_replace('/\D/', '', $telefone);
    $senha = password_hash(trim($senha), PASSWORD_DEFAULT);
    $data_nascimento = htmlspecialchars($data_nascimento);
    $data_nascimento = DateTime::createFromFormat('d/m/Y', $data_nascimento);

    $campo_erro = [];
    if (empty($nome)) {
      $campo_erro[] = 'nome';
    }

    if (empty($sobrenome)) {
      $campo_erro[] = 'sobrenome';
    }

    if (empty($email)) {
      $campo_erro[] = 'email';
    }

    if (empty($senha)) {
      $campo_erro[] = 'senha';
    }

    if (! in_array(strlen($telefone), [10, 11])) {
      $campo_erro[] = 'telefone';
    }

    if (empty($data_nascimento) or date_get_last_errors()) {
      $campo_erro[] = 'data_nascimento';
    }

    $data_nascimento = $data_nascimento->format('Y-m-d');

    if (empty($campo_erro)) {
      $sql = 'INSERT INTO `usuarios`
                (nome, sobrenome, data_nascimento, telefone, email, senha)
              VALUES
                (?, ?, ?, ?, ?, ?)';

      $sql_params = [
        $nome,
        $sobrenome,
        $data_nascimento,
        $telefone,
        $email,
        $senha,
      ];

      if ($this->database->operacoes($sql, $sql_params) !== false) {
        return ['ok' => 'Usuário cadastrado com sucesso'];
      }
    }

    return ['erro' => 'Erro ao cadastrar usuário', 'campo_erro' => $campo_erro];
  }

  public function editar($id, $params = [])
  {
    $nome = $params['nome'] ?? '';
    $sobrenome = $params['sobrenome'] ?? '';
    $data_nascimento = $params['data_nascimento'] ?? '';
    $telefone = $params['telefone'] ?? '';

    $nome = htmlspecialchars($nome);
    $sobrenome = htmlspecialchars($sobrenome);
    $telefone = preg_replace('/\D/', '', $telefone);
    $data_nascimento = htmlspecialchars($data_nascimento);
    $data_nascimento = DateTime::createFromFormat('d/m/Y', $data_nascimento);

    $campo_erro = [];
    if (empty($nome)) {
      $campo_erro[] = 'nome';
    }

    if (empty($sobrenome)) {
      $campo_erro[] = 'sobrenome';
    }

    if (! in_array(strlen($telefone), [10, 11])) {
      $campo_erro[] = 'telefone';
    }

    if (empty($data_nascimento) or date_get_last_errors()) {
      $campo_erro[] = 'data_nascimento';
    }

    $data_nascimento = $data_nascimento->format('Y-m-d');

    if (empty($campo_erro)) {
      $sql = 'UPDATE
                `usuarios`
              SET
                nome = ?,
                sobrenome = ?,
                data_nascimento = ?,
                telefone = ?,
              WHERE
                `usuarios`.`id` = ' . $id . ';';

      $sql_params = [
        $nome,
        $sobrenome,
        $data_nascimento,
        $telefone,
      ];

      if ($this->database->operacoes($sql, $sql_params) !== false) {
        return ['ok' => 'Usuário atualizado com sucesso'];
      }
    }

    return ['erro' => 'Erro ao atualizar usuário', 'campo_erro' => $campo_erro];
  }

  public function editar_senha($id, $senha_atual, $senha_nova)
  {
    $usuario = $this->buscar(['id' => $id], ['senha']);

    if ($usuario === false) {
      return ['erro' => 'Erro ao buscar usuário'];
    }

    if (empty($usuario)) {
      return ['erro' => 'Usuário não existe'];
    }

    if (! password_verify(trim($senha_atual), $usuario[0]['senha'])) {
      return ['erro' => 'Senha atual inválida'];
    }

    $senha_nova = password_hash(trim($senha_nova), PASSWORD_DEFAULT);

    if (empty($campo_erro)) {
      $sql = 'UPDATE
                `usuarios`
              SET
                senha = ?
              WHERE
                `usuarios`.`id` = ' . $id . ';';

      if ($this->database->operacoes($sql, [ $senha_nova ]) !== false) {
        return ['ok' => 'Senha atualizada com sucesso'];
      }
    }

    return ['erro' => 'Erro ao atualizar senha', 'campo_erro' => ''];
  }
}