<!DOCTYPE HTML>

<html>

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<title>Topení - Zahájení sezóny</title>
    
    <?php
        if (isset($_GET["action"])) {
            if ($_GET["action"]=='season_begin') {
                #assert values and begin the season here
            }
        }
    ?>
</head>

<body>

	<header>
        <h1>Začátek sezóny</h1>
	</header>

    <form method="get">
        <input type="hidden" name="action" value="season_begin"/>
        <input type="date" name="date" value="<?php echo date('Y-m-d', time());?>"><br>
        <input type="number" name="stock" value=1000 min=0 max=10000 step=10 style="width: 6em;">kg<br>
        <input type="number" name="stack" value=50 min=0 max=200 step=10 style="width: 6em;">kg<br>
        <input type="number" name="price" value=15000 min=0 step=any style="width: 6em;">kč<br>
        <input type="submit" value="Potvrdit">
    </form>
    
    <a href="menu.php">Zrušit</a>
    
</body>

</html>