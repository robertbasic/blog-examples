<?php

$dbus = new Dbus(Dbus::BUS_SESSION);

$interface = "im.pidgin.purple.PurpleInterface";
$method = "ReceivedImMsg";
// watching only for this particular signal on this particular interface
$dbus->addWatch($interface, $method);

$kill = false;
do {
    $signal = $dbus->waitLoop(1000);
    
    if ($signal instanceof DbusSignal) {
        // even if we watch only for one signal on one interface
        // we still can get rubbish, so making sure this is what we need
        if ($signal->matches($interface, $method)) {
            $data = $signal->getData()->getData();
            // if we received "kill" as a message, then kill it.
            if ($data[2] == 'kill') {
                $kill = true;
                echo "Goodbye, cruel world!\n";
            } else {
                echo "Got stuff!\n";
            }
        }
    }
    
} while (!$kill);