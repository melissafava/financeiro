<?php
require_once 'config.php';
require_once 'mensagens.php';

// Verificar se o usuário está logado
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

$usuario_id = $_SESSION['usuario_id'];
$usuario_nome = $_SESSION['usuario_nome'];

// Buscar Resumo Financeiro
$sql_receitas = "SELECT SUM(valor) as total FROM transacao 
                 WHERE id_usuario = :usuario_id AND tipo = 'receita'";
$stmt_receitas = $conn->prepare($sql_receitas);
$stmt_receitas->bindParam(':usuario_id', $usuario_id);
$stmt_receitas->execute();
$total_receitas = $stmt_receitas->fetch()['total'] ?? 0;

$sql_despesas = "SELECT SUM(valor) as total FROM transacao 
                 WHERE id_usuario = :usuario_id AND tipo = 'despesa'";
$stmt_despesas = $conn->prepare($sql_despesas);
$stmt_despesas->bindParam(':usuario_id', $usuario_id);
$stmt_despesas->execute();
$total_despesas = $stmt_despesas->fetch()['total'] ?? 0;

$saldo = $total_receitas - $total_despesas;

// Buscar últimas transações
$sql_ultimas = "SELECT t.*, c.nome as categoria_nome 
                FROM transacao t 
                LEFT JOIN categoria c ON t.id_categoria = c.id_categoria 
                WHERE t.id_usuario = :usuario_id 
                ORDER BY t.data_transacao DESC, t.id_transacao DESC 
                LIMIT 5";
$stmt_ultimas = $conn->prepare($sql_ultimas);
$stmt_ultimas->bindParam(':usuario_id', $usuario_id);
$stmt_ultimas->execute();
$ultimas_transacoes = $stmt_ultimas->fetchAll();


?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema Financeiro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="style.css">
</head>

<body class="cor">
    <?php include 'navbar.php'; ?>

    <div class="container">

        <?php exibir_mensagem(); ?>

        <h2>Resumo Financeiro</h2>

        <div class="btn-group" role="group" aria-label="Basic example">
            <div class="btn btn-success card-index">
                <h3>💵</h3>
                <h3>Receitas</h3>
                <p>R$ <?php echo number_format($total_receitas, 2, ',', '.') ?></p>
            </div>

            <div class="btn btn-danger card-index">
                <h3>💸</h3>
                <h3>Despesas</h3>
                <p>R$ <?php echo number_format($total_despesas, 2, ',', '.') ?></p>
            </div>

            <div class="btn btn-warning card-index">
                <h3>💰</h3>
                <h3>Saldo</h3>
                <p>R$ <?php echo number_format($saldo, 2, ',', '.') ?></p>
            </div>
        </div>
        <h2>Últimas Transações</h2>

        <?php if (count($ultimas_transacoes) > 0): ?>
            <table class="table">
                <thead>
                    <tr>
                        <th>Data</th>
                        <th>Descrição</th>
                        <th>Categoria</th>
                        <th>Tipo</th>
                        <th>Valor</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($ultimas_transacoes as $transacao): ?>
                        <tr>
                            <td><?php echo date('d/m/Y', strtotime($transacao['data_transacao'])); ?></td>
                            <td><?php echo htmlspecialchars($transacao['descricao']); ?></td>
                            <td><?php echo htmlspecialchars($transacao['categoria_nome'] ?? 'Sem categoria'); ?></td>
                            <td><?php echo ucfirst($transacao['tipo']); ?></td>
                            <td>R$ <?php echo number_format($transacao['valor'], 2, ',', '.'); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <br>

            <h5><a class="btn btn-outline-danger" href="transacoes_listar.php">Ver todas as transações</a></h5>
        <?php else: ?>
            <h5>Nenhuma transação cadastrada ainda.</h5>
            <p><a class="btn btn-outline-danger" href="transacoes_formulario.php">Cadastrar primeira transação</a></p>

        <?php endif; ?>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
    </div>
</body>

</html>