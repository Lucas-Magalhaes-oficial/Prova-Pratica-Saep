<?php
session_start();
require_once 'conexao.php';

//verifica se o usuario tem permissao de adm
if($_SESSION['perfil'] !=1){
    echo "<script>alert('Acesso Negado');window.location.href='principal.php';</script>";
    exit();
}

//inicializa variáveis
$cliente = null;

if($_SERVER["REQUEST_METHOD"] == "POST"){
    if(!empty($_POST['busca_cliente'])){
        $busca = trim($_POST['busca_cliente']);

        //verifica se a busca é um numero (id) ou um nome
        if(is_numeric($busca)){
            $sql = "SELECT * FROM cliente WHERE id_cliente = :busca";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':busca',$busca,PDO::PARAM_INT);
        }else{
            $sql = "SELECT * FROM cliente WHERE nome_cliente LIKE :busca_nome";
            $stmt = $pdo->prepare($sql);
            //adiona o caractere de porcentagem para busca parcial
            $buscaLike = "%$busca%";
            $stmt->bindParam(':busca_nome', $buscaLike, PDO::PARAM_STR);
        }

        $stmt->execute();
        $cliente = $stmt->fetch(PDO::FETCH_ASSOC);

        //se o cliente não for encontrado, exibe um alerta
        if(!$cliente){
            echo "<script>alert('Cliente não encontrado');</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alterar Cliente</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="styles.css">
    <!-- certifique-se de que o javascript está sendo carregado corretamente-->
    <script:src="scripts.js"></script>
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

    <h2>Alterar Cliente</h2>
    <form action="alterar_cliente.php" method="POST" onsubmit="return validarAlterar()">
        <label for="busca_cliente">Digite o id ou nome do cliente</label>
        <input type="text" id="busca_cliente" name="busca_cliente" require onkeyup="buscarSugestoes()">

        <!-- div para exibir sugestões de clientes -->
         <div id="sugestoes"></div>
         <button type="submit">Buscar</button>
    </form>

    <?php if($cliente): ?>
        <!-- formulario para alterar cliente -->
         <form action="processa_alteracao_cliente.php" method="POST">
            <input type="hidden" name="id_cliente" value="<?=htmlspecialchars($cliente['id_cliente'])?>">

            <label for="nome">Nome:</label>
            <input type="text" id="nome" name="nome" value="<?=htmlspecialchars($cliente['nome_cliente'])?>"required>

            <label for="email">E-mail:</label>
            <input type="email" id="email" name="email" value="<?=htmlspecialchars($cliente['email'])?>"required>

            <label for="endereco">Endereco:</label>
            <input type="text" id="endereco" name="endereco" value="<?=htmlspecialchars($cliente['endereco'])?>"required>

            <label for="telefone">Telefone:</label>
            <input type="text" id="telefone" name="telefone" value="<?=htmlspecialchars($cliente['telefone'])?>"required>

            <!-- se o usuario logado for admin, exibir a opção de alterar senha -->
             <?php if ($_SESSION['perfil'] == 1): ?>
                <label for="nova_senha">Nova Senha</label>
                <input type="password" id="nova_senha" name="nova_senha">
            <?php endif; ?>
            <button type="submit">Alterar</button>
            <button type="reset">Cancelar</button>
         </form>
    <?php endif; ?>
    <a href="principal.php" class="btn btn-primary btn-sm">Voltar</a>

<br>
        <br>
    <center>
  <address>Lucas Magalhães Sarmento | Estudante | Técnico de desenvolvimento de sistema</address>
</center>
<script src="js/bootstrap.bundle.min.js"></script>
</body>
</html>