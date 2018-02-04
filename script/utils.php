<?php

define ("DIRECTORY", "./data/");
define ("FILE", DIRECTORY. "entries.txt");

#class entry {
#    $timestamp = "2018-01-01 18:45";
#    $amount = 0;
#}

function get_date() {
    return date('Y-m-d', time());
}

function get_time() {
    return date('h:i', time());
}

function insert_entry($entry){
    if (!file_exists(DIRECTORY)) {
        echo "<br>Neexistuje adresar data!";
    }
    else {
        if (!file_exists(FILE)) {
            echo "<br>vytvarim soubor";
            $myfile = fopen(FILE, 'w');
            if ($myfile) {
                fwrite($myfile, ($entry. PHP_EOL));
                fclose($myfile);
            }
            else {
                echo "<br>soubor nelze vytvorit";
            }
        }
        else {
            $myfile = fopen(FILE, 'a');
            fwrite($myfile, ($entry. PHP_EOL));
            fclose($myfile);
        }
    }
}

#array sorting thanks to https://secure.php.net/manual/en/function.sort.php
function array_sort($array, $on, $order=SORT_ASC)
{
    $new_array = array();
    $sortable_array = array();

    if (count($array) > 0) {
        foreach ($array as $k => $v) {
            if (is_array($v)) {
                foreach ($v as $k2 => $v2) {
                    if ($k2 == $on) {
                        $sortable_array[$k] = $v2;
                    }
                }
            } else {
                $sortable_array[$k] = $v;
            }
        }

        switch ($order) {
            case SORT_ASC:
                asort($sortable_array);
            break;
            case SORT_DESC:
                arsort($sortable_array);
            break;
        }

        foreach ($sortable_array as $k => $v) {
            $new_array[$k] = $array[$k];
        }
    }

    return $new_array;
}

function sort_entries_by_time($entries) {
    return array_reverse(array_sort($entries, 0));
}

function load_entries() {
    $myfile = fopen(FILE, 'r');
    $entries = array();
    if ($myfile) {
        while (!feof($myfile)) {
            $line = fgets($myfile);
            $sline = explode(' ', $line);
            if (count($sline)>2) {
                $tstamp = $sline[0]. ' '. $sline[1];
                $tquant = $sline[2];
                $entry = array($tstamp, $tquant);
                array_push($entries, $entry);
                #echo $tstamp. ' '. $tquant. '<br>'. PHP_EOL;
            }
        }
        fclose($myfile);
    }
    else {
        #echo"<br>soubor nelze otevrit";
    }
    return $entries;
}

?>