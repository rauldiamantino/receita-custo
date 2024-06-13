<?php
require '../vendor/autoload.php';

use app\Models\UsuarioModel;

$usuario_model = new UsuarioModel();

// $params = [
//   'nome' => 'Jane',
//   'sobrenome' => 'Doe Silva',
//   'data_nascimento' => '10/01/1990',
//   'telefone' => '(11) 93433-2319',
//   'email' => 'jane@teste.com.br',
//   'senha' => '1234',
// ];

echo '<pre>';
print_r($usuario_model->buscar(['id' => 205], ['senha']));
echo '</pre>';