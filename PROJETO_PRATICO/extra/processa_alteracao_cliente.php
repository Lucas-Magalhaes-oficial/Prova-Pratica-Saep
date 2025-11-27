<?php
session_start();
require 'conexao.php';

if($_SESSION['perfil'] !=1){
    echo "<script>alert('Acesso Negado!');window.location.href='principal.php'</script>";
    exit();
}

if($_SERVER["REQUEST_METHOD"] == "POST"){
    $id_cliente = $_POST['id_cliente'];
    $nome_cliente = $_POST['nome'];
    $email = $_POST['email'];
    $endereco = $_POST['endereco'];
    $telefone = $_POST['telefone'];
    $nova_senha = !empty($_POST['nova_senha'])? password_hash($_POST['nova_senha'],PASSWORD_DEFAULT) : null;

    //atualiza os dados do cliente
    if($nova_senha){
        $sql = "UPDATE cliente SET nome_cliente=:nome_cliente,email=:email,endereco=:endereco,telefone=:telefone,senha=:senha WHERE id_cliente = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':senha',$nova_senha);
    }else{
        $sql="UPDATE cliente SET nome_cliente=:nome_cliente,email=:email,endereco=:endereco,telefone=:telefone WHERE id_cliente = :id";
        $stmt = $pdo->prepare($sql);
    }
    $stmt->bindParam(':nome_cliente',$nome_cliente);
    $stmt->bindParam(':email',$email);
    $stmt->bindParam(':endereco',$endereco);
    $stmt->bindParam(':telefone',$telefone);
    $stmt->bindParam(':id', $id_cliente);



    if($stmt->execute()){
        echo "<script>alert('Cliente atualizado com sucesso!');window.location.href='buscar_cliente.php';</script>";
    }else{
        echo "<script>alert('Erro ao atualizar cliente');window.location.href='alterar_cliente.php?id=$id_cliente';</script>";
    }
}
?>