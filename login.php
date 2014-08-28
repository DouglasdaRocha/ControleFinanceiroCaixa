<?php

include "classes.php";

$bd = new Postgres();
$bd->conectar("localhost", "postgres", "postgres", "caixa", 5432);


$validador = new Validador(new TabelaUsuario($bd));
$result = $validador->validar($_POST["usuario"], $_POST["senha"]);
?>


