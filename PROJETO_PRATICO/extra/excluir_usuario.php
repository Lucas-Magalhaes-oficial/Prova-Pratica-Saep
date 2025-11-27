<?php
session_start();
require 'conexao.php';

if($_SESSION['perfil'] !=1){
    echo "<script>alert('Acesso Negado!');window.location.href='principal.php'</script>";
    exit();
}

//inicializa variavel para armazenar usuarios
$usuarios = [];

//busca todos os usuarios cadastrados em ordem alfabetica
$sql = "SELECT * FROM usuario ORDER BY nome ASC";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

//se um id for passado via get exclui o usuario
if(isset($_GET['id']) && is_numeric($_GET['id'])){
    $id_usuario = $_GET['id'];

    //exclui o usuario do banco de dados
    $sql = "DELETE FROM usuario WHERE id_usuario = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id',$id_usuario,PDO::PARAM_INT);

    if($stmt->execute()){
        echo "<script>alert('Usuario excluido com Sucesso!');window.location.href='excluir_usuario.php'</script>";
        
    }else{
        echo "<script>alert('Erro ao excluir o usuario!')</script>";
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>excluir usuario</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="styles.css">
</head>
<body>

<?php
$permissoes = [
    1 => [
        "Cadastrar" => ["cadastro_usuario.php", "cadastro_perfil.php", "cadastro_cliente.php", "cadastro_fornecedor.php", "cadastro_produto.php", "cadastro_funcionario.php"],
        "Buscar"    => ["buscar_usuario.php", "buscar_perfil.php", "buscar_cliente.php", "buscar_fornecedor.php", "buscar_produto.php", "buscar_funcionario.php"],
        "Alterar"   => ["alterar_usuario.php", "alterar_perfil.php", "alterar_cliente.php", "alterar_fornecedor.php", "alterar_produto.php", "alterar_funcionario.php"],
        "Excluir"   => ["excluir_usuario.php", "excluir_perfil.php", "excluir_cliente.php", "excluir_fornecedor.php", "excluir_produto.php", "excluir_funcionario.php"]
    ],
    2 => [
        "Cadastrar" => ["cadastro_cliente.php"],
        "Buscar"    => ["buscar_cliente.php", "buscar_fornecedor.php", "buscar_produto.php"],
        "Alterar"   => ["alterar_fornecedor.php", "alterar_produto.php"],
        "Excluir"   => ["excluir_produto.php"]
    ],
    3 => [
        "Cadastrar" => ["cadastro_fornecedor.php", "cadastro_produto.php"],
        "Buscar"    => ["buscar_cliente.php", "buscar_fornecedor.php", "buscar_produto.php"],
        "Alterar"   => ["alterar_fornecedor.php", "alterar_produto.php"],
        "Excluir"   => ["excluir_produto.php"]
    ],
    4 => [
        "Cadastrar" => ["cadastro_usuario.php"],
        "Buscar"    => ["buscar_produto.php"],
        "Alterar"   => ["alterar_cliente.php"]
    ]
];

$id_perfil = (int) $_SESSION['perfil'];
$opcoes_menu = $permissoes[$id_perfil] ?? [];

?>
<!-- MENU -->
<nav>
        <ul class="menu">
            <?php foreach ($opcoes_menu as $categoria => $arquivos): ?>
                <li class="dropdown">
                    <a href="#"><?= htmlspecialchars($categoria) ?></a>
                    <ul class="dropdown-menu">
                        <?php foreach ($arquivos as $arquivo): ?>
                            <li>
                                <a href="<?= htmlspecialchars($arquivo) ?>">
                                    <?= ucwords(str_replace('_', ' ', basename($arquivo, '.php'))) ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </li>
            <?php endforeach; ?>
        </ul>
    </nav>

    <h2>Excluir Usuario</h2>
    <?php if(!empty($usuarios)): ?>
        <div class="container mt-4">
            <div class="table-responsive">
                <table class="table table-striped table-bordered table-hover text-center">
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>Email</th>
                        <th>Perfil</th>
                        <th>Ações</th>
                    </tr>
                <?php foreach($usuarios as $usuario): ?>
                    <tr>
                        <td><?= htmlspecialchars($usuario['id_usuario'])?></td>
                        <td><?= htmlspecialchars($usuario['nome'])?></td>
                        <td><?= htmlspecialchars($usuario['email'])?></td>
                        <td><?= htmlspecialchars($usuario['id_perfil'])?></td>

                        <td>
                            <a href="excluir_usuario.php?id=<?= htmlspecialchars($usuario['id_usuario'])?>" onclick="return confirm('tem certeza que deseja excluir este usuario?')"class="btn btn-danger btn-sm">Excluir</a>
                            
                        </td>
                    </tr>
                <?php endforeach;?>
                </table>
            </div>
        </div>
        <?php else:?>
            <p>Nenhum usuario encontrado</p>
        <?php endif;?>

        <a href="principal.php" class="btn btn-primary btn-sm">Voltar</a>

        <br>
        <br>
        <center>
  <address>Lucas Magalhães Sarmento | Estudante | Técnico de desenvolvimento de sistema</address>
</center>
<script src="js/bootstrap.bundle.min.js"></script>
</body>
</html>