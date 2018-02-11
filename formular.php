<!DOCTYPE html>
<html>

<?php
    include "script/utils.php"
?>
    
<head>
    <meta charset="utf-8"/>
    <script src="script/plotly-latest.min.js"></script>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <link rel="stylesheet" media="all" href="style/newstyle.css" />
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
                    $tstamp = $tdate. ' '. $ttime;
                    $tquant = $_GET["quantity"]. 'p';
                    $entry = array($tstamp, $tquant);
                    echo $tdate. " ". $ttime. " přiloženo ". $tquant. "ytlů";
                    insert_entry($entry);
                    echo "</body></html>";
                    exit();
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
                    echo "</body></html>";
                    exit();
                }
            }
        }
    ?>

    <section>

        <aside id='entries'>
			<header>
				<h2>Přikládání</h2>
			</header>
            <form method="get"><table><tr>
                <input type="hidden" name="action" value="add">
                <!--
                Dne: <input type="date" name="date" value="1981-04-08">
                v: <input type="time" name="time" value="18:45">
                -->
                <?php
                    echo "<td id='tab_date'>". '<input type="date" name="date" value="'. get_date(). '"></td>';
                    echo "<td id='tab_time'>". '<input type="time" name="time" value="'. get_time(). '"></td>';
                ?>
                <td id='tab_volume'><input type="number" name="quantity" value=5 min=1 max=7 style="width: 2em;"></td>
                <td><input type="submit" value="Přiložit"></td>
            </tr></table></form>
        
            <header><h2>Záznamy</h2></header>
            <table><!--<tr><th>Date</th><th>Amount</th><th>Action</th></tr>-->
            <?php
                $entries = sort_entries_by_time(load_entries());
                $first = true;
                foreach (array_reverse($entries) as $entry) {
                    if ($first) $first = false;
                    else echo "\t\t\t";
                    $kg = to_kg($entry[1]);
                    $dt = new DateTime($entry[0]);
                    echo "<tr><td id='tab_date'>". $dt->format('Y.m.d'). "</td><td id='tab_time'>". $dt->format('H:i'). "</td><td id='tab_volume'>". $kg. 'kg</td>';
                    echo '<td><form method="get">';
                    echo '<input type="hidden" name="action" value="delete"> ';
                    echo '<input type="hidden" name="tstamp" value="'. $entry[0]. '"> ';
                    echo '<input type="hidden" name="quantity" value="'. $entry[1]. '"> ';
                    echo '<input type="submit" value="Smazat">';
                    echo '</form></td></tr>'. PHP_EOL;
                }
            ?>
            </table>
        </aside>

		<article id='charts'>
            <header><h2>Spotřeba uhlí</h2></header>
            <div id='chart' />
            <?php
                $cdiv = calculate_div($entries);
                #foreach ($cdiv as $e) {
                #    echo date('Y.m.d H:i ', $e[0]). sprintf("%.01f",$e[1]). ' kg/day<br>'. PHP_EOL;
                #}
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