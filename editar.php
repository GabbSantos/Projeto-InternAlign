
<?php
// Inclui o arquivo de conexão com o banco de dados
require_once 'database/connect.php';
session_start();

// Verifica se o usuário está logado
if (!isset($_SESSION['usuario_email']) || !isset($_SESSION['usuario_nome'])) {
    // Redireciona para a página de login se não estiver logado
    header("Location: login.php");
    exit();
}

// Verifica se o ID do aluno foi passado via GET
if (!isset($_GET['id']) || empty($_GET['id'])) {
    // Se não foi passado o ID, redireciona de volta para a página principal
    header("Location: dashboard.php");
    exit();
}

// Obtém o ID do aluno da URL
$id = $_GET['id'];

// Consulta SQL para buscar os dados do aluno pelo ID
$stmt = $pdo->prepare("SELECT * FROM alunos WHERE id = ?");
$stmt->execute([$id]);
$aluno = $stmt->fetch(PDO::FETCH_ASSOC);

// Verifica se o aluno foi encontrado
if (!$aluno) {
    // Se o aluno não foi encontrado, redireciona de volta para a página principal
    header("Location: dashboard.php");
    exit();
}

// Verifica se o formulário de edição foi submetido
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update'])) {
    // Processa os dados do formulário e atualiza o aluno no banco de dados

    // Obtém os dados do formulário
    $nome = $_POST['nome'];
    $ra = $_POST['ra'];
    $email = $_POST['email'];
    $curso = $_POST['curso'];
    $cpf = $_POST['cpf'];
    $telefone = $_POST['telefone'];
    $situacao = $_POST['situacao'];
    $empresa = $_POST['empresa'];
    $data_inicio = $_POST['data_inicio'];
    $data_fim = $_POST['data_fim'];

    // Atualiza os dados do aluno no banco de dados
    $stmt = $pdo->prepare("UPDATE alunos SET nome = ?, ra = ?, email = ?, curso = ?, cpf = ?, telefone = ?, situacao = ?, empresa = ?, data_inicio = ?, data_fim = ? WHERE id = ?");
    $stmt->execute([$nome, $ra, $email, $curso, $cpf, $telefone, $situacao, $empresa, $data_inicio, $data_fim, $id]);

    // Redireciona de volta para a página principal após a edição
    header("Location: dashboard.php");
    exit();
}
if (isset($_POST['upload_foto']) && isset($_FILES['foto_aluno'])) {
    $foto = $_FILES['foto_aluno'];

    // Verifica erros no upload
    if ($foto['error'] !== UPLOAD_ERR_OK) {
        echo "<div class='alert alert-danger'>Erro ao enviar a foto!</div>";
    } else {
        // Limite de tamanho de arquivo (5MB)
        if ($foto['size'] > 5242880) {
            echo "<div class='alert alert-danger'>Arquivo muito grande! Máx: 5MB</div>";
        } else {
            // Pasta onde as fotos serão salvas
            $pasta = "fotos_perfil/";
            // Gera um nome único para a foto
            $nomeFoto = uniqid() . "." . strtolower(pathinfo($foto['name'], PATHINFO_EXTENSION));

            // Extensões permitidas
            $extensoesPermitidas = ['jpg', 'jpeg', 'png', 'gif'];
            $extensao = strtolower(pathinfo($foto['name'], PATHINFO_EXTENSION));

            // Verifica extensão permitida
            if (!in_array($extensao, $extensoesPermitidas)) {
                echo "<div class='alert alert-danger'>Extensão de arquivo não permitida!</div>";
            } else {
                // Caminho final do arquivo
                $caminhoFoto = $pasta . $nomeFoto;

                // Move o arquivo para o diretório
                if (move_uploaded_file($foto['tmp_name'], $caminhoFoto)) {
                    // Deleta foto antiga do servidor, se existir
                    if (!empty($aluno['foto']) && file_exists($aluno['foto'])) {
                        unlink($aluno['foto']);
                    }

                    // Atualiza o caminho da foto no banco de dados
                    $stmt = $pdo->prepare("UPDATE alunos SET foto = ? WHERE id = ?");
                    $stmt->execute([$caminhoFoto, $id]);

                    echo "<div class='alert alert-success'>Foto atualizada com sucesso!</div>";
                } else {
                    echo "<div class='alert alert-danger'>Falha ao salvar a foto!</div>";
                }
            }
        }
    }
}

