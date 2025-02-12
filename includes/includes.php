<?php
/**
 * Copy a file, or recursively copy a folder and its contents
 *
 * @author      Aidan Lister <aidan@php.net>
 * @version     1.0.1
 * @link        http://aidanlister.com/repos/v/function.copyr.php
 * @param       string   $source    Source path
 * @param       string   $dest      Destination path
 * @return      bool     Returns TRUE on success, FALSE on failure
 */
function copyr($source, $dest)
{
    if (is_file($source)) {
        return copy($source, $dest);
    }
    if (!is_dir($dest)) {
        mkdir($dest, 0777, true);
    }
    foreach (scandir($source) as $entry) {
        if ($entry === '.' || $entry === '..') {
            continue;
        }
        copyr("$source/$entry", "$dest/$entry");
    }
    return true;
}

$ip = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'] ?? 'UNKNOWN';

session_start();
$sessID = $_COOKIE['sessID'] ?? null;

function cleanPath($path) {
    $result = [];
    $pathA = explode('/', $path);
    if (!$pathA[0]) {
        $result[] = '';
    }
    foreach ($pathA as $dir) {
        if ($dir === '..') {
            if (end($result) === '..') {
                $result[] = '..';
            } elseif (!array_pop($result)) {
                $result[] = '..';
            }
        } elseif ($dir && $dir !== '.') {
            $result[] = $dir;
        }
    }
    if (!end($pathA)) {
        $result[] = '';
    }
    return implode('/', $result);
}

function redirect_to($url) {
    if (!headers_sent()) {
        $thedir = rtrim(dirname($_SERVER['PHP_SELF']), '\\');
        header('Location: http://' . $_SERVER['HTTP_HOST'] . $thedir . '/' . ltrim($url, '/'));
        exit;
    } else {
        die('Could not redirect; Headers already sent (output).');
    }
}

function datediff($interval, $datefrom, $dateto, $using_timestamps = false) {
    if (!$using_timestamps) {
        $datefrom = strtotime($datefrom);
        $dateto = strtotime($dateto);
    }
    $difference = $dateto - $datefrom;
    switch ($interval) {
        case 'd':
            return floor($difference / 86400);
        case 'h':
            return floor($difference / 3600);
        case 'n':
            return floor($difference / 60);
        case 's':
        default:
            return $difference;
    }
}

function rm($fileglob) {
    if (is_string($fileglob)) {
        if (is_file($fileglob)) {
            return unlink($fileglob);
        } elseif (is_dir($fileglob)) {
            foreach (scandir($fileglob) as $entry) {
                if ($entry === '.' || $entry === '..') continue;
                rm("$fileglob/$entry");
            }
            return rmdir($fileglob);
        } else {
            return false;
        }
    } elseif (is_array($fileglob)) {
        foreach ($fileglob as $path) {
            rm($path);
        }
        return true;
    }
    return false;
}

function file_extension($filename) {
    return pathinfo($filename, PATHINFO_EXTENSION);
}
?>
