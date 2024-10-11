<?php
session_start();

// Habilita a exibição de erros
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Conexão com o banco de dados
try {
    $pdo = new PDO("mysql:host=localhost;dbname=jogodamemoria", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Erro ao conectar: " . $e->getMessage();
    exit();
}

if (!isset($_SESSION['modo'])) {
    header("Location: index.php");
    exit();
}

// Coleta de dados
$modo = $_SESSION['modo'];
$dificuldade = $_SESSION['dificuldade'];
$jogador1 = $_SESSION['jogador1'] ?? 'Jogador 1';
$jogador2 = $_SESSION['jogador2'] ?? 'Jogador 2';

if (!isset($_SESSION['vez'])) {
    $_SESSION['vez'] = 1;
} else {
    $_SESSION['vez'] = ($_SESSION['vez'] == 1) ? 2 : 1;
}

$vez_jogador = ($_SESSION['vez'] == 1) ? $jogador1 : $jogador2;

switch ($dificuldade) {
    case 'facil':
        $num_pares = 6;
        break;
    case 'medio':
        $num_pares = 8;
        break;
    case 'dificil':
        $num_pares = 10;
        break;
    default:
        $num_pares = 6;
}

$icones = [
    "fa-apple-alt", "fa-lemon", "fa-anchor", "fa-bell", 
    "fa-bicycle", "fa-book", "fa-bug", "fa-camera",
    "fa-car", "fa-cat", "fa-cloud", "fa-dog", 
    "fa-feather", "fa-fish", "fa-gem", "fa-heart"
];

$icones_selecionados = array_slice($icones, 0, $num_pares);
$cartas = array_merge($icones_selecionados, $icones_selecionados);
shuffle($cartas);

if (isset($_POST['finalizar'])) {
    $erros = $_POST['contador-erros'];
    $acertos = $_POST['contador-acertos'];
    $tempo = $_POST['temporizador'];

    // Inserindo os dados no banco
    $stmt = $pdo->prepare("INSERT INTO jogos (jogador1, jogador2, erros, acertos, tempo, data) VALUES (?, ?, ?, ?, ?, NOW())");
    if ($stmt->execute([$jogador1, $jogador2, $erros, $acertos, $tempo])) {
        header("Location: resultado.php");
        exit();
    } else {
        echo "Erro ao salvar os dados. Tente novamente.";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jogo da Memória - Jogando</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<style>
    body {
        font-family: Arial, sans-serif;
        background: linear-gradient(45deg, #ff7e5f, #feb47b);
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        height: 100vh;
        margin: 0;
    }

    header {
        text-align: center;
        margin-bottom: 20px;
    }

    #info {
        text-align: center;
        margin-bottom: 20px;
    }

    #cartas {
        display: grid;
        gap: 10px;
        justify-content: center;
    }

    .carta {
        width: 100px;
        height: 150px;
        background-color: #fff;
        display: flex;
        justify-content: center;
        align-items: center;
        font-size: 50px;
        cursor: pointer;
        border: 2px solid #333;
        border-radius: 10px;
        transition: transform 0.3s ease;
    }

    .carta:hover {
        transform: scale(1.1);
    }

    .carta i {
        color: transparent;
    }

    .carta.virada i {
        color: #333;
    }

    #notificacao {
        margin-top: 20px;
        color: red;
    }
</style>
<body>
    <div class="topo">
        <a href="index.php" class="btn-voltar">Voltar ao Menu</a>
    </div>

    <header>
        <h1>Jogo da Memória - Modo <?php echo ucfirst($_SESSION['modo']); ?> - Dificuldade <?php echo ucfirst($_SESSION['dificuldade']); ?></h1>
    </header>

    <main>
        <div id="info">
            <p>Erros: <span id="contador-erros">0</span></p>
            <p>Acertos: <span id="contador-acertos">0</span></p>
            <p>Tempo: <span id="temporizador">0</span> segundos</p>
        </div>

        <!-- Exibe quem está jogando -->
        <h2>Vez de: <?php echo $vez_jogador; ?></h2>

        <button id="iniciar-jogo">Iniciar Jogo</button> <!-- Botão para iniciar o jogo -->

        <!-- Seção do jogo -->
<section id="game" style="display: none;"> <!-- Inicialmente escondido -->
    <div id="cartas" style="grid-template-columns: repeat(<?php echo ($dificuldade == 'facil') ? '4' : ($dificuldade == 'medio' ? '4' : '5'); ?>, 1fr);">
        <?php
        foreach ($cartas as $carta) {
            echo '<div class="carta">';
            echo '<i class="fas ' . $carta . '"></i>';
            echo '</div>';
        }
        ?>
    </div>
    <div id="notificacao"></div>
</section>


        <form id="finalizar-jogo" method="post">
            <input type="hidden" name="contador-erros" id="input-erros" value="0">
            <input type="hidden" name="contador-acertos" id="input-acertos" value="0">
            <input type="hidden" name="temporizador" id="input-temporizador" value="0">
            <button type="submit" name="finalizar" style="display: none;" id="botao-finalizar">Finalizar Jogo</button>
        </form>
    </main>
    <script>
    let tempo = 0;
    let contadorErros = 0;
    let contadorAcertos = 0;
    let primeiraCarta = null;
    let segundaCarta = null;
    let jogoAtivo = false; // Inicialmente o jogo não está ativo

    // Temporizador
    setInterval(function() {
        if (jogoAtivo) {
            tempo++;
            document.getElementById("temporizador").textContent = tempo;
        }
    }, 1000);

    // Função para iniciar o jogo
    document.getElementById("iniciar-jogo").addEventListener('click', function() {
        jogoAtivo = true; // Ativa o jogo
        document.getElementById("game").style.display = "block"; // Mostra o jogo
        this.style.display = "none"; // Esconde o botão de iniciar
        document.getElementById("botao-finalizar").style.display = "block"; // Mostra o botão de finalizar
    });

    // Lógica para girar cartas
    const cartas = document.querySelectorAll('.carta');
    cartas.forEach(carta => {
        carta.addEventListener('click', function() {
            if (!jogoAtivo || carta.classList.contains('virada')) return;
            carta.classList.add('virada');

            if (!primeiraCarta) {
                primeiraCarta = carta;
            } else {
                segundaCarta = carta;

                // Verifica se as cartas são iguais
                if (primeiraCarta.innerHTML === segundaCarta.innerHTML) {
                    contadorAcertos++; // Incrementa contador de acertos
                    document.getElementById("contador-acertos").textContent = contadorAcertos;

                    primeiraCarta = null;
                    segundaCarta = null;
                } else {
                    contadorErros++;
                    document.getElementById("contador-erros").textContent = contadorErros;

                    // Alterna a vez do jogador após uma jogada errada
                    jogoAtivo = false; // Desativa o jogo temporariamente

                    setTimeout(() => {
                        primeiraCarta.classList.remove('virada');
                        segundaCarta.classList.remove('virada');
                        primeiraCarta = null;
                        segundaCarta = null;

                        // Alterna a vez do jogador
                        <?php $_SESSION['vez'] = ($_SESSION['vez'] == 1) ? 2 : 1; ?>
                        document.querySelector('h2').textContent = "Vez de: <?php echo $_SESSION['vez'] == 1 ? $jogador1 : $jogador2; ?>"; // Atualiza a vez do jogador

                        jogoAtivo = true; // Ativa o jogo novamente
                    }, 1000);
                }
            }
        });
    });

    // Atualiza os valores dos inputs ao final do jogo
    function atualizarDadosNoFormulario(event) {
        event.preventDefault(); // Impede o envio padrão do formulário
        document.getElementById("input-erros").value = contadorErros;
        document.getElementById("input-acertos").value = contadorAcertos; // Adiciona contagem de acertos
        document.getElementById("input-temporizador").value = tempo;
        document.getElementById("finalizar-jogo").submit(); // Envia o formulário após atualizar os valores
    }

    // Chame esta função quando o jogo for finalizado
    document.getElementById("finalizar-jogo").addEventListener('submit', atualizarDadosNoFormulario);
    </script>
</body>
</html>
