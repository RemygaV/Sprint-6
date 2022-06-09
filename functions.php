<?php

function makeDirectoryAction(string $path)
{
    if (!file_exists($path)) {
        mkdir($path, 0777);
    }
}

function deleteAction(string $path)
{
    if (isset($_POST['deleteFile']) && ($_POST['deleteFile'] === 'index.php') || 
    ($_POST['deleteFile'] === 'functions.php') || 
    ($_POST['deleteFile'] === 'style.css') || 
    ($_POST['deleteFile'] === 'readme.md')) {
        echo '<p class="error_message">This file can not be deleted!</p>';
    } elseif (file_exists($path)) {
        if (is_file($path)) {
            unlink($path);
            // echo "rm file $path ";
        } else {
            rmdir($path);
            // echo "rm directory $path ";
        }
    }
}

function renameAction(string $from, string $to)
{
    if (file_exists($from) && !file_exists($to)) {
        echo "rename $from -> $to";
        rename($from, $to);
    }
}
