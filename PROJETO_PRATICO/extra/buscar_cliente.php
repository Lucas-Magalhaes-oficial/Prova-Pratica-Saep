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

$cliente = []; //inicializa a variavel para evitar erros

//se o formulario for enviado, busca o usuario pelo ID ou Nome
if($_SERVER["REQUEST_METHOD"]=="POST" && !empty($_POST['busca'])){
    $busca = trim($_POST['busca']);

    //verifica se a busca é um numero ou um nome
    if(is_numeric($busca)){
        $sql="SELECT * FROM cliente WHERE id_cliente = :busca ORDER BY nome_cliente ASC";
        $stmt=$pdo->prepare($sql);
        $stmt->bindParam(':busca',$busca, PDO::PARAM_INT);
    }else{
        $sql="SELECT * FROM cliente WHERE nome_cliente LIKE :busca_nome ORDER BY nome_cliente ASC";
        $stmt=$pdo->prepare($sql);
        $stmt->bindValue(':busca_nome',"$busca%", PDO::PARAM_STR);
    }
}else{
    $sql="SELECT * FROM cliente ORDER BY nome_cliente ASC";
    $stmt=$pdo->prepare($sql);
}
$stmt->execute();
$clientes = $stmt->fetchALL(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buscar Cliente</title>
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

    <h2>Lista de Clientes</h2>
    <form action="buscar_cliente.php" method="POST" onsubmit="return validarBusca()">
        <label for="busca">Digite o ID ou Nome(opcional): </label>
        <input type="text" id="busca" name="busca">
        <button type="submit">Pesquisar</button>
    </form>
    <?php if(!empty($clientes)):?>
        <div class="container mt-4">
            <div class="table-responsive">
                <table class="table table-striped table-bordered table-hover text-center">
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>Email</th>
                        <th>Endereco</th>
                        <th>Telefone</th>
                        <th>Ações</th>
                    </tr>

                <?php foreach($clientes as $cliente):?>
                    <tr>
                        <td><?=htmlspecialchars($cliente['id_cliente'])?></td>
                        <td><?=htmlspecialchars($cliente['nome_cliente'])?></td>
                        <td><?=htmlspecialchars($cliente['email'])?></td>
                        <td><?=htmlspecialchars($cliente['endereco'])?></td>
                        <td><?=htmlspecialchars($cliente['telefone'])?></td>
                        <td>
                            <a href="alterar_cliente.php?id=<?=htmlspecialchars($cliente['id_cliente'])?>"class="btn btn-success btn-sm">Alterar</a>
                            <a href="excluir_cliente.php?id=<?=htmlspecialchars($cliente['id_cliente'])?>"onclick="return confirm('Tem certeza que deseja excluir este cliente?')"class="btn btn-danger btn-sm">Excluir</a>
                        </td>
                    </tr>
                <?php endforeach;?>
                </table>
            </div>
        </div>
    <?php else:?>
        <p>Nenhum cliente encontrado.</p>
    <?php endif;?>
    <a href="principal.php" class="btn btn-primary btn-sm"> Voltar</a>

<br><br>
    <center>
        <address>Lucas Magalhães Sarmento | Estudante | Técnico de desenvolvimento de sistema</address>
    </center>
<script src="js/bootstrap.bundle.min.js"></script>
</body>
</html>
