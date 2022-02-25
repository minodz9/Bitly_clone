<?php

// si shortcut exist 

if (isset($_GET['q'])) {

    $shortcut = htmlspecialchars($_GET['q']);

    $bdd = new PDO('mysql:host=localhost; dbname=bitly; charset=utf8', 'root', '');

    $requete = $bdd->prepare('SELECT count(*) AS x
                              FROM links
                              WHERE shortcut=? ');

    $requete->execute(array($shortcut));

    while ($result = $requete->fetch()) {

        if ($result['x'] != 1) {

            header('location: ./?error=true&message=adresse url non connue');
            exit();
        }
    }

    $requete = $bdd->prepare('SELECT* 
                              FROM links
                              WHERE shortcut = ? ');

    $requete->execute(array($shortcut));

    while ($result = $requete->fetch()) {

        header('location: ' . $result['url']);
        exit();
    }
}



//   Connexion à la base de données bilty 

try {

    $bdd = new PDO('mysql:host=localhost; dbname=bitly; charset=utf8', 'root', '')
        or die(print_r($bdd->errorInfo()));
} catch (Exception $e) {

    die('Erreur: ' . $e->getMessage());
}


//   Traitement formulaire URL 

if (isset($_POST['url'])) {

    $url = $_POST['url'];



    if (!filter_var($url, FILTER_VALIDATE_URL)) {


        header('location: ./?error=true&message=adresse url non valide');
        exit();
    }

    $shortcut = crypt($url, rand());

    // Vérifier si l'adresse à déja été raccourcie 

    $requete = $bdd->prepare('SELECT count(*) AS x 
                              FROM links 
                              WHERE url =?');

    $requete->execute(array($url));


    while ($result = $requete->fetch()) {

        if ($result['x'] != 0) {
            header('location: ./?error=true&message=Adresse déja raccourcie');
            exit();
        }
    }

    //  Vérifier l'insertion dans la bdd 


    $requete = $bdd->prepare('INSERT INTO links (url, shortcut) 
                              VALUES 
                              (?, ?) ');

    $requete->execute(array($url, $shortcut));

    header('location: ./?short=' . $shortcut);
    exit();
}



?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="./pictures/favico.png" type="image/png">
    <link rel="stylesheet" href="./design/default.css" class="css">
    <title>Raccourcisseur URL</title>
</head>

<body>

    <!-- Presentation sur URL  section 1-->

    <section id="hello">

        <!-- Container 1 -->

        <div class="container">

            <header>

                <img src="./pictures/logo.png" alt="logo bitly" id="logo">

            </header>

            <h1>Une url longue ? Raccourcissez-là!</h1>
            <h2>Largement meilleur et plus court que les autres</h2>

            <form action="" method="post">

                <input type="url" name="url" placeholder="Collez votre lien">
                <input type="submit" value="Raccourcir">
            </form>

            <?php


            if (isset($_GET['error']) && isset($_GET['message'])) {

            ?>

                <div class="center">

                    <div id="result">

                        <b> <?php echo htmlspecialchars($_GET['message']) ?> </b>

                    </div>
                </div>

            <?php } else if (isset($_GET['short'])) { ?>

                <div class="center">

                    <div id="result">

                        <b> URL RACCOURCIE : </b>
                        <a style="font-weight:100" href="http://localhost/Bitly_clone/?q=<?php echo htmlspecialchars($_GET['short']) ?>">http://localhost/Bitly_clone/?q=<?php echo htmlspecialchars($_GET['short']) ?></a>

                    </div>
                </div>


            <?php } ?>

        </div>

    </section>

    <!-- Presentation section 2 -->

    <section id="brands">

        <!-- Container 2  -->


        <div class="container">

            <h3>Ces marques nous font confiance </h3>
            <img src="./pictures/1.png" alt="1" class="picture">
            <img src="./pictures/2.png" alt="2" class="picture">
            <img src="./pictures/3.png" alt="3" class="picture">
            <img src="./pictures/4.png" alt="4" class="picture">
        </div>


    </section>

    <!-- Footer -->


    <footer>

        <div class="container">



            <img src="./pictures/logo2.png" alt="logo bitly 2" id="logo">
            <br>
            <span>2018 &copy; Bitly</span>
            <br>
            <a href="#">Contact</a> -
            <a href="#">A propos</a>


        </div>

    </footer>



</body>

</html>