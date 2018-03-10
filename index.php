<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8"/>
    <script src="script/plotly-latest.min.js"></script>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <link rel="stylesheet" media="all" href="style/newstyle.css" />
    <link rel="shortcut icon" href="img/favicon.ico" type="image/x-icon" />
    <title>Spotřeba uhlí</title>

    <?php
        include "script/utils.php";
    
        if (convert_filebased_to_sqlite()) {
            echo "<meta http-equiv='refresh' content='3;url=". basename($_SERVER['PHP_SELF'])."'/></head><body>";
            echo "System was updated to SQLite.";
            echo "</body></html>";
            exit();
        }
        
        if (isset($_GET["action"])) {
            echo "<meta http-equiv='refresh' content='3;url=". basename($_SERVER['PHP_SELF'])."'/></head><body>";
            if ( $_GET["action"] === "add") {
                if( $_GET["date"] || $_GET["time"] || $_GET["quantity"] ) {
                    $tdate = $_GET["date"];
                    $ttime = $_GET["time"];
                    $tstamp = $tdate. ' '. $ttime;
                    $tquant = $_GET["quantity"]. 'p';
                    $entry = array($tstamp, $tquant);
                    echo $tdate. " ". $ttime. " přiloženo ". $tquant. "ytlů";
                    insert_entry($entry);
                }
            }
            if ( $_GET["action"] === "delete") {
                if( $_GET["tstamp"] || $_GET["quantity"] ) {
                    $tstamp = $_GET["tstamp"];
                    $tquant = $_GET["quantity"];
                    $entry = array($tstamp, $tquant);
                    echo 'Mažu položku "'. $tstamp. ' '. $tquant. '"';
                    if (delete_entry($entry)) {
                        echo ' OK';
                    }
                    else {
                        echo " Can't find entry .. ERROR";
                    }
                }
            }
            if ( $_GET["action"] === "stock_add") {
                $tdate = $_GET["date"];
                $tqkg = $_GET["quantity"];
                $tprice = $_GET["price"];
                echo $tqkg. "kg celkem za ". $tprice. "kč přidávám na sklad .. ";
                echo stock_add($tqkg, $tprice, $tdate);
            }
            if ( $_GET["action"] === "stock_delete") {
                $tid = $_GET["id_entry"];
                $row = stock_delete($tid);
                echo 'Mažu položku '. $row['timestamp']. ' '. $row['amount']. 'kg ze skladu';
            }
            echo "</body></html>";
            exit();
        }
    ?>
</head>

<body>
    <!--<header>
		<nav>
			<ul>
				<li>Your menu</li>
			</ul>
		</nav>
	</header>-->

    <section>

        <aside id='entries'>

            <header><h2>Přikládání</h2></header>
            <form method="get"><input type="hidden" name="action" value="add"/><table><tr>
                <?php
                    echo "<td id='tab_date'>". '<input type="date" name="date" value="'. get_date(). '"></td>';
                    echo "<td id='tab_time'>". '<input type="time" name="time" value="'. get_time(). '"></td>';
                ?>
                <td id='tab_volume'><input type="number" name="quantity" value=5 min=1 max=7 style="width: 2em;"></td>
                <td><input type="submit" value="Přiložit"></td>
            </tr></table></form>

            <header><h2>Zásoby</h2></header>
            <form method="get"><input type="hidden" name="action" value="stock_add"/><table><tr>
                <?php
                    echo "<td id='tab_date'>". '<input type="date" name="date" value="'. get_date(). '"></td>';
                ?>                
                <td id='tab_volume'><input type="number" name="quantity" value=3000 min=0 max=10000 step=1000 style="width: 4em;">kg</td>
                <td id='tab_price'><input type="number" name="price" value=15000 min=0 step=any style="width: 6em;">kč</td>
                <td><input type="submit" value="Naskladnit"></td>
            </tr></table></form>

            <header><h2>Souhrn</h2></header>
            <table>
                <tr><td>Přikládáno</td><td class='ra'><span id='entries_count'></span></td><td>krát</td></tr>
                <tr><td>Celkem přiloženo</td><td class='ra'><span id='kgsum'></span></td><td>kg</td></tr>
            </table>
        
            <header><h2>Záznamy</h2></header>
            <table>
            <?php
                $db_entries = load_entries();
                $entries = array();
                while($row = $db_entries->fetchArray()) {
                    $entries[] = array($row['timestamp'], $row['amount']);
                }
                $first = true;
                $kgsum = 0;
                while($row = $db_entries->fetchArray()) {
                    if ($first) $first = false;
                    else echo "\t\t\t";
                    $kg = $row['amount'];
                    $kgsum += $kg;
                    $dt = new DateTime($row['timestamp']);//$entry[0]);
                    echo "<tr><td id='tab_date'>". $dt->format('Y.m.d'). "</td><td id='tab_time'>". $dt->format('H:i'). "</td><td id='tab_volume'>". $kg. 'kg</td>';
                    echo '<td><form method="get">';
                    echo '<input type="hidden" name="action" value="delete"> ';
                    echo '<input type="hidden" name="id_entry" value="'. $row['id']. '"> ';
                    echo '<input type="submit" value="Smazat">';
                    echo '</form></td></tr>'. PHP_EOL;
                }
            ?>
            </table>
            
            <header><h2>Nákupy</h2></header>
            <table>
            <?php
                $results = stock_read();
                while($row = $results->fetchArray()) {
                    echo "\t\t\t<tr>";
                    echo "<td id='tab_date'>". $row['timestamp']. "</td>";
                    echo "<td if='tab_amount'>". $row['amount']. "kg</td>";
                    echo "<td>". $row['price']. "kč</td>";
                    echo '<td><form method="get">';
                    echo '<input type="hidden" name="action" value="stock_delete">';
                    echo '<input type="hidden" name="id_entry" value="'. $row['id']. '">';
                    echo '<input type="submit" value="Smazat"></form></td></tr>'. PHP_EOL;
                }
            ?>
            </table>
            
            <script>document.getElementById('entries_count').innerHTML = '<?php echo count($entries); ?>'</script>
            <script>document.getElementById('kgsum').innerHTML = '<?php echo $kgsum; ?>'</script>
        </aside>

		<article id='charts'>
            <header><h2>Spotřeba uhlí</h2></header>
            <div id='chart'></div>
            <?php
                $cdiv = calculate_div($entries);
                echo "<script>". PHP_EOL;
                echo "\t\t\t\tvar data = [{x: [";
                $first = true;
                foreach ($cdiv as $e) {
                    if ($first) $first = false;
                    else echo ', ';
                    echo "'". date('Y-m-d H:i:s', $e[0]). "'";
                }
                echo "], y:[";
                $first = true;
                foreach ($cdiv as $e) {
                    if ($first) $first = false;
                    else echo ', ';
                    echo round($e[1],1);
                }
                echo "], type: 'scatter'}];". PHP_EOL;
                echo "\t\t\t\tPlotly.newPlot('chart', data, {margin: { t: 0 } });". PHP_EOL;
                echo "\t\t\t</script>". PHP_EOL;
            ?>
		</article>

	</section>
    
</body> </html>