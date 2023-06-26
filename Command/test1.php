<?php

function pingAddress($ip) {
    $pingresult = exec("/bin/ping -n -c 1 $ip", $outcome, $status);
    if (0 == $status) {
        $status = "alive";
    } else {
        $status = "dead";
    }
    echo "The IP address, $ip, is  ".$status;
}

pingAddress("13.14.0.195");