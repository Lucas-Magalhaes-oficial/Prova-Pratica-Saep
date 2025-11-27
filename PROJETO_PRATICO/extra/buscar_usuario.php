<?php
session_start();
require_once 'conexao.php';

//verifica se o usuario tem permissão de adm ou secretaria
if($_SESSION['perfil'] !=1 && $_SESSION['perfil']!=2){
    echo"<script>alert('Acesso Negado!');window.location.href='principal.php';</script>";
    exit();
}

// Definição das permissões por perfil
$permissoes = [
    // ADMINISTRADOR
    1 => [
        "Cadastrar" => ["cadastro_usuario.php", "cadastro_perfil.php", "cadastro_cliente.php", "cadastro_fornecedor.php", "cadastro_produto.php", "cadastro_funcionario.php"],
        "Buscar"    => ["buscar_usuario.php", "buscar_perfil.php", "buscar_cliente.php", "buscar_fornecedor.php", "buscar_produto.php", "buscar_funcionario.php"],
        "Alterar"   => ["alterar_usuario.php", "alterar_perfil.php", "alterar_cliente.php", "alterar_fornecedor.php", "alterar_produto.php", "alterar_funcionario.php"],
        "Excluir"   => ["excluir_usuario.php", "excluir_perfil.php", "excluir_cliente.php", "excluir_fornecedor.php", "excluir_produto.php", "excluir_funcionario.php"]
    ],
    // SECRETARIA
    2 => [
        "Cadastrar" => ["cadastro_cliente.php"],
        "Buscar"    => ["buscar_cliente.php", "buscar_fornecedor.php", "buscar_produto.php"],
        "Alterar"   => ["alterar_fornecedor.php", "alterar_produto.php"],
        "Excluir"   => ["excluir_produto.php"]
    ],
    // ALMOXARIFE
    3 => [
        "Cadastrar" => ["cadastro_fornecedor.php", "cadastro_produto.php"],
        "Buscar"    => ["buscar_cliente.php", "buscar_fornecedor.php", "buscar_produto.php"],
        "Alterar"   => ["alterar_fornecedor.php", "alterar_produto.php"],
        "Excluir"   => ["excluir_produto.php"]
    ],
    // USUARIO
    4 => [
        "Cadastrar" => ["cadastro_usuario.php"],
        "Buscar"    => ["buscar_produto.php"],
        "Alterar"   => ["alterar_cliente.php"]
    ]
];

// OBTENDO AS OPÇÕES DISPONIVEIS PARA O PERFIL DO USUARIO LOGADO
$id_perfil = (int) $_SESSION['perfil'];
$opcoes_menu = $permissoes[$id_perfil] ?? [];

$usuario = []; //inicializa a variavel para evitar erros

//se o formulario for enviado, busca o usuario pelo ID ou Nome
if($_SERVER["REQUEST_METHOD"]=="POST" && !empty($_POST['busca'])){
    $busca = trim($_POST['busca']);

    //verifica se a busca é um numero ou um nome
    if(is_numeric($busca)){
        $sql="SELECT * FROM usuario WHERE id_usuario = :busca ORDER BY nome ASC";
        $stmt=$pdo->prepare($sql);
        $stmt->bindParam(':busca',$busca, PDO::PARAM_INT);
    }else{
        $sql="SELECT * FROM usuario WHERE nome LIKE :busca_nome ORDER BY nome ASC";
        $stmt=$pdo->prepare($sql);
        $stmt->bindValue(':busca_nome',"$busca%", PDO::PARAM_STR);
    }
}else{
    $sql="SELECT * FROM usuario ORDER BY nome ASC";
    $stmt=$pdo->prepare($sql);
}
$stmt->execute();
$usuarios = $stmt->fetchALL(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buscar Usuario</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="styles.css">
</head>
<body>
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

    <h2>Lista de Usuarios</h2>
    <form action="buscar_usuario.php" method="POST" onsubmit="return validarBusca()">
        <label for="busca">Digite o ID ou Nome(opcional): </label>
        <input type="text" id="busca" name="busca">
        <button type="submit">Pesquisar</button>
    </form>
    <?php if(!empty($usuarios)):?>
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

                <?php foreach($usuarios as $usuario):?>
                    <tr>
                        <td><?=htmlspecialchars($usuario['id_usuario'])?></td>
                        <td><?=htmlspecialchars($usuario['nome'])?></td>
                        <td><?=htmlspecialchars($usuario['email'])?></td>
                        <td><?=htmlspecialchars($usuario['id_perfil'])?></td>
                        <td>
                            <a href="alterar_usuario.php?id=<?=htmlspecialchars($usuario['id_usuario'])?>"class="btn btn-success btn-sm">Alterar</a>
                            <a href="excluir_usuario.php?id=<?=htmlspecialchars($usuario['id_usuario'])?>"onclick="return confirm('Tem certeza que deseja excluir este usuario?')"class="btn btn-danger btn-sm">Excluir</a>
                        </td>
                    </tr>
                <?php endforeach;?>
                </table>
            </div>
        </div>
    <?php else:?>
        <p>Nenhum usuario encontrado.</p>
    <?php endif;?>
    <a href="principal.php" class="btn btn-primary btn-sm"> Voltar</a>

<br><br>
    <center>
        <address>Lucas Magalhães Sarmento | Estudante | Técnico de desenvolvimento de sistema</address>
    </center>
<script src="js/bootstrap.bundle.min.js"></script>
</body>
</html>
