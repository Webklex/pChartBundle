<?php
/*
    pSpring - class to draw spring graphs

    Version     : 2.1.3
    Made by     : Jean-Damien POGOLOTTI
    Last Update : 09/09/11

    This file can be distributed under the license you can find at :

                      http://www.pchart.net/license

    You can find the whole class documentation on the pChart web site.
*/

namespace Webklex\pChart;

define("NODE_TYPE_FREE", 690001);
define("NODE_TYPE_CENTRAL", 690002);

define("NODE_SHAPE_CIRCLE", 690011);
define("NODE_SHAPE_TRIANGLE", 690012);
define("NODE_SHAPE_SQUARE", 690013);

define("ALGORITHM_RANDOM", 690021);
define("ALGORITHM_WEIGHTED", 690022);
define("ALGORITHM_CIRCULAR", 690023);
define("ALGORITHM_CENTRAL", 690024);

define("LABEL_CLASSIC", 690031);
define("LABEL_LIGHT", 690032);

/**
 * Class pSpring
 *
 * @package Webklex\pChart
 */
class pSpring {
    
    var $History;
    var $pChartObject;
    var $Data = [];
    var $Links;
    var $X1;
    var $Y1;
    var $X2;
    var $Y2;
    var $RingSize;
    var $MagneticForceA;
    var $MagneticForceR;
    
    /** @var bool $AutoComputeFreeZone */
    public $AutoComputeFreeZone = false;

    /** @var array $Labels */
    public $Labels = [
        "Type" => LABEL_CLASSIC,
        "R" => 0,
        "G" => 0,
        "B" => 0,
        "Alpha" => 100,
    ];
    
    /** @var array $Default */
    public $Default = [
        "R" => 255,
        "G" => 255,
        "B" => 255,
        "Alpha" => 100,
        "BorderR" => 0,
        "BorderG" => 0,
        "BorderB" => 0,
        "BorderAlpha" => 100,
        "Surrounding" => null,
        "BackgroundR" => 255,
        "BackgroundG" => 255,
        "BackgroundB" => 255,
        "BackgroundAlpha" => 0,
        "Force" => 1,
        "NodeType" => NODE_TYPE_FREE,
        "Size" => 5,
        "Shape" => NODE_SHAPE_CIRCLE,
        "FreeZone" => 40,
        "LinkR" => 0,
        "LinkG" => 0,
        "LinkB" => 0,
        "LinkAlpha" => 0,
    ];

    /**
     * pSpring constructor.
     */
    function __construct() {}

    /**
     * Set default links options
     * @param array $settings
     */
    function setLinkDefaults($settings = []) {
        $this->Default = array_merge($this->Default, $settings);
    }

    /**
     * Set default links options
     * @param array $settings
     */
    function setLabelsSettings($settings = []) {
        $this->Labels = array_merge($this->Labels, $settings);
    }

    /**
     * Auto compute the FreeZone size based on the number of connections
     */
    function autoFreeZone() {
        /* Check connections reciprocity */
        foreach ($this->Data as $key => $settings) {
            if (isset($settings["Connections"])) {
                $this->Data[$key]["FreeZone"] = count($settings["Connections"]) * 10 + 20;
            } else {
                $this->Data[$key]["FreeZone"] = 20;
            }
        }

    }

    /**
     * Set link properties
     * @param $from_node
     * @param $to_node
     * @param $settings
     * 
     * @return int|null
     */
    function linkProperties($from_node, $to_node, $settings = []) {
        if (!isset($this->Data[$from_node])) {
            return 0;
        }
        if (!isset($this->Data[$to_node])) {
            return 0;
        }

        $R = isset($settings["R"]) ? $settings["R"] : 0;
        $G = isset($settings["G"]) ? $settings["G"] : 0;
        $B = isset($settings["B"]) ? $settings["B"] : 0;
        $Alpha = isset($settings["Alpha"]) ? $settings["Alpha"] : 100;
        $Name = isset($settings["Name"]) ? $settings["Name"] : null;
        $Ticks = isset($settings["Ticks"]) ? $settings["Ticks"] : null;

        $this->Links[$from_node][$to_node]["R"] = $R;
        $this->Links[$to_node][$from_node]["R"] = $R;
        $this->Links[$from_node][$to_node]["G"] = $G;
        $this->Links[$to_node][$from_node]["G"] = $G;
        $this->Links[$from_node][$to_node]["B"] = $B;
        $this->Links[$to_node][$from_node]["B"] = $B;
        $this->Links[$from_node][$to_node]["Alpha"] = $Alpha;
        $this->Links[$to_node][$from_node]["Alpha"] = $Alpha;
        $this->Links[$from_node][$to_node]["Name"] = $Name;
        $this->Links[$to_node][$from_node]["Name"] = $Name;
        $this->Links[$from_node][$to_node]["Ticks"] = $Ticks;
        $this->Links[$to_node][$from_node]["Ticks"] = $Ticks;
        
        return null;
    }

