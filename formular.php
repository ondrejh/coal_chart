<!DOCTYPE html>
<html>

<?php
    include "script/utils.php"
?>
    
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title>Title</title>
</head>

<body>
    <!--<header>
		<nav>
			<ul>
				<li>Your menu</li>
			</ul>
		</nav>
	</header>-->

    <?php
        if( $_GET["action"]) {
            if ( $_GET["action"] === "add") {
                if( $_GET["date"] || $_GET["time"] || $_GET["quantity"] ) {
                    $tdate = $_GET["date"];
                    $ttime = $_GET["time"];
                    $tquant = $_GET["quantity"];
                    echo $tdate. " ". $ttime. " přiloženo ". $tquant. "pytlů";
                    insert_entry($tdate. " ". $ttime. " ". $tquant. "p");
                    echo "</body></html>";
                    exit();
                }
            }
            if ( $_GET["action"] === "delete") {
                if( $_GET["tstamp"] || $_GET["quantity"] ) {
                    $tstamp = $_GET["tstamp"];
                    $tquant = $_GET["quantity"];
                    echo 'Mažu položku "'. $tstamp. ' '. $tquant. '"';
                    echo "</body></html>";
                    exit();
                }
            }
        }
    ?>

    <section>

		<article>
			<header><h2>Spotřeba</h2></header>
            <p><img src='graf.png'></p>
		</article>

		<article>
			<header>
				<h2>Přikládání</h2>
			</header>
            <form method="get">
                <input type="hidden" name="action" value="add">
                <!--
                Dne: <input type="date" name="date" value="1981-04-08">
                v: <input type="time" name="time" value="18:45">
                -->
                <?php
                    echo 'Dne: <input type="date" name="date" value="'. get_date(). '">';
                    echo 'v: <input type="time" name="time" value="'. get_time(). '">';
                ?>
                <input type="number" name="quantity" value=5 min=1 max=7 style="width: 2em;"> pytlů
                <input type="submit" value="Přiložit">
            </form>
		</article>
        
        <article>
            <header><h2>Záznamy</h2></header>
            <?php
                $entries = sort_entries_by_time(load_entries());
                foreach ($entries as $entry) {
                    echo '<form method="get">';
                    echo $entry[0]. ' '. $entry[1]. ' ';
                    echo '<input type="hidden" name="action" value="delete"> ';
                    echo '<input type="hidden" name="tstamp" value="'. $entry[0]. '"> ';
                    echo '<input type="hidden" name="quantity" value="'. $entry[1]. '"> ';
                    echo '<input type="submit" value="Smazat">';
                    echo '</form>'. PHP_EOL;
                    echo '<br>'. PHP_EOL;
                }
            ?>
        </article>

	</section>
    
</body> </html>