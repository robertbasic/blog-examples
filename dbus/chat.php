<?php

$dbus = new Dbus(Dbus::BUS_SESSION);

$interface = "im.pidgin.purple.PurpleInterface";
$method = "ReceivedImMsg";
$dbus->addWatch($interface, $method);

$proxy = $dbus->createProxy("im.pidgin.purple.PurpleService",
                            "/im/pidgin/purple/PurpleObject",
                            "im.pidgin.purple.PurpleInterface");

$kill = false;
do {
    $signal = $dbus->waitLoop(1000);
    
    if ($signal instanceof DbusSignal) {
        if ($signal->matches($interface, $method)) {
            /**
             * Contents of the recieved data, see:
             * http://developer.pidgin.im/doxygen/dev/html/pages.html
             * http://developer.pidgin.im/doxygen/dev/html/conversation-signals.html
             * data:
             *  0 => the reciever account
             *  1 => the user sending the message
             *  2 => the message
             *  3 => the IM conversation
             *  4 => flags
             */
            $data = $signal->getData()->getData();
            
            $reciever = $data[0];
            $sender = $data[1];
            $message = $data[2];
            $conversation = $data[3];
            
            $responseMessage = "I'm sorry Dave. I'm affraid I can't do that.";
            
            // 2034 is *my* account's number, yours will be different
            // 3681 is the account number from which *I* accept messages
            if ($reciever == 2034 && $proxy->PurpleAccountIsConnected($reciever)
                    && $proxy->PurpleFindBuddy($reciever, $sender) == 3681) {
                
                switch ($message) {
                    case 'hi': {
                        $responseMessage = 'Hi Dave!';
                    } break;
                    case 'uptime': {
                        exec('uptime', $responseMessage);
                        $responseMessage = $responseMessage[0];
                    } break;
                    case 'current ip': {
                        $responseMessage = file_get_contents('http://ifconfig.me/ip');
                    } break;
                    case 'kill': {
                        $responseMessage = 'Bye Dave!';
                        $kill = true;
                    }
                }
                
                $im = $proxy->PurpleConvIm($conversation);
                $proxy->PurpleConvImSend($im, $responseMessage);
            }
        }
    }
    
} while (!$kill);