    /**
     * @param array $settings
     */
    function setNodeDefaults($settings = []) {
        $this->setLinkDefaults($settings);
    }

    /**
     * Add a node
     * @param $node_id
     * @param array $settings
     * 
     * @return int|null
     */
    function addNode($node_id, $settings = []) {
        
        /* if the node already exists, ignore */
        if (isset($this->Data[$node_id])) {
            return 0;
        }

        $settings = array_merge($this->Default, $settings);
        $connections = isset($settings["Connections"]) ? $settings["Connections"] : null;

        if ($settings['Surrounding'] != null) {
            $settings['BorderR'] = $settings['R'] + $settings['Surrounding'];
            $settings['BorderG'] = $settings['G'] + $settings['Surrounding'];
            $settings['BorderB'] = $settings['B'] + $settings['Surrounding'];
        }

        $this->Data[$node_id]["R"] = $settings['R'];
        $this->Data[$node_id]["G"] = $settings['G'];
        $this->Data[$node_id]["B"] = $settings['B'];
        $this->Data[$node_id]["Alpha"] = $settings['Alpha'];
        $this->Data[$node_id]["BorderR"] = $settings['BorderR'];
        $this->Data[$node_id]["BorderG"] = $settings['BorderG'];
        $this->Data[$node_id]["BorderB"] = $settings['BorderB'];
        $this->Data[$node_id]["BorderAlpha"] = $settings['BorderAlpha'];
        $this->Data[$node_id]["BackgroundR"] = $settings['BackgroundR'];
        $this->Data[$node_id]["BackgroundG"] = $settings['BackgroundG'];
        $this->Data[$node_id]["BackgroundB"] = $settings['BackgroundB'];
        $this->Data[$node_id]["BackgroundAlpha"] = $settings['BackgroundAlpha'];
        $this->Data[$node_id]["Name"] = $settings['Name'];
        $this->Data[$node_id]["Force"] = $settings['Force'];
        $this->Data[$node_id]["Type"] = $settings['NodeType'];
        $this->Data[$node_id]["Size"] = $settings['Size'];
        $this->Data[$node_id]["Shape"] = $settings['Shape'];
        $this->Data[$node_id]["FreeZone"] = $settings['FreeZone'];
        
        if ($connections != null) {
            if (is_array($connections)) {
                foreach ($connections as $key => $Value)
                    $this->Data[$node_id]["Connections"][] = $Value;
            } else
                $this->Data[$node_id]["Connections"][] = $connections;
        }
        
        return null;
    }

    /**
     * Set color attribute for a list of nodes
     * @param $nodes
     * @param array $settings
     */
    function setNodesColor($nodes, $settings = []) {
        if (is_array($nodes)) {
            foreach ($nodes as $key => $node_id) {
                if (isset($this->Data[$node_id])) {
                    if (isset($settings["R"])) {
                        $this->Data[$node_id]["R"] = $settings["R"];
                    }
                    if (isset($settings["G"])) {
                        $this->Data[$node_id]["G"] = $settings["G"];
                    }
                    if (isset($settings["B"])) {
                        $this->Data[$node_id]["B"] = $settings["B"];
                    }
                    if (isset($settings["Alpha"])) {
                        $this->Data[$node_id]["Alpha"] = $settings["Alpha"];
                    }
                    if (isset($settings["BorderR"])) {
                        $this->Data[$node_id]["BorderR"] = $settings["BorderR"];
                    }
                    if (isset($settings["BorderG"])) {
                        $this->Data[$node_id]["BorderG"] = $settings["BorderG"];
                    }
                    if (isset($settings["BorderB"])) {
                        $this->Data[$node_id]["BorderB"] = $settings["BorderB"];
                    }
                    if (isset($settings["BorderAlpha"])) {
                        $this->Data[$node_id]["BorderAlpha"] = $settings["BorderAlpha"];
                    }
                    if (isset($settings["Surrounding"])) {
                        $this->Data[$node_id]["BorderR"] = $this->Data[$node_id]["R"] + $settings["Surrounding"];
                        $this->Data[$node_id]["BorderG"] = $this->Data[$node_id]["G"] + $settings["Surrounding"];
                        $this->Data[$node_id]["BorderB"] = $this->Data[$node_id]["B"] + $settings["Surrounding"];
                    }
                }
            }
        } else {
            if (isset($settings["R"])) {
                $this->Data[$nodes]["R"] = $settings["R"];
            }
            if (isset($settings["G"])) {
                $this->Data[$nodes]["G"] = $settings["G"];
            }
            if (isset($settings["B"])) {
                $this->Data[$nodes]["B"] = $settings["B"];
            }
            if (isset($settings["Alpha"])) {
                $this->Data[$nodes]["Alpha"] = $settings["Alpha"];
            }
            if (isset($settings["BorderR"])) {
                $this->Data[$nodes]["BorderR"] = $settings["BorderR"];
            }
            if (isset($settings["BorderG"])) {
                $this->Data[$nodes]["BorderG"] = $settings["BorderG"];
            }
            if (isset($settings["BorderB"])) {
                $this->Data[$nodes]["BorderB"] = $settings["BorderB"];
            }
            if (isset($settings["BorderAlpha"])) {
                $this->Data[$nodes]["BorderAlpha"] = $settings["BorderAlpha"];
            }
            if (isset($settings["Surrounding"])) {
                $this->Data[$nodes]["BorderR"] = $this->Data[$nodes]["R"] + $settings["Surrounding"];
                $this->Data[$nodes]["BorderG"] = $this->Data[$nodes]["G"] + $settings["Surrounding"];
                $this->Data[$nodes]["BorderB"] = $this->Data[$nodes]["B"] + $settings["Surrounding"];
            }
        }
    }

