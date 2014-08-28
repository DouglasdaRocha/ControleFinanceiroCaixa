<?php
session_start(); // Inicia a sessão
unset($_SESSION['usuario']); // Deleta uma variável da sessão
session_destroy(); // Destrói toda sessão
header("Location: login.html");