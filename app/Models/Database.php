<?php
namespace app\Models;

use Exception;
use \PDO;

class Database
{
  private $conexao;

  public function __construct()
  {
    try {
      $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NOME . ';charset=utf8';
      $this->conexao = new PDO($dsn, DB_USUARIO, DB_SENHA);
      $this->conexao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }
    catch (Exception $e) {
      $log_mensagem = str_repeat("-", 150) . PHP_EOL . PHP_EOL;
      $log_mensagem .= date('Y-m-d H:i:s') . PHP_EOL . PHP_EOL;
      $log_mensagem .= 'Erro: ' . $e->getMessage() . PHP_EOL . PHP_EOL;

      error_log($log_mensagem, 3, '../app/logs/' . date('Y-m-d') . '.log');
    }
  }

  public function __destruct()
  {
    $this->conexao = null;
  }

  public function operacoes($sql, $parametros = [])
  {
    try {
      $stmt = $this->conexao->prepare($sql);

      if ($parametros) {
        $indice = 1;
        foreach ($parametros as $chave => $linha):
          $type = PDO::PARAM_STR;

          if (is_int($linha)) {
            $type = PDO::PARAM_INT;
          }

          $stmt->bindValue($indice++, $linha, $type);
        endforeach;
      }

      $stmt->execute();
      $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

      return $resultados;
    }
    catch (Exception $e){
      $sql_formatado = $sql;
      foreach ($parametros as $valor) :
        $valor_formatado = strip_tags(is_int($valor) ? $valor : $valor);
        $sql_formatado = preg_replace('/\?/', $valor_formatado, $sql_formatado, 1);
      endforeach;

      $log_mensagem = str_repeat("-", 150) . PHP_EOL . PHP_EOL;
      $log_mensagem .= date('Y-m-d H:i:s') . PHP_EOL . PHP_EOL;
      $log_mensagem .= 'Consulta: ' . $sql_formatado . PHP_EOL . PHP_EOL;
      $log_mensagem .= 'Erro: ' . $e->getMessage() . PHP_EOL . PHP_EOL;

      error_log($log_mensagem, 3, '../app/logs/' . date('Y-m-d') . '.log');

      return false;
    }
  }
}