    /**
     * Returns all the nodes details
     * 
     * @return array
     */
    function dumpNodes() {
        return $this->Data;
    }

    /**
     * Check if a connection exists and create it if required
     * @param $SourceID
     * @param $TargetID
     * 
     * @return bool|null
     */
    function checkConnection($SourceID, $TargetID) {
        if (isset($this->Data[$SourceID]["Connections"])) {
            foreach ($this->Data[$SourceID]["Connections"] as $key => $connection_id) {
                if ($TargetID == $connection_id) {
                    return true;
                }
            }
        }
        $this->Data[$SourceID]["Connections"][] = $TargetID;
        
        return null;
    }

    /**
     * Get the median linked nodes position
     * @param $key
     * @param $x
     * @param $y
     * 
     * @return array
     */
    function getMedianOffset($key, $x, $y) {
        $cpt = 1;
        if (isset($this->Data[$key]["Connections"])) {
            foreach ($this->Data[$key]["Connections"] as $id => $node_id) {
                if (isset($this->Data[$node_id]["X"]) && isset($this->Data[$node_id]["Y"])) {
                    $x = $x + $this->Data[$node_id]["X"];
                    $y = $y + $this->Data[$node_id]["Y"];
                    $cpt++;
                }
            }
        }
        return [
            "X" => $x / $cpt, 
            "Y" => $y / $cpt
        ];
    }

    /**
     * Return the ID of the attached partner with the biggest weight
     * @param $key
     * 
     * @return string
     */
    function getBiggestPartner($key) {
        if (!isset($this->Data[$key]["Connections"])) {
            return "";
        }

        $max_weight = 0;
        $result = "";
        foreach ($this->Data[$key]["Connections"] as $key => $PeerID) {
            if ($this->Data[$PeerID]["Weight"] > $max_weight) {
                $max_weight = $this->Data[$PeerID]["Weight"];
                $result = $PeerID;
            }
        }
        return $result;
    }

