<?php
// Clear OPcache
if (function_exists('opcache_reset')) {
    opcache_reset();
    echo "OPcache cleared!<br>";
} else {
    echo "OPcache not available<br>";
}

// Clear Realpath Cache
clearstatcache(true);
echo "Realpath cache cleared!<br>";

echo "<br>Now refresh your service page!";
