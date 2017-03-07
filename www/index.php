<?php
        print "<h1>Weather Monitoring</h1>";
        $db = new mysqli("127.0.0.1", "weather", "<PASSWORDHERE>", "weather");
        if (!isset($_GET["id"])) {
                $qid = $db->query("SELECT * FROM `weatherstation` ORDER BY `id` DESC LIMIT 1;");
                $row = $qid->fetch_assoc();
                print "<h3>Wind Speed: {$row["speed"]}km/h (Gusting to {$row["gustspeed"]}km/h)</h3>";
                print "<h3>Direction: {$row["winddir"]}</h3>";
        }
        print "<img src=\"graph.php?mins=5\" /><br />";
        print "<img src=\"graph.php?mins=60\" /><br />";
        print "<img src=\"graph.php?mins=240\" /><br />";
        print "<img src=\"graph.php?mins=1440\" />";
        $db->close();
?>