    /**
     * Do the initial node positions computing pass
     * @param $algorithm
     */
    function firstPass($algorithm) {
        $center_x = ($this->X2 - $this->X1) / 2 + $this->X1;
        $center_y = ($this->Y2 - $this->Y1) / 2 + $this->Y1;

        /* Check connections reciprocity */
        foreach ($this->Data as $key => $settings) {
            if (isset($settings["Connections"])) {
                foreach ($settings["Connections"] as $id => $connection_id)
                    $this->checkConnection($connection_id, $key);
            }
        }

        if ($this->AutoComputeFreeZone) {
            $this->autoFreeZone();
        }

        /* Get the max number of connections */
        $max_connections = 0;
        foreach ($this->Data as $key => $settings) {
            if (isset($settings["Connections"])) {
                if ($max_connections < count($settings["Connections"])) {
                    $max_connections = count($settings["Connections"]);
                }
            }
        }

        if ($algorithm == ALGORITHM_WEIGHTED) {
            foreach ($this->Data as $key => $settings) {
                if ($settings["Type"] == NODE_TYPE_CENTRAL) {
                    $this->Data[$key]["X"] = $center_x;
                    $this->Data[$key]["Y"] = $center_y;
                }
                if ($settings["Type"] == NODE_TYPE_FREE) {
                    if (isset($settings["Connections"])) {
                        $connections = count($settings["Connections"]);
                    } else {
                        $connections = 0;
                    }

                    $ring = $max_connections - $connections;
                    $angle = rand(0, 360);

                    $this->Data[$key]["X"] = cos(deg2rad($angle)) * ($ring * $this->RingSize) + $center_x;
                    $this->Data[$key]["Y"] = sin(deg2rad($angle)) * ($ring * $this->RingSize) + $center_y;
                }
            }
        } elseif ($algorithm == ALGORITHM_CENTRAL) {
            /* Put a weight on each nodes */
            foreach ($this->Data as $key => $settings) {
                if (isset($settings["Connections"]))
                    $this->Data[$key]["Weight"] = count($settings["Connections"]);
                else
                    $this->Data[$key]["Weight"] = 0;
            }

            $max_connections = $max_connections + 1;
            for ($i = $max_connections; $i >= 0; $i--) {
                foreach ($this->Data as $key => $settings) {
                    if ($settings["Type"] == NODE_TYPE_CENTRAL) {
                        $this->Data[$key]["X"] = $center_x;
                        $this->Data[$key]["Y"] = $center_y;
                    }
                    
                    if ($settings["Type"] == NODE_TYPE_FREE) {
                        if (isset($settings["Connections"])) {
                            $connections = count($settings["Connections"]);
                        } else {
                            $connections = 0;
                        }

                        if ($connections == $i) {
                            $biggest_partner = $this->getBiggestPartner($key);
                            if ($biggest_partner != "") {
                                $ring = $this->Data[$biggest_partner]["FreeZone"];
                                $weight = $this->Data[$biggest_partner]["Weight"];
                                $angle_division = 360 / $this->Data[$biggest_partner]["Weight"];
                                $done = false;
                                $tries = 0;
                                
                                while (!$done && $tries <= $weight * 2) {
                                    $tries++;
                                    $angle = floor(rand(0, $weight) * $angle_division);
                                    
                                    if (!isset($this->Data[$biggest_partner]["Angular"][$angle]) || !isset($this->Data[$biggest_partner]["Angular"])) {
                                        $this->Data[$biggest_partner]["Angular"][$angle] = $angle;
                                        $done = true;
                                    }
                                }
                                if (!$done) {
                                    $angle = rand(0, 360);
                                    $this->Data[$biggest_partner]["Angular"][$angle] = $angle;
                                }

                                $x = cos(deg2rad($angle)) * ($ring) + $this->Data[$biggest_partner]["X"];
                                $y = sin(deg2rad($angle)) * ($ring) + $this->Data[$biggest_partner]["Y"];

                                $this->Data[$key]["X"] = $x;
                                $this->Data[$key]["Y"] = $y;
                            }
                        }
                    }
                }
            }
        } elseif ($algorithm == ALGORITHM_CIRCULAR) {
            $max_connections = $max_connections + 1;
            for ($i = $max_connections; $i >= 0; $i--) {
                foreach ($this->Data as $key => $settings) {
                    if ($settings["Type"] == NODE_TYPE_CENTRAL) {
                        $this->Data[$key]["X"] = $center_x;
                        $this->Data[$key]["Y"] = $center_y;
                    }
                    if ($settings["Type"] == NODE_TYPE_FREE) {
                        if (isset($settings["Connections"])) {
                            $connections = count($settings["Connections"]);
                        } else {
                            $connections = 0;
                        }

                        if ($connections == $i) {
                            $ring = $max_connections - $connections;
                            $angle = rand(0, 360);

                            $x = cos(deg2rad($angle)) * ($ring * $this->RingSize) + $center_x;
                            $y = sin(deg2rad($angle)) * ($ring * $this->RingSize) + $center_y;

                            $median_offset = $this->getMedianOffset($key, $x, $y);

                            $this->Data[$key]["X"] = $median_offset["X"];
                            $this->Data[$key]["Y"] = $median_offset["Y"];
                        }
                    }
                }
            }
        } elseif ($algorithm == ALGORITHM_RANDOM) {
            foreach ($this->Data as $key => $settings) {
                if ($settings["Type"] == NODE_TYPE_FREE) {
                    $this->Data[$key]["X"] = $center_x + rand(-20, 20);
                    $this->Data[$key]["Y"] = $center_y + rand(-20, 20);
                }
                if ($settings["Type"] == NODE_TYPE_CENTRAL) {
                    $this->Data[$key]["X"] = $center_x;
                    $this->Data[$key]["Y"] = $center_y;
                }
            }
        }
    }

