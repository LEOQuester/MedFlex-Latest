<?php


function closeConnection($conn) {
    if ($conn) {
        mysqli_close($conn);
    }
}

register_shutdown_function(function() {
    global $conn;
    closeConnection($conn);
});