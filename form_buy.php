<!DOCTYPE HTML>

<html>

<head>
    <meta charset="utf-8"/>
    <script src="script/plotly-latest.min.js"></script>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <link rel="stylesheet" media="all" href="style/newstyle.css" />
    <link rel="shortcut icon" href="img/favicon.ico" type="image/x-icon" />
    <title>Spotřeba uhlí - Nákup</title>
    
    <?php
    include "script/utils.php";
    if (isset($_GET["date"]) && isset($_GET["amount"]) && isset($_GET["price"])) {
        #redirect to self in 3 sec
        echo "<meta http-equiv='refresh' content='3;url=index.php'/></head><body>";
        #assert values and add stock
        echo "</head><body>" .$_GET["date"] ." naskladněno " .$_GET["amount"] ."kg za " .$_GET["price"] ."kč";
        echo stock_add($_GET["amount"], $_GET["price"], $_GET["date"]);
        echo "</body></html>";
        exit();
    }
    ?>
</head>

<body class='form'>

	<header>
        <h1 id='header'>Nákup</h1>
	</header>

    <section><article>
    <form method="get">
        <?php if (isset($_GET["id_entry"])) {echo "<input type='hidden' name='id' value='". $_GET["id_entry"]. "'>";} ?>
        Datum:<br>
        <input class="entry" id="date" type="date" name="date" value="<?php echo date('Y-m-d', time());?>">
        Množství [kg]:<br>
        <input class="entry" id="amount" type="number" name="amount" value=30000 min=0 max=100000 step=10>
        Cena [kč]:<br>
        <input class="entry" id="price" type="number" name="price" value=15000 min=0 max=100000 step=1>
        <input class="button btnok" id="submit" type="submit" value="Naskladnit">
        <a class="button btncancel" href="index.php">Zrušit</a>
    </form>
    
    <?php
    if (isset($_GET["action"]) && ($_GET["action"]=="edit") && isset($_GET["id_entry"])) {
        echo "<script>document.getElementById('header').innerHTML='Editace přiložení'</script>";
        echo "<script>document.getElementById('submit').value='Upravit'</script>";
        $entry = get_stock_entry($_GET["id_entry"]);
        $date_time = explode(' ', $entry['timestamp']);
        var_dump($date_time);
        echo "<script>document.getElementById('date').value='". $date_time[0] ."'</script>";
        echo "<script>document.getElementById('time').value='". $date_time[1] ."'</script>";
        echo "<script>document.getElementById('amount').value=". $entry['amount'] ."</script>";
    }
    ?>
    
    </article></section>
</body></html>