<!DOCTYPE HTML>

<html>

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<title>Topení - Přikládání</title>
    
    <?php
    include "script/utils.php";
    if (isset($_GET["date"]) && isset($_GET["time"]) && isset($_GET["amount"])) {
        #redirect to self in 3 sec
        echo "<meta http-equiv='refresh' content='3;url=". basename($_SERVER['PHP_SELF'])."'/></head><body>";
        #assert values and add entry
        echo "</head><body>" .$_GET["date"] ." ve " .$_GET["time"] ." přiloženo " .$_GET["amount"] ."kg ... ";
        if (isset($_GET["id"])) {
            echo "Editace</body></html>";
        }
        else { echo insert_entry($_GET["amount"], $_GET["date"] ." ". $_GET["time"]) ."</body></html>"; }
        exit();
    }
    ?>
</head>

<body>

	<header>
        <h1 id='header'>Přiložit</h1>
	</header>

    <form method="get">
        <?php if (isset($_GET["id_entry"])) {echo "<input type='hidden' name='id' value='". $_GET["id_entry"]. "'>";} ?>
        Datum: <input id="date" type="date" name="date" value="<?php echo date('Y-m-d', time());?>"><br>
        Čas: <input id="time" type="time" name="time" value="<?php echo date('H:i', time());?>"><br>
        Množství: <input id="amount" type="number" name="amount" value=120 min=0 max=200 step=5 style="width: 6em;">kg<br>
        <input id="submit" type="submit" value="Potvrdit">
    </form>
    
    <?php
    if (isset($_GET["action"]) && ($_GET["action"]=="edit") && isset($_GET["id_entry"])) {
        echo "<script>document.getElementById('header').innerHTML='Editace přiložení'</script>";
        echo "<script>document.getElementById('submit').value='Upravit'</script>";
        $entry = get_entry($_GET["id_entry"]);
        $date_time = explode(' ', $entry['timestamp']);
        var_dump($date_time);
        echo "<script>document.getElementById('date').value='". $date_time[0] ."'</script>";
        echo "<script>document.getElementById('time').value='". $date_time[1] ."'</script>";
        echo "<script>document.getElementById('amount').value=". $entry['amount'] ."</script>";
    }
    ?>
    
    <a href="menu.php">Zrušit</a>
    
</body>

</html>