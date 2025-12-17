<?php
require_once 'config.php';
require_once 'mensagens.php';

// Se já estiver logado, redireciona para o index
if (isset($_SESSION['usuario_id'])) {
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - Sistema Financeiro</title>
     <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="style.css">
</head>

<body class="cor">
   
    <div class="container">
        <h1>Sistema Financeiro Pessoal</h1>
        <h2>Cadastro de Usuário</h2>

        <?php exibir_mensagem(); ?>

        <form action="registrar.php" method="POST">
            <div>
                <label for="nome">Nome:</label>
                <input type="text" id="nome" name="nome" required>
            </div>
            <br>

            <div>
                <label for="email">E-mail:</label>
                <input type="email" id="email" name="email" required>
            </div>
            <br>

            <div>
                <label for="senha">Senha:</label>
                <input type="password" id="senha" name="senha" required minlength="6">
            </div>
            <br>

            <div>
                <label for="confirmar_senha">Confirmar Senha:</label>
                <input type="password" id="confirmar_senha" name="confirmar_senha" required minlength="6">
            </div>
            <br>

            <div>
                <button class="btn" type="submit">Cadastrar</button>
            </div>
        </form>
        <br>

        <p>Já tem conta? <a class="btn" href="login.php">Faça login aqui</a></p>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
</body>

</html>