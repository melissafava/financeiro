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
$id_categoria = $_GET['id'] ?? null;
$categoria = null;

if ($id_categoria) {
    // Buscar categoria para editar
    $sql = "SELECT * FROM categoria WHERE id_categoria = :id_categoria AND id_usuario = :usuario_id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id_categoria', $id_categoria);
    $stmt->bindParam(':usuario_id', $usuario_id);
    $stmt->execute();
    $categoria = $stmt->fetch();

    // Se não encontrou ou não pertence ao usuário, redireciona
    if (!$categoria) {
        set_mensagem('Categoria não encontrada.', 'erro');
        header('Location: categorias_listar.php');
        exit;
    }
}

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Categorias - Sistema Financeiro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="style.css">
</head>

<body class="cor">
    <?php include 'navbar.php'; ?>
    <div class="container">
        <button type="button" class="btn-close" aria-label="Close" onclick="history.back ()"></button>

        <h1>Sistema Financeiro</h1>

        <?php exibir_mensagem(); ?>

        <h2><?php echo $categoria ? 'Editar' : 'Nova'; ?> Categoria</h2>

        <form action="categorias_salvar.php" method="POST">
            <?php if ($categoria): ?>
                <input type="hidden" name="id_categoria" value="<?php echo $categoria['id_categoria']; ?>">
            <?php endif; ?>

            <div>
                <label for="nome">Nome:</label>
                <input type="text" id="nome" name="nome"
                    value="<?php echo $categoria ? htmlspecialchars($categoria['nome']) : ''; ?>"
                    required>
            </div>
            <br>

            <div>
                <label for="tipo">Tipo:</label>
                <select class="dropdown-toggle btn" id="tipo" name="tipo" required>
                    <option value="">Selecione...</option>
                    <option value="receita" <?php echo ($categoria && $categoria['tipo'] === 'receita') ? 'selected' : ''; ?>>Receita</option>
                    <option value="despesa" <?php echo ($categoria && $categoria['tipo'] === 'despesa') ? 'selected' : ''; ?>>Despesa</option>
                </select>
            </div>
            <br>
            <div>
                <button class="btn" type="submit">Salvar</button>
                <a class="btn" href="categorias_listar.php">Cancelar</a>
            </div>
        </form>
        <br>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
    </div>
</body>

</html>