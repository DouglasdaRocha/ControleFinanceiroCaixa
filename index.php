<?php
session_start();

if ($_SESSION['usuario']==NULL) {
	header("Location: login.html");
 } 

include "classes.php";

$bd = new Postgres();
$bd->conectar("localhost", "postgres", "postgres", "caixa", 5432); 

$tabelaCaixa = new TabelaCaixa($bd);
$caixaTotal = $tabelaCaixa->buscarCaixa();
$total = $caixaTotal->getTotal();

if (array_key_exists("entrada", $_POST) || array_key_exists("saida", $_POST)) {
	$total = $total + $_POST["entrada"] - $_POST["saida"];
	$caixa = new Caixa($_POST["entrada"], $_POST["saida"], $total);
	$tabelaCaixa->salvarCaixa($caixa);	
}

?>

<html>
	<head>
		<title> Caixa </title>
	</head>
	<body>
		<label>Bem vindo: <?php echo $_SESSION['usuario'];?></label>
		<center>
		   <h3>Caixa</h3>
		   <form method="POST" action="index.php">
			    <input type="Hidden" name="id_caixa" />
			    <label>Entrada:</label><input type="text" name="entrada" id="entrada"><br>
			    <input type="submit" value="Cadastrar" id="cadastrar" name="cadastrar">
			</form>
			<form method="POST" action="index.php">
			    <input type="Hidden" name="id_caixa" />
			    <label>Saida:</label><input type="text" name="saida" id="saida"><br>
			    <input type="submit" value="Cadastrar" id="cadastrar" name="cadastrar">
			</form>
			<label>Total em caixa:<?php echo $total;?></label>
		<center>
		<button><a href="logout.php">Sair</button>
	</body>
</html>