    /**
     * Compute one pass
     */
    function doPass() {
        /* Compute vectors */
        foreach ($this->Data as $key => $settings) {
            if ($settings["Type"] != NODE_TYPE_CENTRAL) {
                unset($this->Data[$key]["Vectors"]);

                $x1 = $settings["X"];
                $y1 = $settings["Y"];

                /* Repulsion vectors */
                foreach ($this->Data as $key2 => $settings2) {
                    if ($key != $key2) {
                        $x2 = $this->Data[$key2]["X"];
                        $y2 = $this->Data[$key2]["Y"];
                        $free_zone = $this->Data[$key2]["FreeZone"];

                        $distance = $this->getDistance($x1, $y1, $x2, $y2);
                        $angle = $this->getAngle($x1, $y1, $x2, $y2) + 180;

                        /* Nodes too close, repulsion occurs */
                        if ($distance < $free_zone) {
                            $force = log(pow(2, $free_zone - $distance));
                            if ($force > 1) {
                                $this->Data[$key]["Vectors"][] = [
                                    "Type" => "R", 
                                    "Angle" => $angle % 360, 
                                    "Force" => $force
                                ];
                            }
                        }
                    }
                }

                /* Attraction vectors */
                if (isset($settings["Connections"])) {
                    foreach ($settings["Connections"] as $id => $node_id) {
                        if (isset($this->Data[$node_id])) {
                            $x2 = $this->Data[$node_id]["X"];
                            $y2 = $this->Data[$node_id]["Y"];
                            $free_zone = $this->Data[$key]["FreeZone"];

                            $distance = $this->getDistance($x1, $y1, $x2, $y2);
                            $angle = $this->getAngle($x1, $y1, $x2, $y2);

                            if ($distance > $free_zone)
                                $force = log(($distance - $free_zone) + 1);
                            else {
                                $force = log(($free_zone - $distance) + 1);
                                ($angle = $angle + 180);
                            }

                            if ($force > 1){
                                $this->Data[$key]["Vectors"][] = [
                                    "Type" => "R",
                                    "Angle" => $angle % 360,
                                    "Force" => $force
                                ];
                            }
                        }
                    }
                }
            }
        }

        /* Move the nodes accoding to the vectors */
        foreach ($this->Data as $key => $settings) {
            $x = $settings["X"];
            $y = $settings["Y"];

            if (isset($settings["Vectors"]) && $settings["Type"] != NODE_TYPE_CENTRAL) {
                foreach ($settings["Vectors"] as $id => $vector) {
                    $type = $vector["Type"];
                    $force = $vector["Force"];
                    $angle = $vector["Angle"];
                    $factor = $type == "A" ? $this->MagneticForceA : $this->MagneticForceR;

                    $x = cos(deg2rad($angle)) * $force * $factor + $x;
                    $y = sin(deg2rad($angle)) * $force * $factor + $y;
                }
            }

            $this->Data[$key]["X"] = $x;
            $this->Data[$key]["Y"] = $y;
        }
    }