// Função para excluir o arquivo
if (isset($_POST['delete'])) {
    $filePath = $_POST['path'];

    // Deletar arquivo do servidor
    if (file_exists($filePath)) {
        unlink($filePath);
    }

    // Deletar registro do banco de dados
    try {
        $stmt = $pdo->prepare("DELETE FROM arquivos WHERE path = :path AND aluno_id = :aluno_id");
        $stmt->execute([':path' => $filePath, ':aluno_id' => $id]);

        // Verifica se a exclusão foi bem-sucedida
        if ($stmt->rowCount() > 0) {
            echo "<div class='mensagem sucesso'>Arquivo excluído com sucesso!</div>";
        } else {
            echo "<div class='mensagem erro'>Erro ao excluir o arquivo do banco de dados.</div>";
        }
    } catch (PDOException $e) {
        echo "<div class='mensagem erro'>Erro ao excluir o arquivo do banco de dados: " . $e->getMessage() . "</div>";
    }
}

// Função para enviar arquivo
if (isset($_FILES['arquivo'])) {
    $arquivo = $_FILES['arquivo'];

    if ($arquivo['error']) {
        die("Falha ao enviar arquivo");
    }

    if ($arquivo['size'] > 5242880) {
        die("Arquivo muito grande! Max: 5MB");
    }

    $pasta = "arquivos/";
    $nomeDoArquivo = $arquivo['name'];
    $novoNomeDoArquivo = uniqid();
    $extensao = strtolower(pathinfo($nomeDoArquivo, PATHINFO_EXTENSION));

    $extensoesPermitidas = ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx'];
    if (!in_array($extensao, $extensoesPermitidas)) {
        die("Extensão de arquivo não permitida");
    }

    $path = $pasta . $novoNomeDoArquivo . "." . $extensao;

    $deu_certo = move_uploaded_file($arquivo["tmp_name"], $path);
    if ($deu_certo) {
        try {
            $stmt = $pdo->prepare("INSERT INTO arquivos (nome, path, aluno_id) VALUES (:nome, :path, :aluno_id)");
            $stmt->execute([
                ':nome' => $nomeDoArquivo,
                ':path' => $path,
                ':aluno_id' => $id
            ]);

            if ($stmt->rowCount() > 0) {
                echo "<div class='mensagem sucesso'>Arquivo enviado com sucesso!</div>";
            } else {
                echo "<div class='mensagem erro'>Erro ao inserir informações do arquivo no banco de dados.</div>";
            }
        } catch (PDOException $e) {
            echo "<div class='mensagem erro'>Erro ao inserir informações no banco de dados: " . $e->getMessage() . "</div>";
        }
    } else {
        echo "<div class='mensagem erro'>Falha ao mover o arquivo</div>";
    }
}

// Lógica para exibir os arquivos
try {
    $stmt = $pdo->prepare("SELECT nome, path FROM arquivos WHERE aluno_id = ?");
    $stmt->execute([$id]);
    $arquivos = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "<p>Erro ao buscar arquivos: " . $e->getMessage() . "</p>";
}
?>
<?php if (!empty($aluno['foto']) && file_exists($aluno['foto'])): ?>
    <div class="profile-container">
        <img src="<?php echo htmlspecialchars($aluno['foto']); ?>" alt="Foto do aluno" class="profile-image">
    </div>
<?php else: ?>
    <div class="profile-container">
        <img src="images/default-avatar.png" alt="Foto padrão" class="profile-image">
    </div>
<?php endif; ?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Dados do Aluno</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="editar.css">
</head>
<body>
<div class="profile-container">
    <!--<img src="<?php echo $caminhoImagem; ?>" alt="Foto do aluno" class="profile-image">-->
    <!--<h2><?php echo $nomeAluno; ?></h2>-->
    <form action="" method="post" enctype="multipart/form-data">
    <input type="file" name="foto_aluno" id="foto_aluno" accept="image/*">
    <button type="submit" name="upload_foto" class="btn btn-primary mt-2">Salvar Foto</button>
