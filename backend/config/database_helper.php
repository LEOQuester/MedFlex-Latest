<?php
<<<<<<< HEAD
=======

>>>>>>> 05b5dff91513ac51d7ff77f8ab2fa219bb8439b2

function closeConnection($conn) {
    if ($conn) {
        mysqli_close($conn);
    }
}

register_shutdown_function(function() {
    global $conn;
    closeConnection($conn);
});