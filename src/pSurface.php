<?php
 /*
     pSurface - class to draw surface charts

     Version     : 2.1.3
     Made by     : Jean-Damien POGOLOTTI
     Last Update : 09/09/11

     This file can be distributed under the license you can find at :

                     http://www.pchart.net/license

     You can find the whole class documentation on the pChart web site.
 */

namespace Webklex\pChart;

 define("UNKNOWN"		, 0.123456789);
 define("IGNORED"		, -1);

 define("LABEL_POSITION_LEFT"	, 880001);
 define("LABEL_POSITION_RIGHT"	, 880002);
 define("LABEL_POSITION_TOP"	, 880003);
 define("LABEL_POSITION_BOTTOM"	, 880004);

/**
 * Class pSurface
 *
 * @package Webklex\pChart
 */
 class pSurface {

     /** @var pDraw $pDraw */
    var $pDraw;
    var $GridSizeX;
    var $GridSizeY;
    var $GridSize;
    var $points;

     /**
      * pSurface constructor.
      * @param $pDraw
      */
    function __construct($pDraw) {
        $this->pDraw = $pDraw;
        $this->GridSize     = 10;
        $this->Points       = [];
    }

     /**
      * Define the grid size and initialise the 2D matrix
      * @param int $x_size
      * @param int $y_size
      */
    function setGrid($x_size = 10, $y_size = 10) {
        for($x = 0; $x <= $x_size; $x++) {
            for($y = 0; $y <= $y_size; $y++) {
                $this->Points[$x][$y] = UNKNOWN;
            }
        }

        $this->GridSizeX = $x_size;
        $this->GridSizeY = $y_size;
    }

     /**
      * Add a point on the grid
      * @param integer $x
      * @param integer $y
      * @param mixed   $value
      * @param bool    $force
      *
      * @return boolean
      */
    function addPoint($x, $y, $value, $force = true) {
        if (($x < 0 || $x > $this->GridSizeX) || ($y < 0 || $y > $this->GridSizeY)) { 
            return false; 
        }

        if ( $this->Points[$x][$y] == UNKNOWN || $force ) {
            $this->Points[$x][$y] = $value;
        } elseif ( $this->Points[$x][$y] == UNKNOWN ) {
            $this->Points[$x][$y] = $value;
        } else {
            $this->Points[$x][$y] = ($this->Points[$x][$y] + $value) / 2;
        }

        return true;
    }

     /**
      * Write the X labels
      * @param array $format
      *
      * @return int
      */
    function writeXLabels($format = []) {
        $default = [
            "R" => $this->pDraw->FontColorR,
            "G" => $this->pDraw->FontColorG,
            "B" => $this->pDraw->FontColorB,
            "Alpha" => $this->pDraw->FontColorA,
            "Angle" => 0,
            "Padding"   => 5,
            "Position"  => LABEL_POSITION_TOP,
            "Labels"    => null,
            "CountOffset"   => 0,
        ];
        $format = array_merge($default, $format);

         if ( $format['Labels'] != null && !is_array($format['Labels']) ) {
             $format['Labels'] = [$format['Labels']];
         }

         $x0    = $this->pDraw->GraphAreaX1;
         $x_size = ($this->pDraw->GraphAreaX2 - $this->pDraw->GraphAreaX1) / ($this->GridSizeX+1);

         $settings = [
             "R"    => $format['R'],
             "G"    => $format['G'],
             "B"    => $format['B'],
             "Alpha"    => $format['Alpha'],
             "Angle"    => $format['Angle']
         ];

         if($format['Position'] == LABEL_POSITION_TOP ) {
             $y_pos    = $this->pDraw->GraphAreaY1 - $format['Padding'];
             $settings["Align"] = $format['Angle'] == 0 ? TEXT_ALIGN_BOTTOMMIDDLE : TEXT_ALIGN_MIDDLELEFT;
         } elseif ( $format['Position'] == LABEL_POSITION_BOTTOM ) {
             $y_pos    = $this->pDraw->GraphAreaY2 + $format['Padding'];
             $settings["Align"] = $format['Angle'] == 0 ? TEXT_ALIGN_BOTTOMMIDDLE : TEXT_ALIGN_MIDDLELEFT;
         } else {
             return -1;
         }

         for($x=0; $x <= $this->GridSizeX; $x++) {
            $x_pos = floor($x0 + $x * $x_size + $x_size / 2);

            if( $format['Labels'] == null ) {
                $value = $x + $format['CountOffset'];
            } else {
                $value = isset($format['Labels'][$x]) ? $format['Labels'][$x] : $x + $format['CountOffset'];
            }

            $this->pDraw->drawText($x_pos, $y_pos, $value, $settings);
         }

         return 1;
    }

     /**
      * Write the Y labels
      * @param array $format
      * 
      * @return int
      */
    function writeYLabels($format = []) {
        $default = [
            "R" => $this->pDraw->FontColorR,
            "G" => $this->pDraw->FontColorG,
            "B" => $this->pDraw->FontColorB,
            "Alpha" => $this->pDraw->FontColorA,
            "Angle" => 0,
            "Padding"   => 5,
            "Position"  => LABEL_POSITION_TOP,
            "Labels"    => null,
            "CountOffset"   => 0,
        ];
        $format = array_merge($default, $format);

        if ( $format['Labels'] != null && !is_array($format['Labels']) ) {
            $format['Labels'] = [$format['Labels']];
        }

        $y0    = $this->pDraw->GraphAreaY1;
        $y_size = ($this->pDraw->GraphAreaY2 - $this->pDraw->GraphAreaY1) / ($this->GridSizeY+1);

        $settings = [
            "R"    => $format['R'],
            "G"    => $format['G'],
            "B"    => $format['B'],
            "Alpha"    => $format['Alpha'],
            "Angle"    => $format['Angle']
        ];
        
        if($format['Position'] == LABEL_POSITION_TOP ) {
            $x_pos    = $this->pDraw->GraphAreaX1 - $format['Padding'];
            $settings["Align"] = $format['Angle'] == 0 ? TEXT_ALIGN_BOTTOMMIDDLE : TEXT_ALIGN_MIDDLELEFT;
        } elseif ( $format['Position'] == LABEL_POSITION_BOTTOM ) {
            $x_pos    = $this->pDraw->GraphAreaX2 + $format['Padding'];
            $settings["Align"] = $format['Angle'] == 0 ? TEXT_ALIGN_BOTTOMMIDDLE : TEXT_ALIGN_MIDDLELEFT;
        } else {
            return -1;
        }
        
        for($y = 0; $y <= $this->GridSizeY; $y++) {
            $y_pos = floor($y0 + $y * $y_size + $y_size / 2);
            
            if( $format['Labels'] == null ) {
                $value = $y + $format['CountOffset'];
            } else {
                $value = isset($format['Labels'][$y]) ? $format['Labels'][$y] : $y + $format['CountOffset'];
            }
            
            $this->pDraw->drawText($x_pos, $y_pos, $value, $settings);
        }

        return 1;
    }

     /**
      * Draw the area arround the specified Threshold
      * @param $threshold
      * @param array $format
      */
    function drawContour($threshold, $format = []) {
        $default = [
            "R" => 0,
            "G" => 0,
            "B" => 0,
            "Alpha" => 100,
            "Ticks" => 3,
            "Padding"   => 0
        ];
        $format = array_merge($default, $format);

        $color = [
            "R"    => $format['R'],
            "G"    => $format['G'],
            "B"    => $format['B'],
            "Alpha"    => $format['Alpha'],
            "Ticks"    => $format['Ticks']
        ];
        
        $x0    = $this->pDraw->GraphAreaX1;
        $y0    = $this->pDraw->GraphAreaY1;
        $x_size = ($this->pDraw->GraphAreaX2 - $this->pDraw->GraphAreaX1) / ($this->GridSizeX+1);
        $y_size = ($this->pDraw->GraphAreaY2 - $this->pDraw->GraphAreaY1) / ($this->GridSizeY+1);
        
        for($x = 0; $x <= $this->GridSizeX; $x++) {
            for($y = 0; $y <= $this->GridSizeY; $y++) {
                $value = $this->Points[$x][$y];

                if ( $value != UNKNOWN && $value != IGNORED && $value >= $threshold) {
                    $x1 = floor($x0 + $x * $x_size ) + $format['Padding'];
                    $y1 = floor($y0 + $y * $y_size ) + $format['Padding'];
                    $x2 = floor($x0 + $x * $x_size + $x_size);
                    $y2 = floor($y0 + $y * $y_size + $y_size);
         
                    if ( $x > 0 && $this->Points[$x-1][$y] != UNKNOWN && $this->Points[$x-1][$y] != IGNORED && $this->Points[$x-1][$y] < $threshold)
                       $this->pDraw->drawLine($x1,$y1,$x1,$y2,$color);
                    if ( $y > 0 && $this->Points[$x][$y-1] != UNKNOWN && $this->Points[$x][$y-1] != IGNORED && $this->Points[$x][$y-1] < $threshold)
                       $this->pDraw->drawLine($x1,$y1,$x2,$y1,$color);
                    if ( $x < $this->GridSizeX && $this->Points[$x+1][$y] != UNKNOWN && $this->Points[$x+1][$y] != IGNORED && $this->Points[$x+1][$y] < $threshold)
                       $this->pDraw->drawLine($x2,$y1,$x2,$y2,$color);
                    if ( $y < $this->GridSizeY && $this->Points[$x][$y+1] != UNKNOWN && $this->Points[$x][$y+1] != IGNORED && $this->Points[$x][$y+1] < $threshold)
                       $this->pDraw->drawLine($x1,$y2,$x2,$y2,$color);
                }
            }
        }
    }

     /**
      * Draw the surface chart
      * @param array $format
      */
    function drawSurface($format = []) {
        $default = [
            "Palette" => null,
            "ShadeR1" => 77,
            "ShadeG1" => 205,
            "ShadeB1" => 21,
            "ShadeA1" => 40,
            "ShadeR2" => 227,
            "ShadeG2" => 135,
            "ShadeB2" => 61,
            "ShadeA2" => 100,
            "Border" => false,
            "BorderR" => 0,
            "BorderG" => 0,
            "BorderB" => 0,
            "Surrounding" => -1,
            "Padding" => 1,
        ];
        $format = array_merge($default, $format);

        $x0    = $this->pDraw->GraphAreaX1;
        $y0    = $this->pDraw->GraphAreaY1;
        $x_size = ($this->pDraw->GraphAreaX2 - $this->pDraw->GraphAreaX1) / ($this->GridSizeX+1);
        $y_size = ($this->pDraw->GraphAreaY2 - $this->pDraw->GraphAreaY1) / ($this->GridSizeY+1);

        $default_palette = [
            "R" => 0,
            "G" => 0,
            "B" => 0,
            "Alpha" => 1000,
        ];
        $palette = $format['Palette'];
        

        for($x = 0; $x <= $this->GridSizeX; $x++) {
            for($y = 0; $y <= $this->GridSizeY; $y++) {
                $value = $this->Points[$x][$y];
        
                if ( $value != UNKNOWN && $value != IGNORED ) {
                    $x1 = floor($x0 + $x * $x_size ) + $format['Padding'];
                    $y1 = floor($y0 + $y * $y_size ) + $format['Padding'];
                    $x2 = floor($x0 + $x * $x_size + $x_size);
                    $y2 = floor($y0 + $y * $y_size + $y_size);
                    
                    $color = $default_palette;
                    
                    if ( $palette != null ) {
                        if ( isset($palette[$value]) ) {
                            $color = array_merge($default_palette, $palette[$value]);
                        }
                    } else {
                        $color = [
                            "R" => (($format['ShadeR2'] - $format['ShadeR1']) / 100) * $value + $format['ShadeR1'],
                            "G" => (($format['ShadeG2'] - $format['ShadeG1']) / 100) * $value + $format['ShadeG1'],
                            "B" => (($format['ShadeB2'] - $format['ShadeB1']) / 100) * $value + $format['ShadeB1'],
                            "Alpha" => (($format['ShadeA2'] - $format['ShadeA1']) / 100) * $value + $format['ShadeA1'],
                        ];
                    }
                    
                    if ( $format['Boarder'] ) { 
                        $color["BorderR"] = $format['BorderR']; 
                        $color["BorderG"] = $format['BorderG']; 
                        $color["BorderB"] = $format['BorderB']; 
                    }
                    if ( $format['Surrounding'] != -1 ) { 
                        $color["BorderR"] = $color['R'] + $format['Surrounding']; 
                        $color["BorderG"] = $color['G'] + $format['Surrounding']; 
                        $color["BorderB"] = $color['B'] + $format['Surrounding']; 
                    }
                    
                    $this->pDraw->drawFilledRectangle($x1, $y1,$x2 - 1,$y2 - 1, $color);
                }
            }
        }
    }

     /**
      * Compute the missing points
      */
    function computeMissing() {
        $missing = [];
        for($x = 0; $x <= $this->GridSizeX; $x++) {
            for($y = 0; $y <= $this->GridSizeY; $y++) {
                if ( $this->Points[$x][$y] == UNKNOWN ){
                    $missing[] = $x.",".$y;
                }
            }
        }
        
        shuffle($missing);
        
        foreach($missing as $Key => $pos) {
            $pos = preg_split("/,/",$pos);
            $x    = $pos[0];
            $y    = $pos[1];
        
            if ( $this->Points[$x][$y] == UNKNOWN ) {
                $nearest_neighbor = $this->getNearestNeighbor($x,$y);
                $value = 0; 
                $points = 0;
        
                for($xi = $x - $nearest_neighbor; $xi <= $x + $nearest_neighbor; $xi++) {
                    for($yi = $y - $nearest_neighbor; $yi <= $y + $nearest_neighbor; $yi++) {
        
                        if ($xi >=0 && $yi >= 0 && $xi <= $this->GridSizeX && $yi <= $this->GridSizeY && $this->Points[$xi][$yi] != UNKNOWN && $this->Points[$xi][$yi] != IGNORED) {
                            $value = $value + $this->Points[$xi][$yi]; $points++;
                        }
                    }
                }
                
                if ( $points != 0 ) { 
                    $this->Points[$x][$y] = $value / $points; 
                }
            }
        }
    }

    /**
    * Return the nearest Neighbor distance of a point
    * @param $xp
    * @param $yp
    * 
    * @return float|mixed
    */
    function getNearestNeighbor($xp, $yp) {
        $nearest = UNKNOWN;
        
        for($x = 0; $x <= $this->GridSizeX; $x++) {
            for($y = 0; $y <= $this->GridSizeY; $y++) {
                if ( $this->Points[$x][$y] != UNKNOWN && $this->Points[$x][$y] != IGNORED ) {
                    $distance_x = max($xp,$x)-min($xp,$x);
                    $distance_y = max($yp,$y)-min($yp,$y);
                    $distance    = max($distance_x,$distance_y);

                    if ( $distance < $nearest || $nearest == UNKNOWN ) {
                        $nearest = $distance;
                    }
                }
            }
        }

        return $nearest;
    }
}