</form>
</div>
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <h2 class="my-4">Editar Aluno</h2>
            <div class="form-container">
                <form method="post" action="">
                    <div class="form-row">
                        <!-- Primeira coluna -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="nome">Nome:</label>
                                <input type="text" class="form-control" name="nome" id="nome" value="<?php echo htmlspecialchars($aluno['nome']); ?>">
                            </div>
                            <div class="form-group">
                                <label for="ra">RA:</label>
                                <input type="text" class="form-control" name="ra" id="ra" value="<?php echo htmlspecialchars($aluno['ra']); ?>">
                            </div>
                            <div class="form-group">
                                <label for="email">Email:</label>
                                <input type="email" class="form-control" name="email" id="email" value="<?php echo htmlspecialchars($aluno['email']); ?>">
                            </div>
                            <div class="form-group">
                                <label for="curso">Curso:</label>
                                <select class="form-control" name="curso" id="curso">
                                    <option value="GTI" <?php if ($aluno['curso'] == 'GTI') echo 'selected'; ?>>GTI</option>
                                    <option value="ADS" <?php if ($aluno['curso'] == 'ADS') echo 'selected'; ?>>ADS</option>
                                    <option value="MAR" <?php if ($aluno['curso'] == 'MAR') echo 'selected'; ?>>MAR</option>
                                    <option value="GE" <?php if ($aluno['curso'] == 'GE') echo 'selected'; ?>>GE</option>
                                    <option value="LOG" <?php if ($aluno['curso'] == 'LOG') echo 'selected'; ?>>LOG</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="cpf">CPF:</label>
                                <input type="text" class="form-control" name="cpf" id="cpf" value="<?php echo htmlspecialchars($aluno['cpf']); ?>">
                            </div>
                        </div>
                        <!-- Segunda coluna -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="telefone">Telefone:</label>
                                <input type="text" class="form-control" name="telefone" id="telefone" value="<?php echo htmlspecialchars($aluno['telefone']); ?>">
                            </div>
                            <div class="form-group">
                                <label for="situacao">Situação:</label>
                                <select class="form-control" name="situacao" id="situacao">
                                    <option value="empregado" <?php if ($aluno['situacao'] == 'empregado') echo 'selected'; ?>>Empregado</option>
                                    <option value="estudante" <?php if ($aluno['situacao'] == 'estudante') echo 'selected'; ?>>Estudante</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="empresa">Empresa:</label>
                                <input type="text" class="form-control" name="empresa" id="empresa" value="<?php echo htmlspecialchars($aluno['empresa']); ?>">
                            </div>
                            <div class="form-group">
                                <label for="data_inicio">Data de Início na Empresa:</label>
                                <input type="date" class="form-control" name="data_inicio" id="data_inicio" value="<?php echo htmlspecialchars($aluno['data_inicio']); ?>">
                            </div>
                            <div class="form-group">
                                <label for="data_fim">Data de Fim na Empresa:</label>
                                <input type="date" class="form-control" name="data_fim" id="data_fim" value="<?php echo htmlspecialchars($aluno['data_fim']); ?>">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <button type="submit" name="update" class="btn btn-primary">Salvar Alterações</button>
                        <a href="dashboard.php" class="btn btn-secondary">Cancelar</a>
                    </div>
                </form>

                <form method="POST" enctype="multipart/form-data" action="">
                    <div class="form-group-documento">
                        <label for="arquivo">Adicionar Documento</label>
                        <input name="arquivo" type="file" class="form-control-file">
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">Salvar Documento</button>
                    </div>
                </form>

                <!-- Mensagens de sucesso e erro -->
                <?php if (isset($mensagem_sucesso)): ?>
                    <div class="alert alert-success"><?php echo $mensagem_sucesso; ?></div>
                <?php endif; ?>
                <?php if (isset($mensagem_erro)): ?>
                    <div class="alert alert-danger"><?php echo $mensagem_erro; ?></div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <?php if (!empty($arquivos)): ?>
                <h2 class="my-4">Arquivos enviados:</h2>
                <div class="file-list">
                    <?php foreach ($arquivos as $arquivo): ?>
                        <div class="file-item">
                            <?php if (file_exists($arquivo['path'])): ?>
                                <a href="<?php echo htmlspecialchars($arquivo['path']); ?>" target="_blank">
                                    <?php $ext = pathinfo($arquivo['path'], PATHINFO_EXTENSION); ?>
                                    <?php if ($ext === 'pdf'): ?>
                                        <img src="icons/pdf-icon.png" class="thumbnail" alt="PDF">
                                    <?php elseif ($ext === 'doc' || $ext === 'docx'): ?>
                                        <img src="icons/word-icon.png" class="thumbnail" alt="Word Document">
                                    <?php else: ?>
                                        <img src="icons/file-icon.png" class="thumbnail" alt="File">
                                    <?php endif; ?>
                                </a>
                            <?php endif; ?>
                            <a href="<?php echo htmlspecialchars($arquivo['path']); ?>" download="<?php echo htmlspecialchars($arquivo['nome']); ?>"><?php echo htmlspecialchars($arquivo['nome']); ?></a>
                            <form method="POST" action="" onsubmit="return confirm('Tem certeza que deseja excluir este arquivo?');">
                                <input type="hidden" name="path" value="<?php echo htmlspecialchars($arquivo['path']); ?>">
                                <button type="submit" name="delete" class="btn btn-danger btn-sm">Excluir</button>
                            </form>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="no-files-message">Nenhum arquivo enviado até o momento.</div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Adicionando o jQuery e o JavaScript do Bootstrap -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>