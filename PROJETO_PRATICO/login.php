<?php
// Processamento de login (mantive tudo igual)
session_start();
include('conexao.php');

if (isset($_POST['email'])) {
    $email = $_POST['email'];
    $senha = md5($_POST['senha']);
    $sql = "SELECT * FROM usuarios WHERE email='$email' AND senha='$senha'";
    $result = $conn->query($sql);
    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();
        $_SESSION['usuario'] = $user['nome'];
        $_SESSION['id_usuario'] = $user['id_usuario'];
        header("Location: index.php");
        exit;
    } else {
        $erro = "Usuário ou senha incorretos.";
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Login</title>
<link rel="stylesheet" href="style.css">
</head>
<body>
<div class="app-wrap">
  <div class="saudacao">
    <h1>Seja bem-vindo — faça login</h1>
  </div>

  <div class="container">
    <h2>Login - Marcenaria Magalhães</h2>

    <form method="post" class="login-form">
      <input type="email" name="email" placeholder="E-mail" required>
      <input type="password" name="senha" placeholder="Senha" required>
      <button type="submit">Entrar</button>
    </form>

    <?php if (isset($erro)) echo "<p class='erro'>$erro</p>"; ?>
  </div>
</div>
</body>
</html>
