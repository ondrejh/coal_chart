<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8"/>
    <script src="script/plotly-latest.min.js"></script>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <link rel="stylesheet" media="all" href="style/newstyle.css" />
    <link rel="shortcut icon" href="img/favicon.ico" type="image/x-icon" />
    <title>Spotřeba uhlí</title>
</head>

<body class='screen'>
    <?php
    include "script/utils.php";
    menu();
    ?>

    <section>

		<article id='charts'>
            <!--<header><h2>Spotřeba uhlí</h2></header>-->
            <?php
                $db_entries = load_entries();
                $entries = array();
                while($row = $db_entries->fetchArray()) {
                    $entries[] = array($row['timestamp'], $row['amount']);
                }
                $all_entries = $entries;
                $stock_sum = 0;
                $results = stock_read();
                while($row = $results->fetchArray()) {
                    $stock_sum += $row['amount'];
                    $all_entries[] = array($row['timestamp'], $row['amount']);
                }

                $cdiv = calculate_div($entries);
                $t = array();
                $s = array();
                $sm = 0;
                foreach (array_sort($all_entries, 0) as $e) {
                    if ($e[1]>500) {
                        $sm += $e[1];
                        $t[] = $e[0]. " 00:00:00";
                    } else {
                        $sm -= $e[1];
                        $t[] = $e[0]. ":00";
                    }
                    $s[] = $sm;
                }
            ?>

            <div id='chart'></div><script>
                var t1col = '#3366ff';
                var t2col = '#ff6600';
                var trace1 = {
                    x: [<?php //1, 2, 3],
                        $first = true;
                        foreach ($cdiv as $e) {
                            if ($first) $first = false;
                            else echo ', ';
                            echo "'". date('Y-m-d H:i:s', $e[0]). "'";
                        }?>],
                    y: [<?php //40, 50, 60],
                        $first = true;
                        foreach ($cdiv as $e) {
                            if ($first) $first = false;
                            else echo ', ';
                            echo round($e[1],1);
                        }
                    ?>],
                    name: 'spotřeba [kg/den]',
                    type: 'scatter',
                    line: {
                        color: t1col
                    }
                };
                var trace2 = {
                    x: [<?php //2, 3, 4],
                        $first = true;
                        foreach ($t as $tv) {
                            if ($first) {
                                $first = false;
                            }
                            else {
                                echo ", ";//'". $tv. "', " ;
                            }
                            echo "'". $tv. "'";
                        }
                    ?>],
                    y: [<?php //4, 5, 6],
                        $first = true;
                        //$lsv = 0;
                        foreach ($s as $sv) {
                            if ($first) $first=false;
                            else echo ', ';//. $lsv. ', ';
                            echo $sv;
                            //$lsv = $sv;
                        }
                    ?>],
                    name: 'zásoba [kg]',
                    yaxis: 'y2',
                    type: 'scatter',
                    line: {
                        color: t2col
                    }
                };
                var data = [trace1, trace2];
                var layout = {
                    //title: 'Double Y Axis Example',
                    yaxis: {
                        title: 'spotřeba [kg / den]',
                        titlefont: {color: t1col},
                        tickfont: {color: t1col}
                    },
                    yaxis2: {
                        title: 'zásoba [kg]',
                        titlefont: {color: t2col},
                        tickfont: {color: t2col},
                        overlaying: 'y',
                        side: 'right'
                    },
                    margin: { t: 0},
                    showlegend: false
                };
                Plotly.newPlot('chart', data, layout);
            </script>
		</article>
	</section>
    
	<footer>
        <h3>Todo</h3>
        <ul>
            <li>Dodělat mobilní verze formulářů.</li>
        </ul>
		<p>Copyleft 2018 Ondřej</p>
	</footer>
    
</body> </html>