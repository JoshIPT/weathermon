<?php
        header("Content-type: image/png");
        $db = new mysqli("127.0.0.1", "weather", "<PASSWORDHERE>", "weather");
        require_once("jpgraph/jpgraph.php");
        require_once("jpgraph/jpgraph_line.php");
        require_once("jpgraph/jpgraph_date.php");

        if (isset($_GET["mins"])) { $mins = $_GET["mins"]; }
        else { $mins = 5; }
        $qid = $db->query("SELECT * FROM `weatherstation` WHERE `tstamp` > date_sub(now(), interval {$mins} minute)") or die($db->error);

        $data = array();

        while ($row = $qid->fetch_assoc()) {
                if ($row["speed"] != 0) {
                        if (!is_array($data[$row["sensor"]])) {
                                $data[$row["sensor"]] = array();
                        }
                        $data[$row["sensor"]][] = $row;
                }
        }

        $graph = new Graph(1000, 300);
        $graph->SetMargin(40,40,30,200);
        $graph->SetScale("datlin", 0, 30);                      // Y scale max defined here
        $graph->title->Set("Last {$mins} minutes");
        $graph->title->SetFont(FF_VERDANA, FS_BOLD, 12);
        $graph->title->SetPos(40, 0, "left", "top");
        $graph->xaxis->SetLabelAngle(90);
        $graph->yaxis->SetFont(FF_VERDANA);
        $graph->legend->Pos(0.1, 0, "right", "top");
        $graph->legend->SetFont(FF_VERDANA);
        $cols = array(
                        "lightblue",
                        "darkmagenta",
                        "brown1",
                        "aquamarine",
                        "chartreuse4"
        );
        $colIndex = 0;

        foreach ($data as $label => $values) {
                        $times = array();
                        $data = array();
                        foreach ($values as $val) {
                                        $times[] = strtotime($val["tstamp"]);
                                        $data[] = (float)$val["speed"];
                        }
                        $line = new LinePlot($data, $times);
                        $line->SetLegend("Station #{$label}");
                        $line->SetLineWeight(4);
                        $colIndex++;
                        $graph->Add($line);
        }
        $graph->Stroke(_IMG_HANDLER);
        $graph->img->Stream();
        die();
?>
