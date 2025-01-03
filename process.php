<?php
// Configuração do banco de dados
$host = 'localhost';
$user = 'root';
$password = '';
$database = 'cbs';

try {
    // Conexão com o banco de dados
    $conn = new mysqli($host, $user, $password, $database);

    if ($conn->connect_error) {
        throw new Exception("Falha na conexão: " . $conn->connect_error);
    }

    // Captura e validação dos dados do formulário
    $name = isset($_POST['name']) ? trim($_POST['name']) : null;
    $email = isset($_POST['email']) ? trim($_POST['email']) : null;
    $phone = isset($_POST['phone']) ? trim($_POST['phone']) : null;
    $cpf = isset($_POST['cpf']) ? trim($_POST['cpf']) : null;
    $password = isset($_POST['password']) ? $_POST['password'] : null;
    $confirm_password = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : null;

    // Validação básica dos campos
    if (!$name || !$email || !$phone || !$cpf || !$password || !$confirm_password) {
        throw new Exception("Todos os campos são obrigatórios.");
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception("Email inválido.");
    }

    if ($password !== $confirm_password) {
        throw new Exception("As senhas não coincidem.");
    }

    // Validação do CPF
    function validateCPF($cpf) {
        $cpf = preg_replace('/[^0-9]/', '', $cpf);
        if (strlen($cpf) !== 11 || preg_match('/^(\d)\1{10}$/', $cpf)) return false;

        for ($t = 9; $t < 11; $t++) {
            $d = 0;
            for ($c = 0; $c < $t; $c++) {
                $d += $cpf[$c] * (($t + 1) - $c);
            }
            $d = ((10 * $d) % 11) % 10;
            if ($cpf[$c] != $d) return false;
        }
        return true;
    }

    if (!validateCPF($cpf)) {
        throw new Exception("CPF inválido.");
    }

    // Criptografia da senha
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    // Inserção dos dados no banco
    $stmt = $conn->prepare("INSERT INTO users (name, email, phone, cpf, password, activation_code) VALUES (?, ?, ?, ?, ?, ?)");
    $activation_code = bin2hex(random_bytes(16));
    $stmt->bind_param("ssssss", $name, $email, $phone, $cpf, $hashed_password, $activation_code);

    if (!$stmt->execute()) {
        throw new Exception("Erro ao inserir os dados: " . $stmt->error);
    }

    // Envio do email de ativação
    $activation_link = "http://seu_dominio.com/activate.php?code=$activation_code";
    $subject = "Ativação de Conta";
    $message = "Olá, $name. Clique no link abaixo para ativar sua conta:\n\n$activation_link";
    $headers = "From: no-reply@seu_dominio.com\r\n";

    if (!mail($email, $subject, $message, $headers)) {
        throw new Exception("Erro ao enviar o email de ativação.");
    }

    echo "Cadastro realizado com sucesso! Verifique seu email para ativar a conta.";
} catch (Exception $e) {
    echo "Erro: " . $e->getMessage();
} finally {
    $conn->close();
}
?>
