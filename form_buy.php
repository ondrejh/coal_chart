<!DOCTYPE HTML>

<html>

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<title>Topení - Nákup</title>
    
    <?php
    if (isset($_GET["date"]) && isset($_GET["amount"]) && isset($_GET["price"])) {
        #redirect to self in 3 sec
        echo "<meta http-equiv='refresh' content='3;url=". basename($_SERVER['PHP_SELF'])."'/></head><body>";
        #assert values and add stock
        echo "</head><body>" .$_GET["date"] ." naskladněno " .$_GET["amount"] ."kg za " .$_GET["price"] ."kč</body></html>";
        exit();
    }?>
</head>

<body>

	<header>
        <h1>Přiložit</h1>
	</header>

    <form method="get">
        Datum: <input type="date" name="date" value="<?php echo date('Y-m-d', time());?>"><br>
        Množství: <input type="number" name="amount" value=120 min=0 max=200 step=5 style="width: 6em;">kg<br>
        Cena: <input type="number" name="price" value=15000 min=0 step=any style="width: 6em;">kč<br>
        <input type="submit" value="Potvrdit">
    </form>
    
    <a href="menu.php">Zrušit</a>
    
</body>

</html>