    /**
     * @return float|int
     */
    function lastPass() {
        /* Put everything inside the graph area */
        foreach ($this->Data as $key => $settings) {
            $x = $settings["X"];
            $y = $settings["Y"];

            if ($x < $this->X1) {
                $x = $this->X1;
            }
            if ($x > $this->X2) {
                $x = $this->X2;
            }
            if ($y < $this->Y1) {
                $y = $this->Y1;
            }
            if ($y > $this->Y2) {
                $y = $this->Y2;
            }

            $this->Data[$key]["X"] = $x;
            $this->Data[$key]["Y"] = $y;
        }

        /* Dump all links */
        $links = [];
        foreach ($this->Data as $key => $settings) {
            $x1 = $settings["X"];
            $y1 = $settings["Y"];

            if (isset($settings["Connections"])) {
                foreach ($settings["Connections"] as $id => $node_id) {
                    if (isset($this->Data[$node_id])) {
                        $x2 = $this->Data[$node_id]["X"];
                        $y2 = $this->Data[$node_id]["Y"];

                        $links[] = [
                            "X1" => $x1, 
                            "Y1" => $y1, 
                            "X2" => $x2, 
                            "Y2" => $y2, 
                            "Source" => $settings["Name"], 
                            "Destination" => $this->Data[$node_id]["Name"]
                        ];
                    }
                }
            }
        }

        /* Check collisions */
        $conflicts = 0;
        foreach ($this->Data as $key => $settings) {
            $x1 = $settings["X"];
            $y1 = $settings["Y"];

            if (isset($settings["Connections"])) {
                foreach ($settings["Connections"] as $id => $node_id) {
                    if (isset($this->Data[$node_id])) {
                        $x2 = $this->Data[$node_id]["X"];
                        $y2 = $this->Data[$node_id]["Y"];

                        foreach ($links as $idLinks => $Link) {
                            $x3 = $Link["X1"];
                            $y3 = $Link["Y1"];
                            $x4 = $Link["X2"];
                            $y4 = $Link["Y2"];

                            if (!($x1 == $x3 && $x2 == $x4 && $y1 == $y3 && $y2 == $y4)) {
                                if ($this->intersect($x1, $y1, $x2, $y2, $x3, $y3, $x4, $y4)) {
                                    if ($Link["Source"] != $settings["Name"] && $Link["Source"] != $this->Data[$node_id]["Name"] && $Link["Destination"] != $settings["Name"] && $Link["Destination"] != $this->Data[$node_id]["Name"]) {
                                        $conflicts++;
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        
        return $conflicts / 2;
    }

    /**
     * Center the graph
     */
    function center() {
        /* Determine the real center */
        $target_center_x = ($this->X2 - $this->X1) / 2 + $this->X1;
        $target_center_y = ($this->Y2 - $this->Y1) / 2 + $this->Y1;

        /* Get current boundaries */
        $xMin = $this->X2;
        $xMax = $this->X1;
        $yMin = $this->Y2;
        $yMax = $this->Y1;
        foreach ($this->Data as $key => $settings) {
            $x = $settings["X"];
            $y = $settings["Y"];

            if ($x < $xMin) {
                $xMin = $x;
            }
            if ($x > $xMax) {
                $xMax = $x;
            }
            if ($y < $yMin) {
                $yMin = $y;
            }
            if ($y > $yMax) {
                $yMax = $y;
            }
        }
        $currentCenter_x = ($xMax - $xMin) / 2 + $xMin;
        $currentCenter_y = ($yMax - $yMin) / 2 + $yMin;

        /* Compute the offset to apply */
        $x_offset = $target_center_x - $currentCenter_x;
        $y_offset = $target_center_y - $currentCenter_y;

        /* Correct the points position */
        foreach ($this->Data as $key => $settings) {
            $this->Data[$key]["X"] = $settings["X"] + $x_offset;
            $this->Data[$key]["Y"] = $settings["Y"] + $y_offset;
        }
    }

    /**
     * Create the encoded string
     * @param $Object
     * @param array $settings
     *
     * @return array
     */
    function drawSpring($Object, $settings = []) {
        $this->pChartObject = $Object;

        $Pass = isset($settings["Pass"]) ? $settings["Pass"] : 50;
        $Retries = isset($settings["Retry"]) ? $settings["Retry"] : 10;
        $this->MagneticForceA = isset($settings["MagneticForceA"]) ? $settings["MagneticForceA"] : 1.5;
        $this->MagneticForceR = isset($settings["MagneticForceR"]) ? $settings["MagneticForceR"] : 2;
        $this->RingSize = isset($settings["RingSize"]) ? $settings["RingSize"] : 40;
        $DrawVectors = isset($settings["DrawVectors"]) ? $settings["DrawVectors"] : false;
        $DrawQuietZone = isset($settings["DrawQuietZone"]) ? $settings["DrawQuietZone"] : false;
        $CenterGraph = isset($settings["CenterGraph"]) ? $settings["CenterGraph"] : true;
        $TextPadding = isset($settings["TextPadding"]) ? $settings["TextPadding"] : 4;
        $algorithm = isset($settings["Algorithm"]) ? $settings["Algorithm"] : ALGORITHM_WEIGHTED;

        $FontSize = $Object->FontSize;
        $this->X1 = $Object->GraphAreaX1;
        $this->Y1 = $Object->GraphAreaY1;
        $this->X2 = $Object->GraphAreaX2;
        $this->Y2 = $Object->GraphAreaY2;

        $conflicts = 1;
        $jobs = 0;
        $this->History["MinimumConflicts"] = -1;
        while ($conflicts != 0 && $jobs < $Retries) {
            $jobs++;

            /* Compute the initial settings */
            $this->firstPass($algorithm);

            /* Apply the vectors */
            if ($Pass > 0) {
                for ($i = 0; $i <= $Pass; $i++) {
                    $this->doPass();
                }
            }

            $conflicts = $this->lastPass();
            if ($this->History["MinimumConflicts"] == -1 || $conflicts < $this->History["MinimumConflicts"]) {
                $this->History["MinimumConflicts"] = $conflicts;
                $this->History["Result"] = $this->Data;
            }
        }

        $conflicts = $this->History["MinimumConflicts"];
        $this->Data = $this->History["Result"];

        if ($CenterGraph) {
            $this->center();
        }

        /* Draw the connections */
        $drawn = [];
        foreach ($this->Data as $key => $settings) {
            $x = $settings["X"];
            $y = $settings["Y"];

            if (isset($settings["Connections"])) {
                foreach ($settings["Connections"] as $id => $node_id) {
                    if (!isset($drawn[$key])) {
                        $drawn[$key] = "";
                    }
                    if (!isset($drawn[$node_id])) {
                        $drawn[$node_id] = "";
                    }

                    if (isset($this->Data[$node_id]) && !isset($drawn[$key][$node_id]) && !isset($drawn[$node_id][$key])) {
                        $color = array("R" => $this->Default["LinkR"], "G" => $this->Default["LinkG"], "B" => $this->Default["LinkB"], "Alpha" => $this->Default["Alpha"]);

                        if ($this->Links != "") {
                            if (isset($this->Links[$key][$node_id]["R"])) {
                                $color = array("R" => $this->Links[$key][$node_id]["R"], "G" => $this->Links[$key][$node_id]["G"], "B" => $this->Links[$key][$node_id]["B"], "Alpha" => $this->Links[$key][$node_id]["Alpha"]);
                            }

                            if (isset($this->Links[$key][$node_id]["Ticks"])) {
                                $color["Ticks"] = $this->Links[$key][$node_id]["Ticks"];
                            }
                        }

                        $x2 = $this->Data[$node_id]["X"];
                        $y2 = $this->Data[$node_id]["Y"];
                        $this->pChartObject->drawLine($x, $y, $x2, $y2, $color);
                        $drawn[$key][$node_id] = true;

                        if (isset($this->Links) && $this->Links != "") {
                            if (isset($this->Links[$key][$node_id]["Name"]) || isset($this->Links[$node_id][$key]["Name"])) {
                                $Name = isset($this->Links[$key][$node_id]["Name"]) ? $this->Links[$key][$node_id]["Name"] : $this->Links[$node_id][$key]["Name"];
                                $TxtX = ($x2 - $x) / 2 + $x;
                                $TxtY = ($y2 - $y) / 2 + $y;

                                if ($x <= $x2)
                                    $angle = (360 - $this->getAngle($x, $y, $x2, $y2)) % 360;
                                else
                                    $angle = (360 - $this->getAngle($x2, $y2, $x, $y)) % 360;

                                $settings = $color;
                                $settings["Angle"] = $angle;
                                $settings["Align"] = TEXT_ALIGN_BOTTOMMIDDLE;
                                $this->pChartObject->drawText($TxtX, $TxtY, $Name, $settings);
                            }
                        }
                    }
                }
            }
        }

        /* Draw the quiet zones */
        if ($DrawQuietZone) {
            foreach ($this->Data as $key => $settings) {
                $x = $settings["X"];
                $y = $settings["Y"];
                $free_zone = $settings["FreeZone"];

                $this->pChartObject->drawFilledCircle($x, $y, $free_zone, array("R" => 0, "G" => 0, "B" => 0, "Alpha" => 2));
            }
        }


        /* Draw the nodes */
        foreach ($this->Data as $key => $settings) {
            $x = $settings["X"];
            $y = $settings["Y"];
            $Name = $settings["Name"];
            $free_zone = $settings["FreeZone"];
            $Shape = $settings["Shape"];
            $Size = $settings["Size"];

            $color = array("R" => $settings["R"], "G" => $settings["G"], "B" => $settings["B"], "Alpha" => $settings["Alpha"], "BorderR" => $settings["BorderR"], "BorderG" => $settings["BorderG"], "BorderB" => $settings["BorderB"], "BorderApha" => $settings["BorderAlpha"]);

            if ($Shape == NODE_SHAPE_CIRCLE) {
                $this->pChartObject->drawFilledCircle($x, $y, $Size, $color);
            } elseif ($Shape == NODE_SHAPE_TRIANGLE) {
                $Points = "";
                $Points[] = cos(deg2rad(270)) * $Size + $x;
                $Points[] = sin(deg2rad(270)) * $Size + $y;
                $Points[] = cos(deg2rad(45)) * $Size + $x;
                $Points[] = sin(deg2rad(45)) * $Size + $y;
                $Points[] = cos(deg2rad(135)) * $Size + $x;
                $Points[] = sin(deg2rad(135)) * $Size + $y;
                $this->pChartObject->drawPolygon($Points, $color);
            } elseif ($Shape == NODE_SHAPE_SQUARE) {
                $Offset = $Size / 2;
                $Size = $Size / 2;
                $this->pChartObject->drawFilledRectangle($x - $Offset, $y - $Offset, $x + $Offset, $y + $Offset, $color);
            }

            if ($Name != "") {
                $label_options = array("R" => $this->Labels["R"], "G" => $this->Labels["G"], "B" => $this->Labels["B"], "Alpha" => $this->Labels["Alpha"]);

                if ($this->Labels["Type"] == LABEL_LIGHT) {
                    $label_options["Align"] = TEXT_ALIGN_BOTTOMLEFT;
                    $this->pChartObject->drawText($x, $y, $Name, $label_options);
                } elseif ($this->Labels["Type"] == LABEL_CLASSIC) {
                    $label_options["Align"] = TEXT_ALIGN_TOPMIDDLE;
                    $label_options["DrawBox"] = true;
                    $label_options["BoxAlpha"] = 50;
                    $label_options["BorderOffset"] = 4;
                    $label_options["RoundedRadius"] = 3;
                    $label_options["BoxRounded"] = true;
                    $label_options["NoShadow"] = true;

                    $this->pChartObject->drawText($x, $y + $Size + $TextPadding, $Name, $label_options);
                }
            }
        }

        /* Draw the vectors */
        if ($DrawVectors) {
            foreach ($this->Data as $key => $settings) {
                $x1 = $settings["X"];
                $y1 = $settings["Y"];

                if (isset($settings["Vectors"]) && $settings["Type"] != NODE_TYPE_CENTRAL) {
                    foreach ($settings["Vectors"] as $id => $vector) {
                        $type = $vector["Type"];
                        $force = $vector["Force"];
                        $angle = $vector["Angle"];
                        $factor = $type == "A" ? $this->MagneticForceA : $this->MagneticForceR;
                        $color = $type == "A" ? array("FillR" => 255, "FillG" => 0, "FillB" => 0) : array("FillR" => 0, "FillG" => 255, "FillB" => 0);

                        $x2 = cos(deg2rad($angle)) * $force * $factor + $x1;
                        $y2 = sin(deg2rad($angle)) * $force * $factor + $y1;

                        $this->pChartObject->drawArrow($x1, $y1, $x2, $y2, $color);
                    }
                }
            }
        }

        return [
            "Pass" => $jobs, 
            "Conflicts" => $conflicts
        ];
    }

    /**
     * Return the distance between two points
     * @param $x1
     * @param $y1
     * @param $x2
     * @param $y2
     * 
     * @return float
     */
    function getDistance($x1, $y1, $x2, $y2) {
        return sqrt(($x2 - $x1) * ($x2 - $x1) + ($y2 - $y1) * ($y2 - $y1));
    }

    /**
     * Return the angle made by a line and the X axis
     * @param $x1
     * @param $y1
     * @param $x2
     * @param $y2
     * 
     * @return float|int
     */
    function getAngle($x1, $y1, $x2, $y2) {
        $Opposite = $y2 - $y1;
        $Adjacent = $x2 - $x1;
        $angle = rad2deg(atan2($Opposite, $Adjacent));
        if ($angle > 0) {
            return $angle;
        } else {
            return 360 - abs($angle);
        }
    }

    /**
     * @param $x1
     * @param $y1
     * @param $x2
     * @param $y2
     * @param $x3
     * @param $y3
     * @param $x4
     * @param $y4
     * 
     * @return bool
     */
    function intersect($x1, $y1, $x2, $y2, $x3, $y3, $x4, $y4) {
        $A = (($x3 * $y4 - $x4 * $y3) * ($x1 - $x2) - ($x1 * $y2 - $x2 * $y1) * ($x3 - $x4));
        $B = (($y1 - $y2) * ($x3 - $x4) - ($y3 - $y4) * ($x1 - $x2));

        if ($B == 0) {
            return false;
        }
        $xi = $A / $B;

        $C = ($x1 - $x2);
        if ($C == 0) {
            return false;
        }
        $yi = $xi * (($y1 - $y2) / $C) + (($x1 * $y2 - $x2 * $y1) / $C);

        if ($xi >= min($x1, $x2) && $xi >= min($x3, $x4) && $xi <= max($x1, $x2) && $xi <= max($x3, $x4)) {
            if ($yi >= min($y1, $y2) && $yi >= min($y3, $y4) && $yi <= max($y1, $y2) && $yi <= max($y3, $y4)) {
                return false;
            }
        }

        return false;
    }
}