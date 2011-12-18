<?php

$dbus = new Dbus;

$proxy = $dbus->createProxy("im.pidgin.purple.PurpleService",
                            "/im/pidgin/purple/PurpleObject",
                            "org.freedesktop.DBus.Introspectable");

$data = $proxy->Introspect();

file_put_contents('introspect.xml', $data);

?>