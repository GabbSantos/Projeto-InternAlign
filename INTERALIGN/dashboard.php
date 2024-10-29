<?php
session_start();
require_once 'database/connect.php';

// Verifica se o usuário está logado
if (!isset($_SESSION['usuario_email']) || !isset($_SESSION['usuario_nome'])) {
    header("Location: login.php");
    exit();
}
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Consulta SQL para buscar todos os alunos
$stmt = $pdo->prepare("SELECT * FROM alunos");
$stmt->execute();
$alunos = $stmt->fetchAll(PDO::FETCH_ASSOC); // Armazena todos os resultados na variável $alunos
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="dashboard.css">
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-light bg-light justify-content-center">
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item active">
                    <a class="nav-link" href="dashboard.php">Alunos</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="empresas.php">Empresas</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="opcoes_avancadas.php">Opções Avançadas</a>
                </li>
            </ul>
        </div>
    </nav>
<div class="header">
    <a href="logout.php" class="btn btn-danger">Logout</a>
</div>
<div class="lista">
<h1>Lista de Alunos</h1>
    <form class="pesquisa" method="post">
        <input type="text" class="form-control" name="searchTerm" placeholder="Filtrar Aluno" value="<?php echo htmlspecialchars($searchTerm); ?>">
        <button type="submit" class="btn btn-primary btn-filter" name="search">Filtrar</button>
    </form>
    <div class="buttons">
        <button type="button" class="btn btn-primary my-4" data-toggle="modal" data-target="#registroModal">Registrar Aluno</button>
        <a href="imprimir_alunos.php" class="btn btn-secondary ml-2 my-4" target="_blank">Imprimir</a>
    </div>
</div>
<div class="modal fade" id="registroModal" tabindex="-1" role="dialog" aria-labelledby="registroModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="registroModalLabel">Registro de Aluno</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form method="post" action="server/processarRegistro.php" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="nome">Nome:</label>
                        <input type="text" class="form-control" name="nome" id="nome">
                    </div>
                    <div class="form-group">
                        <label for="email">Email:</label>
                        <input type="email" class="form-control" name="email" id="email">
                    </div>
                    <div class="form-group">
                        <label for="cpf">CPF:</label>
                        <input type="text" class="form-control" name="cpf" id="cpf">
                    </div>
                    <div class="form-group">
                        <label for="curso">Curso:</label>
                        <select class="form-control" name="curso" id="curso">
                            <option value="GTI">GTI</option>
                            <option value="ADS">ADS</option>
                            <option value="MAR">MAR</option>
                            <option value="GE">GE</option>
                            <option value="LOG">LOG</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="telefone">Telefone:</label>
                        <input type="text" class="form-control" name="telefone" id="telefone">
                    </div>
                    <div class="form-group">
                        <label for="situacao">Situação:</label>
                        <select class="form-control" name="situacao" id="situacao">
                            <option value="empregado">Empregado</option>
                            <option value="estudante">Estudante</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="empresa">Empresa:</label>
                        <input type="text" class="form-control" name="empresa" id="empresa">
                    </div>
                    <button type="submit" class="btn btn-primary" name="register">Registrar</button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="table-responsive">
    <table class="table table-bordered table-striped mt-4 text-center" id="alunosTable">
        <thead>
            <tr>
                <th>Nome</th>
                <th>Email</th>
                <th>Curso</th>
                <th>CPF</th>
                <th>Telefone</th>
                <th>Situação</th>
                <th>Empresa</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
        <?php if (!empty($alunos)): ?>
    <?php foreach ($alunos as $aluno): ?>
        <tr>
            <td><?php echo htmlspecialchars($aluno['nome']); ?></td>
            <td><?php echo htmlspecialchars($aluno['email']); ?></td>
            <td><?php echo htmlspecialchars($aluno['curso']); ?></td>
            <td><?php echo htmlspecialchars($aluno['cpf']); ?></td>
            <td><?php echo htmlspecialchars($aluno['telefone']); ?></td>
            <td><?php echo htmlspecialchars($aluno['situacao']); ?></td>
            <td><?php echo htmlspecialchars($aluno['empresa']); ?></td>
            <td>
                <a href="editar.php?id=<?php echo $aluno['id']; ?>" class="btn btn-info">+Info</a>
                <a href="excluir_aluno.php?id=<?php echo $aluno['id']; ?>" class="btn btn-danger btn-excluir" onclick="return confirm('Tem certeza que deseja excluir este aluno?')">Excluir</a>
            </td>
        </tr>
    <?php endforeach; ?>
<?php else: ?>
    <tr>
        <td colspan="8">Nenhum aluno encontrado.</td>
    </tr>
<?php endif; ?>
        </tbody>
    </table>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>