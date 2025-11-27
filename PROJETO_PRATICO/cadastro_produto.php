<?php
// (mantive todo o PHP exatamente como você enviou)
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit;
}
include('conexao.php');

$msg = "";
$tipoMsg = "";

if (isset($_POST['salvar'])) {
    $id = $_POST['id_produto'];
    $nome = trim($_POST['nome']);
    $descricao = trim($_POST['descricao']);
    $categoria = trim($_POST['categoria']);
    $unidade = trim($_POST['unidade']);
    $minimo = (int)$_POST['minimo'];
    $quantidade = (int)$_POST['quantidade'];

    if (!empty($id)) {
        $sql = "UPDATE produtos SET 
                        nome='$nome', 
                        descricao='$descricao', 
                        categoria='$categoria',
                        unidade_medida='$unidade', 
                        quantidade_minima='$minimo'
                    WHERE id_produto=$id";
        $acao = "atualizado";
    } else {
        $sql = "INSERT INTO produtos 
                (nome, descricao, categoria, unidade_medida, quantidade_minima, quantidade_atual)
                VALUES ('$nome','$descricao','$categoria','$unidade','$minimo','$quantidade')";
        $acao = "cadastrado";
    }

    if ($conn->query($sql)) {
        $msg = "Produto $acao com sucesso!";
        $tipoMsg = "sucesso";
    } else {
        $msg = "Erro ao salvar o produto.";
        $tipoMsg = "erro";
    }

    $produtoEdit = [
        'id_produto' => '',
        'nome' => '',
        'descricao' => '',
        'categoria' => '',
        'unidade_medida' => '',
        'quantidade_minima' => '',
        'quantidade_atual' => ''
    ];
}

if (isset($_GET['excluir'])) {
    $id = $_GET['excluir'];
    if ($conn->query("DELETE FROM produtos WHERE id_produto=$id")) {
        $msg = "Produto excluído com sucesso!";
        $tipoMsg = "sucesso";
    } else {
        $msg = "Erro ao excluir produto.";
        $tipoMsg = "erro";
    }
}

$busca = isset($_GET['busca']) ? $_GET['busca'] : '';
$sql = "SELECT * FROM produtos WHERE nome LIKE '%$busca%'";
$result = $conn->query($sql);

$produtoEdit = [
    'id_produto' => '',
    'nome' => '',
    'descricao' => '',
    'categoria' => '',
    'unidade_medida' => '',
    'quantidade_minima' => '',
    'quantidade_atual' => ''
];

if (isset($_GET['editar'])) {
    $idEditar = $_GET['editar'];
    $query = $conn->query("SELECT * FROM produtos WHERE id_produto=$idEditar");
    if ($query->num_rows > 0) {
        $produtoEdit = $query->fetch_assoc();
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Cadastro de Produtos</title>
<link rel="stylesheet" href="style.css">
</head>
<body>
<div class="app-wrap">
  <div class="container-prod">
    <h2>Cadastro de Produtos</h2>

    <?php if (!empty($msg)): ?>
      <div class="msg <?= $tipoMsg ?>"><?= $msg ?></div>
    <?php endif; ?>

    <!-- Busca -->
    <div class="busca-box">
      <form method="get">
        <input type="text" name="busca" placeholder="Buscar produto..." value="<?= htmlspecialchars($busca) ?>">
        <button type="submit">Buscar</button>
      </form>
    </div>

    <!-- Tabela -->
    <div class="table-box">
      <table>
        <thead>
          <tr><th>ID</th><th>Nome</th><th>Categoria</th><th>Qtd Atual</th><th>Ações</th></tr>
        </thead>
        <tbody>
        <?php if ($result->num_rows > 0): ?>
          <?php while($p = $result->fetch_assoc()): ?>
            <tr>
              <td><?= $p['id_produto'] ?></td>
              <td><?= htmlspecialchars($p['nome']) ?></td>
              <td><?= htmlspecialchars($p['categoria']) ?></td>
              <td><?= $p['quantidade_atual'] ?></td>
              <td class="acoes">
                <a href="?editar=<?= $p['id_produto'] ?>">🖋️</a>
                <a href="?excluir=<?= $p['id_produto'] ?>" onclick="return confirm('Deseja realmente excluir este produto?')">❌</a>
              </td>
            </tr>
          <?php endwhile; ?>
        <?php else: ?>
          <tr><td colspan="5">Nenhum produto encontrado.</td></tr>
        <?php endif; ?>
        </tbody>
      </table>
    </div>

    <hr>

    <h3><?= $produtoEdit['id_produto'] ? "Editar Produto" : "Adicionar Novo Produto" ?></h3>

    <form method="post" class="form-cadastro">
      <input type="hidden" name="id_produto" value="<?= $produtoEdit['id_produto'] ?>">
      <input type="text" name="nome" placeholder="Nome" value="<?= htmlspecialchars($produtoEdit['nome']) ?>" required>
      <input type="text" name="descricao" placeholder="Descrição" value="<?= htmlspecialchars($produtoEdit['descricao']) ?>">
      <input type="text" name="categoria" placeholder="Categoria" value="<?= htmlspecialchars($produtoEdit['categoria']) ?>">
      <input type="text" name="unidade" placeholder="Unidade (ex: saco, lata...)" value="<?= htmlspecialchars($produtoEdit['unidade_medida']) ?>">
      <input type="number" name="minimo" placeholder="Qtd Mínima" value="<?= htmlspecialchars($produtoEdit['quantidade_minima']) ?>" required>

      <input type="number"
             name="quantidade"
             placeholder="Qtd Atual"
             value="<?= htmlspecialchars($produtoEdit['quantidade_atual']) ?>"
             <?= $produtoEdit['id_produto'] ? 'readonly' : '' ?>
             required>



             <center>
    <button type="submit" name="salvar">Salvar</button>
</center>

      
    </form>

    <div class="voltar-container">
    <a href="index.php" class="btn-voltar">Voltar</a>
</div>
  </div>
</div>
</body>
</html>
