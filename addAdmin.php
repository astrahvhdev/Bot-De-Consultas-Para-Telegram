<?php
// Conectar ao banco de dados
$host = 'localhost';  // Host do banco de dados
$dbname = 'bot_db';    // Nome do banco de dados
$user = 'root';        // Usuário do banco de dados
$password = 'Pericles!123';        // Senha do banco de dados

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo 'Erro na conexão: ' . $e->getMessage();
    exit;
}

// Função para adicionar um administrador no banco de dados
function adicionarAdmin($username) {
    global $pdo;

    // Verificar se o administrador já existe
    $stmt = $pdo->prepare("SELECT * FROM administradores WHERE username = :username");
    $stmt->bindParam(':username', $username);
    $stmt->execute();
    if ($stmt->rowCount() > 0) {
        return "O usuário {$username} já é um administrador.";
    }

    // Inserir novo administrador
    $stmt = $pdo->prepare("INSERT INTO administradores (username) VALUES (:username)");
    $stmt->bindParam(':username', $username);
    if ($stmt->execute()) {
        return "Administrador {$username} adicionado com sucesso.";
    } else {
        return "Erro ao adicionar o administrador.";
    }
}

// Recebe o nome de usuário via query string
if (isset($_GET['username'])) {
    $username = $_GET['username'];
    echo adicionarAdmin($username);
} else {
    echo "Erro: Nenhum nome de usuário fornecido.";
}
?>
