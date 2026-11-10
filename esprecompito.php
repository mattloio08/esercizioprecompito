<?php
// Variabili iniziali
$prodotto = "";
$prezzo = "";
$sconto = "";

$erroreProdotto = "";
$errorePrezzo = "";
$erroreSconto = "";

$risultato = false;

// Controllo dell'invio del form
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Recupero dei dati
    $prodotto = isset($_POST["prodotto"]) ? trim($_POST["prodotto"]) : "";
    $prezzo = isset($_POST["prezzo"]) ? trim($_POST["prezzo"]) : "";
    $sconto = isset($_POST["sconto"]) ? trim($_POST["sconto"]) : "";

    // Controllo prodotto
    if ($prodotto == "") {
        $erroreProdotto = "Il prodotto è obbligatorio";
    } elseif (strlen($prodotto) > 30) {
        $erroreProdotto = "Il prodotto può avere massimo 30 caratteri";
    }

    // Controllo prezzo
    if ($prezzo == "") {
        $errorePrezzo = "Il prezzo è obbligatorio";
    } elseif (!is_numeric($prezzo) || floatval($prezzo) <= 0) {
        $errorePrezzo = "Il prezzo deve essere maggiore di 0";
    }

    // Controllo sconto
    if ($sconto == "") {
        $erroreSconto = "Lo sconto è obbligatorio";
    } elseif (filter_var($sconto, FILTER_VALIDATE_INT) === false ||
              intval($sconto) < 1 || intval($sconto) > 90) {
        $erroreSconto = "Lo sconto deve essere un numero intero da 1 a 90";
    }

    // Se non ci sono errori eseguo il calcolo
    if ($erroreProdotto == "" &&
        $errorePrezzo == "" &&
        $erroreSconto == "") {

        $prezzo = floatval($prezzo);
        $sconto = intval($sconto);

        // Calcolo del risparmio e del prezzo finale
        $risparmio = $prezzo * $sconto / 100;
        $prezzoFinale = $prezzo - $risparmio;

        // Determino la fascia dello sconto
        if ($sconto < 20) {
            $fascia = "PICCOLO";
            $colore = "#DCEBFF";
        } elseif ($sconto < 50) {
            $fascia = "BUONO";
            $colore = "#D8F3DC";
        } else {
            $fascia = "SUPER";
            $colore = "#FFE5B4";
        }

        $risultato = true;

        // Salvataggio nello storico
        $riga = date("d/m/Y H:i:s") . " | " .
                $prodotto . " | " .
                number_format($prezzo, 2, ".", "") . " | " .
                $sconto . "% | " .
                number_format($prezzoFinale, 2, ".", "") . " | " .
                number_format($risparmio, 2, ".", "") .
                PHP_EOL;

        file_put_contents("storico.txt", $riga, FILE_APPEND | LOCK_EX);
    }
}

// Funzione per visualizzare testo in sicurezza
function sicuro($testo) {
    return htmlspecialchars((string)$testo, ENT_QUOTES, "UTF-8");
}

// Lettura dello storico
$storico = [];

if (file_exists("storico.txt")) {
    $storico = file("storico.txt", FILE_IGNORE_NEW_LINES);
}
?>
