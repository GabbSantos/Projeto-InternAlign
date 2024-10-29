<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Lista de Alunos - Impressão</title>
    <!-- Adicionando o Bootstrap -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        @media print {
            @page {
                size: portrait;
                margin: 1cm; /* Adicionei margem para evitar corte de conteúdo */
            }
            body {
                font-size: 12px; /* Ajuste de tamanho da fonte para impressão */
            }
            .container {
                width: 100%;
                margin: 0 auto;
            }
            table {
                width: 100%;
                table-layout: fixed; /* Distribui colunas de forma igualitária */
            }
            th, td {
                padding: 8px;
                word-wrap: break-word; /* Permite quebra de linha dentro das células */
            }
            h1 {
                text-align: center;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Lista de Alunos</h1>
        <table class="table table-bordered table-striped mt-4 text-center">
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>RA</th>
                    <th>Email</th>
                    <th>Curso</th>
                    <th>CPF</th>
                    <th>Endereço</th>
                    <th>Situação</th>
                    <th>Empresa</th>
                    <th>Data de Início na Empresa</th>
                </tr>
            </thead>
            <tbody>
                <!-- Aqui você precisa de código PHP para buscar e exibir os dados dos alunos -->
                <?php
                // Inclua o arquivo de conexão com o banco de dados
                require_once 'database/connect.php';

                // Consulta SQL para buscar todos os alunos
                $stmt = $pdo->query("SELECT * FROM alunos");

                // Loop para exibir os dados dos alunos
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    echo "<tr>";
                    echo "<td>" . $row['nome'] . "</td>";
                    echo "<td>" . $row['ra'] . "</td>";
                    echo "<td>" . $row['email'] . "</td>";
                    echo "<td>" . $row['curso'] . "</td>";
                    echo "<td>" . $row['cpf'] . "</td>";
                    echo "<td>" . $row['endereco'] . "</td>";
                    echo "<td>" . $row['situacao'] . "</td>";
                    echo "<td>" . $row['empresa'] . "</td>";
                    // Formata a data para dd/mm/yyyy
                    $data_inicio = date("d/m/Y", strtotime($row['data_inicio']));
                    echo "<td>" . $data_inicio . "</td>";
                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <!-- Adicionando o jQuery e o JavaScript do Bootstrap -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <!-- Script para imprimir a página -->
    <script>
        // Função para imprimir a página
        function imprimirPagina() {
            window.print(); // Chama a função de impressão do navegador
        }

        // Chama a função de impressão assim que a página é carregada
        window.onload = function() {
            imprimirPagina();
        };
    </script>
</body>
</html>