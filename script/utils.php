<?php

define ("DIRECTORY", "./data/");
define ("FILE", DIRECTORY. "entries.txt");
define ("DB", DIRECTORY. "entries.sql");

#class entry {
#    $timestamp = "2018-01-01 18:45";
#    $amount = 0;
#}

function get_date() {
    return date('Y-m-d', time());
}

function get_time() {
    return date('H:i', time());
}

function to_kg($vstr) {
    if (substr($vstr,-1) === 'p')
        return intval(substr($vstr, 0, -1))*25;
    else
        return intval($vstr);
}

function insert_entry($entry){
    if (!file_exists(DIRECTORY)) {
        echo "<br>Neexistuje adresar data!";
    }
    else {
        $estr = $entry[0]. ' '. $entry[1];
        if (!file_exists(FILE)) {
            echo "<br>vytvarim soubor";
            $myfile = fopen(FILE, 'w');
            if ($myfile) {
                fwrite($myfile, ($estr. PHP_EOL));
                fclose($myfile);
            }
            else {
                echo "<br>soubor nelze vytvorit";
            }
        }
        else {
            $myfile = fopen(FILE, 'a');
            fwrite($myfile, ($estr. PHP_EOL));
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
    return array_sort($entries, 0);
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
                $tquant = trim($sline[2]);
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

function save_entries($entries) {
    $myfile = fopen(FILE, 'w');
    if ($myfile) {
        foreach ($entries as $e) {
            fwrite($myfile, $e[0]. ' '. $e[1]. PHP_EOL);
        }
        fclose($myfile);
    }
    else {
        #echo "<br>soubor nelze vytvorit";
    }
}

function delete_entry($entry) {
    $entries = load_entries();
    $new_entries = array();
    $found = false;
    
    foreach ($entries as $e) {
        if ($entry === $e) {
            $found = true;
        }
        else {
            array_push($new_entries, $e);
        }
    }
    
    if ($found) {
        save_entries($new_entries);
    }
    
    return $found;
}

function calculate_div($entries) {
    $entries_div = array();
    $last_t = 0;
    $first = true;
    foreach ($entries as $e) {
        if ($first) {
            $last_t = strtotime($e[0]);
            $first = false;
        }
        else {
            $kg = to_kg($e[1]);
            $t = strtotime($e[0]);
            $dt = ($t - $last_t) / 86400; // sec to day
            $vdt = $kg / $dt;
            $last_t = $t;
            array_push($entries_div, array($t, $vdt));
        }
    }
    return $entries_div;
}

function stock_add($amount, $timestamp) {
    $db = new SQLite3(DB);
    $query = "CREATE TABLE IF NOT EXISTS stock (id INTEGER PRIMARY KEY, amount FLOAT, timestamp DATETIME, price FLOAT, bill STRING)";
    $db->query($query);
    $query = "SELECT COUNT(*) as count FROM stock WHERE amount=". $amount. " AND timestamp='". $timestamp. "'";
    $count = $db->querySingle($query);
    if ($count>0)
        return "Chyba (položka již existuje)";
    $query = "INSERT INTO stock (amount, timestamp) VALUES (". $amount. ", '". $timestamp. "')";
    $db->query($query);
    return "OK";
}

function stock_read() {
    $db = new SQLite3(DB);
    $query = "CREATE TABLE IF NOT EXISTS stock (id INTEGER PRIMARY KEY, amount FLOAT, timestamp DATETIME, price FLOAT, bill STRING)";
    $db->query($query);
    $query = "SELECT * FROM stock ORDER BY timestamp";
    $result = $db->query($query);
    return $result;
}

function stock_delete($id_entry) {
    $db = new SQLite3(DB);
    $query = "SELECT * FROM stock WHERE id=". $id_entry;
    $result = $db->query($query);
    $row = $result->fetchArray();
    $query = "DELETE FROM stock WHERE id=". $id_entry;
    $db->query($query);
    return $row;
}

?>