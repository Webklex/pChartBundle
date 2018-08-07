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
    var $Points;

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
      * @param int $XSize
      * @param int $YSize
      */
    function setGrid($XSize = 10, $YSize = 10) {
        for($X = 0; $X <= $XSize; $X++) {
            for($Y = 0; $Y <= $YSize; $Y++) {
                $this->Points[$X][$Y]=UNKNOWN;
            }
        }

        $this->GridSizeX = $XSize;
        $this->GridSizeY = $YSize;
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
        if (($x < 0 || $x > $this->GridSizeX) || ($y < 0 || $y > $this->GridSizeY)) { return false; }

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
      * @param array $Format
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

        foreach($default as $key => $value){
            if(isset($format[$key]) === false){
                $format[$key] = $value;
            }
        }

         if ( $format['Labels'] != null && !is_array($format['Labels']) ) {
             $format['Labels'] = [$format['Labels']];
         }

         $X0    = $this->pDraw->GraphAreaX1;
         $XSize = ($this->pDraw->GraphAreaX2 - $this->pDraw->GraphAreaX1) / ($this->GridSizeX+1);

         $Settings = [
             "R"    => $format['R'],
             "G"    => $format['G'],
             "B"    => $format['B'],
             "Alpha"    => $format['Alpha'],
             "Angle"    => $format['Angle']
         ];

         if($format['Position'] == LABEL_POSITION_TOP ) {
             $YPos    = $this->pDraw->GraphAreaY1 - $format['Padding'];
             $Settings["Align"] = $format['Angle'] == 0 ? TEXT_ALIGN_BOTTOMMIDDLE : TEXT_ALIGN_MIDDLELEFT;
         } elseif ( $format['Position'] == LABEL_POSITION_BOTTOM ) {
             $YPos    = $this->pDraw->GraphAreaY2 + $format['Padding'];
             $Settings["Align"] = $format['Angle'] == 0 ? TEXT_ALIGN_BOTTOMMIDDLE : TEXT_ALIGN_MIDDLELEFT;
         } else {
             return -1;
         }

         for($x=0; $x <= $this->GridSizeX; $x++) {
            $XPos = floor($X0 + $x * $XSize + $XSize / 2);

            if( $format['Labels'] == null ) {
                $Value = $x + $format['CountOffset'];
            } else {
                $Value = isset($format['Labels'][$x]) ? $format['Labels'][$x] : $x + $format['CountOffset'];
            }

            $this->pDraw->drawText($XPos, $YPos, $Value, $Settings);
         }

         return 1;
    }

    /* Write the Y labels */
    function writeYLabels($Format="") {
     $R			= isset($Format["R"]) ? $Format["R"] : $this->pDraw->FontColorR;
     $G			= isset($Format["G"]) ? $Format["G"] : $this->pDraw->FontColorG;
     $B			= isset($Format["B"]) ? $Format["B"] : $this->pDraw->FontColorB;
     $Alpha		= isset($Format["Alpha"]) ? $Format["Alpha"] : $this->pDraw->FontColorA;
     $Angle		= isset($Format["Angle"]) ? $Format["Angle"] : 0;
     $Padding		= isset($Format["Padding"]) ? $Format["Padding"] : 5;
     $Position		= isset($Format["Position"]) ? $Format["Position"] : LABEL_POSITION_LEFT;
     $Labels		= isset($Format["Labels"]) ? $Format["Labels"] : null;
     $CountOffset	= isset($Format["CountOffset"]) ? $Format["CountOffset"] : 0;

     if ( $Labels != null && !is_array($Labels) ) { $Label = $Labels; $Labels = ""; $Labels[] = $Label; }

     $Y0    = $this->pDraw->GraphAreaY1;
     $YSize = ($this->pDraw->GraphAreaY2 - $this->pDraw->GraphAreaY1) / ($this->GridSizeY+1);

     $Settings = array("Angle"=>$Angle,"R"=>$R,"G"=>$G,"B"=>$B,"Alpha"=>$Alpha);
     if ( $Position == LABEL_POSITION_LEFT )
       { $XPos    = $this->pDraw->GraphAreaX1 - $Padding; $Settings["Align"] = TEXT_ALIGN_MIDDLERIGHT; }
     elseif ( $Position == LABEL_POSITION_RIGHT )
       { $XPos    = $this->pDraw->GraphAreaX2 + $Padding; $Settings["Align"] = TEXT_ALIGN_MIDDLELEFT; }
     else
       return -1;

     for($Y=0;$Y<=$this->GridSizeY;$Y++)
       {
        $YPos = floor($Y0+$Y*$YSize + $YSize/2);

        if( $Labels == null || !isset($Labels[$Y]) )
          $Value = $Y+$CountOffset;
        else
          $Value = $Labels[$Y];

        $this->pDraw->drawText($XPos,$YPos,$Value,$Settings);
       }
    }

    /* Draw the area arround the specified Threshold */
    function drawContour($Threshold,$Format="")
    {
     $R		= isset($Format["R"]) ? $Format["R"] : 0;
     $G		= isset($Format["G"]) ? $Format["G"] : 0;
     $B		= isset($Format["B"]) ? $Format["B"] : 0;
     $Alpha	= isset($Format["Alpha"]) ? $Format["Alpha"] : 100;
     $Ticks	= isset($Format["Ticks"]) ? $Format["Ticks"] : 3;
     $Padding	= isset($Format["Padding"]) ? $Format["Padding"] : 0;

     $X0    = $this->pDraw->GraphAreaX1;
     $Y0    = $this->pDraw->GraphAreaY1;
     $XSize = ($this->pDraw->GraphAreaX2 - $this->pDraw->GraphAreaX1) / ($this->GridSizeX+1);
     $YSize = ($this->pDraw->GraphAreaY2 - $this->pDraw->GraphAreaY1) / ($this->GridSizeY+1);

     $Color = array("R"=>$R,"G"=>$G,"B"=>$B,"Alpha"=>$Alpha,"Ticks"=>$Ticks);

     for($X=0;$X<=$this->GridSizeX;$X++)
       {
        for($Y=0;$Y<=$this->GridSizeY;$Y++)
          {
        $Value = $this->Points[$X][$Y];

        if ( $Value != UNKNOWN && $Value != IGNORED && $Value >= $Threshold)
          {
           $X1 = floor($X0+$X*$XSize)+$Padding;
           $Y1 = floor($Y0+$Y*$YSize)+$Padding;
           $X2 = floor($X0+$X*$XSize+$XSize);
           $Y2 = floor($Y0+$Y*$YSize+$YSize);

           if ( $X > 0 && $this->Points[$X-1][$Y] != UNKNOWN && $this->Points[$X-1][$Y] != IGNORED && $this->Points[$X-1][$Y] < $Threshold)
              $this->pDraw->drawLine($X1,$Y1,$X1,$Y2,$Color);
           if ( $Y > 0 && $this->Points[$X][$Y-1] != UNKNOWN && $this->Points[$X][$Y-1] != IGNORED && $this->Points[$X][$Y-1] < $Threshold)
              $this->pDraw->drawLine($X1,$Y1,$X2,$Y1,$Color);
           if ( $X < $this->GridSizeX && $this->Points[$X+1][$Y] != UNKNOWN && $this->Points[$X+1][$Y] != IGNORED && $this->Points[$X+1][$Y] < $Threshold)
              $this->pDraw->drawLine($X2,$Y1,$X2,$Y2,$Color);
           if ( $Y < $this->GridSizeY && $this->Points[$X][$Y+1] != UNKNOWN && $this->Points[$X][$Y+1] != IGNORED && $this->Points[$X][$Y+1] < $Threshold)
              $this->pDraw->drawLine($X1,$Y2,$X2,$Y2,$Color);
          }
          }
       }
    }

    /* Draw the surface chart */
    function drawSurface($Format="")
    {
     $Palette		= isset($Format["Palette"]) ? $Format["Palette"] : null;
     $ShadeR1		= isset($Format["ShadeR1"]) ? $Format["ShadeR1"] : 77;
     $ShadeG1		= isset($Format["ShadeG1"]) ? $Format["ShadeG1"] : 205;
     $ShadeB1		= isset($Format["ShadeB1"]) ? $Format["ShadeB1"] : 21;
     $ShadeA1		= isset($Format["ShadeA1"]) ? $Format["ShadeA1"] : 40;
     $ShadeR2		= isset($Format["ShadeR2"]) ? $Format["ShadeR2"] : 227;
     $ShadeG2		= isset($Format["ShadeG2"]) ? $Format["ShadeG2"] : 135;
     $ShadeB2		= isset($Format["ShadeB2"]) ? $Format["ShadeB2"] : 61;
     $ShadeA2		= isset($Format["ShadeA2"]) ? $Format["ShadeA2"] : 100;
     $Border		= isset($Format["Border"]) ? $Format["Border"] : false;
     $BorderR		= isset($Format["BorderR"]) ? $Format["BorderR"] : 0;
     $BorderG		= isset($Format["BorderG"]) ? $Format["BorderG"] : 0;
     $BorderB		= isset($Format["BorderB"]) ? $Format["BorderB"] : 0;
     $Surrounding	= isset($Format["Surrounding"]) ? $Format["Surrounding"] : -1;
     $Padding		= isset($Format["Padding"]) ? $Format["Padding"] : 1;

     $X0    = $this->pDraw->GraphAreaX1;
     $Y0    = $this->pDraw->GraphAreaY1;
     $XSize = ($this->pDraw->GraphAreaX2 - $this->pDraw->GraphAreaX1) / ($this->GridSizeX+1);
     $YSize = ($this->pDraw->GraphAreaY2 - $this->pDraw->GraphAreaY1) / ($this->GridSizeY+1);

     for($X=0;$X<=$this->GridSizeX;$X++)
       {
        for($Y=0;$Y<=$this->GridSizeY;$Y++)
          {
        $Value = $this->Points[$X][$Y];

        if ( $Value != UNKNOWN && $Value != IGNORED )
          {
           $X1 = floor($X0+$X*$XSize)+$Padding;
           $Y1 = floor($Y0+$Y*$YSize)+$Padding;
           $X2 = floor($X0+$X*$XSize+$XSize);
           $Y2 = floor($Y0+$Y*$YSize+$YSize);

           if ( $Palette != null )
              {
              if ( isset($Palette[$Value]) && isset($Palette[$Value]["R"]) ) { $R = $Palette[$Value]["R"]; } else { $R = 0; }
              if ( isset($Palette[$Value]) && isset($Palette[$Value]["G"]) ) { $G = $Palette[$Value]["G"]; } else { $G = 0; }
              if ( isset($Palette[$Value]) && isset($Palette[$Value]["B"]) ) { $B = $Palette[$Value]["B"]; } else { $B = 0; }
              if ( isset($Palette[$Value]) && isset($Palette[$Value]["Alpha"]) ) { $Alpha = $Palette[$Value]["Alpha"]; } else { $Alpha = 1000; }
              }
           else
              {
              $R = (($ShadeR2-$ShadeR1)/100)*$Value + $ShadeR1;
              $G = (($ShadeG2-$ShadeG1)/100)*$Value + $ShadeG1;
              $B = (($ShadeB2-$ShadeB1)/100)*$Value + $ShadeB1;
              $Alpha = (($ShadeA2-$ShadeA1)/100)*$Value + $ShadeA1;
              }

           $Settings = array("R"=>$R,"G"=>$G,"B"=>$B,"Alpha"=>$Alpha);
           if ( $Border ) { $Settings["BorderR"] = $BorderR; $Settings["BorderG"] = $BorderG; $Settings["BorderB"] = $BorderB; }
           if ( $Surrounding != -1 ) { $Settings["BorderR"] = $R+$Surrounding; $Settings["BorderG"] = $G+$Surrounding; $Settings["BorderB"] = $B+$Surrounding; }

           $this->pDraw->drawFilledRectangle($X1,$Y1,$X2-1,$Y2-1,$Settings);
          }
          }
       }
    }

    /* Compute the missing points */
    function computeMissing()
    {
     $Missing = "";
     for($X=0;$X<=$this->GridSizeX;$X++)
       {
        for($Y=0;$Y<=$this->GridSizeY;$Y++)
          {
        if ( $this->Points[$X][$Y] == UNKNOWN )
          $Missing[] = $X.",".$Y;
          }
       }
     shuffle($Missing);
     
     foreach($Missing as $Key => $Pos)
       {
        $Pos = preg_split("/,/",$Pos);
        $X    = $Pos[0];
        $Y    = $Pos[1];

        if ( $this->Points[$X][$Y] == UNKNOWN )
          {
        $NearestNeighbor = $this->getNearestNeighbor($X,$Y);

        $Value = 0; $Points = 0;
        for($Xi=$X-$NearestNeighbor;$Xi<=$X+$NearestNeighbor;$Xi++)
          {
           for($Yi=$Y-$NearestNeighbor;$Yi<=$Y+$NearestNeighbor;$Yi++)
              {
              if ($Xi >=0 && $Yi >= 0 && $Xi <= $this->GridSizeX && $Yi <= $this->GridSizeY && $this->Points[$Xi][$Yi] != UNKNOWN && $this->Points[$Xi][$Yi] != IGNORED)
              {
               $Value = $Value + $this->Points[$Xi][$Yi]; $Points++;
              }
              }
          }

        if ( $Points != 0 ) { $this->Points[$X][$Y] = $Value / $Points; }
          }
       }
    }

    /* Return the nearest Neighbor distance of a point */
    function getNearestNeighbor($Xp,$Yp)
    {
     $Nearest = UNKNOWN;
     for($X=0;$X<=$this->GridSizeX;$X++)
       {
        for($Y=0;$Y<=$this->GridSizeY;$Y++)
          {
        if ( $this->Points[$X][$Y] != UNKNOWN && $this->Points[$X][$Y] != IGNORED )
          {
           $DistanceX = max($Xp,$X)-min($Xp,$X);
           $DistanceY = max($Yp,$Y)-min($Yp,$Y);
           $Distance    = max($DistanceX,$DistanceY);
           if ( $Distance < $Nearest || $Nearest == UNKNOWN ) { $Nearest = $Distance; }
          }
          }
       }
     return $Nearest;
    }
    }
?>
