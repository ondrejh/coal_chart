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
    
        if (isset($_GET["action"])) {
            echo "<meta http-equiv='refresh' content='3;url=". basename($_SERVER['PHP_SELF'])."'/></head><body>";
            if ( $_GET["action"] === "add") {
                if( $_GET["date"] || $_GET["quantity"] ) {
                    $tdate = $_GET["date"];
                    $tquant = $_GET["quantity"];
                    echo $tdate. " má těhotný orangután ". $tquant. " kg";
                    insert_entry($tquant, $tdate);
                }
            }
            if ( $_GET["action"] === "delete") {
                if( $_GET["id_entry"] ) {
                    //$tstamp = $_GET["tstamp"];
                    //$tquant = $_GET["quantity"];
                    //$entry = array($tstamp, $tquant);
                    $id = $_GET["id_entry"];
                    echo 'Mažu položku "'. $id. '" .. ';
                    if (delete_entry($id)) {
                        echo ' OK';
                    }
                    else {
                        echo " Can't find entry .. ERROR";
                    }
                }
            }
            if ( $_GET["action"] === "target_add") {
                $tdate = $_GET["date"];
                $tqkg = $_GET["quantity"];
                echo $tqkg. " kg bude těhotný orangután vážit až porodí hrocha";
                echo target_add($tqkg, $tdate);
            }
            if ( $_GET["action"] === "target_delete") {
                $tid = $_GET["id_entry"];
                $row = target_delete($tid);
                echo 'Mažu položku '. $row['timestamp']. ' '. $row['amount']. ' kg z opičích cílů';
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

            <header><h2>Vážení</h2></header>
            <form method="get"><input type="hidden" name="action" value="add"/><table><tr>
                <?php
                    echo "<td id='tab_date'>". '<input type="date" name="date" value="'. get_date(). '"></td>';
                ?>
                <td id='tab_volume'><input type="number" name="quantity" value=95 min=10 max=150 step=0.1 style="width: 4em;">kg</td>
                <td><input type="submit" value="Zvážit"></td>
            </tr></table></form>

            <header><h2>Předsevzetí</h2></header>
            <form method="get"><input type="hidden" name="action" value="target_add"/><table><tr>
                <?php
                    echo "<td id='tab_date'>". '<input type="date" name="date" value="'. get_date(). '"></td>';
                ?>                
                <td id='tab_volume'><input type="number" name="quantity" value=95 min=10 max=150 step=1 style="width: 4em;">kg</td>
                <td><input type="submit" value="Doufat"></td>
            </tr></table></form>

            <header><h2>Souhrn</h2></header>
            <table>
                <tr><td>Maká na tom</td><td class='ra'><span id='days_count'></span></td><td>dní</td></tr>
                <tr><td>Váha zapisovaná</td><td class='ra'><span id='entries_count'></span></td><td>krát</td></tr>
                <tr><td>Celkem opice shodila</td><td class='ra'><span id='kgsum'></span></td><td>kg</td></tr>
                <tr><td>Zbývá</td><td class='ra'><span id='kgleft'></span></td><td>kg</td></tr>
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
                while($row = $db_entries->fetchArray()) {
                    if ($first) $first = false;
                    else echo "\t\t\t";
                    $kg = $row['amount'];
                    $dt = new DateTime($row['timestamp']);//$entry[0]);
                    echo "<tr><td id='tab_date'>". $dt->format('Y.m.d'). "</td><td id='tab_volume'>". $kg. 'kg</td>';
                    echo '<td><form method="get">';
                    echo '<input type="hidden" name="action" value="delete"> ';
                    echo '<input type="hidden" name="id_entry" value="'. $row['id']. '"> ';
                    echo '<input type="submit" value="Smazat">';
                    echo '</form></td></tr>'. PHP_EOL;
                }
                $all_entries = $entries;
            ?>
            </table>
            
            <header><h2>Cíle</h2></header>
            <table>
            <?php
                $results = target_read();
                while($row = $results->fetchArray()) {
                    echo "\t\t\t<tr>";
                    echo "<td id='tab_date'>". $row['timestamp']. "</td>";
                    echo "<td if='tab_amount'>". $row['amount']. "kg</td>";
                    echo '<td><form method="get">';
                    echo '<input type="hidden" name="action" value="target_delete">';
                    echo '<input type="hidden" name="id_entry" value="'. $row['id']. '">';
                    echo '<input type="submit" value="Smazat"></form></td></tr>'. PHP_EOL;
                    $all_entries[] = array($row['timestamp'], $row['amount']);
                }
            ?>
            </table>
            
            <!--<script>document.getElementById('stock_count').innerHTML = '<?php echo $stock_sum; ?>'</script>
            <script>document.getElementById('entries_count').innerHTML = '<?php echo count($entries); ?>'</script>
            <script>document.getElementById('kgsum').innerHTML = '<?php echo $kgsum; ?>'</script>
            <script>document.getElementById('stock_left').innerHTML = '<?php echo ($stock_sum - $kgsum); ?>'</script>-->
        </aside>

		<article id='charts'>
            <header><h2>Orangutan těhotný</h2></header>
            <div id='chart'></div><script>
                var t1col = '#3366ff';
                var t2col = '#ff6600';
                var trace1 = {
                    x: [<?php //1, 2, 3],
                        $first = true;
                        foreach ($entries as $e) {
                            if ($first)
                                $first = false;
                            else
                                echo ',';
                            echo "'". $e[0]. "'";
                        }?>],
                    y: [<?php //40, 50, 60],
                        $first = true;
                        foreach ($entries as $e) {
                            if ($first)
                                $first = false;
                            else
                                echo ',';
                            echo $e[1];
                        }?>],
                    name: 'hmotnost [kg]',
                    type: 'scatter',
                    line: {
                        color: t1col
                    }
                };
                var trace2 = {
                    x: [<?php //1, 2, 3],
                        $first = true;
                        foreach ($entries as $e) {
                            if ($first)
                                $first = false;
                            else
                                echo ',';
                            echo "'". $e[0]. "'";
                        }?>],
                    y: [<?php //40, 50, 60],
                        $first = true;
                        foreach ($entries as $e) {
                            if ($first)
                                $first = false;
                            else
                                echo ',';
                            echo $e[1];
                        }?>],
                    name: 'cíl [kg]',
                    type: 'scatter',
                    line: {
                        color: t2col
                    }
                };
                var data = [trace1, trace2];
                var layout = {
                    //title: 'Double Y Axis Example',
                    yaxis: {
                        title: 'hmotnost [kg]',
                        titlefont: {color: t1col},
                        tickfont: {color: t1col}
                    },
                    margin: { t: 0},
                    showlegend: false
                };
                Plotly.newPlot('chart', data, layout);
            </script>
		</article>
	</section>
    
</body> </html>