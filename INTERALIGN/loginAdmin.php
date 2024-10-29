<?php
session_start();

// Email, senha e nome predefinidos
$predefinido_email = 'admin@example.com';

// Alterar a senha pré-definida para um hash seguro
$predefinido_senha_hash = password_hash('senha123', PASSWORD_DEFAULT); // Gere este hash uma única vez e armazene-o corretamente

$predefinido_nome = 'Tati';

// Verifica se o formulário de login foi enviado
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login'])) {
    // Obtém os dados do formulário
    $email = $_POST['email'];
    $senha = $_POST['senha'];

    // Verifica se o email é correto e se a senha corresponde ao hash
    if ($email === $predefinido_email && password_verify($senha, $predefinido_senha_hash)) {
        // Define as informações do usuário na sessão
        $_SESSION['usuario_email'] = $email;
        $_SESSION['usuario_nome'] = $predefinido_nome;
        
        // Redireciona para a página protegida
        header("Location: dashboard.php");
        exit();
    } else {
        // Exibe uma mensagem de erro se o email ou senha estiverem incorretos
        $erro = "Email ou senha incorretos";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Formulário de Login</title>
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <link rel="stylesheet" href="loginAdmin.css">
</head>
<body>
<div class="container d-flex flex-column align-items-center justify-content-center min-vh-100">
  <header class="text-center mb-4">
    <p class="titulo">INTERALIGN</p>
  </header>
  <div class="col-md-6">
    <div class="card">
      <div class="card-body">
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
          <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" class="form-control" id="email" name="email" placeholder="Digite seu email" required>
          </div>
          <div class="form-group">
            <label for="password">Senha:</label>
            <input type="password" class="form-control" id="password" name="senha" placeholder="Digite sua senha" required>
          </div>
          <?php
          if (isset($erro)) {
              echo '<div class="alert alert-danger" role="alert">' . $erro . '</div>';
          }
          ?>
          <button type="submit" class="btn btn-primary btn-block" name="login">Entrar</button>
        </form>
      </div>
    </div>
  </div>
</div>

</body>
</html>
