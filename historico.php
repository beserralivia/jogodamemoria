<?php
session_start();

if (!isset($_SESSION['modo'])) {
    // Redireciona para a página inicial se o modo não estiver definido
    header("Location: index.php");
    exit();
}

require 'database.php'; // Conexão com o banco de dados

if (isset($_POST['finalizar'])) {
    // Captura os dados do jogo
    $tempo_total = $_POST['tempo_total'];
    $erros = $_POST['erros'];
    $pontuacao_jogador1 = $_POST['pontuacao_jogador1'];
    $pontuacao_jogador2 = $_POST['pontuacao_jogador2'] ?? null;
    $dificuldade = $_SESSION['dificuldade'];
    $jogador1 = $_SESSION['jogador1'];
    $jogador2 = $_SESSION['jogador2'] ?? null;

    // Insere os dados no banco de dados
    $sql = "INSERT INTO historico (tempo, dificuldade, jogador1, pontos_jogador1, jogador2, pontos_jogador2) 
            VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isssii", $tempo_total, $dificuldade, $jogador1, $pontuacao_jogador1, $jogador2, $pontuacao_jogador2);

    if ($stmt->execute()) {
        echo "Dados do jogo salvos com sucesso!";
    } else {
        echo "Erro ao salvar os dados: " . $conn->error;
    }

    $stmt->close();
    $conn->close();

    // Redirecionar para outra página, se necessário
    header("Location: historico.php");
    exit();
}
?>
