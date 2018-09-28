<!DOCTYPE HTML>

<html>

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<title>Topení - Přikládání</title>
    
    <?php
        if (isset($_GET["action"])) {
            if ($_GET["action"]=='add') {
                #assert values and begin the season here
            }
        }
    ?>
</head>

<body>

	<header>
        <h1>Přiložit</h1>
	</header>

    <form method="get">
        <input type="hidden" name="action" value="add"/>
        <input type="date" name="date" value="<?php echo date('Y-m-d', time());?>"><br>
        <input type="time" name="time" value="<?php echo date('H:i', time());?>"><br>
        <input type="number" name="amount" value=1000 min=0 max=10000 step=10 style="width: 6em;">kg<br>
        <input type="submit" value="Potvrdit">
    </form>
    
    <a href="menu.php">Zrušit</a>
    
</body>

</html>