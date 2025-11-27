<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit;
}
$usuario = $_SESSION['usuario'];
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Painel - Marcenaria Magalhães</title>
<link rel="stylesheet" href="style.css">
</head>
<body>
<div class="app-wrap">
  <div class="painel">
    <h2 class="titulo">Bem-vindo, <?php echo $usuario; ?>!</h2>

    <div class="menu-links">
      <a href="cadastro_produto.php" class="botao botao-primario">Cadastro de Produtos</a>
      <a href="estoque.php" class="botao botao-secundario">Gestão de Estoque dos Produtos</a>
      <a href="logout.php" class="botao botao-danger">Logoff</a>
    </div>
  </div>
</div>
</body>
</html>
