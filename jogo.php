<?php
session_start();

if (isset($_POST['modo']) && isset($_POST['dificuldade'])) {
    $_SESSION['modo'] = $_POST['modo'];
    $_SESSION['dificuldade'] = $_POST['dificuldade'];
    $_SESSION['tempo_inicio'] = time();  // Registrar início do jogo
    
    // Redireciona para a página onde o jogo será jogado
    header("Location: jogar.php");
} else {
    // Caso faltem dados, redireciona para a página inicial
    header("Location: index.php");
    exit();
}
?>
