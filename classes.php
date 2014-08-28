<?php

abstract class BancoDados
{
	public abstract function conectar($host, $usuario, $senha, $banco, $porta);
	public abstract function consultar($sql);
}

class Postgres extends BancoDados
{
	public function conectar($host, $usuario, $senha, $banco, $porta)
	{
		$conexao = pg_connect("host=$host port=$porta dbname=$banco user=$usuario password=$senha")
 	    	or die ("Não foi possivel conectar ao servidor PostGreSQL");
	}
 
	public function consultar($sql)
	{
		$consulta = pg_query($sql);
		$result = array();

		while ($row = pg_fetch_row($consulta)) {
			$result[] = $row;
		}
    	return $result;
	}
}

class Tabela 
{
	private $bancoDados;

	public function __construct($bancoDados)
	{
		return $this->bancoDados = $bancoDados;
	}

	public function getBancoDados() 
	{
		return $this->bancoDados;
	}
}

class TabelaUsuario extends Tabela 
{
	public function buscarUsuario($login) 
	{
		$resultado = $this->getBancoDados()->consultar('select * from usuario');

		$usuarios = array();
		foreach($resultado as $chave => $valor) {
			$usuario = new Usuario();

			$usuario->setId($valor[0]);
			$usuario->setNome($valor[1]);
			$usuario->setSenha($valor[2]);

			$usuarios[] = $usuario;
		}
		return $usuarios;
	}
}

class TabelaCaixa extends Tabela 
{
	public function salvarCaixa($caixa) 
	{
		$sql = "INSERT INTO caixa (id_caixa, entrada, saida, total)
        VALUES (default, '". $caixa->getEntrada() ."', '". $caixa->getSaida() ."', '". $caixa->getTotal() ."');";
        $this->getBancoDados()->consultar($sql);
	}

	public function buscarCaixa() 
	{
		$resultado = $this->getBancoDados()->consultar('
			select 			
			entrada::money::numeric::float8,
			saida::money::numeric::float8, 
			total::money::numeric::float8
			from caixa 
			ORDER BY "id_caixa" DESC LIMIT 1')[0];

		return new Caixa($resultado[0], $resultado[1], $resultado[2]) ;
	}
}

class Validador 
{
	private $tabelaUsuario;

	public function __construct($tabelaUsuario)
	{
		$this->tabelaUsuario = $tabelaUsuario;
	}


	public function validar($usuario, $senha)
	{
		$result = $this->tabelaUsuario->buscarUsuario($usuario);

		if (!$result) {
		  	echo "Erro na consulta.<br>";
		  	exit;
		}

		foreach ($result as $value) {
			if ($usuario == $value->getNome() && $senha == $value->getSenha()) {
				session_start();
				$_SESSION['usuario'] = $usuario;
				header("Location: index.php");exit;
			}
		}
		header("Location: login.html");
	}
}

class Usuario 
{
	private $id;
	private $nome;
	private $senha;
	
	public function setNome($nome)
	{
		$this->nome = $nome;
	}

	public function setSenha($senha)
	{
		$this->senha = $senha;
	}

	public function setId($id)
	{
		$this->id = $id;
	}

	public function getId()
	{
		return $this->id;
	}

	public function getSenha()
	{
		return $this->senha;
	}

	public function getNome()
	{
		return $this->nome;
	}
}

class Caixa 
{
	private $id;
	private $entrada;
	private $saida;
	private $total;
	
	public function __construct($entrada, $saida, $total) {
		$this->entrada = $entrada;
		$this->saida = $saida;
		$this->total = $total;
	}

	public function setEntrada($entrada)
	{
		$this->entrada = $entrada;
	}

	public function setSaida($saida)
	{
		$this->saida = $saida;
	}

	public function setId($id)
	{
		$this->id = $id;
	}

	public function setTotal($total)
	{
		$this->total = $total;
	}

	public function getTotal()
	{
		return $this->total;
	}

	public function getId()
	{
		return $this->id;
	}

	public function getSaida()
	{
		return $this->saida;
	}

	public function getEntrada()
	{
		return $this->entrada;
	}
}