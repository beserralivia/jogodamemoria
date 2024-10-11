<?php
session_start();
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "jogodamemoria";

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}

$tempo_total = time() - $_SESSION['tempo_inicio'];
$jogador1 = $_SESSION['jogador1'];
$pontos_jogador1 = $_SESSION['pontos_jogador1'];
$jogador2 = isset($_SESSION['jogador2']) ? $_SESSION['jogador2'] : null;
$pontos_jogador2 = isset($_SESSION['pontos_jogador2']) ? $_SESSION['pontos_jogador2'] : null;
$dificuldade = $_SESSION['dificuldade'];

$sql = "INSERT INTO historico (tempo, dificuldade, jogador1, pontos_jogador1, jogador2, pontos_jogador2) 
        VALUES ('$tempo_total', '$dificuldade', '$jogador1', '$pontos_jogador1', '$jogador2', '$pontos_jogador2')";

if ($conn->query($sql) === TRUE) {
    echo "Jogo salvo com sucesso!";
} else {
    echo "Erro ao salvar jogo: " . $conn->error;
}

$conn->close();
session_destroy();
?>
