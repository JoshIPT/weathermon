#!/usr/bin/php -q
<?php
        $port = "/dev/ttyUSB0";
        $db = new mysqli("p:127.0.0.1", "weather", "<PASSWORDHERE>", "weather");
        function gts() {
                return "[".date("d-m-Y H:i:s")."] ";
        }
        print gts()."Arduino Weather Monitor\n";
        print gts()."Initialising interface controller...";
        $x = `stty -F $port cs8 57600 ignbrk -brkint -imaxbel -opost -onlcr -isig -icanon -iexten -echo -echoe -echok -echoctl -echoke noflsh -ixon -crtscts`;
        print "OK\n";
        print gts()."Opening Port...";
        $fp = fopen($port, "w+");
        if (!$fp) { die("Error opening port: {$port}\n"); }
        print "OK\n";
        $ln = "";
        while (true) {
                $dat = fread($fp, 1);
                $ln .= $dat;
                if ($dat == "\n") {
                        addData(trim($ln));
                        $ln = "";
                }
        }

        function addData($dat) {
                global $db;
                if (!$db) { $db = new mysqli("p:127.0.0.1", "weather", "<PASSWORDHERE>", "weather"); }
                if ($dat != "") {
                        $vals = @str_getcsv($dat);
                        if (count($vals) == 7) {
                                print gts()."Got {$dat}\n";
                                $qid = $db->query("INSERT INTO `weatherstation` (`id`, `sensor`, `hertz`, `speed`, `gusthz`, `gustspeed`, `dirvolts`, `winddir`) VALUES (0, {$vals[0]}, {$vals[1]}, {$vals[2]}, {$vals[3]}, {$vals[4]}, {$vals[5]}, '".trim($vals[6])."');");
                        }
                }
        }
?>
