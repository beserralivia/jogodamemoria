<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jogo da Memória</title>
    <link rel="stylesheet" href="styles.css">
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
</style>
<body>
    <header>
        <h1>Jogo da Memória</h1>
    </header>

    <main>
        <section id="menu">
            <h2>Selecione o Modo de Jogo</h2>
            <form action="jogo.php" method="post">
    <label for="modo">Modo de Jogo:</label>
    <select name="modo" id="modo">
        <option value="solo">Solo</option>
        <option value="dupla">Dupla</option>
    </select>
    
    <label for="dificuldade">Dificuldade:</label>
    <select name="dificuldade" id="dificuldade">
        <option value="facil">Fácil</option>
        <option value="medio">Médio</option>
        <option value="dificil">Difícil</option>
    </select>

    <button type="submit">Iniciar Jogo</button>
</form>

        </section>

        
    </main>
</body>
</html>
