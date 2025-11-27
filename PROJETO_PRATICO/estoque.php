<?php
// --- 1. Controle de Sessão e Acesso ---
session_start();
if (!isset($_SESSION['usuario']) || !isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit;
}
include('conexao.php');
$usuario = $_SESSION['usuario'];
$id_usuario = $_SESSION['id_usuario'];

$msg = "";
$tipoMsg = "";

if (isset($_POST['mover'])) {
    $id_produto = $_POST['produto'];
    $tipo = $_POST['tipo'];
    $quantidade = (int)$_POST['quantidade'];
    $data = $_POST['data'];

    if ($tipo == 'entrada') {
        $conn->query("UPDATE produtos SET quantidade_atual = quantidade_atual + $quantidade WHERE id_produto=$id_produto");
    } else {
        $conn->query("UPDATE produtos SET quantidade_atual = quantidade_atual - $quantidade WHERE id_produto=$id_produto");
    }

    if ($conn->query("INSERT INTO movimentacoes (id_produto, tipo, quantidade, data_movimentacao, id_usuario)
                      VALUES ($id_produto, '$tipo', $quantidade, '$data', $id_usuario)")) {

        $p = $conn->query("SELECT nome, quantidade_atual, quantidade_minima FROM produtos WHERE id_produto=$id_produto")->fetch_assoc();

        if ($p['quantidade_atual'] < $p['quantidade_minima']) {
            $msg = "⚠️ Estoque de {$p['nome']} abaixo do mínimo configurado!";
            $tipoMsg = "alerta";
        } else {
            $msg = "Movimentação registrada com sucesso!";
            $tipoMsg = "sucesso";
        }
    } else {
        $msg = "Erro ao registrar movimentação!";
        $tipoMsg = "erro";
    }
}

$result = $conn->query("SELECT * FROM produtos");
$produtos = [];
while ($row = $result->fetch_assoc()) {
    $produtos[] = $row;
}
usort($produtos, function($a, $b) {
    return strcasecmp($a['nome'], $b['nome']);
});

$historico = $conn->query("
    SELECT m.*, p.nome AS produto, u.nome AS usuario
    FROM movimentacoes m
    INNER JOIN produtos p ON m.id_produto = p.id_produto
    INNER JOIN usuarios u ON m.id_usuario = u.id_usuario
    ORDER BY m.data_movimentacao DESC, m.id_movimentacao DESC
");
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Gestão de Estoque - ConstruMais</title>
<link rel="stylesheet" href="style.css">
</head>
<body>
<div class="app-wrap">
  <div class="container-estoque">
    <h2>Gestão de Estoque</h2>

    <!-- Mensagem de feedback -->
    <?php if (!empty($msg)): ?>
      <div class="msg <?= $tipoMsg ?>"><?= $msg ?></div>
    <?php endif; ?>

    <!-- Formulário de movimentação -->
    <form method="post" class="form-movimentacao">
      <label>Produto:</label>
      <select name="produto" required>
        <?php foreach ($produtos as $p): ?>
          <option value="<?= $p['id_produto'] ?>"><?= htmlspecialchars($p['nome']) ?></option>
        <?php endforeach; ?>
      </select>

      <label>Tipo de Movimentação:</label>
      <select name="tipo" required>
        <option value="entrada">Entrada</option>
        <option value="saida">Saída</option>
      </select>

      <label>Quantidade:</label>
      <input type="number" name="quantidade" min="1" required>

      <label>Data:</label>
      <input type="date" name="data" required>

      <button type="submit" name="mover">Registrar Movimentação</button>
    </form>

    <hr>

    <h3>Produtos Cadastrados (Ordenados Alfabeticamente)</h3>

    <div class="table-box">
      <table>
        <thead>
          <tr>
            <th>ID</th><th>Nome</th><th>Categoria</th><th>Qtd Atual</th><th>Qtd Mínima</th>
          </tr>
        </thead>
        <tbody>
        <?php foreach ($produtos as $p): ?>
          <tr>
            <td><?= $p['id_produto'] ?></td>
            <td><?= htmlspecialchars($p['nome']) ?></td>
            <td><?= htmlspecialchars($p['categoria']) ?></td>
            <td><?= $p['quantidade_atual'] ?></td>
            <td><?= $p['quantidade_minima'] ?></td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    </div>

    <hr>

    <h3>Histórico de Movimentações</h3>

    <div class="table-box">
      <table>
        <thead>
          <tr><th>Data</th><th>Produto</th><th>Tipo</th><th>Quantidade</th><th>Usuário Responsável</th></tr>
        </thead>
        <tbody>
        <?php if ($historico->num_rows > 0): ?>
          <?php while ($mov = $historico->fetch_assoc()): ?>
            <tr>
              <td><?= date("d/m/Y", strtotime($mov['data_movimentacao'])) ?></td>
              <td><?= htmlspecialchars($mov['produto']) ?></td>
              <td><?= ucfirst($mov['tipo']) ?></td>
              <td><?= $mov['quantidade'] ?></td>
              <td><?= htmlspecialchars($mov['usuario']) ?></td>
            </tr>
          <?php endwhile; ?>
        <?php else: ?>
          <tr><td colspan="5">Nenhuma movimentação registrada ainda.</td></tr>
        <?php endif; ?>
        </tbody>
      </table>
    </div>

    <div class="voltar-estoque">
    <a href="index.php" class="btn-voltar">Voltar</a>
</div>
</div>
</body>
</html>
