<!DOCTYPE HTML>

<html>

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<title>Topení - seznam</title>

    <?php
    include "script/utils.php";
    if (isset($_GET["action"]) && ($_GET["action"]=='delete') && isset($_GET["id_entry"])) {
        #redirect to self in 3 sec
        echo "<meta http-equiv='refresh' content='3;url=". basename($_SERVER['PHP_SELF'])."'/></head><body>";
        #assert values and delete entry
        $row = delete_entry($_GET["id_entry"]);
        echo "</head><body>Mažu položku " .$_GET["id_entry"] ." " .$row["timestamp"] ." " .$row["amount"] ."kg</body></html>";
        exit();
    }
    ?>
</head>

<body>

	<header>
		<nav>
			<ul>
				<li><a href="form_begin.php">Začít sezónu</a></li>
				<li><a href="form_add.php">Přiložit</a></li>
				<li><a href="form_buy.php">Naskladnit</a></li>
				<li><a href="form_end.php">Ukončit sezónu</a></li>
                <li><a href="entries.php">Seznam</a></li>
			</ul>
		</nav>
	</header>
	
	<section>
	
		<article>
			<header>
				<h2>Seznam přikládání</h2>
			</header>
			<table>
                <!--<tr><th>Kdy</th><th>Kolik [kg]</th></tr>-->
                <?php
                #include "script/utils.php";
                $db_entries = load_entries();
                while($row = $db_entries->fetchArray()) {
                    echo '<tr><td>' .$row['timestamp'] .'</td><td>' .$row['amount'] .'</td><td><form>';
                    echo '<input type="hidden" name="action" value="delete">';
                    echo '<input type="hidden" name="id_entry" value="'. $row['id']. '"> ';
                    echo '<input type="submit" value="Smazat"></form></td><td><form>';
                    echo '<input type="hidden" name="action" value="edit">';
                    echo '<input type="hidden" name="id_entry" value="'. $row['id']. '">';
                    echo '<input type="submit" value="Editovat"></form></td></tr>';
                }
                ?>
            </table>
		</article>
		
	</section>

	<aside>
		<h2>About section</h2>
		<p>Donec eu libero sit amet quam egestas semper. Aenean ultricies mi vitae est. Mauris placerat eleifend leo.</p>
	</aside>

	<footer>
		<p>Copyright 2009 Your name</p>
	</footer>

</body>

</html>