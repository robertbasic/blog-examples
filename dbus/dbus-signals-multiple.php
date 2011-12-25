<?php

$dbus = new Dbus(Dbus::BUS_SESSION);

$interface = "im.pidgin.purple.PurpleInterface";
$methodIm = "ReceivedImMsg";
$methodChat = "ReceivedChatMsg";
// watching for all signals on one interface
$dbus->addWatch($interface);
// if need to watch on more interfaces, just call addWatch(other_interface) again

do {
    $signal = $dbus->waitLoop(1000);
    
    if ($signal instanceof DbusSignal) {
        $data = $signal->getData();
        
        if (method_exists($data, 'getData')) {
            $data = $data->getData();
        }
        
        // filtering out multiple signals
        if ($signal->matches($interface, $methodIm)) {
            echo "Got stuff via IM!\n";
        } else if ($signal->matches($interface, $methodChat)) {
            echo "Got stuff via Chat (IRC)!\n";
        }
    }
    
} while (true);