<?php

$dbus = new Dbus;

$proxy = $dbus->createProxy("im.pidgin.purple.PurpleService",
                            "/im/pidgin/purple/PurpleObject",
                            "im.pidgin.purple.PurpleInterface");

$accounts = $proxy->PurpleAccountsGetAllActive();

foreach ($accounts->getData() as $account) {
    if ($proxy->PurpleAccountIsConnected($account)) {
        $username = $proxy->PurpleAccountGetUsername($account);
        $protocolId = $proxy->PurpleAccountGetProtocolId($account);
        $protocolName = $proxy->PurpleAccountGetProtocolName($account);
        echo $username . " is connected on the " . $protocolName . " (" . $protocolId . ") protocol.\n";
    }
}

?>