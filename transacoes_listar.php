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

// Filtros
$filtro_tipo = $_GET['tipo'] ?? '';
$filtro_categoria = $_GET['categoria'] ?? '';

// Buscar todas as transações do usuário
$sql = "SELECT t.*, c.nome as categoria_nome 
        FROM transacao t 
        LEFT JOIN categoria c ON t.id_categoria = c.id_categoria 
        WHERE t.id_usuario = :usuario_id";

$params = [':usuario_id' => $usuario_id];

// Aplicar filtros
if ($filtro_tipo && in_array($filtro_tipo, ['receita', 'despesa'])) {
    $sql .= " AND t.tipo = :tipo";
    $params[':tipo'] = $filtro_tipo;
}

if ($filtro_categoria) {
    $sql .= " AND t.id_categoria = :categoria";
    $params[':categoria'] = $filtro_categoria;
}

$sql .= " ORDER BY t.data_transacao DESC, t.id_transacao DESC";

$stmt = $conn->prepare($sql);
$stmt->execute($params);
$transacoes = $stmt->fetchAll();

// Buscar categorias para o filtro
$sql_categorias = "SELECT * FROM categoria WHERE id_usuario = :usuario_id ORDER BY nome";
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
    <title>Transações - Sistema Financeiro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="style.css">
</head>

<body class="cor">
    <?php include 'navbar.php'; ?>
    <div class="container">

        <?php exibir_mensagem(); ?>

        <h2>Transações</h2>

        <div>
            <a class="btn" href="transacoes_formulario.php">Nova Transação</a>
        </div>
        <br>

        <h3>Filtros</h3>
        <form method="GET" action="transacoes_listar.php">

            <!-- TIPO -->
            <div>
                <label for="tipo">Tipo:</label>
                <select class="dropdown-toggle btn" id="tipo" name="tipo">
                    <option value="">Todos</option>
                    <option value="receita" <?php echo $filtro_tipo === 'receita' ? 'selected' : ''; ?>>Receita</option>
                    <option value="despesa" <?php echo $filtro_tipo === 'despesa' ? 'selected' : ''; ?>>Despesa</option>
                </select>

                <!-- CATEGORIA -->
                <label for="categoria">Categoria:</label>
                <select class="dropdown-toggle btn" id="categoria" name="categoria">
                    <option value="">Todas</option>
                    <?php foreach ($categorias as $categoria): ?>
                        <option value="<?php echo $categoria['id_categoria']; ?>"
                            <?php echo $filtro_categoria == $categoria['id_categoria'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($categoria['nome']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <br>
            <div>
                <button class="btn" type="submit">Filtrar</button>
                <a class="btn" href="transacoes_listar.php">Limpar Filtros</a>
            </div>
            <br>
        </form>

        <?php if (count($transacoes) > 0): ?>
            <table class="table">
                <thead>
                    <tr>
                        <th>Data</th>
                        <th>Descrição</th>
                        <th>Categoria</th>
                        <th>Tipo</th>
                        <th>Valor</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($transacoes as $transacao): ?>
                        <tr>
                            <td><?php echo date('d/m/Y', strtotime($transacao['data_transacao'])); ?></td>
                            <td><?php echo htmlspecialchars($transacao['descricao']); ?></td>
                            <td><?php echo htmlspecialchars($transacao['categoria_nome'] ?? 'Sem categoria'); ?></td>
                            <td><?php echo ucfirst($transacao['tipo']); ?></td>
                            <td>R$ <?php echo number_format($transacao['valor'], 2, ',', '.'); ?></td>
                            <td>
                                <a class="btn" href="transacoes_formulario.php?id=<?php echo $transacao['id_transacao']; ?>">Editar</a>
                                <a class="btn" href="transacoes_excluir.php?id=<?php echo $transacao['id_transacao']; ?>"
                                    onclick="return confirm('Tem certeza que deseja excluir esta transação?');">Excluir</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>Nenhuma transação encontrada.</p>
        <?php endif; ?>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
    </div>
    <br>
</body>

</html>