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

// Verificar se está editando
$id_transacao = $_GET['id'] ?? null;
$transacao = null;

if ($id_transacao) {
    // Buscar transação para editar
    $sql = "SELECT * FROM transacao WHERE id_transacao = :id_transacao AND id_usuario = :usuario_id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id_transacao', $id_transacao);
    $stmt->bindParam(':usuario_id', $usuario_id);
    $stmt->execute();
    $transacao = $stmt->fetch();

    // Se não encontrou ou não pertence ao usuário, redireciona
    if (!$transacao) {
        set_mensagem('Transação não encontrada.', 'erro');
        header('Location: transacoes_listar.php');
        exit;
    }
}

// Buscar categorias do usuário
$sql_categorias = "SELECT * FROM categoria WHERE id_usuario = :usuario_id ORDER BY tipo, nome";
$stmt_categorias = $conn->prepare($sql_categorias);
$stmt_categorias->bindParam(':usuario_id', $usuario_id);
$stmt_categorias->execute();
$categorias = $stmt_categorias->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $transacao ? 'Editar' : 'Nova'; ?> Transação - Sistema Financeiro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="style.css">
</head>

<body class="cor">
    <?php include 'navbar.php'; ?>
    <div class="container">
        <button type="button" class="btn-close" aria-label="Close" onclick="history.back ()"></button>
        <h1>Sistema Financeiro Pessoal</h1>

        <?php exibir_mensagem(); ?>

        <h2><?php echo $transacao ? 'Editar' : 'Nova'; ?> Transação</h2>

        <?php if (count($categorias) === 0): ?>
            <p><strong>Atenção:</strong> Você precisa cadastrar pelo menos uma categoria antes de criar transações.</p>
            <p><a href="categorias_formulario.php">Cadastrar Categoria</a></p>
        <?php else: ?>
            <form action="transacoes_salvar.php" method="POST">
                <?php if ($transacao): ?>
                    <input type="hidden" name="id_transacao" value="<?php echo $transacao['id_transacao']; ?>">
                <?php endif; ?>

                <div>
                    <label for="descricao">Descrição:</label>
                    <input type="text" id="descricao" name="descricao"
                        value="<?php echo $transacao ? htmlspecialchars($transacao['descricao']) : ''; ?>"
                        required>
                </div>
                <br>

                <div>
                    <label for="valor">Valor:</label>
                    <input type="number" id="valor" name="valor" step="0.01" min="0.01"
                        value="<?php echo $transacao ? number_format($transacao['valor'], 2, '.', '') : ''; ?>"
                        required>
                </div>
                <br>

                <div>
                    <label for="data_transacao">Data:</label>
                    <input type="date" id="data_transacao" name="data_transacao"
                        value="<?php echo $transacao ? $transacao['data_transacao'] : date('Y-m-d'); ?>"
                        required>
                </div>
                <br>

                <div>
                    <label for="tipo">Tipo:</label>
                    <select id="tipo" name="tipo" required>
                        <option value="">Selecione...</option>
                        <option value="receita" <?php echo ($transacao && $transacao['tipo'] === 'receita') ? 'selected' : ''; ?>>Receita</option>
                        <option value="despesa" <?php echo ($transacao && $transacao['tipo'] === 'despesa') ? 'selected' : ''; ?>>Despesa</option>
                    </select>
                </div>
                <br>

                <div>
                    <label for="id_categoria">Categoria:</label>
                    <select id="id_categoria" name="id_categoria" required>
                        <option value="">Selecione...</option>
                        <?php foreach ($categorias as $categoria): ?>
                            <option value="<?php echo $categoria['id_categoria']; ?>"
                                <?php echo ($transacao && $transacao['id_categoria'] == $categoria['id_categoria']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($categoria['nome']) . ' (' . ucfirst($categoria['tipo']) . ')'; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <br>

                <div>
                    <button class="btn" type="submit">Salvar</button>
                    <a class="btn" href="transacoes_listar.php">Cancelar</a>
                </div>
                <br>
            </form>
        <?php endif; ?>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
    </div>
</body>

</html>