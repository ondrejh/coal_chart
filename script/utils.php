<?php

define ("DIRECTORY", "./data/");
define ("FILE", DIRECTORY. "entries.txt");
define ("DB", DIRECTORY. "entries.sql");

function get_date() {
    return date('Y-m-d', time());
}

function get_time() {
    return date('H:i', time());
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

function insert_entry($amount, $timestamp){
    $db = new SQLite3(DB);
    $query = "CREATE TABLE IF NOT EXISTS entries (id INTEGER PRIMARY KEY, amount FLOAT, timestamp DATETIME)";
    $db->query($query);
    $query = "SELECT COUNT(*) as count FROM entries WHERE amount=". $amount. " AND timestamp='". $timestamp. "'";
    $count = $db->querySingle($query);
    if ($count>0)
        return "Chyba (položka již existuje)";
    $query = "INSERT INTO entries (amount, timestamp) VALUES (". $amount. ", '". $timestamp. "')";
    $db->query($query);
    return "OK";
}

function load_entries() {
    $db = new SQLite3(DB);
    $query = "CREATE TABLE IF NOT EXISTS entries (id INTEGER PRIMARY KEY, amount FLOAT, timestamp DATETIME)";
    $db->query($query);
    $query = "SELECT * FROM entries ORDER BY timestamp";
    $result = $db->query($query);
    return $result;
}

function convert_filebased_to_sqlite() {
    if (file_exists(FILE)) {
        $myfile = fopen(FILE, 'r');
        if ($myfile) {
            $db = new SQLite3(DB);
            $query = "CREATE TABLE IF NOT EXISTS entries (id INTEGER PRIMARY KEY, amount FLOAT, timestamp DATETIME)";
            $db->query($query);

            while (!feof($myfile)) {
                $line = fgets($myfile);
                $sline = explode(' ', $line);
                if (count($sline)>2) {
                    $tstamp = $sline[0]. ' '. $sline[1];
                    $tquant = intval(substr(trim($sline[2]), 0, 1));
                    $tkg = $tquant*25;
                    //echo $tstamp. ' '. $tkg. 'kg ';
                    insert_entry($tkg, $tstamp);
                }
            }
            fclose($myfile);

            rename(FILE, FILE.'.bak');
            return true;
        }
    }
    return false;
}

function delete_entry($id_entry) {
    $db = new SQLite3(DB);
    $query = "SELECT * FROM entries WHERE id=". $id_entry;
    $result = $db->query($query);
    $row = $result->fetchArray();
    $query = "DELETE FROM entries WHERE id=". $id_entry;
    $db->query($query);
    return $row;
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
            $kg = $e[1];
            $t = strtotime($e[0]);
            $dt = ($t - $last_t) / 86400; // sec to day
            $vdt = $kg / $dt;
            $last_t = $t;
            array_push($entries_div, array($t, $vdt));
        }
    }
    return $entries_div;
}

function stock_add($amount, $price, $timestamp) {
    $db = new SQLite3(DB);
    $query = "CREATE TABLE IF NOT EXISTS stock (id INTEGER PRIMARY KEY, amount FLOAT, timestamp DATETIME, price FLOAT, bill STRING)";
    $db->query($query);
    $query = "SELECT COUNT(*) as count FROM stock WHERE amount=". $amount. " AND timestamp='". $timestamp. "'";
    $count = $db->querySingle($query);
    if ($count>0)
        return "Chyba (položka již existuje)";
    $query = "INSERT INTO stock (amount, price, timestamp) VALUES (". $amount. ", ". $price .", '". $timestamp. "')";
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