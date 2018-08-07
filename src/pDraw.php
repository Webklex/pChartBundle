<?php
 /*
     pDraw - class extension with drawing methods

     Version     : 2.1.3
     Made by     : Jean-Damien POGOLOTTI
     Last Update : 09/09/11

     This file can be distributed under the license you can find at :

                       http://www.pchart.net/license

     You can find the whole class documentation on the pChart web site.
 */

namespace Webklex\pChart;

 define("DIRECTION_VERTICAL"		, 690001);
 define("DIRECTION_HORIZONTAL"		, 690002);

 define("SCALE_POS_LEFTRIGHT"		, 690101);
 define("SCALE_POS_TOPBOTTOM"		, 690102);

 define("SCALE_MODE_FLOATING"		, 690201);
 define("SCALE_MODE_START0"		, 690202);
 define("SCALE_MODE_ADDALL"		, 690203);
 define("SCALE_MODE_ADDALL_START0"	, 690204);
 define("SCALE_MODE_MANUAL"		, 690205);

 define("SCALE_SKIP_NONE"		, 690301);
 define("SCALE_SKIP_SAME"		, 690302);
 define("SCALE_SKIP_NUMBERS"		, 690303);

 define("TEXT_ALIGN_TOPLEFT"		, 690401);
 define("TEXT_ALIGN_TOPMIDDLE"		, 690402);
 define("TEXT_ALIGN_TOPRIGHT"		, 690403);
 define("TEXT_ALIGN_MIDDLELEFT"		, 690404);
 define("TEXT_ALIGN_MIDDLEMIDDLE"	, 690405);
 define("TEXT_ALIGN_MIDDLERIGHT"	, 690406);
 define("TEXT_ALIGN_BOTTOMLEFT"		, 690407);
 define("TEXT_ALIGN_BOTTOMMIDDLE"	, 690408);
 define("TEXT_ALIGN_BOTTOMRIGHT"	, 690409);

 define("POSITION_TOP"                  , 690501);
 define("POSITION_BOTTOM"               , 690502);

 define("LABEL_POS_LEFT"		, 690601);
 define("LABEL_POS_CENTER"		, 690602);
 define("LABEL_POS_RIGHT"		, 690603);
 define("LABEL_POS_TOP"			, 690604);
 define("LABEL_POS_BOTTOM"		, 690605);
 define("LABEL_POS_INSIDE"		, 690606);
 define("LABEL_POS_OUTSIDE"		, 690607);

 define("ORIENTATION_HORIZONTAL"	, 690701);
 define("ORIENTATION_VERTICAL"		, 690702);
 define("ORIENTATION_AUTO"		, 690703);

 define("LEGEND_NOBORDER"		, 690800);
 define("LEGEND_BOX"			, 690801);
 define("LEGEND_ROUND"			, 690802);

 define("LEGEND_VERTICAL"		, 690901);
 define("LEGEND_HORIZONTAL"		, 690902);

 define("LEGEND_FAMILY_BOX"		, 691051);
 define("LEGEND_FAMILY_CIRCLE"		, 691052);
 define("LEGEND_FAMILY_LINE"		, 691053);

 define("DISPLAY_AUTO"			, 691001);
 define("DISPLAY_MANUAL"		, 691002);

 define("LABELING_ALL"			, 691011);
 define("LABELING_DIFFERENT"		, 691012);

 define("BOUND_MIN"			, 691021);
 define("BOUND_MAX"			, 691022);
 define("BOUND_BOTH"			, 691023);

 define("BOUND_LABEL_POS_TOP"		, 691031);
 define("BOUND_LABEL_POS_BOTTOM"	, 691032);
 define("BOUND_LABEL_POS_AUTO"		, 691033);

 define("CAPTION_LEFT_TOP"		, 691041);
 define("CAPTION_RIGHT_BOTTOM"		, 691042);

 define("GRADIENT_SIMPLE"		, 691051);
 define("GRADIENT_EFFECT_CAN"		, 691052);

 define("LABEL_TITLE_NOBACKGROUND"	, 691061);
 define("LABEL_TITLE_BACKGROUND"	, 691062);

 define("LABEL_POINT_NONE"		, 691071);
 define("LABEL_POINT_CIRCLE"		, 691072);
 define("LABEL_POINT_BOX"		, 691073);

 define("ZONE_NAME_ANGLE_AUTO"		, 691081);

 define("PI"		, 3.14159265);
 define("ALL"		, 69);
 define("NONE"		, 31);
 define("AUTO"		, 690000);
 define("OUT_OF_SIGHT"	, -10000000000000);

class pDraw {

    public $FontColorR;
    public $FontColorG;
    public $FontColorB;
    public $FontColorA;

    public $GraphAreaX1;
    public $GraphAreaX2;

    public $GraphAreaY1;
    public $GraphAreaY2;

    /** @var pData $DataSet */
    public $DataSet;

    /**
     * Returns the number of drawable series
     *
     * @return int
     */
    function countDrawableSeries() {
        $results = 0;
        $Data = $this->DataSet->getData();

        foreach($Data["Series"] as $serie_name => $serie)  {
            if ( $serie["isDrawable"] == true && $serie_name != $Data["Abscissa"] ) {
                $results++;
            }
        }

        return $results;
    }

    /* Fix box coordinates */
    function fixBoxCoordinates($Xa,$Ya,$Xb,$Yb)
    {
     $X1 = min($Xa,$Xb); $Y1 = min($Ya,$Yb);
     $X2 = max($Xa,$Xb); $Y2 = max($Ya,$Yb);

     return array($X1,$Y1,$X2,$Y2);
    }

    /* Draw a polygon */
    function drawPolygon($points, $format = []) {
     $R			= isset($format["R"]) ? $format["R"] : 0;
     $G			= isset($format["G"]) ? $format["G"] : 0;
     $B			= isset($format["B"]) ? $format["B"] : 0;
     $Alpha		= isset($format["Alpha"]) ? $format["Alpha"] : 100;
     $NoFill		= isset($format["NoFill"]) ? $format["NoFill"] : false;
     $NoBorder		= isset($format["NoBorder"]) ? $format["NoBorder"] : false;
     $BorderR		= isset($format["BorderR"]) ? $format["BorderR"] : $R;
     $BorderG		= isset($format["BorderG"]) ? $format["BorderG"] : $G;
     $BorderB		= isset($format["BorderB"]) ? $format["BorderB"] : $B;
     $BorderAlpha 	= isset($format["Alpha"]) ? $format["Alpha"] : $Alpha / 2;
     $Surrounding	= isset($format["Surrounding"]) ? $format["Surrounding"] : null;
     $SkipX		= isset($format["SkipX"]) ? $format["SkipX"] : OUT_OF_SIGHT;
     $SkipY		= isset($format["SkipY"]) ? $format["SkipY"] : OUT_OF_SIGHT;

     /* Calling the ImageFilledPolygon() function over the $points array will round it */
     $Backup = $points;

     if ( $Surrounding != null ) { $BorderR = $R+$Surrounding; $BorderG = $G+$Surrounding; $BorderB = $B+$Surrounding; }

     if ( $SkipX != OUT_OF_SIGHT ) { $SkipX = floor($SkipX); }
     if ( $SkipY != OUT_OF_SIGHT ) { $SkipY = floor($SkipY); }

     $RestoreShadow = $this->Shadow;
     if ( !$NoFill )
      {
       if ( $this->Shadow && $this->ShadowX != 0 && $this->ShadowY != 0 )
        {
         $this->Shadow = false;
         for($i=0;$i<=count($points)-1;$i=$i+2)
          { $Shadow[] = $points[$i] + $this->ShadowX; $Shadow[] = $points[$i+1] + $this->ShadowY; }
         $this->drawPolygon($Shadow,array("R"=>$this->ShadowR,"G"=>$this->ShadowG,"B"=>$this->ShadowB,"Alpha"=>$this->Shadowa,"NoBorder"=>true));
        }

       $FillColor = $this->allocateColor($this->Picture,$R,$G,$B,$Alpha);

       if ( count($points) >= 6 )
        { ImageFilledPolygon($this->Picture,$points,count($points)/2,$FillColor); }
      }

     if ( !$NoBorder )
      {
       $points = $Backup;

       if ( $NoFill )
        $BorderSettings = array("R"=>$R,"G"=>$G,"B"=>$B,"Alpha"=>$Alpha);
       else
        $BorderSettings = array("R"=>$BorderR,"G"=>$BorderG,"B"=>$BorderB,"Alpha"=>$BorderAlpha);

       for($i=0;$i<=count($points)-1;$i=$i+2)
        {
         if ( isset($points[$i+2]) )
          {
           if ( !($points[$i] == $points[$i+2] && $points[$i] == $SkipX ) && !($points[$i+1] == $points[$i+3] && $points[$i+1] == $SkipY ) )
            $this->drawLine($points[$i],$points[$i+1],$points[$i+2],$points[$i+3],$BorderSettings);
          }
         else
          {
           if ( !($points[$i] == $points[0] && $points[$i] == $SkipX ) && !($points[$i+1] == $points[1] && $points[$i+1] == $SkipY ) )
            $this->drawLine($points[$i],$points[$i+1],$points[0],$points[1],$BorderSettings);
          }
        }
      }

     $this->Shadow = $RestoreShadow;
    }

    /* Apply AALias correction to the rounded box boundaries */
    function offsetCorrection($Value,$Mode)
    {
     $Value = round($Value,1);

     if ( $Value == 0 && $Mode == 1 ) { return(.9); }
     if ( $Value == 0 ) { return 0; }

     if ( $Mode == 1)
      { if ( $Value == 1 ) { return(.9); }; if ( $Value == .1 ) { return(.9); }; if ( $Value == .2 ) { return(.8); }; if ( $Value == .3 ) { return(.8); }; if ( $Value == .4 ) { return(.7); }; if ( $Value == .5 ) { return(.5); }; if ( $Value == .6 ) { return(.8); }; if ( $Value == .7 ) { return(.7); }; if ( $Value == .8 ) { return(.6); }; if ( $Value == .9 ) { return(.9); }; }

     if ( $Mode == 2)
      { if ( $Value == 1 ) { return(.9); }; if ( $Value == .1 ) { return(.1); }; if ( $Value == .2 ) { return(.2); }; if ( $Value == .3 ) { return(.3); }; if ( $Value == .4 ) { return(.4); }; if ( $Value == .5 ) { return(.5); }; if ( $Value == .6 ) { return(.8); }; if ( $Value == .7 ) { return(.7); }; if ( $Value == .8 ) { return(.8); }; if ( $Value == .9 ) { return(.9); }; }

     if ( $Mode == 3)
      { if ( $Value == 1 ) { return(.1); }; if ( $Value == .1 ) { return(.1); }; if ( $Value == .2 ) { return(.2); }; if ( $Value == .3 ) { return(.3); }; if ( $Value == .4 ) { return(.4); }; if ( $Value == .5 ) { return(.9); }; if ( $Value == .6 ) { return(.6); }; if ( $Value == .7 ) { return(.7); }; if ( $Value == .8 ) { return(.4); }; if ( $Value == .9 ) { return(.5); }; }

     if ( $Mode == 4)
      { if ( $Value == 1 ) { return(-1); }; if ( $Value == .1 ) { return(.1); }; if ( $Value == .2 ) { return(.2); }; if ( $Value == .3 ) { return(.3); }; if ( $Value == .4 ) { return(.1); }; if ( $Value == .5 ) { return(-.1); }; if ( $Value == .6 ) { return(.8); }; if ( $Value == .7 ) { return(.1); }; if ( $Value == .8 ) { return(.1); }; if ( $Value == .9 ) { return(.1); }; }
    }

    /* Draw a rectangle with rounded corners */
    function drawRoundedRectangle($X1,$Y1,$X2,$Y2,$Radius,$format="")
    {
     $R	    = isset($format["R"]) ? $format["R"] : 0;
     $G	    = isset($format["G"]) ? $format["G"] : 0;
     $B	    = isset($format["B"]) ? $format["B"] : 0;
     $Alpha = isset($format["Alpha"]) ? $format["Alpha"] : 100;

     list($X1,$Y1,$X2,$Y2) = $this->fixBoxCoordinates($X1,$Y1,$X2,$Y2);

     if ( $X2 - $X1 < $Radius ) { $Radius = floor((($X2-$X1))/2); }
     if ( $Y2 - $Y1 < $Radius ) { $Radius = floor((($Y2-$Y1))/2); }

     $Color = array("R"=>$R,"G"=>$G,"B"=>$B,"Alpha"=>$Alpha,"NoBorder"=>true);

     if ( $Radius <= 0 ) { $this->drawRectangle($X1,$Y1,$X2,$Y2,$Color); return 0; }

     if ( $this->Antialias )
      {
       $this->drawLine($X1+$Radius,$Y1,$X2-$Radius,$Y1,$Color);
       $this->drawLine($X2,$Y1+$Radius,$X2,$Y2-$Radius,$Color);
       $this->drawLine($X2-$Radius,$Y2,$X1+$Radius,$Y2,$Color);
       $this->drawLine($X1,$Y1+$Radius,$X1,$Y2-$Radius,$Color);
      }
     else
      {
       $Color = $this->allocateColor($this->Picture,$R,$G,$B,$Alpha);
       imageline($this->Picture,$X1+$Radius,$Y1,$X2-$Radius,$Y1,$Color);
       imageline($this->Picture,$X2,$Y1+$Radius,$X2,$Y2-$Radius,$Color);
       imageline($this->Picture,$X2-$Radius,$Y2,$X1+$Radius,$Y2,$Color);
       imageline($this->Picture,$X1,$Y1+$Radius,$X1,$Y2-$Radius,$Color);
      }

     $Step = 360 / (2 * PI * $Radius);
     for($i=0;$i<=90;$i=$i+$Step)
      {
       $X = cos(($i+180)*PI/180) * $Radius + $X1 + $Radius;
       $Y = sin(($i+180)*PI/180) * $Radius + $Y1 + $Radius;
       $this->drawAntialiasPixel($X,$Y,array("R"=>$R,"G"=>$G,"B"=>$B,"Alpha"=>$Alpha));

       $X = cos(($i+90)*PI/180) * $Radius + $X1 + $Radius;
       $Y = sin(($i+90)*PI/180) * $Radius + $Y2 - $Radius;
       $this->drawAntialiasPixel($X,$Y,array("R"=>$R,"G"=>$G,"B"=>$B,"Alpha"=>$Alpha));

       $X = cos($i*PI/180) * $Radius + $X2 - $Radius;
       $Y = sin($i*PI/180) * $Radius + $Y2 - $Radius;
       $this->drawAntialiasPixel($X,$Y,array("R"=>$R,"G"=>$G,"B"=>$B,"Alpha"=>$Alpha));

       $X = cos(($i+270)*PI/180) * $Radius + $X2 - $Radius;
       $Y = sin(($i+270)*PI/180) * $Radius + $Y1 + $Radius;
       $this->drawAntialiasPixel($X,$Y,array("R"=>$R,"G"=>$G,"B"=>$B,"Alpha"=>$Alpha));
      }
    }

    /* Draw a rectangle with rounded corners */
    function drawRoundedFilledRectangle($X1,$Y1,$X2,$Y2,$Radius,$format="")
    {
     $R			= isset($format["R"]) ? $format["R"] : 0;
     $G			= isset($format["G"]) ? $format["G"] : 0;
     $B			= isset($format["B"]) ? $format["B"] : 0;
     $BorderR		= isset($format["BorderR"]) ? $format["BorderR"] : -1;
     $BorderG		= isset($format["BorderG"]) ? $format["BorderG"] : -1;
     $BorderB		= isset($format["BorderB"]) ? $format["BorderB"] : -1;
     $Alpha		= isset($format["Alpha"]) ? $format["Alpha"] : 100;
     $Surrounding	= isset($format["Surrounding"]) ? $format["Surrounding"] : null;

     /* Temporary fix for AA issue */
     $Y1 = floor($Y1); $Y2 = floor($Y2); $X1 = floor($X1); $X2 = floor($X2);

     if ( $Surrounding != null ) { $BorderR = $R+$Surrounding; $BorderG = $G+$Surrounding; $BorderB = $B+$Surrounding; }
     if ( $BorderR == -1 ) { $BorderR = $R; $BorderG = $G; $BorderB = $B; }

     list($X1,$Y1,$X2,$Y2) = $this->fixBoxCoordinates($X1,$Y1,$X2,$Y2);

     if ( $X2 - $X1 < $Radius*2 ) { $Radius = floor((($X2-$X1))/4); }
     if ( $Y2 - $Y1 < $Radius*2 ) { $Radius = floor((($Y2-$Y1))/4); }

     $RestoreShadow = $this->Shadow;
     if ( $this->Shadow && $this->ShadowX != 0 && $this->ShadowY != 0 )
      {
       $this->Shadow = false;
       $this->drawRoundedFilledRectangle($X1+$this->ShadowX,$Y1+$this->ShadowY,$X2+$this->ShadowX,$Y2+$this->ShadowY,$Radius,array("R"=>$this->ShadowR,"G"=>$this->ShadowG,"B"=>$this->ShadowB,"Alpha"=>$this->Shadowa));
      }

     $Color = array("R"=>$R,"G"=>$G,"B"=>$B,"Alpha"=>$Alpha,"NoBorder"=>true);

     if ( $Radius <= 0 ) { $this->drawFilledRectangle($X1,$Y1,$X2,$Y2,$Color); return 0; }

     $YTop    = $Y1+$Radius;
     $YBottom = $Y2-$Radius;

     $Step = 360 / (2 * PI * $Radius);
     $Positions = ""; $Radius--; $MinY = ""; $MaxY = "";
     for($i=0;$i<=90;$i=$i+$Step)
      {
       $Xp1 = cos(($i+180)*PI/180) * $Radius + $X1 + $Radius;
       $Xp2 = cos(((90-$i)+270)*PI/180) * $Radius + $X2 - $Radius;
       $Yp  = floor(sin(($i+180)*PI/180) * $Radius + $YTop);
       if ( $MinY == "" || $Yp > $MinY ) { $MinY = $Yp; }

       if ( $Xp1 <= floor($X1) )  { $Xp1++; }
       if ( $Xp2 >= floor($X2) )  { $Xp2--; }
       $Xp1++;

       if ( !isset($Positions[$Yp]) )
        { $Positions[$Yp]["X1"] = $Xp1; $Positions[$Yp]["X2"] = $Xp2; }
       else
        { $Positions[$Yp]["X1"] = ($Positions[$Yp]["X1"]+$Xp1)/2; $Positions[$Yp]["X2"] = ($Positions[$Yp]["X2"]+$Xp2)/2; }

       $Xp1 = cos(($i+90)*PI/180) * $Radius + $X1 + $Radius;
       $Xp2 = cos((90-$i)*PI/180) * $Radius + $X2 - $Radius;
       $Yp  = floor(sin(($i+90)*PI/180) * $Radius + $YBottom);
       if ( $MaxY == "" || $Yp < $MaxY ) { $MaxY = $Yp; }

       if ( $Xp1 <= floor($X1) ) { $Xp1++; }
       if ( $Xp2 >= floor($X2) ) { $Xp2--; }
       $Xp1++;

       if ( !isset($Positions[$Yp]) )
        { $Positions[$Yp]["X1"] = $Xp1; $Positions[$Yp]["X2"] = $Xp2; }
       else
        { $Positions[$Yp]["X1"] = ($Positions[$Yp]["X1"]+$Xp1)/2; $Positions[$Yp]["X2"] = ($Positions[$Yp]["X2"]+$Xp2)/2; }
      }

     $ManualColor  = $this->allocateColor($this->Picture,$R,$G,$B,$Alpha);
     foreach($Positions as $Yp => $Bounds)
      {
       $X1 = $Bounds["X1"]; $X1Dec = $this->getFirstDecimal($X1); if ( $X1Dec != 0 ) { $X1 = floor($X1)+1; }
       $X2 = $Bounds["X2"]; $X2Dec = $this->getFirstDecimal($X2); if ( $X2Dec != 0 ) { $X2 = floor($X2)-1; }
       imageline($this->Picture,$X1,$Yp,$X2,$Yp,$ManualColor);
      }
     $this->drawFilledRectangle($X1,$MinY+1,floor($X2),$MaxY-1,$Color);

     $Radius++;
     $this->drawRoundedRectangle($X1,$Y1,$X2+1,$Y2-1,$Radius,array("R"=>$BorderR,"G"=>$BorderG,"B"=>$BorderB,"Alpha"=>$Alpha));

     $this->Shadow = $RestoreShadow;
    }

    /* Draw a rectangle with rounded corners */
    function drawRoundedFilledRectangle_deprecated($X1,$Y1,$X2,$Y2,$Radius,$format="")
    {
     $R			= isset($format["R"]) ? $format["R"] : 0;
     $G			= isset($format["G"]) ? $format["G"] : 0;
     $B			= isset($format["B"]) ? $format["B"] : 0;
     $BorderR		= isset($format["BorderR"]) ? $format["BorderR"] : -1;
     $BorderG		= isset($format["BorderG"]) ? $format["BorderG"] : -1;
     $BorderB		= isset($format["BorderB"]) ? $format["BorderB"] : -1;
     $Alpha		= isset($format["Alpha"]) ? $format["Alpha"] : 100;
     $Surrounding	= isset($format["Surrounding"]) ? $format["Surrounding"] : null;

     if ( $Surrounding != null ) { $BorderR = $R+$Surrounding; $BorderG = $G+$Surrounding; $BorderB = $B+$Surrounding; }
     if ( $BorderR == -1 ) { $BorderR = $R; $BorderG = $G; $BorderB = $B; }

     list($X1,$Y1,$X2,$Y2) = $this->fixBoxCoordinates($X1,$Y1,$X2,$Y2);

     if ( $X2 - $X1 < $Radius ) { $Radius = floor((($X2-$X1)+2)/2); }
     if ( $Y2 - $Y1 < $Radius ) { $Radius = floor((($Y2-$Y1)+2)/2); }

     $RestoreShadow = $this->Shadow;
     if ( $this->Shadow && $this->ShadowX != 0 && $this->ShadowY != 0 )
      {
       $this->Shadow = false;
       $this->drawRoundedFilledRectangle($X1+$this->ShadowX,$Y1+$this->ShadowY,$X2+$this->ShadowX,$Y2+$this->ShadowY,$Radius,array("R"=>$this->ShadowR,"G"=>$this->ShadowG,"B"=>$this->ShadowB,"Alpha"=>$this->Shadowa));
      }

     if ( $this->getFirstDecimal($X2) >= 5 )  { $XOffset2 = 1; } else { $XOffset2 = 0; }
     if ( $this->getFirstDecimal($X1) <= 5 )  { $XOffset1 = 1; } else { $XOffset1 = 0; }

     if ( !$this->Antialias ) { $XOffset1 = 1; $XOffset2 = 1; }

     $YTop    = floor($Y1+$Radius);
     $YBottom = floor($Y2-$Radius);

     $this->drawFilledRectangle($X1-$XOffset1,$YTop,$X2+$XOffset2,$YBottom,array("R"=>$R,"G"=>$G,"B"=>$B,"Alpha"=>$Alpha,"NoBorder"=>true));

     $Step = 360 / (2 * PI * $Radius);
     $Color  = $this->allocateColor($this->Picture,$R,$G,$B,$Alpha);
     $Color2 = $this->allocateColor($this->Picture,255,0,0,$Alpha);
     $Drawn = "";

     if ( $Alpha < 100 )  { $Drawn[$YTop] = false; }
     if ( $Alpha < 100 )  { $Drawn[$YBottom] = true; }

     for($i=0;$i<=90;$i=$i+$Step)
      {
       $Xp1 = cos(($i+180)*PI/180) * $Radius + $X1 + $Radius;
       $Xp2 = cos(((90-$i)+270)*PI/180) * $Radius + $X2 - $Radius;
       $Yp  = sin(($i+180)*PI/180) * $Radius + $YTop;

       if ( $this->getFirstDecimal($Xp1) > 5 )  { $XOffset1 = 1; } else { $XOffset1 = 0; }
       if ( $this->getFirstDecimal($Xp2) > 5 )  { $XOffset2 = 1; } else { $XOffset2 = 0; }
       if ( $this->getFirstDecimal($Yp) > 5 )  { $YOffset = 1; } else { $YOffset = 0; }

       if ( !isset($Drawn[$Yp+$YOffset]) || $Alpha == 100 )
        imageline($this->Picture,$Xp1+$XOffset1,$Yp+$YOffset,$Xp2+$XOffset2,$Yp+$YOffset,$Color);

       $Drawn[$Yp+$YOffset] = $Xp2;

       $Xp1 = cos(($i+90)*PI/180) * $Radius + $X1 + $Radius;
       $Xp2 = cos((90-$i)*PI/180) * $Radius + $X2 - $Radius;
       $Yp  = sin(($i+90)*PI/180) * $Radius + $YBottom;

       if ( $this->getFirstDecimal($Xp1) > 7 )  { $XOffset1 = 1; } else { $XOffset1 = 0; }
       if ( $this->getFirstDecimal($Xp2) > 7 )  { $XOffset2 = 1; } else { $XOffset2 = 0; }
       if ( $this->getFirstDecimal($Yp) > 5 )  { $YOffset = 1; } else { $YOffset = 0; }

       if ( !isset($Drawn[$Yp+$YOffset]) || $Alpha == 100 )
        imageline($this->Picture,$Xp1+$XOffset1,$Yp+$YOffset,$Xp2+$XOffset2,$Yp+$YOffset,$Color);

       $Drawn[$Yp+$YOffset] = $Xp2;
      }

     $this->drawRoundedRectangle($X1,$Y1,$X2,$Y2,$Radius,array("R"=>$BorderR,"G"=>$BorderG,"B"=>$BorderB,"Alpha"=>$Alpha));

     $this->Shadow = $RestoreShadow;
    }

    /* Draw a rectangle */
    function drawRectangle($X1,$Y1,$X2,$Y2,$format="")
    {
     $R		= isset($format["R"]) ? $format["R"] : 0;
     $G		= isset($format["G"]) ? $format["G"] : 0;
     $B		= isset($format["B"]) ? $format["B"] : 0;
     $Alpha	= isset($format["Alpha"]) ? $format["Alpha"] : 100;
     $Ticks	= isset($format["Ticks"]) ? $format["Ticks"] : null;
     $NoAngle	= isset($format["NoAngle"]) ? $format["NoAngle"] : false;

     if ($X1 > $X2) { list($X1, $X2) = array($X2, $X1); }
     if ($Y1 > $Y2) { list($Y1, $Y2) = array($Y2, $Y1); }

     if ( $this->Antialias )
      {
       if ( $NoAngle )
        {
         $this->drawLine($X1+1,$Y1,$X2-1,$Y1,array("R"=>$R,"G"=>$G,"B"=>$B,"Alpha"=>$Alpha,"Ticks"=>$Ticks));
         $this->drawLine($X2,$Y1+1,$X2,$Y2-1,array("R"=>$R,"G"=>$G,"B"=>$B,"Alpha"=>$Alpha,"Ticks"=>$Ticks));
         $this->drawLine($X2-1,$Y2,$X1+1,$Y2,array("R"=>$R,"G"=>$G,"B"=>$B,"Alpha"=>$Alpha,"Ticks"=>$Ticks));
         $this->drawLine($X1,$Y1+1,$X1,$Y2-1,array("R"=>$R,"G"=>$G,"B"=>$B,"Alpha"=>$Alpha,"Ticks"=>$Ticks));
        }
       else
        {
         $this->drawLine($X1+1,$Y1,$X2-1,$Y1,array("R"=>$R,"G"=>$G,"B"=>$B,"Alpha"=>$Alpha,"Ticks"=>$Ticks));
         $this->drawLine($X2,$Y1,$X2,$Y2,array("R"=>$R,"G"=>$G,"B"=>$B,"Alpha"=>$Alpha,"Ticks"=>$Ticks));
         $this->drawLine($X2-1,$Y2,$X1+1,$Y2,array("R"=>$R,"G"=>$G,"B"=>$B,"Alpha"=>$Alpha,"Ticks"=>$Ticks));
         $this->drawLine($X1,$Y1,$X1,$Y2,array("R"=>$R,"G"=>$G,"B"=>$B,"Alpha"=>$Alpha,"Ticks"=>$Ticks));
        }
      }
     else
      {
       $Color = $this->allocateColor($this->Picture,$R,$G,$B,$Alpha);
       imagerectangle($this->Picture,$X1,$Y1,$X2,$Y2,$Color);
      }
    }
    
    /* Draw a filled rectangle */
    function drawFilledRectangle($X1,$Y1,$X2,$Y2,$format="")
    {
     $R			= isset($format["R"]) ? $format["R"] : 0;
     $G			= isset($format["G"]) ? $format["G"] : 0;
     $B			= isset($format["B"]) ? $format["B"] : 0;
     $Alpha		= isset($format["Alpha"]) ? $format["Alpha"] : 100;
     $BorderR		= isset($format["BorderR"]) ? $format["BorderR"] : -1;
     $BorderG		= isset($format["BorderG"]) ? $format["BorderG"] : -1;
     $BorderB		= isset($format["BorderB"]) ? $format["BorderB"] : -1;
     $BorderAlpha	= isset($format["BorderAlpha"]) ? $format["BorderAlpha"] : $Alpha;
     $Surrounding	= isset($format["Surrounding"]) ? $format["Surrounding"] : null;
     $Ticks		= isset($format["Ticks"]) ? $format["Ticks"] : null;
     $NoAngle		= isset($format["NoAngle"]) ? $format["NoAngle"] : null;
     $Dash		= isset($format["Dash"]) ? $format["Dash"] : false;
     $DashStep		= isset($format["DashStep"]) ? $format["DashStep"] : 4;
     $DashR		= isset($format["DashR"]) ? $format["DashR"] : 0;
     $DashG		= isset($format["DashG"]) ? $format["DashG"] : 0;
     $DashB		= isset($format["DashB"]) ? $format["DashB"] : 0;
     $NoBorder		= isset($format["NoBorder"]) ? $format["NoBorder"] : false;

     if ( $Surrounding != null ) { $BorderR = $R+$Surrounding; $BorderG = $G+$Surrounding; $BorderB = $B+$Surrounding; }

     if ($X1 > $X2) { list($X1, $X2) = array($X2, $X1); }
     if ($Y1 > $Y2) { list($Y1, $Y2) = array($Y2, $Y1); }

     $RestoreShadow = $this->Shadow;
     if ( $this->Shadow && $this->ShadowX != 0 && $this->ShadowY != 0 )
      {
       $this->Shadow = false;
       $this->drawFilledRectangle($X1+$this->ShadowX,$Y1+$this->ShadowY,$X2+$this->ShadowX,$Y2+$this->ShadowY,array("R"=>$this->ShadowR,"G"=>$this->ShadowG,"B"=>$this->ShadowB,"Alpha"=>$this->Shadowa,"Ticks"=>$Ticks,"NoAngle"=>$NoAngle));
      }

     $Color = $this->allocateColor($this->Picture,$R,$G,$B,$Alpha);
     if ( $NoAngle )
      {
       imagefilledrectangle($this->Picture,ceil($X1)+1,ceil($Y1),floor($X2)-1,floor($Y2),$Color);
       imageline($this->Picture,ceil($X1),ceil($Y1)+1,ceil($X1),floor($Y2)-1,$Color);
       imageline($this->Picture,floor($X2),ceil($Y1)+1,floor($X2),floor($Y2)-1,$Color);
      }
     else
      imagefilledrectangle($this->Picture,ceil($X1),ceil($Y1),floor($X2),floor($Y2),$Color);

     if ( $Dash )
      {
       if ( $BorderR != -1 ) { $iX1=$X1+1; $iY1=$Y1+1; $iX2=$X2-1; $iY2=$Y2-1; } else { $iX1=$X1; $iY1=$Y1; $iX2=$X2; $iY2=$Y2; }

       $Color = $this->allocateColor($this->Picture,$DashR,$DashG,$DashB,$Alpha);
       $Y=$iY1-$DashStep;
       for($X=$iX1; $X<=$iX2+($iY2-$iY1); $X=$X+$DashStep)
        {
         $Y=$Y+$DashStep;
         if ( $X > $iX2 ) { $Xa = $X-($X-$iX2); $Ya = $iY1+($X-$iX2); } else { $Xa = $X; $Ya = $iY1; }
         if ( $Y > $iY2 ) { $Xb = $iX1+($Y-$iY2); $Yb = $Y-($Y-$iY2); } else { $Xb = $iX1; $Yb = $Y; }
         imageline($this->Picture,$Xa,$Ya,$Xb,$Yb,$Color);
        }
      }

     if ( $this->Antialias && !$NoBorder )
      {
       if ( $X1 < ceil($X1) )
        {
         $AlphaA = $Alpha * (ceil($X1) - $X1);
         $Color = $this->allocateColor($this->Picture,$R,$G,$B,$AlphaA);
         imageline($this->Picture,ceil($X1)-1,ceil($Y1),ceil($X1)-1,floor($Y2),$Color);
        }

       if ( $Y1 < ceil($Y1) )
        {
         $AlphaA = $Alpha * (ceil($Y1) - $Y1);
         $Color = $this->allocateColor($this->Picture,$R,$G,$B,$AlphaA);
         imageline($this->Picture,ceil($X1),ceil($Y1)-1,floor($X2),ceil($Y1)-1,$Color);
        }

       if ( $X2 > floor($X2) )
        {
         $AlphaA = $Alpha * (.5-($X2 - floor($X2)));
         $Color = $this->allocateColor($this->Picture,$R,$G,$B,$AlphaA);
         imageline($this->Picture,floor($X2)+1,ceil($Y1),floor($X2)+1,floor($Y2),$Color);
        }

       if ( $Y2 > floor($Y2) )
        {
         $AlphaA = $Alpha * (.5-($Y2 - floor($Y2)));
         $Color = $this->allocateColor($this->Picture,$R,$G,$B,$AlphaA);
         imageline($this->Picture,ceil($X1),floor($Y2)+1,floor($X2),floor($Y2)+1,$Color);
        }
      }

     if ( $BorderR != -1 )
      $this->drawRectangle($X1,$Y1,$X2,$Y2,array("R"=>$BorderR,"G"=>$BorderG,"B"=>$BorderB,"Alpha"=>$BorderAlpha,"Ticks"=>$Ticks,"NoAngle"=>$NoAngle));

     $this->Shadow = $RestoreShadow;
    }

    /* Draw a rectangular marker of the specified size */
    function drawRectangleMarker($X,$Y,$format="")
    {
     $Size = isset($format["Size"]) ? $format["Size"] : 4;

     $HalfSize = floor($Size/2);
     $this->drawFilledRectangle($X-$HalfSize,$Y-$HalfSize,$X+$HalfSize,$Y+$HalfSize,$format);
    }

    /* Drawn a spline based on the bezier function */
    function drawSpline($Coordinates,$format="")
    {
     $R		= isset($format["R"]) ? $format["R"] : 0;
     $G		= isset($format["G"]) ? $format["G"] : 0;
     $B		= isset($format["B"]) ? $format["B"] : 0;
     $Alpha	= isset($format["Alpha"]) ? $format["Alpha"] : 100;
     $Force	= isset($format["Force"]) ? $format["Force"] : 30;
     $Forces	= isset($format["Forces"]) ? $format["Forces"] : null;
     $ShowC	= isset($format["ShowControl"]) ? $format["ShowControl"] : false;
     $Ticks	= isset($format["Ticks"]) ? $format["Ticks"] : null;
     $PathOnly	= isset($format["PathOnly"]) ? $format["PathOnly"] : false;
     $Weight	= isset($format["Weight"]) ? $format["Weight"] : null;

     $Cpt = null; $Mode = null; $Result = "";
     for($i=1;$i<=count($Coordinates)-1;$i++)
      {
       $X1 = $Coordinates[$i-1][0]; $Y1 = $Coordinates[$i-1][1];
       $X2 = $Coordinates[$i][0];   $Y2 = $Coordinates[$i][1];

       if ( $Forces != null ) { $Force = $Forces[$i]; }

       /* First segment */
       if ( $i == 1 )
        { $Xv1 = $X1; $Yv1 = $Y1; }
       else
        {
         $Angle1 = $this->getAngle($XLast,$YLast,$X1,$Y1);
         $Angle2 = $this->getAngle($X1,$Y1,$X2,$Y2);
         $XOff = cos($Angle2 * PI / 180) * $Force + $X1;
         $YOff = sin($Angle2 * PI / 180) * $Force + $Y1;

         $Xv1 = cos($Angle1 * PI / 180) * $Force + $XOff;
         $Yv1 = sin($Angle1 * PI / 180) * $Force + $YOff;
        }

       /* Last segment */
       if ( $i == count($Coordinates)-1 )
        { $Xv2 = $X2; $Yv2 = $Y2; }
       else
        {
         $Angle1 = $this->getAngle($X2,$Y2,$Coordinates[$i+1][0],$Coordinates[$i+1][1]);
         $Angle2 = $this->getAngle($X1,$Y1,$X2,$Y2);
         $XOff = cos(($Angle2+180) * PI / 180) * $Force + $X2;
         $YOff = sin(($Angle2+180) * PI / 180) * $Force + $Y2;

         $Xv2 = cos(($Angle1+180) * PI / 180) * $Force + $XOff;
         $Yv2 = sin(($Angle1+180) * PI / 180) * $Force + $YOff;
        }

       $Path = $this->drawBezier($X1,$Y1,$X2,$Y2,$Xv1,$Yv1,$Xv2,$Yv2,$format);
       if ($PathOnly) { $Result[] = $Path; }

       $XLast = $X1; $YLast = $Y1;
      }

     return $Result;
    }

    /* Draw a bezier curve with two controls points */
    function drawBezier($X1,$Y1,$X2,$Y2,$Xv1,$Yv1,$Xv2,$Yv2,$format="")
    {
     $R		= isset($format["R"]) ? $format["R"] : 0;
     $G		= isset($format["G"]) ? $format["G"] : 0;
     $B		= isset($format["B"]) ? $format["B"] : 0;
     $Alpha	= isset($format["Alpha"]) ? $format["Alpha"] : 100;
     $ShowC	= isset($format["ShowControl"]) ? $format["ShowControl"] : false;
     $Segments	= isset($format["Segments"]) ? $format["Segments"] : null;
     $Ticks	= isset($format["Ticks"]) ? $format["Ticks"] : null;
     $NoDraw    = isset($format["NoDraw"]) ? $format["NoDraw"] : false;
     $PathOnly  = isset($format["PathOnly"]) ? $format["PathOnly"] : false;
     $Weight    = isset($format["Weight"]) ? $format["Weight"] : null;
     $DrawArrow		= isset($format["DrawArrow"]) ? $format["DrawArrow"] : false;
     $ArrowSize		= isset($format["ArrowSize"]) ? $format["ArrowSize"] : 10;
     $ArrowRatio	= isset($format["ArrowRatio"]) ? $format["ArrowRatio"] : .5;
     $ArrowTwoHeads	= isset($format["ArrowTwoHeads"]) ? $format["ArrowTwoHeads"] : false;

     if ( $Segments == null )
      {
       $Length    = $this->getLength($X1,$Y1,$X2,$Y2);
       $Precision = ($Length*125)/1000;
      }
     else
      $Precision = $Segments;

     $P[0]["X"] = $X1;  $P[0]["Y"] = $Y1;
     $P[1]["X"] = $Xv1; $P[1]["Y"] = $Yv1;
     $P[2]["X"] = $Xv2; $P[2]["Y"] = $Yv2;
     $P[3]["X"] = $X2;  $P[3]["Y"] = $Y2;

     /* Compute the bezier points */
     $Q = ""; $ID = 0; $Path = "";
     for($i=0;$i<=$Precision;$i=$i+1)
      {
       $u = $i / $Precision;

       $C    = "";
       $C[0] = (1 - $u) * (1 - $u) * (1 - $u);
       $C[1] = ($u * 3) * (1 - $u) * (1 - $u);
       $C[2] = 3 * $u * $u * (1 - $u);
       $C[3] = $u * $u * $u;

       for($j=0;$j<=3;$j++)
        {
         if ( !isset($Q[$ID]) ) { $Q[$ID] = ""; }
         if ( !isset($Q[$ID]["X"]) ) { $Q[$ID]["X"] = 0; }
         if ( !isset($Q[$ID]["Y"]) ) { $Q[$ID]["Y"] = 0; }

         $Q[$ID]["X"] = $Q[$ID]["X"] + $P[$j]["X"] * $C[$j];
         $Q[$ID]["Y"] = $Q[$ID]["Y"] + $P[$j]["Y"] * $C[$j];
        }
       $ID++;
      }
     $Q[$ID]["X"] = $X2; $Q[$ID]["Y"] = $Y2;

     if ( !$NoDraw )
      {
       /* Display the control points */
       if ( $ShowC && !$PathOnly )
        {
         $Xv1 = floor($Xv1); $Yv1 = floor($Yv1); $Xv2 = floor($Xv2); $Yv2 = floor($Yv2);

         $this->drawLine($X1,$Y1,$X2,$Y2,array("R"=>0,"G"=>0,"B"=>0,"Alpha"=>30));

         $MyMarkerSettings = array("R"=>255,"G"=>0,"B"=>0,"BorderR"=>255,"BorderB"=>255,"BorderG"=>255,"Size"=>4);
         $this->drawRectangleMarker($Xv1,$Yv1,$MyMarkerSettings);
         $this->drawText($Xv1+4,$Yv1,"v1");
         $MyMarkerSettings = array("R"=>0,"G"=>0,"B"=>255,"BorderR"=>255,"BorderB"=>255,"BorderG"=>255,"Size"=>4);
         $this->drawRectangleMarker($Xv2,$Yv2,$MyMarkerSettings);
         $this->drawText($Xv2+4,$Yv2,"v2");
        }

       /* Draw the bezier */
       $LastX = null; $LastY = null; $Cpt = null; $Mode = null; $ArrowS = null;
       foreach ($Q as $Key => $Point)
        {
         $X = $Point["X"]; $Y = $Point["Y"];

         /* Get the first segment */
         if ( $ArrowS == null && $LastX != null && $LastY != null )
          { $ArrowS["X2"] = $LastX; $ArrowS["Y2"] = $LastY; $ArrowS["X1"] = $X; $ArrowS["Y1"] = $Y; }

         if ( $LastX != null && $LastY != null && !$PathOnly)
          list($Cpt,$Mode) = $this->drawLine($LastX,$LastY,$X,$Y,array("R"=>$R,"G"=>$G,"B"=>$B,"Alpha"=>$Alpha,"Ticks"=>$Ticks,"Cpt"=>$Cpt,"Mode"=>$Mode,"Weight"=>$Weight));

         /* Get the last segment */
         $ArrowE["X1"] = $LastX; $ArrowE["Y1"] = $LastY; $ArrowE["X2"] = $X; $ArrowE["Y2"] = $Y;

         $LastX = $X; $LastY = $Y;
        }

       if ( $DrawArrow && !$PathOnly )
        {
         $ArrowSettings = array("FillR"=>$R,"FillG"=>$G,"FillB"=>$B,"Alpha"=>$Alpha,"Size"=>$ArrowSize,"Ratio"=>$ArrowRatio);
         if ( $ArrowTwoHeads )
          $this->drawArrow($ArrowS["X1"],$ArrowS["Y1"],$ArrowS["X2"],$ArrowS["Y2"],$ArrowSettings);

         $this->drawArrow($ArrowE["X1"],$ArrowE["Y1"],$ArrowE["X2"],$ArrowE["Y2"],$ArrowSettings);
        }
      }
     return $Q;
    }

    /* Draw a line between two points */
    function drawLine($X1,$Y1,$X2,$Y2,$format="")
    {
     $R		= isset($format["R"]) ? $format["R"] : 0;
     $G		= isset($format["G"]) ? $format["G"] : 0;
     $B		= isset($format["B"]) ? $format["B"] : 0;
     $Alpha	= isset($format["Alpha"]) ? $format["Alpha"] : 100;
     $Ticks	= isset($format["Ticks"]) ? $format["Ticks"] : null;
     $Cpt	= isset($format["Cpt"]) ? $format["Cpt"] : 1;
     $Mode	= isset($format["Mode"]) ? $format["Mode"] : 1;
     $Weight	= isset($format["Weight"]) ? $format["Weight"] : null;
     $Threshold	= isset($format["Threshold"]) ? $format["Threshold"] : null;

     if ( $this->Antialias == false && $Ticks == null )
      {
       if ( $this->Shadow && $this->ShadowX != 0 && $this->ShadowY != 0 )
        {
         $ShadowColor = $this->allocateColor($this->Picture,$this->ShadowR,$this->ShadowG,$this->ShadowB,$this->Shadowa);
         imageline($this->Picture,$X1+$this->ShadowX,$Y1+$this->ShadowY,$X2+$this->ShadowX,$Y2+$this->ShadowY,$ShadowColor);
        }

       $Color = $this->allocateColor($this->Picture,$R,$G,$B,$Alpha);
       imageline($this->Picture,$X1,$Y1,$X2,$Y2,$Color);
       return 0;
      }

     $Distance = sqrt(($X2-$X1)*($X2-$X1)+($Y2-$Y1)*($Y2-$Y1));
     if ( $Distance == 0 ) { return(-1); }

     /* Derivative algorithm for overweighted lines, re-route to polygons primitives */
     if ( $Weight != null )
      {
       $Angle        = $this->getAngle($X1,$Y1,$X2,$Y2);
       $PolySettings = array ("R"=>$R,"G"=>$G,"B"=>$B,"Alpha"=>$Alpha,"BorderAlpha"=>$Alpha);

       if ( $Ticks == null )
        {
         $points   = [];
         $points[] = cos(deg2rad($Angle-90)) * $Weight + $X1; $points[] = sin(deg2rad($Angle-90)) * $Weight + $Y1;
         $points[] = cos(deg2rad($Angle+90)) * $Weight + $X1; $points[] = sin(deg2rad($Angle+90)) * $Weight + $Y1;
         $points[] = cos(deg2rad($Angle+90)) * $Weight + $X2; $points[] = sin(deg2rad($Angle+90)) * $Weight + $Y2;
         $points[] = cos(deg2rad($Angle-90)) * $Weight + $X2; $points[] = sin(deg2rad($Angle-90)) * $Weight + $Y2;

         $this->drawPolygon($points,$PolySettings);
        }
       else
        {
         for($i=0;$i<=$Distance;$i=$i+$Ticks*2)
          {
           $Xa = (($X2-$X1)/$Distance) * $i + $X1; $Ya = (($Y2-$Y1)/$Distance) * $i + $Y1;
           $Xb = (($X2-$X1)/$Distance) * ($i+$Ticks) + $X1; $Yb = (($Y2-$Y1)/$Distance) * ($i+$Ticks) + $Y1;

           $points   = [];
           $points[] = cos(deg2rad($Angle-90)) * $Weight + $Xa; $points[] = sin(deg2rad($Angle-90)) * $Weight + $Ya;
           $points[] = cos(deg2rad($Angle+90)) * $Weight + $Xa; $points[] = sin(deg2rad($Angle+90)) * $Weight + $Ya;
           $points[] = cos(deg2rad($Angle+90)) * $Weight + $Xb; $points[] = sin(deg2rad($Angle+90)) * $Weight + $Yb;
           $points[] = cos(deg2rad($Angle-90)) * $Weight + $Xb; $points[] = sin(deg2rad($Angle-90)) * $Weight 	+ $Yb;

           $this->drawPolygon($points,$PolySettings);
          }
        }

       return 1;
      }

     $XStep = ($X2-$X1) / $Distance;
     $YStep = ($Y2-$Y1) / $Distance;

     for($i=0;$i<=$Distance;$i++)
      {
       $X = $i * $XStep + $X1;
       $Y = $i * $YStep + $Y1;

       $Color = array("R"=>$R,"G"=>$G,"B"=>$B,"Alpha"=>$Alpha);

       if ( $Threshold != null )
        {
         foreach($Threshold as $Key => $Parameters)
          {
           if ( $Y <= $Parameters["MinX"] && $Y >= $Parameters["MaxX"])
            {
             if ( isset($Parameters["R"]) ) { $RT = $Parameters["R"]; } else { $RT = 0; }
             if ( isset($Parameters["G"]) ) { $GT = $Parameters["G"]; } else { $GT = 0; }
             if ( isset($Parameters["B"]) ) { $BT = $Parameters["B"]; } else { $BT = 0; }
             if ( isset($Parameters["Alpha"]) ) { $AlphaT = $Parameters["Alpha"]; } else { $AlphaT = 0; }
             $Color = array("R"=>$RT,"G"=>$GT,"B"=>$BT,"Alpha"=>$AlphaT);
            }
          }
        }

       if ( $Ticks != null )
        {
         if ( $Cpt % $Ticks == 0 )
          { $Cpt = 0; if ( $Mode == 1 ) { $Mode = 0; } else { $Mode = 1; } }

         if ( $Mode == 1 )
          $this->drawAntialiasPixel($X,$Y,$Color);

         $Cpt++;
        }
       else
        $this->drawAntialiasPixel($X,$Y,$Color);
      }

     return array($Cpt,$Mode);
    }

    /* Draw a circle */
    function drawCircle($Xc,$Yc,$Height,$Width,$format="")
    {
     $R	    = isset($format["R"]) ? $format["R"] : 0;
     $G	    = isset($format["G"]) ? $format["G"] : 0;
     $B	    = isset($format["B"]) ? $format["B"] : 0;
     $Alpha = isset($format["Alpha"]) ? $format["Alpha"] : 100;
     $Ticks = isset($format["Ticks"]) ? $format["Ticks"] : null;

     $Height	= abs($Height);
     $Width	= abs($Width);

     if ( $Height == 0 ) { $Height = 1; }
     if ( $Width == 0 )  { $Width = 1; }
     $Xc = floor($Xc); $Yc = floor($Yc);

     $RestoreShadow = $this->Shadow;
     if ( $this->Shadow && $this->ShadowX != 0 && $this->ShadowY != 0 )
      {
       $this->Shadow = false;
       $this->drawCircle($Xc+$this->ShadowX,$Yc+$this->ShadowY,$Height,$Width,array("R"=>$this->ShadowR,"G"=>$this->ShadowG,"B"=>$this->ShadowB,"Alpha"=>$this->Shadowa,"Ticks"=>$Ticks));
      }

     if ( $Width == 0 ) { $Width = $Height; }
     if ( $R < 0 ) { $R = 0; } if ( $R > 255 ) { $R = 255; }
     if ( $G < 0 ) { $G = 0; } if ( $G > 255 ) { $G = 255; }
     if ( $B < 0 ) { $B = 0; } if ( $B > 255 ) { $B = 255; }

     $Step = 360 / (2 * PI * max($Width,$Height));
     $Mode = 1; $Cpt = 1;
     for($i=0;$i<=360;$i=$i+$Step)
      {
       $X = cos($i*PI/180) * $Height + $Xc;
       $Y = sin($i*PI/180) * $Width + $Yc;

       if ( $Ticks != null )
        {
         if ( $Cpt % $Ticks == 0 )
          { $Cpt = 0; if ( $Mode == 1 ) { $Mode = 0; } else { $Mode = 1; } }

         if ( $Mode == 1 )
          $this->drawAntialiasPixel($X,$Y,array("R"=>$R,"G"=>$G,"B"=>$B,"Alpha"=>$Alpha));

         $Cpt++;
        }
       else
        $this->drawAntialiasPixel($X,$Y,array("R"=>$R,"G"=>$G,"B"=>$B,"Alpha"=>$Alpha));

      }
     $this->Shadow = $RestoreShadow;
    }

    /* Draw a filled circle */
    function drawFilledCircle($X,$Y,$Radius,$format="")
    {
     $R			= isset($format["R"]) ? $format["R"] : 0;
     $G			= isset($format["G"]) ? $format["G"] : 0;
     $B			= isset($format["B"]) ? $format["B"] : 0;
     $Alpha		= isset($format["Alpha"]) ? $format["Alpha"] : 100;
     $BorderR		= isset($format["BorderR"]) ? $format["BorderR"] : -1;
     $BorderG		= isset($format["BorderG"]) ? $format["BorderG"] : -1;
     $BorderB		= isset($format["BorderB"]) ? $format["BorderB"] : -1;
     $BorderAlpha	= isset($format["BorderAlpha"]) ? $format["BorderAlpha"] : $Alpha;
     $Ticks     	= isset($format["Ticks"]) ? $format["Ticks"] : null;
     $Surrounding 	= isset($format["Surrounding"]) ? $format["Surrounding"] : null;

     if ( $Radius == 0 ) { $Radius = 1; }
     if ( $Surrounding != null ) { $BorderR = $R+$Surrounding; $BorderG = $G+$Surrounding; $BorderB = $B+$Surrounding; }
     $X = floor($X); $Y = floor($Y);

     $Radius = abs($Radius);

     $RestoreShadow = $this->Shadow;
     if ( $this->Shadow && $this->ShadowX != 0 && $this->ShadowY != 0 )
      {
       $this->Shadow = false;
       $this->drawFilledCircle($X+$this->ShadowX,$Y+$this->ShadowY,$Radius,array("R"=>$this->ShadowR,"G"=>$this->ShadowG,"B"=>$this->ShadowB,"Alpha"=>$this->Shadowa,"Ticks"=>$Ticks));
      }

     $this->Mask  = "";
     $Color = $this->allocateColor($this->Picture,$R,$G,$B,$Alpha);
     for ($i=0; $i<=$Radius*2; $i++)
      {
       $Slice  = sqrt($Radius * $Radius - ($Radius - $i) * ($Radius - $i));
       $XPos   = floor($Slice);
       $YPos   = $Y + $i - $Radius;
       $AAlias = $Slice - floor($Slice);

       $this->Mask[$X-$XPos][$YPos] = true;
       $this->Mask[$X+$XPos][$YPos] = true;
       imageline($this->Picture,$X-$XPos,$YPos,$X+$XPos,$YPos,$Color);
      }
     if ( $this->Antialias )
      $this->drawCircle($X,$Y,$Radius,$Radius,array("R"=>$R,"G"=>$G,"B"=>$B,"Alpha"=>$Alpha,"Ticks"=>$Ticks));

     $this->Mask = "";

     if ( $BorderR != -1 )
      $this->drawCircle($X,$Y,$Radius,$Radius,array("R"=>$BorderR,"G"=>$BorderG,"B"=>$BorderB,"Alpha"=>$BorderAlpha,"Ticks"=>$Ticks));

     $this->Shadow	= $RestoreShadow;
    }

    /* Write text */
    function drawText($X,$Y,$Text,$format="")
    {
     $R			= isset($format["R"]) ? $format["R"] : $this->FontColorR;
     $G			= isset($format["G"]) ? $format["G"] : $this->FontColorG;
     $B			= isset($format["B"]) ? $format["B"] : $this->FontColorB;
     $Angle		= isset($format["Angle"]) ? $format["Angle"] : 0;
     $Align		= isset($format["Align"]) ? $format["Align"] : TEXT_ALIGN_BOTTOMLEFT;
     $Alpha		= isset($format["Alpha"]) ? $format["Alpha"] : $this->FontColorA;
     $FontName		= isset($format["FontName"]) ? $format["FontName"] : $this->FontName;
     $FontSize		= isset($format["FontSize"]) ? $format["FontSize"] : $this->FontSize;
     $ShowOrigine	= isset($format["ShowOrigine"]) ? $format["ShowOrigine"] : false;
     $TOffset		= isset($format["TOffset"]) ? $format["TOffset"] : 2;
     $DrawBox		= isset($format["DrawBox"]) ? $format["DrawBox"] : false;
     $DrawBoxBorder	= isset($format["DrawBoxBorder"]) ? $format["DrawBoxBorder"] : true;
     $BorderOffset	= isset($format["BorderOffset"]) ? $format["BorderOffset"] : 6;
     $BoxRounded	= isset($format["BoxRounded"]) ? $format["BoxRounded"] : false;
     $RoundedRadius	= isset($format["RoundedRadius"]) ? $format["RoundedRadius"] : 6;
     $BoxR		= isset($format["BoxR"]) ? $format["BoxR"] : 255;
     $BoxG		= isset($format["BoxG"]) ? $format["BoxG"] : 255;
     $BoxB		= isset($format["BoxB"]) ? $format["BoxB"] : 255;
     $BoxAlpha		= isset($format["BoxAlpha"]) ? $format["BoxAlpha"] : 50;
     $BoxSurrounding	= isset($format["BoxSurrounding"]) ? $format["BoxSurrounding"] : "";
     $BoxBorderR	= isset($format["BoxR"]) ? $format["BoxR"] : 0;
     $BoxBorderG	= isset($format["BoxG"]) ? $format["BoxG"] : 0;
     $BoxBorderB	= isset($format["BoxB"]) ? $format["BoxB"] : 0;
     $BoxBorderAlpha	= isset($format["BoxAlpha"]) ? $format["BoxAlpha"] : 50;
     $NoShadow		= isset($format["NoShadow"]) ? $format["NoShadow"] : false;

     $Shadow = $this->Shadow;
     if ( $NoShadow ) { $this->Shadow = false; }

     if ( $BoxSurrounding != "" ) { $BoxBorderR = $BoxR - $BoxSurrounding; $BoxBorderG = $BoxG - $BoxSurrounding; $BoxBorderB = $BoxB - $BoxSurrounding; $BoxBorderAlpha = $BoxAlpha; }

     if ( $ShowOrigine )
      {
       $MyMarkerSettings = array("R"=>255,"G"=>0,"B"=>0,"BorderR"=>255,"BorderB"=>255,"BorderG"=>255,"Size"=>4);
       $this->drawRectangleMarker($X,$Y,$MyMarkerSettings);
      }

     $TxtPos = $this->getTextBox($X,$Y,$FontName,$FontSize,$Angle,$Text);

     if ( $DrawBox && ($Angle == 0 || $Angle == 90 || $Angle == 180 || $Angle == 270))
      {
       $T[0]["X"]=0;$T[0]["Y"]=0;$T[1]["X"]=0;$T[1]["Y"]=0;$T[2]["X"]=0;$T[2]["Y"]=0;$T[3]["X"]=0;$T[3]["Y"]=0;
       if ( $Angle == 0 ) { $T[0]["X"]=-$TOffset;$T[0]["Y"]=$TOffset;$T[1]["X"]=$TOffset;$T[1]["Y"]=$TOffset;$T[2]["X"]=$TOffset;$T[2]["Y"]=-$TOffset;$T[3]["X"]=-$TOffset;$T[3]["Y"]=-$TOffset; }

       $X1 = min($TxtPos[0]["X"],$TxtPos[1]["X"],$TxtPos[2]["X"],$TxtPos[3]["X"]) - $BorderOffset + 3;
       $Y1 = min($TxtPos[0]["Y"],$TxtPos[1]["Y"],$TxtPos[2]["Y"],$TxtPos[3]["Y"]) - $BorderOffset;
       $X2 = max($TxtPos[0]["X"],$TxtPos[1]["X"],$TxtPos[2]["X"],$TxtPos[3]["X"]) + $BorderOffset + 3;
       $Y2 = max($TxtPos[0]["Y"],$TxtPos[1]["Y"],$TxtPos[2]["Y"],$TxtPos[3]["Y"]) + $BorderOffset - 3;

       $X1 = $X1 - $TxtPos[$Align]["X"] + $X + $T[0]["X"];
       $Y1 = $Y1 - $TxtPos[$Align]["Y"] + $Y + $T[0]["Y"];
       $X2 = $X2 - $TxtPos[$Align]["X"] + $X + $T[0]["X"];
       $Y2 = $Y2 - $TxtPos[$Align]["Y"] + $Y + $T[0]["Y"];

       $Settings = array("R"=>$BoxR,"G"=>$BoxG,"B"=>$BoxB,"Alpha"=>$BoxAlpha,"BorderR"=>$BoxBorderR,"BorderG"=>$BoxBorderG,"BorderB"=>$BoxBorderB,"BorderAlpha"=>$BoxBorderAlpha);

       if ( $BoxRounded )
        { $this->drawRoundedFilledRectangle($X1,$Y1,$X2,$Y2,$RoundedRadius,$Settings); }
       else
        { $this->drawFilledRectangle($X1,$Y1,$X2,$Y2,$Settings); }
      }

     $X = $X - $TxtPos[$Align]["X"] + $X;
     $Y = $Y - $TxtPos[$Align]["Y"] + $Y;

     if ( $this->Shadow && $this->ShadowX != 0 && $this->ShadowY != 0 )
      {
       $C_ShadowColor = $this->allocateColor($this->Picture,$this->ShadowR,$this->ShadowG,$this->ShadowB,$this->Shadowa);
       imagettftext($this->Picture,$FontSize,$Angle,$X+$this->ShadowX,$Y+$this->ShadowY,$C_ShadowColor,$FontName,$Text);
      }

     $C_TextColor = $this->AllocateColor($this->Picture,$R,$G,$B,$Alpha);
     imagettftext($this->Picture,$FontSize,$Angle,$X,$Y,$C_TextColor,$FontName,$Text);

     $this->Shadow = $Shadow;

     return $TxtPos;
    }

    /* Draw a gradient within a defined area */
    function drawGradientArea($X1,$Y1,$X2,$Y2,$Direction,$format="")
    {
     $StartR	= isset($format["StartR"]) ? $format["StartR"] : 90;
     $StartG	= isset($format["StartG"]) ? $format["StartG"] : 90;
     $StartB	= isset($format["StartB"]) ? $format["StartB"] : 90;
     $EndR	= isset($format["EndR"]) ? $format["EndR"] : 0;
     $EndG	= isset($format["EndG"]) ? $format["EndG"] : 0;
     $EndB	= isset($format["EndB"]) ? $format["EndB"] : 0;
     $Alpha	= isset($format["Alpha"]) ? $format["Alpha"] : 100;
     $Levels	= isset($format["Levels"]) ? $format["Levels"] : null;

     $Shadow = $this->Shadow;
     $this->Shadow = false;

     if ( $StartR == $EndR && $StartG == $EndG && $StartB == $EndB )
      {
       $this->drawFilledRectangle($X1,$Y1,$X2,$Y2,array("R"=>$StartR,"G"=>$StartG,"B"=>$StartB,"Alpha"=>$Alpha));
       return 0;
      }

     if ( $Levels != null )
      { $EndR=$StartR+$Levels; $EndG=$StartG+$Levels; $EndB=$StartB+$Levels; }

     if ($X1 > $X2) { list($X1, $X2) = array($X2, $X1); }
     if ($Y1 > $Y2) { list($Y1, $Y2) = array($Y2, $Y1); }

     if ( $Direction == DIRECTION_VERTICAL )   { $Width = abs($Y2-$Y1); }
     if ( $Direction == DIRECTION_HORIZONTAL ) { $Width = abs($X2-$X1); }

     $Step     = max(abs($EndR-$StartR),abs($EndG-$StartG),abs($EndB-$StartB));
     $StepSize = $Width/$Step;
     $RStep    = ($EndR-$StartR)/$Step;
     $GStep    = ($EndG-$StartG)/$Step;
     $BStep    = ($EndB-$StartB)/$Step;

     $R=$StartR;$G=$StartG;$B=$StartB;
     switch($Direction)
      {
       case DIRECTION_VERTICAL:
        $StartY = $Y1; $EndY = floor($Y2)+1; $LastY2 = $StartY;
        for($i=0;$i<=$Step;$i++)
         {
          $Y2 = floor($StartY + ($i * $StepSize));

          if ($Y2 > $EndY) { $Y2 = $EndY; }
          if (($Y1 != $Y2 && $Y1 < $Y2) || $Y2 == $EndY)
           {
            $Color = array("R"=>$R,"G"=>$G,"B"=>$B,"Alpha"=>$Alpha);
            $this->drawFilledRectangle($X1,$Y1,$X2,$Y2,$Color);
            $LastY2 = max($LastY2,$Y2);
            $Y1 = $Y2+1;
           }
          $R = $R + $RStep; $G = $G + $GStep; $B = $B + $BStep;
         }
        if ( $LastY2 < $EndY && isset($Color)) { for ($i=$LastY2+1;$i<=$EndY;$i++) { $this->drawLine($X1,$i,$X2,$i,$Color); } }
        break;

       case DIRECTION_HORIZONTAL:
        $StartX = $X1; $EndX = $X2;
        for($i=0;$i<=$Step;$i++)
         {
          $X2 = floor($StartX + ($i * $StepSize));

          if ($X2 > $EndX) { $X2 = $EndX; }
          if (($X1 != $X2 && $X1 < $X2) || $X2 == $EndX)
           {
            $Color = array("R"=>$R,"G"=>$G,"B"=>$B,"Alpha"=>$Alpha);
            $this->drawFilledRectangle($X1,$Y1,$X2,$Y2,$Color);
            $X1 = $X2+1;
           }
          $R = $R + $RStep; $G = $G + $GStep; $B = $B + $BStep;
         }
        if ( $X2 < $EndX && isset($Color)) { $this->drawFilledRectangle($X2,$Y1,$EndX,$Y2,$Color); }
        break;
      }

     $this->Shadow = $Shadow;

    }

    /* Draw an aliased pixel */
    function drawAntialiasPixel($X,$Y,$format="")
    {
     $R     = isset($format["R"]) ? $format["R"] : 0;
     $G     = isset($format["G"]) ? $format["G"] : 0;
     $B     = isset($format["B"]) ? $format["B"] : 0;
     $Alpha = isset($format["Alpha"]) ? $format["Alpha"] : 100;

     if ( $X < 0 || $Y < 0 || $X >= $this->XSize || $Y >= $this->YSize )
      return -1;

     if ( $R < 0 ) { $R = 0; } if ( $R > 255 ) { $R = 255; }
     if ( $G < 0 ) { $G = 0; } if ( $G > 255 ) { $G = 255; }
     if ( $B < 0 ) { $B = 0; } if ( $B > 255 ) { $B = 255; }

     if ( !$this->Antialias )
      {
       if ( $this->Shadow && $this->ShadowX != 0 && $this->ShadowY != 0 )
        {
         $ShadowColor = $this->allocateColor($this->Picture,$this->ShadowR,$this->ShadowG,$this->ShadowB,$this->Shadowa);
         imagesetpixel($this->Picture,$X+$this->ShadowX,$Y+$this->ShadowY,$ShadowColor);
        }

       $PlotColor = $this->allocateColor($this->Picture,$R,$G,$B,$Alpha);
       imagesetpixel($this->Picture,$X,$Y,$PlotColor);

       return 0;
      }

     $Plot = "";
     $Xi   = floor($X);
     $Yi   = floor($Y);

     if ( $Xi == $X && $Yi == $Y)
      {
       if ( $Alpha == 100 )
        $this->drawAlphaPixel($X,$Y,100,$R,$G,$B);
       else
        $this->drawAlphaPixel($X,$Y,$Alpha,$R,$G,$B);
      }
     else
      {
       $Alpha1 = (((1 - ($X - floor($X))) * (1 - ($Y - floor($Y))) * 100) / 100) * $Alpha;
       if ( $Alpha1 > $this->AntialiasQuality ) { $this->drawAlphaPixel($Xi,$Yi,$Alpha1,$R,$G,$B); }

       $Alpha2 = ((($X - floor($X)) * (1 - ($Y - floor($Y))) * 100) / 100) * $Alpha;
       if ( $Alpha2 > $this->AntialiasQuality ) { $this->drawAlphaPixel($Xi+1,$Yi,$Alpha2,$R,$G,$B); }

       $Alpha3 = (((1 - ($X - floor($X))) * ($Y - floor($Y)) * 100) / 100) * $Alpha;
       if ( $Alpha3 > $this->AntialiasQuality ) { $this->drawAlphaPixel($Xi,$Yi+1,$Alpha3,$R,$G,$B); }

       $Alpha4 = ((($X - floor($X)) * ($Y - floor($Y)) * 100) / 100) * $Alpha;
       if ( $Alpha4 > $this->AntialiasQuality ) { $this->drawAlphaPixel($Xi+1,$Yi+1,$Alpha4,$R,$G,$B); }
      }
    }

    /* Draw a semi-transparent pixel */
    function drawAlphaPixel($X,$Y,$Alpha,$R,$G,$B)
    {
     if ( isset($this->Mask[$X])) { if ( isset($this->Mask[$X][$Y]) ) { return 0; } }

     if ( $X < 0 || $Y < 0 || $X >= $this->XSize || $Y >= $this->YSize )
      return -1;

     if ( $R < 0 ) { $R = 0; } if ( $R > 255 ) { $R = 255; }
     if ( $G < 0 ) { $G = 0; } if ( $G > 255 ) { $G = 255; }
     if ( $B < 0 ) { $B = 0; } if ( $B > 255 ) { $B = 255; }

     if ( $this->Shadow && $this->ShadowX != 0 && $this->ShadowY != 0 )
      {
       $AlphaFactor = floor(($Alpha / 100) * $this->Shadowa);
       $ShadowColor = $this->allocateColor($this->Picture,$this->ShadowR,$this->ShadowG,$this->ShadowB,$AlphaFactor);
       imagesetpixel($this->Picture,$X+$this->ShadowX,$Y+$this->ShadowY,$ShadowColor);
      }

     $C_Aliased = $this->allocateColor($this->Picture,$R,$G,$B,$Alpha);
     imagesetpixel($this->Picture,$X,$Y,$C_Aliased);
    }

    /* Convert apha to base 10 */
    function convertAlpha($AlphaValue)
    { return((127/100)*(100-$AlphaValue)); }

    /* Allocate a color with transparency */
    function allocateColor($Picture,$R,$G,$B,$Alpha=100)
    {
     if ( $R < 0 ) { $R = 0; } if ( $R > 255 ) { $R = 255; }
     if ( $G < 0 ) { $G = 0; } if ( $G > 255 ) { $G = 255; }
     if ( $B < 0 ) { $B = 0; } if ( $B > 255 ) { $B = 255; }
     if ( $Alpha < 0 )  { $Alpha = 0; }
     if ( $Alpha > 100) { $Alpha = 100; }

     $Alpha = $this->convertAlpha($Alpha);
     return imagecolorallocatealpha($Picture,$R,$G,$B,$Alpha);
    }

    /* Load a PNG file and draw it over the chart */
    function drawFromPNG($X,$Y,$FileName)
    { $this->drawFromPicture(1,$FileName,$X,$Y); }

    /* Load a GIF file and draw it over the chart */
    function drawFromGIF($X,$Y,$FileName)
    { $this->drawFromPicture(2,$FileName,$X,$Y); }

    /* Load a JPEG file and draw it over the chart */
    function drawFromJPG($X,$Y,$FileName)
    { $this->drawFromPicture(3,$FileName,$X,$Y); }

    function getPicInfo($FileName)
    {
     $Infos  = getimagesize($FileName);
     $Width  = $Infos[0];
     $Height = $Infos[1];
     $Type   = $Infos["mime"];

     if ( $Type == "image/png") { $Type = 1; }
     if ( $Type == "image/gif") { $Type = 2; }
     if ( $Type == "image/jpeg ") { $Type = 3; }

     return array($Width,$Height,$Type);
    }

    /* Generic loader function for external pictures */
    function drawFromPicture($PicType,$FileName,$X,$Y)
    {
     if ( file_exists($FileName))
      {
       list($Width,$Height) = $this->getPicInfo($FileName);

       if ( $PicType == 1 )
        { $Raster = imagecreatefrompng($FileName); }
       elseif ( $PicType == 2 )
        { $Raster = imagecreatefromgif($FileName); }
       elseif ( $PicType == 3 )
        { $Raster = imagecreatefromjpeg($FileName); }
       else
        { return 0; }


       $RestoreShadow = $this->Shadow;
       if ( $this->Shadow && $this->ShadowX != 0 && $this->ShadowY != 0 )
        {
         $this->Shadow = false;
         if ( $PicType == 3 )
          $this->drawFilledRectangle($X+$this->ShadowX,$Y+$this->ShadowY,$X+$Width+$this->ShadowX,$Y+$Height+$this->ShadowY,array("R"=>$this->ShadowR,"G"=>$this->ShadowG,"B"=>$this->ShadowB,"Alpha"=>$this->Shadowa));
         else
          {
           $TranparentID = imagecolortransparent($Raster);
           for ($Xc=0;$Xc<=$Width-1;$Xc++)
            {
             for ($Yc=0;$Yc<=$Height-1;$Yc++)
              {
               $RGBa   = imagecolorat($Raster,$Xc,$Yc);
               $Values = imagecolorsforindex($Raster,$RGBa);
               if ( $Values["alpha"] < 120 )
                {
                 $AlphaFactor = floor(($this->Shadowa / 100) * ((100 / 127) * (127-$Values["alpha"])));
                 $this->drawAlphaPixel($X+$Xc+$this->ShadowX,$Y+$Yc+$this->ShadowY,$AlphaFactor,$this->ShadowR,$this->ShadowG,$this->ShadowB);
                }
              }
            }
          }
        }
       $this->Shadow = $RestoreShadow;

       imagecopy($this->Picture,$Raster,$X,$Y,0,0,$Width,$Height);
       imagedestroy($Raster);
      }
    }

    /* Draw an arrow */
    function drawArrow($X1,$Y1,$X2,$Y2,$format="")
    {
     $FillR	= isset($format["FillR"]) ? $format["FillR"] : 0;
     $FillG	= isset($format["FillG"]) ? $format["FillG"] : 0;
     $FillB	= isset($format["FillB"]) ? $format["FillB"] : 0;
     $BorderR	= isset($format["BorderR"]) ? $format["BorderR"] : $FillR;
     $BorderG	= isset($format["BorderG"]) ? $format["BorderG"] : $FillG;
     $BorderB	= isset($format["BorderB"]) ? $format["BorderB"] : $FillB;
     $Alpha	= isset($format["Alpha"]) ? $format["Alpha"] : 100;
     $Size	= isset($format["Size"]) ? $format["Size"] : 10;
     $Ratio	= isset($format["Ratio"]) ? $format["Ratio"] : .5;
     $TwoHeads	= isset($format["TwoHeads"]) ? $format["TwoHeads"] : false;
     $Ticks	= isset($format["Ticks"]) ? $format["Ticks"] : false;

     /* Calculate the line angle */
     $Angle = $this->getAngle($X1,$Y1,$X2,$Y2);

     /* Override Shadow support, this will be managed internally */
     $RestoreShadow = $this->Shadow;
     if ( $this->Shadow && $this->ShadowX != 0 && $this->ShadowY != 0 )
      {
       $this->Shadow = false;
       $this->drawArrow($X1+$this->ShadowX,$Y1+$this->ShadowY,$X2+$this->ShadowX,$Y2+$this->ShadowY,array("FillR"=>$this->ShadowR,"FillG"=>$this->ShadowG,"FillB"=>$this->ShadowB,"Alpha"=>$this->Shadowa,"Size"=>$Size,"Ratio"=>$Ratio,"TwoHeads"=>$TwoHeads,"Ticks"=>$Ticks));
      }

     /* Draw the 1st Head */
     $TailX = cos(($Angle-180)*PI/180)*$Size+$X2;
     $TailY = sin(($Angle-180)*PI/180)*$Size+$Y2;

     $points	= "";
     $points[]  = $X2; $points[]  = $Y2;
     $points[]	= cos(($Angle-90)*PI/180)*$Size*$Ratio+$TailX; $points[] = sin(($Angle-90)*PI/180)*$Size*$Ratio+$TailY;
     $points[]	= cos(($Angle-270)*PI/180)*$Size*$Ratio+$TailX; $points[] = sin(($Angle-270)*PI/180)*$Size*$Ratio+$TailY;
     $points[]  = $X2; $points[]  = $Y2;

     /* Visual correction */
     if ($Angle == 180 || $Angle == 360 ) { $points[4] = $points[2]; }
     if ($Angle == 90 || $Angle == 270 ) { $points[5] = $points[3]; }

     $ArrowColor = $this->allocateColor($this->Picture,$FillR,$FillG,$FillB,$Alpha);
     ImageFilledPolygon($this->Picture,$points,4,$ArrowColor);

     $this->drawLine($points[0],$points[1],$points[2],$points[3],array("R"=>$BorderR,"G"=>$BorderG,"B"=>$BorderB,"Alpha"=>$Alpha));
     $this->drawLine($points[2],$points[3],$points[4],$points[5],array("R"=>$BorderR,"G"=>$BorderG,"B"=>$BorderB,"Alpha"=>$Alpha));
     $this->drawLine($points[0],$points[1],$points[4],$points[5],array("R"=>$BorderR,"G"=>$BorderG,"B"=>$BorderB,"Alpha"=>$Alpha));

     /* Draw the second head */
     if ( $TwoHeads )
      {
       $Angle = $this->getAngle($X2,$Y2,$X1,$Y1);

       $TailX2 = cos(($Angle-180)*PI/180)*$Size+$X1;
       $TailY2 = sin(($Angle-180)*PI/180)*$Size+$Y1;

       $points   = [];
       $points[] = $X1; $points[]  = $Y1;
       $points[] = cos(($Angle-90)*PI/180)*$Size*$Ratio+$TailX2; $points[] = sin(($Angle-90)*PI/180)*$Size*$Ratio+$TailY2;
       $points[] = cos(($Angle-270)*PI/180)*$Size*$Ratio+$TailX2; $points[] = sin(($Angle-270)*PI/180)*$Size*$Ratio+$TailY2;
       $points[] = $X1; $points[]  = $Y1;

       /* Visual correction */
       if ($Angle == 180 || $Angle == 360 ) { $points[4] = $points[2]; }
       if ($Angle == 90 || $Angle == 270 ) { $points[5] = $points[3]; }

       $ArrowColor = $this->allocateColor($this->Picture,$FillR,$FillG,$FillB,$Alpha);
       ImageFilledPolygon($this->Picture,$points,4,$ArrowColor);

       $this->drawLine($points[0],$points[1],$points[2],$points[3],array("R"=>$BorderR,"G"=>$BorderG,"B"=>$BorderB,"Alpha"=>$Alpha));
       $this->drawLine($points[2],$points[3],$points[4],$points[5],array("R"=>$BorderR,"G"=>$BorderG,"B"=>$BorderB,"Alpha"=>$Alpha));
       $this->drawLine($points[0],$points[1],$points[4],$points[5],array("R"=>$BorderR,"G"=>$BorderG,"B"=>$BorderB,"Alpha"=>$Alpha));

       $this->drawLine($TailX,$TailY,$TailX2,$TailY2,array("R"=>$BorderR,"G"=>$BorderG,"B"=>$BorderB,"Alpha"=>$Alpha,"Ticks"=>$Ticks));
      }
     else
      $this->drawLine($X1,$Y1,$TailX,$TailY,array("R"=>$BorderR,"G"=>$BorderG,"B"=>$BorderB,"Alpha"=>$Alpha,"Ticks"=>$Ticks));

     /* Re-enable shadows */
     $this->Shadow = $RestoreShadow;
    }

    /* Draw a label with associated arrow */
    function drawArrowLabel($X1,$Y1,$Text,$format="")
    {
     $FillR    = isset($format["FillR"]) ? $format["FillR"] : 0;
     $FillG    = isset($format["FillG"]) ? $format["FillG"] : 0;
     $FillB    = isset($format["FillB"]) ? $format["FillB"] : 0;
     $BorderR  = isset($format["BorderR"]) ? $format["BorderR"] : $FillR;
     $BorderG  = isset($format["BorderG"]) ? $format["BorderG"] : $FillG;
     $BorderB  = isset($format["BorderB"]) ? $format["BorderB"] : $FillB;
     $FontName = isset($format["FontName"]) ? $format["FontName"] : $this->FontName;
     $FontSize = isset($format["FontSize"]) ? $format["FontSize"] : $this->FontSize;
     $Alpha    = isset($format["Alpha"]) ? $format["Alpha"] : 100;
     $Length   = isset($format["Length"]) ? $format["Length"] : 50;
     $Angle    = isset($format["Angle"]) ? $format["Angle"] : 315;
     $Size     = isset($format["Size"]) ? $format["Size"] : 10;
     $Position = isset($format["Position"]) ? $format["Position"] : POSITION_TOP;
     $RoundPos = isset($format["RoundPos"]) ? $format["RoundPos"] : false;
     $Ticks    = isset($format["Ticks"]) ? $format["Ticks"] : null;

     $Angle = $Angle % 360;

     $X2 = sin(($Angle+180)*PI/180)*$Length+$X1;
     $Y2 = cos(($Angle+180)*PI/180)*$Length+$Y1;

     if ( $RoundPos && $Angle > 0 && $Angle < 180 ) { $Y2 = ceil($Y2); }
     if ( $RoundPos && $Angle > 180 ) { $Y2 = floor($Y2); }

     $this->drawArrow($X2,$Y2,$X1,$Y1,$format);

     $Size	= imagettfbbox($FontSize,0,$FontName,$Text);
     $TxtWidth	= max(abs($Size[2]-$Size[0]),abs($Size[0]-$Size[6]));
     $TxtHeight	= max(abs($Size[1]-$Size[7]),abs($Size[3]-$Size[1]));

     if ( $Angle > 0 && $Angle < 180 )
      {
       $this->drawLine($X2,$Y2,$X2-$TxtWidth,$Y2,array("R"=>$BorderR,"G"=>$BorderG,"B"=>$BorderB,"Alpha"=>$Alpha,"Ticks"=>$Ticks));
       if ( $Position == POSITION_TOP )
        $this->drawText($X2,$Y2-2,$Text,array("R"=>$BorderR,"G"=>$BorderG,"B"=>$BorderB,"Alpha"=>$Alpha,"Align"=>TEXT_ALIGN_BOTTOMRIGHT));
       else
        $this->drawText($X2,$Y2+4,$Text,array("R"=>$BorderR,"G"=>$BorderG,"B"=>$BorderB,"Alpha"=>$Alpha,"Align"=>TEXT_ALIGN_TOPRIGHT));
      }
     else
      {
       $this->drawLine($X2,$Y2,$X2+$TxtWidth,$Y2,array("R"=>$BorderR,"G"=>$BorderG,"B"=>$BorderB,"Alpha"=>$Alpha,"Ticks"=>$Ticks));
       if ( $Position == POSITION_TOP )
        $this->drawText($X2,$Y2-2,$Text,array("R"=>$BorderR,"G"=>$BorderG,"B"=>$BorderB,"Alpha"=>$Alpha));
       else
        $this->drawText($X2,$Y2+4,$Text,array("R"=>$BorderR,"G"=>$BorderG,"B"=>$BorderB,"Alpha"=>$Alpha,"Align"=>TEXT_ALIGN_TOPLEFT));
      }
    }

    /* Draw a progress bar filled with specified % */
    function drawProgress($X,$Y,$Percent,$format="")
    {
     if ( $Percent > 100 ) { $Percent = 100; }
     if ( $Percent < 0 ) { $Percent = 0; }

     $Width	= isset($format["Width"]) ? $format["Width"] : 200;
     $Height	= isset($format["Height"]) ? $format["Height"] : 20;
     $Orientation = isset($format["Orientation"]) ? $format["Orientation"] : ORIENTATION_HORIZONTAL;
     $ShowLabel = isset($format["ShowLabel"]) ? $format["ShowLabel"] : false;
     $LabelPos	= isset($format["LabelPos"]) ? $format["LabelPos"] : LABEL_POS_INSIDE;
     $Margin    = isset($format["Margin"]) ? $format["Margin"] : 10;
     $R		= isset($format["R"]) ? $format["R"] : 130;
     $G		= isset($format["G"]) ? $format["G"] : 130;
     $B		= isset($format["B"]) ? $format["B"] : 130;
     $RFade	= isset($format["RFade"]) ? $format["RFade"] : -1;
     $GFade	= isset($format["GFade"]) ? $format["GFade"] : -1;
     $BFade	= isset($format["BFade"]) ? $format["BFade"] : -1;
     $BorderR	= isset($format["BorderR"]) ? $format["BorderR"] : $R;
     $BorderG	= isset($format["BorderG"]) ? $format["BorderG"] : $G;
     $BorderB	= isset($format["BorderB"]) ? $format["BorderB"] : $B;
     $BoxBorderR = isset($format["BoxBorderR"]) ? $format["BoxBorderR"] : 0;
     $BoxBorderG = isset($format["BoxBorderG"]) ? $format["BoxBorderG"] : 0;
     $BoxBorderB = isset($format["BoxBorderB"]) ? $format["BoxBorderB"] : 0;
     $BoxBackR	= isset($format["BoxBackR"]) ? $format["BoxBackR"] : 255;
     $BoxBackG	= isset($format["BoxBackG"]) ? $format["BoxBackG"] : 255;
     $BoxBackB	= isset($format["BoxBackB"]) ? $format["BoxBackB"] : 255;
     $Alpha	= isset($format["Alpha"]) ? $format["Alpha"] : 100;
     $Surrounding = isset($format["Surrounding"]) ? $format["Surrounding"] : null;
     $BoxSurrounding = isset($format["BoxSurrounding"]) ? $format["BoxSurrounding"] : null;
     $NoAngle	= isset($format["NoAngle"]) ? $format["NoAngle"] : false;

     if ( $RFade != -1 && $GFade != -1 && $BFade != -1 )
      {
       $RFade = (($RFade-$R)/100)*$Percent+$R;
       $GFade = (($GFade-$G)/100)*$Percent+$G;
       $BFade = (($BFade-$B)/100)*$Percent+$B;
      }

     if ( $Surrounding != null ) { $BorderR = $R + $Surrounding; $BorderG = $G + $Surrounding; $BorderB = $B + $Surrounding; }
     if ( $BoxSurrounding != null ) { $BoxBorderR = $BoxBackR + $Surrounding; $BoxBorderG = $BoxBackG + $Surrounding; $BoxBorderB = $BoxBackB + $Surrounding; }

     if ( $Orientation == ORIENTATION_VERTICAL )
      {
       $InnerHeight = (($Height-2)/100)*$Percent;
       $this->drawFilledRectangle($X,$Y,$X+$Width,$Y-$Height,array("R"=>$BoxBackR,"G"=>$BoxBackG,"B"=>$BoxBackB,"BorderR"=>$BoxBorderR,"BorderG"=>$BoxBorderG,"BorderB"=>$BoxBorderB,"NoAngle"=>$NoAngle));

       $RestoreShadow = $this->Shadow; $this->Shadow  = false;
       if ( $RFade != -1 && $GFade != -1 && $BFade != -1 )
        {
         $GradientOptions = array("StartR"=>$RFade,"StartG"=>$GFade,"StartB"=>$BFade,"EndR"=>$R,"EndG"=>$G,"EndB"=>$B);
         $this->drawGradientArea($X+1,$Y-1,$X+$Width-1,$Y-$InnerHeight,DIRECTION_VERTICAL,$GradientOptions);

         if ( $Surrounding )
          $this->drawRectangle($X+1,$Y-1,$X+$Width-1,$Y-$InnerHeight,array("R"=>255,"G"=>255,"B"=>255,"Alpha"=>$Surrounding));
        }
       else
        $this->drawFilledRectangle($X+1,$Y-1,$X+$Width-1,$Y-$InnerHeight,array("R"=>$R,"G"=>$G,"B"=>$B,"BorderR"=>$BorderR,"BorderG"=>$BorderG,"BorderB"=>$BorderB));

       $this->Shadow = $RestoreShadow;

       if ( $ShowLabel && $LabelPos == LABEL_POS_BOTTOM )	{ $this->drawText($X+($Width/2),$Y+$Margin,$Percent."%",array("Align"=>TEXT_ALIGN_TOPMIDDLE)); }
       if ( $ShowLabel && $LabelPos == LABEL_POS_TOP )		{ $this->drawText($X+($Width/2),$Y-$Height-$Margin,$Percent."%",array("Align"=>TEXT_ALIGN_BOTTOMMIDDLE)); }
       if ( $ShowLabel && $LabelPos == LABEL_POS_INSIDE )	{ $this->drawText($X+($Width/2),$Y-$InnerHeight-$Margin,$Percent."%",array("Align"=>TEXT_ALIGN_MIDDLELEFT,"Angle"=>90)); }
       if ( $ShowLabel && $LabelPos == LABEL_POS_CENTER )	{ $this->drawText($X+($Width/2),$Y-($Height/2),$Percent."%",array("Align"=>TEXT_ALIGN_MIDDLEMIDDLE,"Angle"=>90)); }
      }
     else
      {
       if ( $Percent == 100 )
        $InnerWidth = $Width-1;
       else
        $InnerWidth = (($Width-2)/100)*$Percent;

       $this->drawFilledRectangle($X,$Y,$X+$Width,$Y+$Height,array("R"=>$BoxBackR,"G"=>$BoxBackG,"B"=>$BoxBackB,"BorderR"=>$BoxBorderR,"BorderG"=>$BoxBorderG,"BorderB"=>$BoxBorderB,"NoAngle"=>$NoAngle));

       $RestoreShadow = $this->Shadow; $this->Shadow  = false;
       if ( $RFade != -1 && $GFade != -1 && $BFade != -1 )
        {
         $GradientOptions = array("StartR"=>$R,"StartG"=>$G,"StartB"=>$B,"EndR"=>$RFade,"EndG"=>$GFade,"EndB"=>$BFade);
         $this->drawGradientArea($X+1,$Y+1,$X+$InnerWidth,$Y+$Height-1,DIRECTION_HORIZONTAL,$GradientOptions);

         if ( $Surrounding )
          $this->drawRectangle($X+1,$Y+1,$X+$InnerWidth,$Y+$Height-1,array("R"=>255,"G"=>255,"B"=>255,"Alpha"=>$Surrounding));
        }
       else
        $this->drawFilledRectangle($X+1,$Y+1,$X+$InnerWidth,$Y+$Height-1,array("R"=>$R,"G"=>$G,"B"=>$B,"BorderR"=>$BorderR,"BorderG"=>$BorderG,"BorderB"=>$BorderB));

       $this->Shadow = $RestoreShadow;

       if ( $ShowLabel && $LabelPos == LABEL_POS_LEFT )		{ $this->drawText($X-$Margin,$Y+($Height/2),$Percent."%",array("Align"=>TEXT_ALIGN_MIDDLERIGHT)); }
       if ( $ShowLabel && $LabelPos == LABEL_POS_RIGHT )	{ $this->drawText($X+$Width+$Margin,$Y+($Height/2),$Percent."%",array("Align"=>TEXT_ALIGN_MIDDLELEFT)); }
       if ( $ShowLabel && $LabelPos == LABEL_POS_CENTER )	{ $this->drawText($X+($Width/2),$Y+($Height/2),$Percent."%",array("Align"=>TEXT_ALIGN_MIDDLEMIDDLE)); }
       if ( $ShowLabel && $LabelPos == LABEL_POS_INSIDE )	{ $this->drawText($X+$InnerWidth+$Margin,$Y+($Height/2),$Percent."%",array("Align"=>TEXT_ALIGN_MIDDLELEFT)); }
      }
    }

    /* Get the legend box size */
    function getLegendSize($format="")
    {
     $FontName		= isset($format["FontName"]) ? $format["FontName"] : $this->FontName;
     $FontSize		= isset($format["FontSize"]) ? $format["FontSize"] : $this->FontSize;
     $BoxSize		= isset($format["BoxSize"]) ? $format["BoxSize"] : 5;
     $Margin		= isset($format["Margin"]) ? $format["Margin"] : 5;
     $Style		= isset($format["Style"]) ? $format["Style"] : LEGEND_ROUND;
     $Mode		= isset($format["Mode"]) ? $format["Mode"] : LEGEND_VERTICAL;
     $BoxWidth		= isset($format["BoxWidth"]) ? $format["BoxWidth"] : 5;
     $BoxHeight		= isset($format["BoxHeight"]) ? $format["BoxHeight"] : 5;
     $IconAreaWidth	= isset($format["IconAreaWidth"]) ? $format["IconAreaWidth"] : $BoxWidth;
     $IconAreaHeight	= isset($format["IconAreaHeight"]) ? $format["IconAreaHeight"] : $BoxHeight;
     $XSpacing		= isset($format["XSpacing"]) ? $format["XSpacing"] : 5;

     $Data = $this->DataSet->getData();

     foreach($Data["Series"] as $serie_name => $serie)
      {
       if ( $serie["isDrawable"] == true && $serie_name != $Data["Abscissa"] && isset($serie["Picture"]))
        {
         list($PicWidth,$PicHeight) = $this->getPicInfo($serie["Picture"]);
         if ( $IconAreaWidth < $PicWidth ) { $IconAreaWidth = $PicWidth; }
         if ( $IconAreaHeight < $PicHeight ) { $IconAreaHeight = $PicHeight; }
        }
      }

     $YStep = max($this->FontSize,$IconAreaHeight) + 5;
     $XStep = $IconAreaWidth + 5;
     $XStep = $XSpacing;

     $X=100; $Y=100;

     $Boundaries = []; $Boundaries["L"] = $X; $Boundaries["T"] = $Y; $Boundaries["R"] = 0; $Boundaries["B"] = 0; $vY = $Y; $vX = $X;
     foreach($Data["Series"] as $serie_name => $serie)
      {
       if ( $serie["isDrawable"] == true && $serie_name != $Data["Abscissa"] )
        {
         if ( $Mode == LEGEND_VERTICAL )
          {
           $BoxArray = $this->getTextBox($vX+$IconAreaWidth+4,$vY+$IconAreaHeight/2,$FontName,$FontSize,0,$serie["Description"]);

           if ( $Boundaries["T"] > $BoxArray[2]["Y"]+$IconAreaHeight/2 ) { $Boundaries["T"] = $BoxArray[2]["Y"]+$IconAreaHeight/2; }
           if ( $Boundaries["R"] < $BoxArray[1]["X"]+2 ) { $Boundaries["R"] = $BoxArray[1]["X"]+2; }
           if ( $Boundaries["B"] < $BoxArray[1]["Y"]+2+$IconAreaHeight/2 ) { $Boundaries["B"] = $BoxArray[1]["Y"]+2+$IconAreaHeight/2; }

           $Lines = preg_split("/\n/",$serie["Description"]);
           $vY = $vY + max($this->FontSize*count($Lines),$IconAreaHeight) + 5;
          }
         elseif ( $Mode == LEGEND_HORIZONTAL )
          {
           $Lines = preg_split("/\n/",$serie["Description"]);
           $Width = [];
           foreach($Lines as $Key => $Value)
            {
             $BoxArray = $this->getTextBox($vX+$IconAreaWidth+6,$Y+$IconAreaHeight/2+(($this->FontSize+3)*$Key),$FontName,$FontSize,0,$Value);

             if ( $Boundaries["T"] > $BoxArray[2]["Y"]+$IconAreaHeight/2 ) { $Boundaries["T"] = $BoxArray[2]["Y"]+$IconAreaHeight/2; }
             if ( $Boundaries["R"] < $BoxArray[1]["X"]+2 ) { $Boundaries["R"] = $BoxArray[1]["X"]+2; }
             if ( $Boundaries["B"] < $BoxArray[1]["Y"]+2+$IconAreaHeight/2 ) { $Boundaries["B"] = $BoxArray[1]["Y"]+2+$IconAreaHeight/2; }

             $Width[] = $BoxArray[1]["X"];
            }

           $vX=max($Width)+$XStep;
          }
        }
      }
     $vY=$vY-$YStep; $vX=$vX-$XStep;

     $TopOffset  = $Y - $Boundaries["T"];
     if ( $Boundaries["B"]-($vY+$IconAreaHeight) < $TopOffset ) { $Boundaries["B"] = $vY+$IconAreaHeight+$TopOffset; }

     $Width  = ($Boundaries["R"]+$Margin) - ($Boundaries["L"]-$Margin);
     $Height = ($Boundaries["B"]+$Margin) - ($Boundaries["T"]-$Margin);

     return array("Width"=>$Width,"Height"=>$Height);
    }

    /* Draw the legend of the active series */
    function drawLegend($X,$Y,$format="")
    {
     $Family	= isset($format["Family"]) ? $format["Family"] : LEGEND_FAMILY_BOX;
     $FontName	= isset($format["FontName"]) ? $format["FontName"] : $this->FontName;
     $FontSize	= isset($format["FontSize"]) ? $format["FontSize"] : $this->FontSize;
     $FontR	= isset($format["FontR"]) ? $format["FontR"] : $this->FontColorR;
     $FontG	= isset($format["FontG"]) ? $format["FontG"] : $this->FontColorG;
     $FontB	= isset($format["FontB"]) ? $format["FontB"] : $this->FontColorB;
     $BoxWidth	= isset($format["BoxWidth"]) ? $format["BoxWidth"] : 5;
     $BoxHeight	= isset($format["BoxHeight"]) ? $format["BoxHeight"] : 5;
     $IconAreaWidth	= isset($format["IconAreaWidth"]) ? $format["IconAreaWidth"] : $BoxWidth;
     $IconAreaHeight	= isset($format["IconAreaHeight"]) ? $format["IconAreaHeight"] : $BoxHeight;
     $XSpacing	= isset($format["XSpacing"]) ? $format["XSpacing"] : 5;
     $Margin	= isset($format["Margin"]) ? $format["Margin"] : 5;
     $R		= isset($format["R"]) ? $format["R"] : 200;
     $G		= isset($format["G"]) ? $format["G"] : 200;
     $B		= isset($format["B"]) ? $format["B"] : 200;
     $Alpha	= isset($format["Alpha"]) ? $format["Alpha"] : 100;
     $BorderR	= isset($format["BorderR"]) ? $format["BorderR"] : 255;
     $BorderG	= isset($format["BorderG"]) ? $format["BorderG"] : 255;
     $BorderB	= isset($format["BorderB"]) ? $format["BorderB"] : 255;
     $Surrounding = isset($format["Surrounding"]) ? $format["Surrounding"] : null;
     $Style	= isset($format["Style"]) ? $format["Style"] : LEGEND_ROUND;
     $Mode	= isset($format["Mode"]) ? $format["Mode"] : LEGEND_VERTICAL;

     if ( $Surrounding != null ) { $BorderR = $R + $Surrounding; $BorderG = $G + $Surrounding; $BorderB = $B + $Surrounding; }

     $Data = $this->DataSet->getData();

     foreach($Data["Series"] as $serie_name => $serie)
      {
       if ( $serie["isDrawable"] == true && $serie_name != $Data["Abscissa"] && isset($serie["Picture"]))
        {
         list($PicWidth,$PicHeight) = $this->getPicInfo($serie["Picture"]);
         if ( $IconAreaWidth < $PicWidth ) { $IconAreaWidth = $PicWidth; }
         if ( $IconAreaHeight < $PicHeight ) { $IconAreaHeight = $PicHeight; }
        }
      }

     $YStep = max($this->FontSize,$IconAreaHeight) + 5;
     $XStep = $IconAreaWidth + 5;
     $XStep = $XSpacing;

     $Boundaries = []; $Boundaries["L"] = $X; $Boundaries["T"] = $Y; $Boundaries["R"] = 0; $Boundaries["B"] = 0; $vY = $Y; $vX = $X;
     foreach($Data["Series"] as $serie_name => $serie)
      {
       if ( $serie["isDrawable"] == true && $serie_name != $Data["Abscissa"] )
        {
         if ( $Mode == LEGEND_VERTICAL )
          {
           $BoxArray = $this->getTextBox($vX+$IconAreaWidth+4,$vY+$IconAreaHeight/2,$FontName,$FontSize,0,$serie["Description"]);

           if ( $Boundaries["T"] > $BoxArray[2]["Y"]+$IconAreaHeight/2 ) { $Boundaries["T"] = $BoxArray[2]["Y"]+$IconAreaHeight/2; }
           if ( $Boundaries["R"] < $BoxArray[1]["X"]+2 ) { $Boundaries["R"] = $BoxArray[1]["X"]+2; }
           if ( $Boundaries["B"] < $BoxArray[1]["Y"]+2+$IconAreaHeight/2 ) { $Boundaries["B"] = $BoxArray[1]["Y"]+2+$IconAreaHeight/2; }

           $Lines = preg_split("/\n/",$serie["Description"]);
           $vY = $vY + max($this->FontSize*count($Lines),$IconAreaHeight) + 5;
          }
         elseif ( $Mode == LEGEND_HORIZONTAL )
          {
           $Lines = preg_split("/\n/",$serie["Description"]);
           $Width = [];
           foreach($Lines as $Key => $Value)
            {
             $BoxArray = $this->getTextBox($vX+$IconAreaWidth+6,$Y+$IconAreaHeight/2+(($this->FontSize+3)*$Key),$FontName,$FontSize,0,$Value);

             if ( $Boundaries["T"] > $BoxArray[2]["Y"]+$IconAreaHeight/2 ) { $Boundaries["T"] = $BoxArray[2]["Y"]+$IconAreaHeight/2; }
             if ( $Boundaries["R"] < $BoxArray[1]["X"]+2 ) { $Boundaries["R"] = $BoxArray[1]["X"]+2; }
             if ( $Boundaries["B"] < $BoxArray[1]["Y"]+2+$IconAreaHeight/2 ) { $Boundaries["B"] = $BoxArray[1]["Y"]+2+$IconAreaHeight/2; }

             $Width[] = $BoxArray[1]["X"];
            }

           $vX=max($Width)+$XStep;
          }
        }
      }
     $vY=$vY-$YStep; $vX=$vX-$XStep;

     $TopOffset  = $Y - $Boundaries["T"];
     if ( $Boundaries["B"]-($vY+$IconAreaHeight) < $TopOffset ) { $Boundaries["B"] = $vY+$IconAreaHeight+$TopOffset; }

     if ( $Style == LEGEND_ROUND )
      $this->drawRoundedFilledRectangle($Boundaries["L"]-$Margin,$Boundaries["T"]-$Margin,$Boundaries["R"]+$Margin,$Boundaries["B"]+$Margin,$Margin,array("R"=>$R,"G"=>$G,"B"=>$B,"Alpha"=>$Alpha,"BorderR"=>$BorderR,"BorderG"=>$BorderG,"BorderB"=>$BorderB));
     elseif ( $Style == LEGEND_BOX )
      $this->drawFilledRectangle($Boundaries["L"]-$Margin,$Boundaries["T"]-$Margin,$Boundaries["R"]+$Margin,$Boundaries["B"]+$Margin,array("R"=>$R,"G"=>$G,"B"=>$B,"Alpha"=>$Alpha,"BorderR"=>$BorderR,"BorderG"=>$BorderG,"BorderB"=>$BorderB));

     $RestoreShadow = $this->Shadow; $this->Shadow = false;
     foreach($Data["Series"] as $serie_name => $serie)
      {
       if ( $serie["isDrawable"] == true && $serie_name != $Data["Abscissa"] )
        {
         $R = $serie["Color"]["R"]; $G = $serie["Color"]["G"]; $B = $serie["Color"]["B"];
         $Ticks = $serie["Ticks"]; $Weight = $serie["Weight"];

         if ( isset($serie["Picture"]) )
          {
           $Picture = $serie["Picture"];
           list($PicWidth,$PicHeight) = $this->getPicInfo($Picture);
           $PicX = $X+$IconAreaWidth/2; $PicY = $Y+$IconAreaHeight/2;

           $this->drawFromPNG($PicX-$PicWidth/2,$PicY-$PicHeight/2,$Picture);
          }
         else
          {
           if ( $Family == LEGEND_FAMILY_BOX )
            {
             if ( $BoxWidth != $IconAreaWidth ) { $XOffset = floor(($IconAreaWidth-$BoxWidth)/2); } else { $XOffset = 0; }
             if ( $BoxHeight != $IconAreaHeight ) { $YOffset = floor(($IconAreaHeight-$BoxHeight)/2); } else { $YOffset = 0; }

             $this->drawFilledRectangle($X+1+$XOffset,$Y+1+$YOffset,$X+$BoxWidth+$XOffset+1,$Y+$BoxHeight+1+$YOffset,array("R"=>0,"G"=>0,"B"=>0,"Alpha"=>20));
             $this->drawFilledRectangle($X+$XOffset,$Y+$YOffset,$X+$BoxWidth+$XOffset,$Y+$BoxHeight+$YOffset,array("R"=>$R,"G"=>$G,"B"=>$B,"Surrounding"=>20));
            }
           elseif ( $Family == LEGEND_FAMILY_CIRCLE )
            {
             $this->drawFilledCircle($X+1+$IconAreaWidth/2,$Y+1+$IconAreaHeight/2,min($IconAreaHeight/2,$IconAreaWidth/2),array("R"=>0,"G"=>0,"B"=>0,"Alpha"=>20));
             $this->drawFilledCircle($X+$IconAreaWidth/2,$Y+$IconAreaHeight/2,min($IconAreaHeight/2,$IconAreaWidth/2),array("R"=>$R,"G"=>$G,"B"=>$B,"Surrounding"=>20));
            }
           elseif ( $Family == LEGEND_FAMILY_LINE )
            {
             $this->drawLine($X+1,$Y+1+$IconAreaHeight/2,$X+1+$IconAreaWidth,$Y+1+$IconAreaHeight/2,array("R"=>0,"G"=>0,"B"=>0,"Alpha"=>20,"Ticks"=>$Ticks,"Weight"=>$Weight));
             $this->drawLine($X,$Y+$IconAreaHeight/2,$X+$IconAreaWidth,$Y+$IconAreaHeight/2,array("R"=>$R,"G"=>$G,"B"=>$B,"Ticks"=>$Ticks,"Weight"=>$Weight));
            }
          }

         if ( $Mode == LEGEND_VERTICAL )
          {
           $Lines = preg_split("/\n/",$serie["Description"]);
           foreach($Lines as $Key => $Value)
            $this->drawText($X+$IconAreaWidth+4,$Y+$IconAreaHeight/2+(($this->FontSize+3)*$Key),$Value,array("R"=>$FontR,"G"=>$FontG,"B"=>$FontB,"Align"=>TEXT_ALIGN_MIDDLELEFT,"FontSize"=>$FontSize,"FontName"=>$FontName));

           $Y=$Y+max($this->FontSize*count($Lines),$IconAreaHeight) + 5;
          }
         elseif ( $Mode == LEGEND_HORIZONTAL )
          {
           $Lines = preg_split("/\n/",$serie["Description"]);
           $Width = [];
           foreach($Lines as $Key => $Value)
            {
             $BoxArray = $this->drawText($X+$IconAreaWidth+4,$Y+$IconAreaHeight/2+(($this->FontSize+3)*$Key),$Value,array("R"=>$FontR,"G"=>$FontG,"B"=>$FontB,"Align"=>TEXT_ALIGN_MIDDLELEFT,"FontSize"=>$FontSize,"FontName"=>$FontName));
             $Width[] = $BoxArray[1]["X"];
            }
           $X=max($Width)+2+$XStep;
          }
        }
      }


     $this->Shadow = $RestoreShadow;
    }

    function drawScale($format="")
    {
     $Pos		= isset($format["Pos"]) ? $format["Pos"] : SCALE_POS_LEFTRIGHT;
     $Floating		= isset($format["Floating"]) ? $format["Floating"] : false;
     $Mode		= isset($format["Mode"]) ? $format["Mode"] : SCALE_MODE_FLOATING;
     $RemoveXAxis	= isset($format["RemoveXAxis"]) ? $format["RemoveXAxis"] : false;
     $MinDivHeight	= isset($format["MinDivHeight"]) ? $format["MinDivHeight"] : 20;
     $Factors		= isset($format["Factors"]) ? $format["Factors"] : array(1,2,5);
     $ManualScale	= isset($format["ManualScale"]) ? $format["ManualScale"] : array("0"=>array("Min"=>-100,"Max"=>100));
     $XMargin		= isset($format["XMargin"]) ? $format["XMargin"] : AUTO;
     $YMargin		= isset($format["YMargin"]) ? $format["YMargin"] : 0;
     $ScaleSpacing	= isset($format["ScaleSpacing"]) ? $format["ScaleSpacing"] : 15;
     $InnerTickWidth	= isset($format["InnerTickWidth"]) ? $format["InnerTickWidth"] : 2;
     $OuterTickWidth	= isset($format["OuterTickWidth"]) ? $format["OuterTickWidth"] : 2;
     $DrawXLines	= isset($format["DrawXLines"]) ? $format["DrawXLines"] : true;
     $DrawYLines	= isset($format["DrawYLines"]) ? $format["DrawYLines"] : ALL;
     $GridTicks		= isset($format["GridTicks"]) ? $format["GridTicks"] : 4;
     $GridR		= isset($format["GridR"]) ? $format["GridR"] : 255;
     $GridG		= isset($format["GridG"]) ? $format["GridG"] : 255;
     $GridB		= isset($format["GridB"]) ? $format["GridB"] : 255;
     $GridAlpha		= isset($format["GridAlpha"]) ? $format["GridAlpha"] : 40;
     $AxisRo		= isset($format["AxisR"]) ? $format["AxisR"] : 0;
     $AxisGo		= isset($format["AxisG"]) ? $format["AxisG"] : 0;
     $AxisBo		= isset($format["AxisB"]) ? $format["AxisB"] : 0;
     $AxisAlpha		= isset($format["AxisAlpha"]) ? $format["AxisAlpha"] : 100;
     $TickRo		= isset($format["TickR"]) ? $format["TickR"] : 0;
     $TickGo		= isset($format["TickG"]) ? $format["TickG"] : 0;
     $TickBo		= isset($format["TickB"]) ? $format["TickB"] : 0;
     $TickAlpha		= isset($format["TickAlpha"]) ? $format["TickAlpha"] : 100;
     $DrawSubTicks	= isset($format["DrawSubTicks"]) ? $format["DrawSubTicks"] : false;
     $InnerSubTickWidth	= isset($format["InnerSubTickWidth"]) ? $format["InnerSubTickWidth"] : 0;
     $OuterSubTickWidth	= isset($format["OuterSubTickWidth"]) ? $format["OuterSubTickWidth"] : 2;
     $SubTickR		= isset($format["SubTickR"]) ? $format["SubTickR"] : 255;
     $SubTickG		= isset($format["SubTickG"]) ? $format["SubTickG"] : 0;
     $SubTickB		= isset($format["SubTickB"]) ? $format["SubTickB"] : 0;
     $SubTickAlpha	= isset($format["SubTickAlpha"]) ? $format["SubTickAlpha"] : 100;
     $AutoAxisLabels	= isset($format["AutoAxisLabels"]) ? $format["AutoAxisLabels"] : true;
     $XReleasePercent	= isset($format["XReleasePercent"]) ? $format["XReleasePercent"] : 1;
     $DrawArrows	= isset($format["DrawArrows"]) ? $format["DrawArrows"] : false;
     $ArrowSize         = isset($format["ArrowSize"]) ? $format["ArrowSize"] : 8;
     $CycleBackground	= isset($format["CycleBackground"]) ? $format["CycleBackground"] : false;
     $BackgroundR1	= isset($format["BackgroundR1"]) ? $format["BackgroundR1"] : 255;
     $BackgroundG1	= isset($format["BackgroundG1"]) ? $format["BackgroundG1"] : 255;
     $BackgroundB1	= isset($format["BackgroundB1"]) ? $format["BackgroundB1"] : 255;
     $BackgroundAlpha1	= isset($format["BackgroundAlpha1"]) ? $format["BackgroundAlpha1"] : 20;
     $BackgroundR2	= isset($format["BackgroundR2"]) ? $format["BackgroundR2"] : 230;
     $BackgroundG2	= isset($format["BackgroundG2"]) ? $format["BackgroundG2"] : 230;
     $BackgroundB2	= isset($format["BackgroundB2"]) ? $format["BackgroundB2"] : 230;
     $BackgroundAlpha2	= isset($format["BackgroundAlpha2"]) ? $format["BackgroundAlpha2"] : 20;
     $LabelingMethod	= isset($format["LabelingMethod"]) ? $format["LabelingMethod"] : LABELING_ALL;
     $LabelSkip		= isset($format["LabelSkip"]) ? $format["LabelSkip"] : 0;
     $LabelRotation	= isset($format["LabelRotation"]) ? $format["LabelRotation"] : 0;
     $SkippedAxisTicks	= isset($format["SkippedAxisTicks"]) ? $format["SkippedAxisTicks"] : $GridTicks+2;
     $SkippedAxisR	= isset($format["SkippedAxisR"]) ? $format["SkippedAxisR"] : $GridR;
     $SkippedAxisG	= isset($format["SkippedAxisG"]) ? $format["SkippedAxisG"] : $GridG;
     $SkippedAxisB	= isset($format["SkippedAxisB"]) ? $format["SkippedAxisB"] : $GridB;
     $SkippedAxisAlpha	= isset($format["SkippedAxisAlpha"]) ? $format["SkippedAxisAlpha"] : $GridAlpha-30;
     $SkippedTickR	= isset($format["SkippedTickR"]) ? $format["SkippedTickR"] : $TickRo;
     $SkippedTickG	= isset($format["SkippedTickG"]) ? $format["SkippedTickG"] : $TickGo;
     $SkippedTickB	= isset($format["SkippedTicksB"]) ? $format["SkippedTickB"] : $TickBo;
     $SkippedTickAlpha	= isset($format["SkippedTickAlpha"]) ? $format["SkippedTickAlpha"] : $TickAlpha-80;
     $SkippedInnerTickWidth	= isset($format["SkippedInnerTickWidth"]) ? $format["SkippedInnerTickWidth"] : 0;
     $SkippedOuterTickWidth	= isset($format["SkippedOuterTickWidth"]) ? $format["SkippedOuterTickWidth"] : 2;

     /* Floating scale require X & Y margins to be set manually */
     if ( $Floating && ( $XMargin == AUTO || $YMargin == 0 ) ) { $Floating = false; }

     /* Skip a NOTICE event in case of an empty array */
     if ( $DrawYLines == NONE || $DrawYLines == false ) { $DrawYLines = array("zarma"=>"31"); }

     /* Define the color for the skipped elements */
     $SkippedAxisColor = array("R"=>$SkippedAxisR,"G"=>$SkippedAxisG,"B"=>$SkippedAxisB,"Alpha"=>$SkippedAxisAlpha,"Ticks"=>$SkippedAxisTicks);
     $SkippedTickColor = array("R"=>$SkippedTickR,"G"=>$SkippedTickG,"B"=>$SkippedTickB,"Alpha"=>$SkippedTickAlpha);

     $Data = $this->DataSet->getData();
     if ( isset($Data["Abscissa"]) ) { $Abscissa = $Data["Abscissa"]; } else { $Abscissa = null; }

     /* Unset the abscissa axis, needed if we display multiple charts on the same picture */
     if ( $Abscissa != null )
      {
       foreach($Data["Axis"] as $AxisID => $Parameters)
        { if ($Parameters["Identity"] == AXIS_X) { unset($Data["Axis"][$AxisID]); } }
      }

     /* Build the scale settings */
     $GotXAxis = false;
     foreach($Data["Axis"] as $AxisID => $AxisParameter)
      {
       if ( $AxisParameter["Identity"] == AXIS_X ) { $GotXAxis = true; }

       if ( $Pos == SCALE_POS_LEFTRIGHT && $AxisParameter["Identity"] == AXIS_Y)
        { $Height = $this->GraphAreaY2-$this->GraphAreaY1 - $YMargin*2; }
       elseif ( $Pos == SCALE_POS_LEFTRIGHT && $AxisParameter["Identity"] == AXIS_X)
        { $Height = $this->GraphAreaX2-$this->GraphAreaX1; }
       elseif ( $Pos == SCALE_POS_TOPBOTTOM && $AxisParameter["Identity"] == AXIS_Y)
        { $Height = $this->GraphAreaX2-$this->GraphAreaX1 - $YMargin*2;; }
       else
        { $Height = $this->GraphAreaY2-$this->GraphAreaY1; }

       $AxisMin = ABSOLUTE_MAX; $AxisMax = OUT_OF_SIGHT;
       if ( $Mode == SCALE_MODE_FLOATING || $Mode == SCALE_MODE_START0 )
        {
         foreach($Data["Series"] as $serieID => $serieParameter)
          {
           if ( $serieParameter["Axis"] == $AxisID && $Data["Series"][$serieID]["isDrawable"] && $Data["Abscissa"] != $serieID)
            {
             $AxisMax = max($AxisMax,$Data["Series"][$serieID]["Max"]);
             $AxisMin = min($AxisMin,$Data["Series"][$serieID]["Min"]);
            }
          }
         $AutoMargin = (($AxisMax-$AxisMin)/100)*$XReleasePercent;

         $Data["Axis"][$AxisID]["Min"] = $AxisMin-$AutoMargin; $Data["Axis"][$AxisID]["Max"] = $AxisMax+$AutoMargin;
         if ( $Mode == SCALE_MODE_START0 ) { $Data["Axis"][$AxisID]["Min"] = 0; }
        }
       elseif ( $Mode == SCALE_MODE_MANUAL )
        {
         if ( isset($ManualScale[$AxisID]["Min"]) && isset($ManualScale[$AxisID]["Max"]) )
          {
           $Data["Axis"][$AxisID]["Min"] = $ManualScale[$AxisID]["Min"];
           $Data["Axis"][$AxisID]["Max"] = $ManualScale[$AxisID]["Max"];
          }
         else
          { echo "Manual scale boundaries not set."; exit(); }
        }
       elseif ( $Mode == SCALE_MODE_ADDALL || $Mode == SCALE_MODE_ADDALL_START0 )
        {
         $series = [];
         foreach($Data["Series"] as $serieID => $serieParameter)
          { if ( $serieParameter["Axis"] == $AxisID && $serieParameter["isDrawable"] && $Data["Abscissa"] != $serieID ) { $series[$serieID] = count($Data["Series"][$serieID]["Data"]); } }

         for ($ID=0;$ID<=max($series)-1;$ID++)
          {
           $PointMin = 0; $PointMax = 0;
           foreach($series as $serieID => $ValuesCount )
            {
             if (isset($Data["Series"][$serieID]["Data"][$ID]) && $Data["Series"][$serieID]["Data"][$ID] != null )
              {
               $Value = $Data["Series"][$serieID]["Data"][$ID];
               if ( $Value > 0 ) { $PointMax = $PointMax + $Value; } else { $PointMin = $PointMin + $Value; }
              }
            }
           $AxisMax = max($AxisMax,$PointMax);
           $AxisMin = min($AxisMin,$PointMin);
          }
         $AutoMargin = (($AxisMax-$AxisMin)/100)*$XReleasePercent;
         $Data["Axis"][$AxisID]["Min"] = $AxisMin-$AutoMargin; $Data["Axis"][$AxisID]["Max"] = $AxisMax+$AutoMargin;
        }
       $MaxDivs = floor($Height/$MinDivHeight);

       if ( $Mode == SCALE_MODE_ADDALL_START0 ) { $Data["Axis"][$AxisID]["Min"] = 0; }

       $Scale   = $this->computeScale($Data["Axis"][$AxisID]["Min"],$Data["Axis"][$AxisID]["Max"],$MaxDivs,$Factors,$AxisID);

       $Data["Axis"][$AxisID]["Margin"]    = $AxisParameter["Identity"] == AXIS_X ? $XMargin : $YMargin;
       $Data["Axis"][$AxisID]["ScaleMin"]  = $Scale["XMin"];
       $Data["Axis"][$AxisID]["ScaleMax"]  = $Scale["XMax"];
       $Data["Axis"][$AxisID]["Rows"]      = $Scale["Rows"];
       $Data["Axis"][$AxisID]["RowHeight"] = $Scale["RowHeight"];

       if ( isset($Scale["Format"]) ) { $Data["Axis"][$AxisID]["Format"] = $Scale["Format"]; }

       if ( !isset($Data["Axis"][$AxisID]["Display"]) ) { $Data["Axis"][$AxisID]["Display"] = null; }
       if ( !isset($Data["Axis"][$AxisID]["Format"]) )  { $Data["Axis"][$AxisID]["Format"] = null; }
       if ( !isset($Data["Axis"][$AxisID]["Unit"]) )    { $Data["Axis"][$AxisID]["Unit"] = null; }
      }

     /* Still no X axis */
     if ( $GotXAxis == false )
      {
       if ( $Abscissa != null )
        {
         $points = count($Data["Series"][$Abscissa]["Data"]);
         if ( $AutoAxisLabels )
          $AxisName = isset($Data["Series"][$Abscissa]["Description"]) ? $Data["Series"][$Abscissa]["Description"] : null;
         else
          $AxisName = null;
        }
       else
        {
         $points = 0;
         $AxisName = isset($Data["XAxisName"]) ? $Data["XAxisName"] : null;
         foreach($Data["Series"] as $serieID => $serieParameter)
          { if ( $serieParameter["isDrawable"] ) { $points = max($points,count($serieParameter["Data"])); } }
        }

       $AxisID = count($Data["Axis"]);
       $Data["Axis"][$AxisID]["Identity"] = AXIS_X;
       if ( $Pos == SCALE_POS_LEFTRIGHT ) { $Data["Axis"][$AxisID]["Position"] = AXIS_POSITION_BOTTOM; } else { $Data["Axis"][$AxisID]["Position"] = AXIS_POSITION_LEFT; }
       if ( isset($Data["AbscissaName"]) ) { $Data["Axis"][$AxisID]["Name"] = $Data["AbscissaName"]; }
       if ( $XMargin == AUTO )
        {
         if ( $Pos == SCALE_POS_LEFTRIGHT )
          { $Height = $this->GraphAreaX2-$this->GraphAreaX1; }
         else
          { $Height = $this->GraphAreaY2-$this->GraphAreaY1; }

         if ( $points == 1 )
          $Data["Axis"][$AxisID]["Margin"] = $Height / 2;
         else
          $Data["Axis"][$AxisID]["Margin"] = ($Height/$points) / 2;
        }
       else
        { $Data["Axis"][$AxisID]["Margin"] = $XMargin; }
       $Data["Axis"][$AxisID]["Rows"] = $points-1;
       if ( !isset($Data["Axis"][$AxisID]["Display"]) ) { $Data["Axis"][$AxisID]["Display"] = null; }
       if ( !isset($Data["Axis"][$AxisID]["Format"]) )  { $Data["Axis"][$AxisID]["Format"] = null; }
       if ( !isset($Data["Axis"][$AxisID]["Unit"]) )    { $Data["Axis"][$AxisID]["Unit"] = null; }
      }

     /* Do we need to reverse the abscissa position? */
     if ( $Pos != SCALE_POS_LEFTRIGHT )
      {
       if ( $Data["AbsicssaPosition"] == AXIS_POSITION_BOTTOM )
        { $Data["AbsicssaPosition"] = AXIS_POSITION_LEFT; }
       else
        { $Data["AbsicssaPosition"] = AXIS_POSITION_RIGHT; }
      }
     $Data["Axis"][$AxisID]["Position"] = $Data["AbsicssaPosition"];

     $this->DataSet->saveOrientation($Pos);
     $this->DataSet->saveAxisConfig($Data["Axis"]);
     $this->DataSet->saveYMargin($YMargin);

     $FontColorRo = $this->FontColorR; $FontColorGo = $this->FontColorG; $FontColorBo = $this->FontColorB;

     $AxisPos["L"] = $this->GraphAreaX1; $AxisPos["R"] = $this->GraphAreaX2; $AxisPos["T"] = $this->GraphAreaY1; $AxisPos["B"] = $this->GraphAreaY2;
     foreach($Data["Axis"] as $AxisID => $Parameters)
      {
       if ( isset($Parameters["Color"]) )
        {
         $AxisR = $Parameters["Color"]["R"]; $AxisG = $Parameters["Color"]["G"]; $AxisB = $Parameters["Color"]["B"];
         $TickR = $Parameters["Color"]["R"]; $TickG = $Parameters["Color"]["G"]; $TickB = $Parameters["Color"]["B"];
         $this->setFontProperties(array("R"=>$Parameters["Color"]["R"],"G"=>$Parameters["Color"]["G"],"B"=>$Parameters["Color"]["B"]));
        }
       else
        {
         $AxisR = $AxisRo; $AxisG = $AxisGo; $AxisB = $AxisBo;
         $TickR = $TickRo; $TickG = $TickGo; $TickB = $TickBo;
         $this->setFontProperties(array("R"=>$FontColorRo,"G"=>$FontColorGo,"B"=>$FontColorBo));
        }

       $LastValue = "w00t"; $ID = 1;
       if ( $Parameters["Identity"] == AXIS_X )
        {
         if ( $Pos == SCALE_POS_LEFTRIGHT )
          {
           if ( $Parameters["Position"] == AXIS_POSITION_BOTTOM )
            {
             if ( $LabelRotation == 0 )					{ $LabelAlign = TEXT_ALIGN_TOPMIDDLE; $YLabelOffset = 2; }
             if ( $LabelRotation > 0 && $LabelRotation < 190 )		{ $LabelAlign = TEXT_ALIGN_MIDDLERIGHT; $YLabelOffset = 5; }
             if ( $LabelRotation == 180 )				{ $LabelAlign = TEXT_ALIGN_BOTTOMMIDDLE; $YLabelOffset = 5; }
             if ( $LabelRotation > 180 && $LabelRotation < 360 )	{ $LabelAlign = TEXT_ALIGN_MIDDLELEFT; $YLabelOffset = 2; }

             if ( !$RemoveXAxis )
              {
               if ( $Floating )
                { $FloatingOffset = $YMargin; $this->drawLine($this->GraphAreaX1+$Parameters["Margin"],$AxisPos["B"],$this->GraphAreaX2-$Parameters["Margin"],$AxisPos["B"],array("R"=>$AxisR,"G"=>$AxisG,"B"=>$AxisB,"Alpha"=>$AxisAlpha)); }
               else
                { $FloatingOffset = 0; $this->drawLine($this->GraphAreaX1,$AxisPos["B"],$this->GraphAreaX2,$AxisPos["B"],array("R"=>$AxisR,"G"=>$AxisG,"B"=>$AxisB,"Alpha"=>$AxisAlpha)); }

               if ( $DrawArrows ) { $this->drawArrow($this->GraphAreaX2-$Parameters["Margin"],$AxisPos["B"],$this->GraphAreaX2+($ArrowSize*2),$AxisPos["B"],array("FillR"=>$AxisR,"FillG"=>$AxisG,"FillB"=>$AxisB,"Size"=>$ArrowSize)); }
              }

             $Width = ($this->GraphAreaX2 - $this->GraphAreaX1) - $Parameters["Margin"]*2;

             if ($Parameters["Rows"] == 0 ) { $Step  = $Width; } else { $Step  = $Width / ($Parameters["Rows"]); }

             $MaxBottom = $AxisPos["B"];
             for($i=0;$i<=$Parameters["Rows"];$i++)
              {
               $XPos  = $this->GraphAreaX1 + $Parameters["Margin"] + $Step*$i;
               $YPos  = $AxisPos["B"];

               if ( $Abscissa != null )
                { if ( isset($Data["Series"][$Abscissa]["Data"][$i]) ) { $Value = $this->scaleFormat($Data["Series"][$Abscissa]["Data"][$i],$Data["XAxisDisplay"],$Data["XAxisFormat"],$Data["XAxisUnit"]); } else { $Value = ""; } }
               else
                {
                 if ( isset($Parameters["ScaleMin"]) && isset ($Parameters["RowHeight"]) )
                  $Value = $this->scaleFormat($Parameters["ScaleMin"] + $Parameters["RowHeight"]*$i,$Data["XAxisDisplay"],$Data["XAxisFormat"],$Data["XAxisUnit"]);
                 else
                  $Value = $i;
                }

               $ID++; $Skipped = true;
               if ( $this->isValidLabel($Value,$LastValue,$LabelingMethod,$ID,$LabelSkip) && !$RemoveXAxis)
                {
                 $Bounds    = $this->drawText($XPos,$YPos+$OuterTickWidth+$YLabelOffset,$Value,array("Angle"=>$LabelRotation,"Align"=>$LabelAlign));
                 $TxtBottom = $YPos+$OuterTickWidth+2+($Bounds[0]["Y"]-$Bounds[2]["Y"]);
                 $MaxBottom = max($MaxBottom,$TxtBottom);
                 $LastValue = $Value;
                 $Skipped   = false;
                }

               if ( $RemoveXAxis ) { $Skipped   = false; }

               if ( $Skipped )
                {
                 if ( $DrawXLines ) { $this->drawLine($XPos,$this->GraphAreaY1+$FloatingOffset,$XPos,$this->GraphAreaY2-$FloatingOffset,$SkippedAxisColor); }
                 if ( ($SkippedInnerTickWidth !=0 || $SkippedOuterTickWidth != 0) && !$RemoveXAxis ) { $this->drawLine($XPos,$YPos-$SkippedInnerTickWidth,$XPos,$YPos+$SkippedOuterTickWidth,$SkippedTickColor); }
                }
               else
                {
                 if ( $DrawXLines && ($XPos != $this->GraphAreaX1 && $XPos != $this->GraphAreaX2) ) { $this->drawLine($XPos,$this->GraphAreaY1+$FloatingOffset,$XPos,$this->GraphAreaY2-$FloatingOffset,array("R"=>$GridR,"G"=>$GridG,"B"=>$GridB,"Alpha"=>$GridAlpha,"Ticks"=>$GridTicks)); }
                 if ( ($InnerTickWidth !=0 || $OuterTickWidth != 0) && !$RemoveXAxis ) { $this->drawLine($XPos,$YPos-$InnerTickWidth,$XPos,$YPos+$OuterTickWidth,array("R"=>$TickR,"G"=>$TickG,"B"=>$TickB,"Alpha"=>$TickAlpha)); }
                }
              }

             if ( isset($Parameters["Name"]) && !$RemoveXAxis)
              {
               $YPos   = $MaxBottom+2;
               $XPos   = $this->GraphAreaX1+($this->GraphAreaX2-$this->GraphAreaX1)/2;
               $Bounds = $this->drawText($XPos,$YPos,$Parameters["Name"],array("Align"=>TEXT_ALIGN_TOPMIDDLE));
               $MaxBottom = $Bounds[0]["Y"];

               $this->DataSet->Data["GraphArea"]["Y2"] = $MaxBottom + $this->FontSize;
              }

             $AxisPos["B"] = $MaxBottom + $ScaleSpacing;
            }
           elseif ( $Parameters["Position"] == AXIS_POSITION_TOP )
            {
             if ( $LabelRotation == 0 )					{ $LabelAlign = TEXT_ALIGN_BOTTOMMIDDLE; $YLabelOffset = 2; }
             if ( $LabelRotation > 0 && $LabelRotation < 190 )		{ $LabelAlign = TEXT_ALIGN_MIDDLELEFT; $YLabelOffset = 2; }
             if ( $LabelRotation == 180 )				{ $LabelAlign = TEXT_ALIGN_TOPMIDDLE; $YLabelOffset = 5; }
             if ( $LabelRotation > 180 && $LabelRotation < 360 )	{ $LabelAlign = TEXT_ALIGN_MIDDLERIGHT; $YLabelOffset = 5; }

             if ( !$RemoveXAxis )
              {
               if ( $Floating )
                { $FloatingOffset = $YMargin; $this->drawLine($this->GraphAreaX1+$Parameters["Margin"],$AxisPos["T"],$this->GraphAreaX2-$Parameters["Margin"],$AxisPos["T"],array("R"=>$AxisR,"G"=>$AxisG,"B"=>$AxisB,"Alpha"=>$AxisAlpha)); }
               else
                { $FloatingOffset = 0; $this->drawLine($this->GraphAreaX1,$AxisPos["T"],$this->GraphAreaX2,$AxisPos["T"],array("R"=>$AxisR,"G"=>$AxisG,"B"=>$AxisB,"Alpha"=>$AxisAlpha)); }

               if ( $DrawArrows ) { $this->drawArrow($this->GraphAreaX2-$Parameters["Margin"],$AxisPos["T"],$this->GraphAreaX2+($ArrowSize*2),$AxisPos["T"],array("FillR"=>$AxisR,"FillG"=>$AxisG,"FillB"=>$AxisB,"Size"=>$ArrowSize)); }
              }

             $Width = ($this->GraphAreaX2 - $this->GraphAreaX1) - $Parameters["Margin"]*2;

             if ($Parameters["Rows"] == 0 ) { $Step  = $Width; } else { $Step  = $Width / $Parameters["Rows"]; }

             $MinTop = $AxisPos["T"];
             for($i=0;$i<=$Parameters["Rows"];$i++)
              {
               $XPos  = $this->GraphAreaX1 + $Parameters["Margin"] + $Step*$i;
               $YPos  = $AxisPos["T"];

               if ( $Abscissa != null )
                { if ( isset($Data["Series"][$Abscissa]["Data"][$i]) ) { $Value = $this->scaleFormat($Data["Series"][$Abscissa]["Data"][$i],$Data["XAxisDisplay"],$Data["XAxisFormat"],$Data["XAxisUnit"]); } else { $Value = ""; } }
               else
                {
                 if ( isset($Parameters["ScaleMin"]) && isset ($Parameters["RowHeight"]) )
                  $Value = $this->scaleFormat($Parameters["ScaleMin"] + $Parameters["RowHeight"]*$i,$Data["XAxisDisplay"],$Data["XAxisFormat"],$Data["XAxisUnit"]);
                 else
                  $Value = $i;
                }

               $ID++; $Skipped = true;
               if ( $this->isValidLabel($Value,$LastValue,$LabelingMethod,$ID,$LabelSkip) && !$RemoveXAxis)
                {
                 $Bounds = $this->drawText($XPos,$YPos-$OuterTickWidth-$YLabelOffset,$Value,array("Angle"=>$LabelRotation,"Align"=>$LabelAlign));
                 $TxtBox = $YPos-$OuterTickWidth-2-($Bounds[0]["Y"]-$Bounds[2]["Y"]);
                 $MinTop = min($MinTop,$TxtBox);
                 $LastValue = $Value;
                 $Skipped   = false;
                }

               if ( $RemoveXAxis ) { $Skipped   = false; }

               if ( $Skipped )
                {
                 if ( $DrawXLines ) { $this->drawLine($XPos,$this->GraphAreaY1+$FloatingOffset,$XPos,$this->GraphAreaY2-$FloatingOffset,$SkippedAxisColor); }
                 if ( ($SkippedInnerTickWidth !=0 || $SkippedOuterTickWidth != 0) && !$RemoveXAxis ) { $this->drawLine($XPos,$YPos+$SkippedInnerTickWidth,$XPos,$YPos-$SkippedOuterTickWidth,$SkippedTickColor); }
                }
               else
                {
                 if ( $DrawXLines ) { $this->drawLine($XPos,$this->GraphAreaY1+$FloatingOffset,$XPos,$this->GraphAreaY2-$FloatingOffset,array("R"=>$GridR,"G"=>$GridG,"B"=>$GridB,"Alpha"=>$GridAlpha,"Ticks"=>$GridTicks)); }
                 if ( ($InnerTickWidth !=0 || $OuterTickWidth != 0) && !$RemoveXAxis ) { $this->drawLine($XPos,$YPos+$InnerTickWidth,$XPos,$YPos-$OuterTickWidth,array("R"=>$TickR,"G"=>$TickG,"B"=>$TickB,"Alpha"=>$TickAlpha)); }
                }

              }

             if ( isset($Parameters["Name"]) && !$RemoveXAxis )
              {
               $YPos   = $MinTop-2;
               $XPos   = $this->GraphAreaX1+($this->GraphAreaX2-$this->GraphAreaX1)/2;
               $Bounds = $this->drawText($XPos,$YPos,$Parameters["Name"],array("Align"=>TEXT_ALIGN_BOTTOMMIDDLE));
               $MinTop = $Bounds[2]["Y"];

               $this->DataSet->Data["GraphArea"]["Y1"] = $MinTop;
              }

             $AxisPos["T"] = $MinTop - $ScaleSpacing;
            }
          }
         elseif ( $Pos == SCALE_POS_TOPBOTTOM )
          {
           if ( $Parameters["Position"] == AXIS_POSITION_LEFT )
            {
             if ( $LabelRotation == 0 )					{ $LabelAlign = TEXT_ALIGN_MIDDLERIGHT; $XLabelOffset = -2; }
             if ( $LabelRotation > 0 && $LabelRotation < 190 )		{ $LabelAlign = TEXT_ALIGN_MIDDLERIGHT; $XLabelOffset = -6; }
             if ( $LabelRotation == 180 )				{ $LabelAlign = TEXT_ALIGN_MIDDLELEFT; $XLabelOffset = -2; }
             if ( $LabelRotation > 180 && $LabelRotation < 360 )	{ $LabelAlign = TEXT_ALIGN_MIDDLELEFT; $XLabelOffset = -5; }

             if ( !$RemoveXAxis )
              {
               if ( $Floating )
                { $FloatingOffset = $YMargin; $this->drawLine($AxisPos["L"],$this->GraphAreaY1+$Parameters["Margin"],$AxisPos["L"],$this->GraphAreaY2-$Parameters["Margin"],array("R"=>$AxisR,"G"=>$AxisG,"B"=>$AxisB,"Alpha"=>$AxisAlpha)); }
               else
                { $FloatingOffset = 0; $this->drawLine($AxisPos["L"],$this->GraphAreaY1,$AxisPos["L"],$this->GraphAreaY2,array("R"=>$AxisR,"G"=>$AxisG,"B"=>$AxisB,"Alpha"=>$AxisAlpha)); }

               if ( $DrawArrows ) { $this->drawArrow($AxisPos["L"],$this->GraphAreaY2-$Parameters["Margin"],$AxisPos["L"],$this->GraphAreaY2+($ArrowSize*2),array("FillR"=>$AxisR,"FillG"=>$AxisG,"FillB"=>$AxisB,"Size"=>$ArrowSize)); }
              }

             $Height = ($this->GraphAreaY2 - $this->GraphAreaY1) - $Parameters["Margin"]*2;

             if ($Parameters["Rows"] == 0 ) { $Step  = $Height; } else { $Step   = $Height / $Parameters["Rows"]; }

             $MinLeft = $AxisPos["L"];
             for($i=0;$i<=$Parameters["Rows"];$i++)
              {
               $YPos  = $this->GraphAreaY1 + $Parameters["Margin"] + $Step*$i;
               $XPos  = $AxisPos["L"];

               if ( $Abscissa != null )
                { if ( isset($Data["Series"][$Abscissa]["Data"][$i]) ) { $Value = $this->scaleFormat($Data["Series"][$Abscissa]["Data"][$i],$Data["XAxisDisplay"],$Data["XAxisFormat"],$Data["XAxisUnit"]); } else { $Value = ""; } }
               else
                {
                 if ( isset($Parameters["ScaleMin"]) && isset ($Parameters["RowHeight"]) )
                  $Value = $this->scaleFormat($Parameters["ScaleMin"] + $Parameters["RowHeight"]*$i,$Data["XAxisDisplay"],$Data["XAxisFormat"],$Data["XAxisUnit"]);
                 else
                  $Value = $i;
                }

               $ID++; $Skipped = true;
               if ( $this->isValidLabel($Value,$LastValue,$LabelingMethod,$ID,$LabelSkip) && !$RemoveXAxis)
                {
                 $Bounds  = $this->drawText($XPos-$OuterTickWidth+$XLabelOffset,$YPos,$Value,array("Angle"=>$LabelRotation,"Align"=>$LabelAlign));
                 $TxtBox  = $XPos-$OuterTickWidth-2-($Bounds[1]["X"]-$Bounds[0]["X"]);
                 $MinLeft = min($MinLeft,$TxtBox);
                 $LastValue = $Value;
                 $Skipped   = false;
                }

               if ( $RemoveXAxis ) { $Skipped   = false; }

               if ( $Skipped )
                {
                 if ( $DrawXLines ) { $this->drawLine($this->GraphAreaX1+$FloatingOffset,$YPos,$this->GraphAreaX2-$FloatingOffset,$YPos,$SkippedAxisColor); }
                 if ( ($SkippedInnerTickWidth !=0 || $SkippedOuterTickWidth != 0) && !$RemoveXAxis ) { $this->drawLine($XPos-$SkippedOuterTickWidth,$YPos,$XPos+$SkippedInnerTickWidth,$YPos,$SkippedTickColor); }
                }
               else
                {
                 if ( $DrawXLines && ($YPos != $this->GraphAreaY1 && $YPos != $this->GraphAreaY2) ) { $this->drawLine($this->GraphAreaX1+$FloatingOffset,$YPos,$this->GraphAreaX2-$FloatingOffset,$YPos,array("R"=>$GridR,"G"=>$GridG,"B"=>$GridB,"Alpha"=>$GridAlpha,"Ticks"=>$GridTicks)); }
                 if ( ($InnerTickWidth !=0 || $OuterTickWidth != 0) && !$RemoveXAxis ) { $this->drawLine($XPos-$OuterTickWidth,$YPos,$XPos+$InnerTickWidth,$YPos,array("R"=>$TickR,"G"=>$TickG,"B"=>$TickB,"Alpha"=>$TickAlpha)); }
                }

              }
             if ( isset($Parameters["Name"]) && !$RemoveXAxis )
              {
               $XPos   = $MinLeft-2;
               $YPos   = $this->GraphAreaY1+($this->GraphAreaY2-$this->GraphAreaY1)/2;
               $Bounds = $this->drawText($XPos,$YPos,$Parameters["Name"],array("Align"=>TEXT_ALIGN_BOTTOMMIDDLE,"Angle"=>90));
               $MinLeft = $Bounds[0]["X"];

               $this->DataSet->Data["GraphArea"]["X1"] = $MinLeft;
              }

             $AxisPos["L"] = $MinLeft - $ScaleSpacing;
            }
           elseif ( $Parameters["Position"] == AXIS_POSITION_RIGHT )
            {
             if ( $LabelRotation == 0 )					{ $LabelAlign = TEXT_ALIGN_MIDDLELEFT; $XLabelOffset = 2; }
             if ( $LabelRotation > 0 && $LabelRotation < 190 )		{ $LabelAlign = TEXT_ALIGN_MIDDLELEFT; $XLabelOffset = 6; }
             if ( $LabelRotation == 180 )				{ $LabelAlign = TEXT_ALIGN_MIDDLERIGHT; $XLabelOffset = 5; }
             if ( $LabelRotation > 180 && $LabelRotation < 360 )	{ $LabelAlign = TEXT_ALIGN_MIDDLERIGHT; $XLabelOffset = 7; }

             if ( !$RemoveXAxis )
              {
               if ( $Floating )
                { $FloatingOffset = $YMargin; $this->drawLine($AxisPos["R"],$this->GraphAreaY1+$Parameters["Margin"],$AxisPos["R"],$this->GraphAreaY2-$Parameters["Margin"],array("R"=>$AxisR,"G"=>$AxisG,"B"=>$AxisB,"Alpha"=>$AxisAlpha)); }
               else
                { $FloatingOffset = 0; $this->drawLine($AxisPos["R"],$this->GraphAreaY1,$AxisPos["R"],$this->GraphAreaY2,array("R"=>$AxisR,"G"=>$AxisG,"B"=>$AxisB,"Alpha"=>$AxisAlpha)); }

               if ( $DrawArrows ) { $this->drawArrow($AxisPos["R"],$this->GraphAreaY2-$Parameters["Margin"],$AxisPos["R"],$this->GraphAreaY2+($ArrowSize*2),array("FillR"=>$AxisR,"FillG"=>$AxisG,"FillB"=>$AxisB,"Size"=>$ArrowSize)); }
              }

             $Height = ($this->GraphAreaY2 - $this->GraphAreaY1) - $Parameters["Margin"]*2;

             if ($Parameters["Rows"] == 0 ) { $Step  = $Height; } else { $Step   = $Height / $Parameters["Rows"]; }

             $MaxRight = $AxisPos["R"];
             for($i=0;$i<=$Parameters["Rows"];$i++)
              {
               $YPos  = $this->GraphAreaY1 + $Parameters["Margin"] + $Step*$i;
               $XPos  = $AxisPos["R"];

               if ( $Abscissa != null )
                { if ( isset($Data["Series"][$Abscissa]["Data"][$i]) ) { $Value = $this->scaleFormat($Data["Series"][$Abscissa]["Data"][$i],$Data["XAxisDisplay"],$Data["XAxisFormat"],$Data["XAxisUnit"]); } else { $Value = ""; } }
               else
                {
                 if ( isset($Parameters["ScaleMin"]) && isset ($Parameters["RowHeight"]) )
                  $Value = $this->scaleFormat($Parameters["ScaleMin"] + $Parameters["RowHeight"]*$i,$Data["XAxisDisplay"],$Data["XAxisFormat"],$Data["XAxisUnit"]);
                 else
                  $Value = $i;
                }

               $ID++; $Skipped = true;
               if ( $this->isValidLabel($Value,$LastValue,$LabelingMethod,$ID,$LabelSkip) && !$RemoveXAxis)
                {
                 $Bounds   = $this->drawText($XPos+$OuterTickWidth+$XLabelOffset,$YPos,$Value,array("Angle"=>$LabelRotation,"Align"=>$LabelAlign));
                 $TxtBox   = $XPos+$OuterTickWidth+2+($Bounds[1]["X"]-$Bounds[0]["X"]);
                 $MaxRight = max($MaxRight,$TxtBox);
                 $LastValue = $Value;
                 $Skipped   = false;
                }

               if ( $RemoveXAxis ) { $Skipped   = false; }

               if ( $Skipped )
                {
                 if ( $DrawXLines ) { $this->drawLine($this->GraphAreaX1+$FloatingOffset,$YPos,$this->GraphAreaX2-$FloatingOffset,$YPos,$SkippedAxisColor); }
                 if ( ($SkippedInnerTickWidth != 0 || $SkippedOuterTickWidth != 0) && !$RemoveXAxis ) { $this->drawLine($XPos+$SkippedOuterTickWidth,$YPos,$XPos-$SkippedInnerTickWidth,$YPos,$SkippedTickColor); }
                }
               else
                {
                 if ( $DrawXLines ) { $this->drawLine($this->GraphAreaX1+$FloatingOffset,$YPos,$this->GraphAreaX2-$FloatingOffset,$YPos,array("R"=>$GridR,"G"=>$GridG,"B"=>$GridB,"Alpha"=>$GridAlpha,"Ticks"=>$GridTicks)); }
                 if ( ($InnerTickWidth != 0 || $OuterTickWidth != 0) && !$RemoveXAxis ) { $this->drawLine($XPos+$OuterTickWidth,$YPos,$XPos-$InnerTickWidth,$YPos,array("R"=>$TickR,"G"=>$TickG,"B"=>$TickB,"Alpha"=>$TickAlpha)); }
                }

              }

             if ( isset($Parameters["Name"]) && !$RemoveXAxis)
              {
               $XPos   = $MaxRight+4;
               $YPos   = $this->GraphAreaY1+($this->GraphAreaY2-$this->GraphAreaY1)/2;
               $Bounds = $this->drawText($XPos,$YPos,$Parameters["Name"],array("Align"=>TEXT_ALIGN_BOTTOMMIDDLE,"Angle"=>270));
               $MaxRight = $Bounds[1]["X"];

               $this->DataSet->Data["GraphArea"]["X2"] = $MaxRight + $this->FontSize;
              }

             $AxisPos["R"] = $MaxRight + $ScaleSpacing;
            }
          }
        }



       if ( $Parameters["Identity"] == AXIS_Y )
        {
         if ( $Pos == SCALE_POS_LEFTRIGHT )
          {
           if ( $Parameters["Position"] == AXIS_POSITION_LEFT )
            {

             if ( $Floating )
              { $FloatingOffset = $XMargin; $this->drawLine($AxisPos["L"],$this->GraphAreaY1+$Parameters["Margin"],$AxisPos["L"],$this->GraphAreaY2-$Parameters["Margin"],array("R"=>$AxisR,"G"=>$AxisG,"B"=>$AxisB,"Alpha"=>$AxisAlpha)); }
             else
              { $FloatingOffset = 0; $this->drawLine($AxisPos["L"],$this->GraphAreaY1,$AxisPos["L"],$this->GraphAreaY2,array("R"=>$AxisR,"G"=>$AxisG,"B"=>$AxisB,"Alpha"=>$AxisAlpha)); }

             if ( $DrawArrows ) { $this->drawArrow($AxisPos["L"],$this->GraphAreaY1+$Parameters["Margin"],$AxisPos["L"],$this->GraphAreaY1-($ArrowSize*2),array("FillR"=>$AxisR,"FillG"=>$AxisG,"FillB"=>$AxisB,"Size"=>$ArrowSize)); }

             $Height = ($this->GraphAreaY2 - $this->GraphAreaY1) - $Parameters["Margin"]*2;
             $Step   = $Height / $Parameters["Rows"]; $SubTicksSize = $Step /2; $MinLeft = $AxisPos["L"];
             $LastY  = null;
             for($i=0;$i<=$Parameters["Rows"];$i++)
              {
               $YPos  = $this->GraphAreaY2 - $Parameters["Margin"] - $Step*$i;
               $XPos  = $AxisPos["L"];
               $Value = $this->scaleFormat($Parameters["ScaleMin"] + $Parameters["RowHeight"]*$i,$Parameters["Display"],$Parameters["Format"],$Parameters["Unit"]);

               if ( $i%2 == 1 ) { $BGColor = array("R"=>$BackgroundR1,"G"=>$BackgroundG1,"B"=>$BackgroundB1,"Alpha"=>$BackgroundAlpha1); } else { $BGColor = array("R"=>$BackgroundR2,"G"=>$BackgroundG2,"B"=>$BackgroundB2,"Alpha"=>$BackgroundAlpha2); }
               if ( $LastY != null && $CycleBackground && ( $DrawYLines == ALL || in_array($AxisID,$DrawYLines) )) { $this->drawFilledRectangle($this->GraphAreaX1+$FloatingOffset,$LastY,$this->GraphAreaX2-$FloatingOffset,$YPos,$BGColor); }

               if ( $DrawYLines == ALL || in_array($AxisID,$DrawYLines) ) { $this->drawLine($this->GraphAreaX1+$FloatingOffset,$YPos,$this->GraphAreaX2-$FloatingOffset,$YPos,array("R"=>$GridR,"G"=>$GridG,"B"=>$GridB,"Alpha"=>$GridAlpha,"Ticks"=>$GridTicks)); }

               if ( $DrawSubTicks && $i != $Parameters["Rows"] )
                $this->drawLine($XPos-$OuterSubTickWidth,$YPos-$SubTicksSize,$XPos+$InnerSubTickWidth,$YPos-$SubTicksSize,array("R"=>$SubTickR,"G"=>$SubTickG,"B"=>$SubTickB,"Alpha"=>$SubTickAlpha));

               $this->drawLine($XPos-$OuterTickWidth,$YPos,$XPos+$InnerTickWidth,$YPos,array("R"=>$TickR,"G"=>$TickG,"B"=>$TickB,"Alpha"=>$TickAlpha));
               $Bounds  = $this->drawText($XPos-$OuterTickWidth-2,$YPos,$Value,array("Align"=>TEXT_ALIGN_MIDDLERIGHT));
               $TxtLeft = $XPos-$OuterTickWidth-2-($Bounds[1]["X"]-$Bounds[0]["X"]);
               $MinLeft = min($MinLeft,$TxtLeft);

               $LastY = $YPos;
              }

             if ( isset($Parameters["Name"]) )
              {
               $XPos    = $MinLeft-2;
               $YPos    = $this->GraphAreaY1+($this->GraphAreaY2-$this->GraphAreaY1)/2;
               $Bounds  = $this->drawText($XPos,$YPos,$Parameters["Name"],array("Align"=>TEXT_ALIGN_BOTTOMMIDDLE,"Angle"=>90));
               $MinLeft = $Bounds[2]["X"];

               $this->DataSet->Data["GraphArea"]["X1"] = $MinLeft;
              }

             $AxisPos["L"] = $MinLeft - $ScaleSpacing;
            }
           elseif ( $Parameters["Position"] == AXIS_POSITION_RIGHT )
            {
             if ( $Floating )
              { $FloatingOffset = $XMargin; $this->drawLine($AxisPos["R"],$this->GraphAreaY1+$Parameters["Margin"],$AxisPos["R"],$this->GraphAreaY2-$Parameters["Margin"],array("R"=>$AxisR,"G"=>$AxisG,"B"=>$AxisB,"Alpha"=>$AxisAlpha)); }
             else
              { $FloatingOffset = 0; $this->drawLine($AxisPos["R"],$this->GraphAreaY1,$AxisPos["R"],$this->GraphAreaY2,array("R"=>$AxisR,"G"=>$AxisG,"B"=>$AxisB,"Alpha"=>$AxisAlpha)); }

             if ( $DrawArrows ) { $this->drawArrow($AxisPos["R"],$this->GraphAreaY1+$Parameters["Margin"],$AxisPos["R"],$this->GraphAreaY1-($ArrowSize*2),array("FillR"=>$AxisR,"FillG"=>$AxisG,"FillB"=>$AxisB,"Size"=>$ArrowSize)); }

             $Height = ($this->GraphAreaY2 - $this->GraphAreaY1) - $Parameters["Margin"]*2;
             $Step   = $Height / $Parameters["Rows"]; $SubTicksSize = $Step /2; $MaxLeft = $AxisPos["R"];
             $LastY  = null;
             for($i=0;$i<=$Parameters["Rows"];$i++)
              {
               $YPos  = $this->GraphAreaY2 - $Parameters["Margin"] - $Step*$i;
               $XPos  = $AxisPos["R"];
               $Value = $this->scaleFormat($Parameters["ScaleMin"] + $Parameters["RowHeight"]*$i,$Parameters["Display"],$Parameters["Format"],$Parameters["Unit"]);

               if ( $i%2 == 1 ) { $BGColor = array("R"=>$BackgroundR1,"G"=>$BackgroundG1,"B"=>$BackgroundB1,"Alpha"=>$BackgroundAlpha1); } else { $BGColor = array("R"=>$BackgroundR2,"G"=>$BackgroundG2,"B"=>$BackgroundB2,"Alpha"=>$BackgroundAlpha2); }
               if ( $LastY != null && $CycleBackground  && ( $DrawYLines == ALL || in_array($AxisID,$DrawYLines) )) { $this->drawFilledRectangle($this->GraphAreaX1+$FloatingOffset,$LastY,$this->GraphAreaX2-$FloatingOffset,$YPos,$BGColor); }

               if ( $DrawYLines == ALL || in_array($AxisID,$DrawYLines) ) { $this->drawLine($this->GraphAreaX1+$FloatingOffset,$YPos,$this->GraphAreaX2-$FloatingOffset,$YPos,array("R"=>$GridR,"G"=>$GridG,"B"=>$GridB,"Alpha"=>$GridAlpha,"Ticks"=>$GridTicks)); }

               if ( $DrawSubTicks && $i != $Parameters["Rows"] )
                $this->drawLine($XPos-$OuterSubTickWidth,$YPos-$SubTicksSize,$XPos+$InnerSubTickWidth,$YPos-$SubTicksSize,array("R"=>$SubTickR,"G"=>$SubTickG,"B"=>$SubTickB,"Alpha"=>$SubTickAlpha));

               $this->drawLine($XPos-$InnerTickWidth,$YPos,$XPos+$OuterTickWidth,$YPos,array("R"=>$TickR,"G"=>$TickG,"B"=>$TickB,"Alpha"=>$TickAlpha));
               $Bounds  = $this->drawText($XPos+$OuterTickWidth+2,$YPos,$Value,array("Align"=>TEXT_ALIGN_MIDDLELEFT));
               $TxtLeft = $XPos+$OuterTickWidth+2+($Bounds[1]["X"]-$Bounds[0]["X"]);
               $MaxLeft = max($MaxLeft,$TxtLeft);

               $LastY = $YPos;
              }

             if ( isset($Parameters["Name"]) )
              {
               $XPos    = $MaxLeft+6;
               $YPos    = $this->GraphAreaY1+($this->GraphAreaY2-$this->GraphAreaY1)/2;
               $Bounds  = $this->drawText($XPos,$YPos,$Parameters["Name"],array("Align"=>TEXT_ALIGN_BOTTOMMIDDLE,"Angle"=>270));
               $MaxLeft = $Bounds[2]["X"];

               $this->DataSet->Data["GraphArea"]["X2"] = $MaxLeft + $this->FontSize;
              }
             $AxisPos["R"] = $MaxLeft + $ScaleSpacing;
            }
          }
         elseif ( $Pos == SCALE_POS_TOPBOTTOM )
          {
           if ( $Parameters["Position"] == AXIS_POSITION_TOP )
            {
             if ( $Floating )
              { $FloatingOffset = $XMargin; $this->drawLine($this->GraphAreaX1+$Parameters["Margin"],$AxisPos["T"],$this->GraphAreaX2-$Parameters["Margin"],$AxisPos["T"],array("R"=>$AxisR,"G"=>$AxisG,"B"=>$AxisB,"Alpha"=>$AxisAlpha)); }
             else
              { $FloatingOffset = 0; $this->drawLine($this->GraphAreaX1,$AxisPos["T"],$this->GraphAreaX2,$AxisPos["T"],array("R"=>$AxisR,"G"=>$AxisG,"B"=>$AxisB,"Alpha"=>$AxisAlpha)); }

             if ( $DrawArrows ) { $this->drawArrow($this->GraphAreaX2-$Parameters["Margin"],$AxisPos["T"],$this->GraphAreaX2+($ArrowSize*2),$AxisPos["T"],array("FillR"=>$AxisR,"FillG"=>$AxisG,"FillB"=>$AxisB,"Size"=>$ArrowSize)); }

             $Width = ($this->GraphAreaX2 - $this->GraphAreaX1) - $Parameters["Margin"]*2;
             $Step   = $Width / $Parameters["Rows"]; $SubTicksSize = $Step /2; $MinTop = $AxisPos["T"];
             $LastX  = null;
             for($i=0;$i<=$Parameters["Rows"];$i++)
              {
               $XPos  = $this->GraphAreaX1 + $Parameters["Margin"] + $Step*$i;
               $YPos  = $AxisPos["T"];
               $Value = $this->scaleFormat($Parameters["ScaleMin"] + $Parameters["RowHeight"]*$i,$Parameters["Display"],$Parameters["Format"],$Parameters["Unit"]);

               if ( $i%2 == 1 ) { $BGColor = array("R"=>$BackgroundR1,"G"=>$BackgroundG1,"B"=>$BackgroundB1,"Alpha"=>$BackgroundAlpha1); } else { $BGColor = array("R"=>$BackgroundR2,"G"=>$BackgroundG2,"B"=>$BackgroundB2,"Alpha"=>$BackgroundAlpha2); }
               if ( $LastX != null && $CycleBackground  && ( $DrawYLines == ALL || in_array($AxisID,$DrawYLines) )) { $this->drawFilledRectangle($LastX,$this->GraphAreaY1+$FloatingOffset,$XPos,$this->GraphAreaY2-$FloatingOffset,$BGColor); }

               if ( $DrawYLines == ALL || in_array($AxisID,$DrawYLines) ) { $this->drawLine($XPos,$this->GraphAreaY1+$FloatingOffset,$XPos,$this->GraphAreaY2-$FloatingOffset,array("R"=>$GridR,"G"=>$GridG,"B"=>$GridB,"Alpha"=>$GridAlpha,"Ticks"=>$GridTicks)); }

               if ( $DrawSubTicks && $i != $Parameters["Rows"] )
                $this->drawLine($XPos+$SubTicksSize,$YPos-$OuterSubTickWidth,$XPos+$SubTicksSize,$YPos+$InnerSubTickWidth,array("R"=>$SubTickR,"G"=>$SubTickG,"B"=>$SubTickB,"Alpha"=>$SubTickAlpha));

               $this->drawLine($XPos,$YPos-$OuterTickWidth,$XPos,$YPos+$InnerTickWidth,array("R"=>$TickR,"G"=>$TickG,"B"=>$TickB,"Alpha"=>$TickAlpha));
               $Bounds    = $this->drawText($XPos,$YPos-$OuterTickWidth-2,$Value,array("Align"=>TEXT_ALIGN_BOTTOMMIDDLE));
               $TxtHeight = $YPos-$OuterTickWidth-2-($Bounds[1]["Y"]-$Bounds[2]["Y"]);
               $MinTop    = min($MinTop,$TxtHeight);

               $LastX = $XPos;
              }

             if ( isset($Parameters["Name"]) )
              {
               $YPos   = $MinTop-2;
               $XPos   = $this->GraphAreaX1+($this->GraphAreaX2-$this->GraphAreaX1)/2;
               $Bounds = $this->drawText($XPos,$YPos,$Parameters["Name"],array("Align"=>TEXT_ALIGN_BOTTOMMIDDLE));
               $MinTop = $Bounds[2]["Y"];

               $this->DataSet->Data["GraphArea"]["Y1"] = $MinTop;
              }

             $AxisPos["T"] = $MinTop - $ScaleSpacing;
            }
           elseif ( $Parameters["Position"] == AXIS_POSITION_BOTTOM )
            {
             if ( $Floating )
              { $FloatingOffset = $XMargin; $this->drawLine($this->GraphAreaX1+$Parameters["Margin"],$AxisPos["B"],$this->GraphAreaX2-$Parameters["Margin"],$AxisPos["B"],array("R"=>$AxisR,"G"=>$AxisG,"B"=>$AxisB,"Alpha"=>$AxisAlpha)); }
             else
              { $FloatingOffset = 0; $this->drawLine($this->GraphAreaX1,$AxisPos["B"],$this->GraphAreaX2,$AxisPos["B"],array("R"=>$AxisR,"G"=>$AxisG,"B"=>$AxisB,"Alpha"=>$AxisAlpha)); }

             if ( $DrawArrows ) { $this->drawArrow($this->GraphAreaX2-$Parameters["Margin"],$AxisPos["B"],$this->GraphAreaX2+($ArrowSize*2),$AxisPos["B"],array("FillR"=>$AxisR,"FillG"=>$AxisG,"FillB"=>$AxisB,"Size"=>$ArrowSize)); }

             $Width = ($this->GraphAreaX2 - $this->GraphAreaX1) - $Parameters["Margin"]*2;
             $Step   = $Width / $Parameters["Rows"]; $SubTicksSize = $Step /2; $MaxBottom = $AxisPos["B"];
             $LastX  = null;
             for($i=0;$i<=$Parameters["Rows"];$i++)
              {
               $XPos  = $this->GraphAreaX1 + $Parameters["Margin"] + $Step*$i;
               $YPos  = $AxisPos["B"];
               $Value = $this->scaleFormat($Parameters["ScaleMin"] + $Parameters["RowHeight"]*$i,$Parameters["Display"],$Parameters["Format"],$Parameters["Unit"]);

               if ( $i%2 == 1 ) { $BGColor = array("R"=>$BackgroundR1,"G"=>$BackgroundG1,"B"=>$BackgroundB1,"Alpha"=>$BackgroundAlpha1); } else { $BGColor = array("R"=>$BackgroundR2,"G"=>$BackgroundG2,"B"=>$BackgroundB2,"Alpha"=>$BackgroundAlpha2); }
               if ( $LastX != null && $CycleBackground  && ( $DrawYLines == ALL || in_array($AxisID,$DrawYLines) )) { $this->drawFilledRectangle($LastX,$this->GraphAreaY1+$FloatingOffset,$XPos,$this->GraphAreaY2-$FloatingOffset,$BGColor); }

               if ( $DrawYLines == ALL || in_array($AxisID,$DrawYLines) ) { $this->drawLine($XPos,$this->GraphAreaY1+$FloatingOffset,$XPos,$this->GraphAreaY2-$FloatingOffset,array("R"=>$GridR,"G"=>$GridG,"B"=>$GridB,"Alpha"=>$GridAlpha,"Ticks"=>$GridTicks)); }

               if ( $DrawSubTicks && $i != $Parameters["Rows"] )
                $this->drawLine($XPos+$SubTicksSize,$YPos-$OuterSubTickWidth,$XPos+$SubTicksSize,$YPos+$InnerSubTickWidth,array("R"=>$SubTickR,"G"=>$SubTickG,"B"=>$SubTickB,"Alpha"=>$SubTickAlpha));

               $this->drawLine($XPos,$YPos-$OuterTickWidth,$XPos,$YPos+$InnerTickWidth,array("R"=>$TickR,"G"=>$TickG,"B"=>$TickB,"Alpha"=>$TickAlpha));
               $Bounds    = $this->drawText($XPos,$YPos+$OuterTickWidth+2,$Value,array("Align"=>TEXT_ALIGN_TOPMIDDLE));
               $TxtHeight = $YPos+$OuterTickWidth+2+($Bounds[1]["Y"]-$Bounds[2]["Y"]);
               $MaxBottom = max($MaxBottom,$TxtHeight);

               $LastX = $XPos;
              }

             if ( isset($Parameters["Name"]) )
              {
               $YPos   = $MaxBottom+2;
               $XPos   = $this->GraphAreaX1+($this->GraphAreaX2-$this->GraphAreaX1)/2;
               $Bounds = $this->drawText($XPos,$YPos,$Parameters["Name"],array("Align"=>TEXT_ALIGN_TOPMIDDLE));
               $MaxBottom = $Bounds[0]["Y"];

               $this->DataSet->Data["GraphArea"]["Y2"] = $MaxBottom + $this->FontSize;
              }

             $AxisPos["B"] = $MaxBottom + $ScaleSpacing;
            }
          }
        }
      }
    }

    function isValidLabel($Value,$LastValue,$LabelingMethod,$ID,$LabelSkip)
    {
     if ( $LabelingMethod == LABELING_DIFFERENT && $Value != $LastValue ) { return(true); }
     if ( $LabelingMethod == LABELING_DIFFERENT && $Value == $LastValue ) { return(false); }
     if ( $LabelingMethod == LABELING_ALL && $LabelSkip == 0 ) { return(true); }
     if ( $LabelingMethod == LABELING_ALL && ($ID+$LabelSkip) % ($LabelSkip+1) != 1 ) { return(false); }

     return true;
    }

    /* Compute the scale, check for the best visual factors */
    function computeScale($XMin,$XMax,$MaxDivs,$Factors,$AxisID=0)
    {
     /* Compute each factors */
     $results = [];
     foreach ($Factors as $Key => $Factor){
         $results[$Factor] = $this->processScale($XMin,$XMax,$MaxDivs,array($Factor),$AxisID);
     }

     /* Remove scales that are creating to much decimals */
     $GoodScaleFactors = [];
     foreach ($results as $Key => $Result)
      {
       $Decimals = preg_split("/\./",$Result["RowHeight"]);
       if ( (!isset($Decimals[1])) || (strlen($Decimals[1]) < 6) ) { $GoodScaleFactors[] = $Key; }
      }

     /* Found no correct scale, shame,... returns the 1st one as default */
     if ( empty($GoodScaleFactors)) { return($results[$Factors[0]]); }

     /* Find the factor that cause the maximum number of Rows */
     $MaxRows = 0; $BestFactor = 0;
     foreach($GoodScaleFactors as $Key => $Factor)
      { if ( $results[$Factor]["Rows"] > $MaxRows ) { $MaxRows = $results[$Factor]["Rows"]; $BestFactor = $Factor; } }

     /* Return the best visual scale */
     return $results[$BestFactor];
    }

    /* Compute the best matching scale based on size & factors */
    function processScale($XMin,$XMax,$MaxDivs,$Factors,$AxisID)
    {
     $ScaleHeight = abs(ceil($XMax)-floor($XMin));

     if ( isset($this->DataSet->Data["Axis"][$AxisID]["Format"]) )
      $format = $this->DataSet->Data["Axis"][$AxisID]["Format"];
     else
      $format = null;

     if ( isset($this->DataSet->Data["Axis"][$AxisID]["Display"]) )
      $Mode = $this->DataSet->Data["Axis"][$AxisID]["Display"];
     else
      $Mode = AXIS_FORMAT_DEFAULT;

     $Scale = [];
     if ( $XMin != $XMax )
      {
       $Found = false; $Rescaled = false; $Scaled10Factor = .0001; $Result = 0;
       while(!$Found)
        {
         foreach($Factors as $Key => $Factor)
          {
           if ( !$Found )
            {
             if ( !($this->modulo($XMin,$Factor*$Scaled10Factor) == 0) || ($XMin != floor($XMin))) { $XMinRescaled = floor($XMin/($Factor*$Scaled10Factor))*$Factor*$Scaled10Factor; } else { $XMinRescaled = $XMin; }
             if ( !($this->modulo($XMax,$Factor*$Scaled10Factor) == 0) || ($XMax != floor($XMax))) { $XMaxRescaled = floor($XMax/($Factor*$Scaled10Factor))*$Factor*$Scaled10Factor+($Factor*$Scaled10Factor); } else { $XMaxRescaled = $XMax; }
             $ScaleHeightRescaled = abs($XMaxRescaled-$XMinRescaled);

             if ( !$Found && floor($ScaleHeightRescaled/($Factor*$Scaled10Factor)) <= $MaxDivs ) { $Found = true; $Rescaled = true; $Result = $Factor * $Scaled10Factor; }
            }
          }
         $Scaled10Factor = $Scaled10Factor * 10;
        }

       /* ReCall Min / Max / Height */
       if ( $Rescaled ) { $XMin = $XMinRescaled; $XMax = $XMaxRescaled; $ScaleHeight = $ScaleHeightRescaled; }

       /* Compute rows size */
       $Rows      = floor($ScaleHeight / $Result); if ( $Rows == 0 ) { $Rows = 1; }
       $RowHeight = $ScaleHeight / $Rows;

       /* Return the results */
       $Scale["Rows"] = $Rows; $Scale["RowHeight"] = $RowHeight; $Scale["XMin"] = $XMin;  $Scale["XMax"] = $XMax;

       /* Compute the needed decimals for the metric view to avoid repetition of the same X Axis labels */
       if ( $Mode == AXIS_FORMAT_METRIC && $format == null )
        {
         $Done = false; $GoodDecimals = 0;
         for($Decimals=0;$Decimals<=10;$Decimals++)
          {
           if ( !$Done )
            {
             $LastLabel = "zob"; $ScaleOK = true;
             for($i=0;$i<=$Rows;$i++)
              {
               $Value = $XMin + $i*$RowHeight;
               $Label = $this->scaleFormat($Value,AXIS_FORMAT_METRIC,$Decimals);

               if ( $LastLabel == $Label ) { $ScaleOK = false; }
               $LastLabel = $Label;
              }
             if ( $ScaleOK ) { $Done = true; $GoodDecimals = $Decimals; }
            }
          }

         $Scale["Format"] = $GoodDecimals;
        }
      }
     else
      {
       /* If all values are the same we keep a +1/-1 scale */
       $Rows = 2; $XMin = $XMax-1; $XMax = $XMax+1; $RowHeight = 1;

       /* Return the results */
       $Scale["Rows"] = $Rows; $Scale["RowHeight"] = $RowHeight; $Scale["XMin"] = $XMin;  $Scale["XMax"] = $XMax;
      }

     return $Scale;
    }

    function modulo($Value1,$Value2)
    {
     if (floor($Value2) == 0) { return 0; }
     if (floor($Value2) != 0) { return($Value1 % $Value2); }

     $MinValue = min($Value1,$Value2); $Factor = 10;
     while ( floor($MinValue*$Factor) == 0 )
      { $Factor = $Factor * 10; }

     return ($Value1*$Factor) % ($Value2*$Factor);
    }

    /* Draw an X threshold */
    function drawXThreshold($Value,$format="")
    {
     $R			= isset($format["R"]) ? $format["R"] : 255;
     $G			= isset($format["G"]) ? $format["G"] : 0;
     $B			= isset($format["B"]) ? $format["B"] : 0;
     $Alpha		= isset($format["Alpha"]) ? $format["Alpha"] : 50;
     $Weight		= isset($format["Weight"]) ? $format["Weight"] : null;
     $Ticks		= isset($format["Ticks"]) ? $format["Ticks"] : 6;
     $Wide		= isset($format["Wide"]) ? $format["Wide"] : false;
     $WideFactor	= isset($format["WideFactor"]) ? $format["WideFactor"] : 5;
     $WriteCaption	= isset($format["WriteCaption"]) ? $format["WriteCaption"] : false;
     $Caption		= isset($format["Caption"]) ? $format["Caption"] : null;
     $CaptionAlign	= isset($format["CaptionAlign"]) ? $format["CaptionAlign"] : CAPTION_LEFT_TOP;
     $CaptionOffset     = isset($format["CaptionOffset"]) ? $format["CaptionOffset"] : 5;
     $CaptionR		= isset($format["CaptionR"]) ? $format["CaptionR"] : 255;
     $CaptionG		= isset($format["CaptionG"]) ? $format["CaptionG"] : 255;
     $CaptionB		= isset($format["CaptionB"]) ? $format["CaptionB"] : 255;
     $CaptionAlpha	= isset($format["CaptionAlpha"]) ? $format["CaptionAlpha"] : 100;
     $DrawBox		= isset($format["DrawBox"]) ? $format["DrawBox"] : true;
     $DrawBoxBorder	= isset($format["DrawBoxBorder"]) ? $format["DrawBoxBorder"] : false;
     $BorderOffset	= isset($format["BorderOffset"]) ? $format["BorderOffset"] : 3;
     $BoxRounded	= isset($format["BoxRounded"]) ? $format["BoxRounded"] : true;
     $RoundedRadius	= isset($format["RoundedRadius"]) ? $format["RoundedRadius"] : 3;
     $BoxR		= isset($format["BoxR"]) ? $format["BoxR"] : 0;
     $BoxG		= isset($format["BoxG"]) ? $format["BoxG"] : 0;
     $BoxB		= isset($format["BoxB"]) ? $format["BoxB"] : 0;
     $BoxAlpha		= isset($format["BoxAlpha"]) ? $format["BoxAlpha"] : 30;
     $BoxSurrounding	= isset($format["BoxSurrounding"]) ? $format["BoxSurrounding"] : "";
     $BoxBorderR	= isset($format["BoxBorderR"]) ? $format["BoxBorderR"] : 255;
     $BoxBorderG	= isset($format["BoxBorderG"]) ? $format["BoxBorderG"] : 255;
     $BoxBorderB	= isset($format["BoxBorderB"]) ? $format["BoxBorderB"] : 255;
     $BoxBorderAlpha	= isset($format["BoxBorderAlpha"]) ? $format["BoxBorderAlpha"] : 100;
     $ValueIsLabel	= isset($format["ValueIsLabel"]) ? $format["ValueIsLabel"] : false;

     $Data           = $this->DataSet->getData();
     $AbscissaMargin = $this->getAbscissaMargin($Data);
     $XScale         = $this->scaleGetXSettings();

     if ( is_array($Value) ) { foreach ($Value as $Key => $ID) { $this->drawXThreshold($ID,$format); } return 0; }

     if ( $ValueIsLabel )
      {
       $format["ValueIsLabel"] = false;
       foreach($Data["Series"][$Data["Abscissa"]]["Data"] as $Key => $serieValue)
        { if ( $serieValue == $Value ) { $this->drawXThreshold($Key,$format); } }

       return 0;
      }

     $CaptionSettings = array("DrawBox"=>$DrawBox,"DrawBoxBorder"=>$DrawBoxBorder,"BorderOffset"=>$BorderOffset,"BoxRounded"=>$BoxRounded,"RoundedRadius"=>$RoundedRadius,
                              "BoxR"=>$BoxR,"BoxG"=>$BoxG,"BoxB"=>$BoxB,"BoxAlpha"=>$BoxAlpha,"BoxSurrounding"=>$BoxSurrounding,
                              "BoxBorderR"=>$BoxBorderR,"BoxBorderG"=>$BoxBorderG,"BoxBorderB"=>$BoxBorderB,"BoxBorderAlpha"=>$BoxBorderAlpha,
                              "R"=>$CaptionR,"G"=>$CaptionG,"B"=>$CaptionB,"Alpha"=>$CaptionAlpha);

     if ( $Caption == null )
      {
       if ( isset($Data["Abscissa"]) )
        {
         if ( isset($Data["Series"][$Data["Abscissa"]]["Data"][$Value]) )
          $Caption = $Data["Series"][$Data["Abscissa"]]["Data"][$Value];
         else
          $Caption = $Value;
        }
       else
        $Caption = $Value;
      }

     if ( $Data["Orientation"] == SCALE_POS_LEFTRIGHT )
      {
       $XStep = (($this->GraphAreaX2 - $this->GraphAreaX1) - $XScale[0] *2 ) / $XScale[1];
       $XPos  = $this->GraphAreaX1 + $XScale[0] + $XStep * $Value;
       $YPos1 = $this->GraphAreaY1 + $Data["YMargin"];
       $YPos2 = $this->GraphAreaY2 - $Data["YMargin"];

       if ( $XPos >= $this->GraphAreaX1 + $AbscissaMargin && $XPos <= $this->GraphAreaX2 - $AbscissaMargin )
        {
         $this->drawLine($XPos,$YPos1,$XPos,$YPos2,array("R"=>$R,"G"=>$G,"B"=>$B,"Alpha"=>$Alpha,"Ticks"=>$Ticks,"Weight"=>$Weight));

         if ( $Wide )
          {
           $this->drawLine($XPos-1,$YPos1,$XPos-1,$YPos2,array("R"=>$R,"G"=>$G,"B"=>$B,"Alpha"=>$Alpha/$WideFactor,"Ticks"=>$Ticks));
           $this->drawLine($XPos+1,$YPos1,$XPos+1,$YPos2,array("R"=>$R,"G"=>$G,"B"=>$B,"Alpha"=>$Alpha/$WideFactor,"Ticks"=>$Ticks));
          }

         if ( $WriteCaption )
          {
           if ( $CaptionAlign == CAPTION_LEFT_TOP )
            { $Y = $YPos1 + $CaptionOffset; $CaptionSettings["Align"] = TEXT_ALIGN_TOPMIDDLE; }
           else
            { $Y = $YPos2 - $CaptionOffset; $CaptionSettings["Align"] = TEXT_ALIGN_BOTTOMMIDDLE; }

           $this->drawText($XPos,$Y,$Caption,$CaptionSettings);
          }

         return array("X"=>$XPos);
        }
      }
     elseif( $Data["Orientation"] == SCALE_POS_TOPBOTTOM )
      {
       $XStep = (($this->GraphAreaY2 - $this->GraphAreaY1) - $XScale[0] *2 ) / $XScale[1];
       $XPos  = $this->GraphAreaY1 + $XScale[0] + $XStep * $Value;
       $YPos1 = $this->GraphAreaX1 + $Data["YMargin"];
       $YPos2 = $this->GraphAreaX2 - $Data["YMargin"];

       if ( $XPos >= $this->GraphAreaY1 + $AbscissaMargin && $XPos <= $this->GraphAreaY2 - $AbscissaMargin )
        {
         $this->drawLine($YPos1,$XPos,$YPos2,$XPos,array("R"=>$R,"G"=>$G,"B"=>$B,"Alpha"=>$Alpha,"Ticks"=>$Ticks,"Weight"=>$Weight));

         if ( $Wide )
          {
           $this->drawLine($YPos1,$XPos-1,$YPos2,$XPos-1,array("R"=>$R,"G"=>$G,"B"=>$B,"Alpha"=>$Alpha/$WideFactor,"Ticks"=>$Ticks));
           $this->drawLine($YPos1,$XPos+1,$YPos2,$XPos+1,array("R"=>$R,"G"=>$G,"B"=>$B,"Alpha"=>$Alpha/$WideFactor,"Ticks"=>$Ticks));
          }

         if ( $WriteCaption )
          {
           if ( $CaptionAlign == CAPTION_LEFT_TOP )
            { $Y = $YPos1 + $CaptionOffset; $CaptionSettings["Align"] = TEXT_ALIGN_MIDDLELEFT; }
           else
            { $Y = $YPos2 - $CaptionOffset; $CaptionSettings["Align"] = TEXT_ALIGN_MIDDLERIGHT; }

           $this->drawText($Y,$XPos,$Caption,$CaptionSettings);
          }

         return array("X"=>$XPos);
        }
      }
    }

    /* Draw an X threshold area */
    function drawXThresholdArea($Value1,$Value2,$format="")
    {
     $R		= isset($format["R"]) ? $format["R"] : 255;
     $G		= isset($format["G"]) ? $format["G"] : 0;
     $B		= isset($format["B"]) ? $format["B"] : 0;
     $Alpha	= isset($format["Alpha"]) ? $format["Alpha"] : 20;
     $Border    = isset($format["Border"]) ? $format["Border"] : true;
     $BorderR   = isset($format["BorderR"]) ? $format["BorderR"] : $R;
     $BorderG   = isset($format["BorderG"]) ? $format["BorderG"] : $G;
     $BorderB   = isset($format["BorderB"]) ? $format["BorderB"] : $B;
     $BorderAlpha = isset($format["BorderAlpha"]) ? $format["BorderAlpha"] : $Alpha + 20;
     $BorderTicks = isset($format["BorderTicks"]) ? $format["BorderTicks"] : 2;
     $AreaName 	= isset($format["AreaName"]) ? $format["AreaName"] : null;
     $NameAngle	= isset($format["NameAngle"]) ? $format["NameAngle"] : ZONE_NAME_ANGLE_AUTO;
     $NameR	= isset($format["NameR"]) ? $format["NameR"] : 255;
     $NameG	= isset($format["NameG"]) ? $format["NameG"] : 255;
     $NameB	= isset($format["NameB"]) ? $format["NameB"] : 255;
     $NameAlpha	= isset($format["NameAlpha"]) ? $format["NameAlpha"] : 100;
     $DisableShadowOnArea = isset($format["DisableShadowOnArea"]) ? $format["DisableShadowOnArea"] : true;

     $RestoreShadow = $this->Shadow;
     if ( $DisableShadowOnArea && $this->Shadow ) { $this->Shadow = false; }

     if ($BorderAlpha >100) { $BorderAlpha = 100;}

     $Data           = $this->DataSet->getData();
     $XScale         = $this->scaleGetXSettings();
     $AbscissaMargin = $this->getAbscissaMargin($Data);

     if ( $Data["Orientation"] == SCALE_POS_LEFTRIGHT )
      {
       $XStep = (($this->GraphAreaX2 - $this->GraphAreaX1) - $XScale[0] *2 ) / $XScale[1];
       $XPos1 = $this->GraphAreaX1 + $XScale[0] + $XStep * $Value1;
       $XPos2 = $this->GraphAreaX1 + $XScale[0] + $XStep * $Value2;
       $YPos1 = $this->GraphAreaY1 + $Data["YMargin"];
       $YPos2 = $this->GraphAreaY2 - $Data["YMargin"];

       if ( $XPos1 < $this->GraphAreaX1 + $XScale[0] ) { $XPos1 = $this->GraphAreaX1 + $XScale[0]; }
       if ( $XPos1 > $this->GraphAreaX2 - $XScale[0] ) { $XPos1 = $this->GraphAreaX2 - $XScale[0]; }
       if ( $XPos2 < $this->GraphAreaX1 + $XScale[0] ) { $XPos2 = $this->GraphAreaX1 + $XScale[0]; }
       if ( $XPos2 > $this->GraphAreaX2 - $XScale[0] ) { $XPos2 = $this->GraphAreaX2 - $XScale[0]; }

       $this->drawFilledRectangle($XPos1,$YPos1,$XPos2,$YPos2,array("R"=>$R,"G"=>$G,"B"=>$B,"Alpha"=>$Alpha));

       if ( $Border )
        {
         $this->drawLine($XPos1,$YPos1,$XPos1,$YPos2,array("R"=>$BorderR,"G"=>$BorderG,"B"=>$BorderB,"Alpha"=>$BorderAlpha,"Ticks"=>$BorderTicks));
         $this->drawLine($XPos2,$YPos1,$XPos2,$YPos2,array("R"=>$BorderR,"G"=>$BorderG,"B"=>$BorderB,"Alpha"=>$BorderAlpha,"Ticks"=>$BorderTicks));
        }

       if ( $AreaName != null )
        {
         $XPos = ($XPos2-$XPos1)/2 + $XPos1;
         $YPos = ($YPos2-$YPos1)/2 + $YPos1;

         if ( $NameAngle == ZONE_NAME_ANGLE_AUTO )
          {
           $TxtPos   = $this->getTextBox($XPos,$YPos,$this->FontName,$this->FontSize,0,$AreaName);
           $TxtWidth = $TxtPos[1]["X"] - $TxtPos[0]["X"];
           if ( abs($XPos2 - $XPos1) > $TxtWidth ) { $NameAngle = 0; } else { $NameAngle = 90; }
          }
         $this->Shadow = $RestoreShadow;
         $this->drawText($XPos,$YPos,$AreaName,array("R"=>$NameR,"G"=>$NameG,"B"=>$NameB,"Alpha"=>$NameAlpha,"Angle"=>$NameAngle,"Align"=>TEXT_ALIGN_MIDDLEMIDDLE));
         if ( $DisableShadowOnArea ) { $this->Shadow = false; }
        }

       $this->Shadow = $RestoreShadow;
       return array("X1"=>$XPos1,"X2"=>$XPos2);
      }
     elseif ( $Data["Orientation"] == SCALE_POS_TOPBOTTOM )
      {
       $XStep = (($this->GraphAreaY2 - $this->GraphAreaY1) - $XScale[0] *2 ) / $XScale[1];
       $XPos1 = $this->GraphAreaY1 + $XScale[0] + $XStep * $Value1;
       $XPos2 = $this->GraphAreaY1 + $XScale[0] + $XStep * $Value2;
       $YPos1 = $this->GraphAreaX1 + $Data["YMargin"];
       $YPos2 = $this->GraphAreaX2 - $Data["YMargin"];

       if ( $XPos1 < $this->GraphAreaY1 + $XScale[0] ) { $XPos1 = $this->GraphAreaY1 + $XScale[0]; }
       if ( $XPos1 > $this->GraphAreaY2 - $XScale[0] ) { $XPos1 = $this->GraphAreaY2 - $XScale[0]; }
       if ( $XPos2 < $this->GraphAreaY1 + $XScale[0] ) { $XPos2 = $this->GraphAreaY1 + $XScale[0]; }
       if ( $XPos2 > $this->GraphAreaY2 - $XScale[0] ) { $XPos2 = $this->GraphAreaY2 - $XScale[0]; }

       $this->drawFilledRectangle($YPos1,$XPos1,$YPos2,$XPos2,array("R"=>$R,"G"=>$G,"B"=>$B,"Alpha"=>$Alpha));

       if ( $Border )
        {
         $this->drawLine($YPos1,$XPos1,$YPos2,$XPos1,array("R"=>$BorderR,"G"=>$BorderG,"B"=>$BorderB,"Alpha"=>$BorderAlpha,"Ticks"=>$BorderTicks));
         $this->drawLine($YPos1,$XPos2,$YPos2,$XPos2,array("R"=>$BorderR,"G"=>$BorderG,"B"=>$BorderB,"Alpha"=>$BorderAlpha,"Ticks"=>$BorderTicks));
        }

       if ( $AreaName != null )
        {
         $XPos = ($XPos2-$XPos1)/2 + $XPos1;
         $YPos = ($YPos2-$YPos1)/2 + $YPos1;

         $this->Shadow = $RestoreShadow;
         $this->drawText($YPos,$XPos,$AreaName,array("R"=>$NameR,"G"=>$NameG,"B"=>$NameB,"Alpha"=>$NameAlpha,"Angle"=>0,"Align"=>TEXT_ALIGN_MIDDLEMIDDLE));
         if ( $DisableShadowOnArea ) { $this->Shadow = false; }
        }

       $this->Shadow = $RestoreShadow;
       return array("X1"=>$XPos1,"X2"=>$XPos2);
      }
    }

    /* Draw an Y threshold with the computed scale */
    function drawThreshold($Value,$format="")
    {
     $AxisID		= isset($format["AxisID"]) ? $format["AxisID"] : 0;
     $R			= isset($format["R"]) ? $format["R"] : 255;
     $G			= isset($format["G"]) ? $format["G"] : 0;
     $B			= isset($format["B"]) ? $format["B"] : 0;
     $Alpha		= isset($format["Alpha"]) ? $format["Alpha"] : 50;
     $Weight		= isset($format["Weight"]) ? $format["Weight"] : null;
     $Ticks		= isset($format["Ticks"]) ? $format["Ticks"] : 6;
     $Wide		= isset($format["Wide"]) ? $format["Wide"] : false;
     $WideFactor	= isset($format["WideFactor"]) ? $format["WideFactor"] : 5;
     $WriteCaption	= isset($format["WriteCaption"]) ? $format["WriteCaption"] : false;
     $Caption		= isset($format["Caption"]) ? $format["Caption"] : null;
     $CaptionAlign	= isset($format["CaptionAlign"]) ? $format["CaptionAlign"] : CAPTION_LEFT_TOP;
     $CaptionOffset     = isset($format["CaptionOffset"]) ? $format["CaptionOffset"] : 10;
     $CaptionR		= isset($format["CaptionR"]) ? $format["CaptionR"] : 255;
     $CaptionG		= isset($format["CaptionG"]) ? $format["CaptionG"] : 255;
     $CaptionB		= isset($format["CaptionB"]) ? $format["CaptionB"] : 255;
     $CaptionAlpha	= isset($format["CaptionAlpha"]) ? $format["CaptionAlpha"] : 100;
     $DrawBox		= isset($format["DrawBox"]) ? $format["DrawBox"] : true;
     $DrawBoxBorder	= isset($format["DrawBoxBorder"]) ? $format["DrawBoxBorder"] : false;
     $BorderOffset	= isset($format["BorderOffset"]) ? $format["BorderOffset"] : 5;
     $BoxRounded	= isset($format["BoxRounded"]) ? $format["BoxRounded"] : true;
     $RoundedRadius	= isset($format["RoundedRadius"]) ? $format["RoundedRadius"] : 3;
     $BoxR		= isset($format["BoxR"]) ? $format["BoxR"] : 0;
     $BoxG		= isset($format["BoxG"]) ? $format["BoxG"] : 0;
     $BoxB		= isset($format["BoxB"]) ? $format["BoxB"] : 0;
     $BoxAlpha		= isset($format["BoxAlpha"]) ? $format["BoxAlpha"] : 20;
     $BoxSurrounding	= isset($format["BoxSurrounding"]) ? $format["BoxSurrounding"] : "";
     $BoxBorderR	= isset($format["BoxBorderR"]) ? $format["BoxBorderR"] : 255;
     $BoxBorderG	= isset($format["BoxBorderG"]) ? $format["BoxBorderG"] : 255;
     $BoxBorderB	= isset($format["BoxBorderB"]) ? $format["BoxBorderB"] : 255;
     $BoxBorderAlpha	= isset($format["BoxBorderAlpha"]) ? $format["BoxBorderAlpha"] : 100;
     $NoMargin		= isset($format["NoMargin"]) ? $format["NoMargin"] : false;

     if ( is_array($Value) ) { foreach ($Value as $Key => $ID) { $this->drawThreshold($ID,$format); } return 0; }

     $CaptionSettings = array("DrawBox"=>$DrawBox,"DrawBoxBorder"=>$DrawBoxBorder,"BorderOffset"=>$BorderOffset,"BoxRounded"=>$BoxRounded,"RoundedRadius"=>$RoundedRadius,
                              "BoxR"=>$BoxR,"BoxG"=>$BoxG,"BoxB"=>$BoxB,"BoxAlpha"=>$BoxAlpha,"BoxSurrounding"=>$BoxSurrounding,
                              "BoxBorderR"=>$BoxBorderR,"BoxBorderG"=>$BoxBorderG,"BoxBorderB"=>$BoxBorderB,"BoxBorderAlpha"=>$BoxBorderAlpha,
                              "R"=>$CaptionR,"G"=>$CaptionG,"B"=>$CaptionB,"Alpha"=>$CaptionAlpha);

     $Data           = $this->DataSet->getData();
     $AbscissaMargin = $this->getAbscissaMargin($Data);

     if ( $NoMargin ) { $AbscissaMargin = 0; }
     if ( !isset($Data["Axis"][$AxisID]) ) { return(-1); }
     if ( $Caption == null ) { $Caption = $Value; }

     if ( $Data["Orientation"] == SCALE_POS_LEFTRIGHT )
      {
       $YPos = $this->scaleComputeY($Value,array("AxisID"=>$AxisID));
       if ( $YPos >= $this->GraphAreaY1+$Data["Axis"][$AxisID]["Margin"] && $YPos <= $this->GraphAreaY2-$Data["Axis"][$AxisID]["Margin"] )
        {
         $X1 = $this->GraphAreaX1 + $AbscissaMargin;
         $X2 = $this->GraphAreaX2 - $AbscissaMargin;

         $this->drawLine($X1,$YPos,$X2,$YPos,array("R"=>$R,"G"=>$G,"B"=>$B,"Alpha"=>$Alpha,"Ticks"=>$Ticks,"Weight"=>$Weight));

         if ( $Wide )
          {
           $this->drawLine($X1,$YPos-1,$X2,$YPos-1,array("R"=>$R,"G"=>$G,"B"=>$B,"Alpha"=>$Alpha/$WideFactor,"Ticks"=>$Ticks));
           $this->drawLine($X1,$YPos+1,$X2,$YPos+1,array("R"=>$R,"G"=>$G,"B"=>$B,"Alpha"=>$Alpha/$WideFactor,"Ticks"=>$Ticks));
          }

         if ( $WriteCaption )
          {
           if ( $CaptionAlign == CAPTION_LEFT_TOP )
            { $X = $X1 + $CaptionOffset; $CaptionSettings["Align"] = TEXT_ALIGN_MIDDLELEFT; }
           else
            { $X = $X2 - $CaptionOffset; $CaptionSettings["Align"] = TEXT_ALIGN_MIDDLERIGHT; }

           $this->drawText($X,$YPos,$Caption,$CaptionSettings);
          }
        }

       return array("Y"=>$YPos);
      }

     if ( $Data["Orientation"] == SCALE_POS_TOPBOTTOM )
      {
       $XPos = $this->scaleComputeY($Value,array("AxisID"=>$AxisID));
       if ( $XPos >= $this->GraphAreaX1+$Data["Axis"][$AxisID]["Margin"] && $XPos <= $this->GraphAreaX2-$Data["Axis"][$AxisID]["Margin"] )
        {
         $Y1 = $this->GraphAreaY1 + $AbscissaMargin;
         $Y2 = $this->GraphAreaY2 - $AbscissaMargin;

         $this->drawLine($XPos,$Y1,$XPos,$Y2,array("R"=>$R,"G"=>$G,"B"=>$B,"Alpha"=>$Alpha,"Ticks"=>$Ticks,"Weight"=>$Weight));

         if ( $Wide )
          {
           $this->drawLine($XPos-1,$Y1,$XPos-1,$Y2,array("R"=>$R,"G"=>$G,"B"=>$B,"Alpha"=>$Alpha/$WideFactor,"Ticks"=>$Ticks));
           $this->drawLine($XPos+1,$Y1,$XPos+1,$Y2,array("R"=>$R,"G"=>$G,"B"=>$B,"Alpha"=>$Alpha/$WideFactor,"Ticks"=>$Ticks));
          }

         if ( $WriteCaption )
          {
           if ( $CaptionAlign == CAPTION_LEFT_TOP )
            { $Y = $Y1 + $CaptionOffset; $CaptionSettings["Align"] = TEXT_ALIGN_TOPMIDDLE; }
           else
            { $Y = $Y2 - $CaptionOffset; $CaptionSettings["Align"] = TEXT_ALIGN_BOTTOMMIDDLE; }

           $CaptionSettings["Align"] = TEXT_ALIGN_TOPMIDDLE;
           $this->drawText($XPos,$Y,$Caption,$CaptionSettings);
          }
        }

       return array("Y"=>$XPos);
      }
    }

    /* Draw a threshold with the computed scale */
    function drawThresholdArea($Value1,$Value2,$format="")
    {
     $AxisID	= isset($format["AxisID"]) ? $format["AxisID"] : 0;
     $R		= isset($format["R"]) ? $format["R"] : 255;
     $G		= isset($format["G"]) ? $format["G"] : 0;
     $B		= isset($format["B"]) ? $format["B"] : 0;
     $Alpha	= isset($format["Alpha"]) ? $format["Alpha"] : 20;
     $Border    = isset($format["Border"]) ? $format["Border"] : true;
     $BorderR   = isset($format["BorderR"]) ? $format["BorderR"] : $R;
     $BorderG   = isset($format["BorderG"]) ? $format["BorderG"] : $G;
     $BorderB   = isset($format["BorderB"]) ? $format["BorderB"] : $B;
     $BorderAlpha = isset($format["BorderAlpha"]) ? $format["BorderAlpha"] : $Alpha + 20;
     $BorderTicks = isset($format["BorderTicks"]) ? $format["BorderTicks"] : 2;
     $AreaName 	= isset($format["AreaName"]) ? $format["AreaName"] : null;
     $NameAngle	= isset($format["NameAngle"]) ? $format["NameAngle"] : ZONE_NAME_ANGLE_AUTO;
     $NameR	= isset($format["NameR"]) ? $format["NameR"] : 255;
     $NameG	= isset($format["NameG"]) ? $format["NameG"] : 255;
     $NameB	= isset($format["NameB"]) ? $format["NameB"] : 255;
     $NameAlpha	= isset($format["NameAlpha"]) ? $format["NameAlpha"] : 100;
     $DisableShadowOnArea = isset($format["DisableShadowOnArea"]) ? $format["DisableShadowOnArea"] : true;
     $NoMargin	= isset($format["NoMargin"]) ? $format["NoMargin"] : false;

     if ($Value1 > $Value2) { list($Value1, $Value2) = array($Value2, $Value1); }

     $RestoreShadow = $this->Shadow;
     if ( $DisableShadowOnArea && $this->Shadow ) { $this->Shadow = false; }

     if ($BorderAlpha >100) { $BorderAlpha = 100;}

     $Data           = $this->DataSet->getData();
     $AbscissaMargin = $this->getAbscissaMargin($Data);

     if ( $NoMargin ) { $AbscissaMargin = 0; }
     if ( !isset($Data["Axis"][$AxisID]) ) { return(-1); }

     if ( $Data["Orientation"] == SCALE_POS_LEFTRIGHT )
      {
       $XPos1 = $this->GraphAreaX1 + $AbscissaMargin;
       $XPos2 = $this->GraphAreaX2 - $AbscissaMargin;
       $YPos1 = $this->scaleComputeY($Value1,array("AxisID"=>$AxisID));
       $YPos2 = $this->scaleComputeY($Value2,array("AxisID"=>$AxisID));

       if ( $YPos1 < $this->GraphAreaY1+$Data["Axis"][$AxisID]["Margin"] ) { $YPos1 = $this->GraphAreaY1+$Data["Axis"][$AxisID]["Margin"]; }
       if ( $YPos1 > $this->GraphAreaY2-$Data["Axis"][$AxisID]["Margin"] ) { $YPos1 = $this->GraphAreaY2-$Data["Axis"][$AxisID]["Margin"]; }
       if ( $YPos2 < $this->GraphAreaY1+$Data["Axis"][$AxisID]["Margin"] ) { $YPos2 = $this->GraphAreaY1+$Data["Axis"][$AxisID]["Margin"]; }
       if ( $YPos2 > $this->GraphAreaY2-$Data["Axis"][$AxisID]["Margin"] ) { $YPos2 = $this->GraphAreaY2-$Data["Axis"][$AxisID]["Margin"]; }

       $this->drawFilledRectangle($XPos1,$YPos1,$XPos2,$YPos2,array("R"=>$R,"G"=>$G,"B"=>$B,"Alpha"=>$Alpha));
       if ( $Border )
        {
         $this->drawLine($XPos1,$YPos1,$XPos2,$YPos1,array("R"=>$BorderR,"G"=>$BorderG,"B"=>$BorderB,"Alpha"=>$BorderAlpha,"Ticks"=>$BorderTicks));
         $this->drawLine($XPos1,$YPos2,$XPos2,$YPos2,array("R"=>$BorderR,"G"=>$BorderG,"B"=>$BorderB,"Alpha"=>$BorderAlpha,"Ticks"=>$BorderTicks));
        }

       if ( $AreaName != null )
        {
         $XPos = ($XPos2-$XPos1)/2 + $XPos1;
         $YPos = ($YPos2-$YPos1)/2 + $YPos1;
         $this->Shadow = $RestoreShadow;
         $this->drawText($XPos,$YPos,$AreaName,array("R"=>$NameR,"G"=>$NameG,"B"=>$NameB,"Alpha"=>$NameAlpha,"Angle"=>0,"Align"=>TEXT_ALIGN_MIDDLEMIDDLE));
         if ( $DisableShadowOnArea ) { $this->Shadow = false; }
        }

       $this->Shadow = $RestoreShadow;
       return array("Y1"=>$YPos1,"Y2"=>$YPos2);
      }
     elseif ( $Data["Orientation"] == SCALE_POS_TOPBOTTOM )
      {
       $YPos1 = $this->GraphAreaY1 + $AbscissaMargin;
       $YPos2 = $this->GraphAreaY2 - $AbscissaMargin;
       $XPos1 = $this->scaleComputeY($Value1,array("AxisID"=>$AxisID));
       $XPos2 = $this->scaleComputeY($Value2,array("AxisID"=>$AxisID));

       if ( $XPos1 < $this->GraphAreaX1+$Data["Axis"][$AxisID]["Margin"] ) { $XPos1 = $this->GraphAreaX1+$Data["Axis"][$AxisID]["Margin"]; }
       if ( $XPos1 > $this->GraphAreaX2-$Data["Axis"][$AxisID]["Margin"] ) { $XPos1 = $this->GraphAreaX2-$Data["Axis"][$AxisID]["Margin"]; }
       if ( $XPos2 < $this->GraphAreaX1+$Data["Axis"][$AxisID]["Margin"] ) { $XPos2 = $this->GraphAreaX1+$Data["Axis"][$AxisID]["Margin"]; }
       if ( $XPos2 > $this->GraphAreaX2-$Data["Axis"][$AxisID]["Margin"] ) { $XPos2 = $this->GraphAreaX2-$Data["Axis"][$AxisID]["Margin"]; }

       $this->drawFilledRectangle($XPos1,$YPos1,$XPos2,$YPos2,array("R"=>$R,"G"=>$G,"B"=>$B,"Alpha"=>$Alpha));
       if ( $Border )
        {
         $this->drawLine($XPos1,$YPos1,$XPos1,$YPos2,array("R"=>$BorderR,"G"=>$BorderG,"B"=>$BorderB,"Alpha"=>$BorderAlpha,"Ticks"=>$BorderTicks));
         $this->drawLine($XPos2,$YPos1,$XPos2,$YPos2,array("R"=>$BorderR,"G"=>$BorderG,"B"=>$BorderB,"Alpha"=>$BorderAlpha,"Ticks"=>$BorderTicks));
        }

       if ( $AreaName != null )
        {
         $XPos = ($YPos2-$YPos1)/2 + $YPos1;
         $YPos = ($XPos2-$XPos1)/2 + $XPos1;

         if ( $NameAngle == ZONE_NAME_ANGLE_AUTO )
          {
           $TxtPos   = $this->getTextBox($XPos,$YPos,$this->FontName,$this->FontSize,0,$AreaName);
           $TxtWidth = $TxtPos[1]["X"] - $TxtPos[0]["X"];
           if ( abs($XPos2 - $XPos1) > $TxtWidth ) { $NameAngle = 0; } else { $NameAngle = 90; }
          }
         $this->Shadow = $RestoreShadow;
         $this->drawText($YPos,$XPos,$AreaName,array("R"=>$NameR,"G"=>$NameG,"B"=>$NameB,"Alpha"=>$NameAlpha,"Angle"=>$NameAngle,"Align"=>TEXT_ALIGN_MIDDLEMIDDLE));
         if ( $DisableShadowOnArea ) { $this->Shadow = false; }
        }

       $this->Shadow = $RestoreShadow;
       return array("Y1"=>$XPos1,"Y2"=>$XPos2);
      }
    }

    function scaleGetXSettings()
    {
     $Data = $this->DataSet->getData();
     foreach($Data["Axis"] as $AxisID => $Settings)
      {
       if ( $Settings["Identity"] == AXIS_X )
        {
         $Rows = $Settings["Rows"];

         return array($Settings["Margin"],$Rows);
        }
      }
    }

    function scaleComputeY($Values,$Option="",$ReturnOnly0Height=false)
    {
     $AxisID	= isset($Option["AxisID"]) ? $Option["AxisID"] : 0;
     $serie_name	= isset($Option["SerieName"]) ? $Option["SerieName"] : null;

     $Data = $this->DataSet->getData();
     if ( !isset($Data["Axis"][$AxisID]) ) { return(-1); }

     if ( $serie_name != null ) { $AxisID = $Data["Series"][$serie_name]["Axis"]; }
     if ( !is_array($Values) ) { $Values= [$tmp]; }

     $Result = [];
     if ( $Data["Orientation"] == SCALE_POS_LEFTRIGHT )
      {
       $Height      = ($this->GraphAreaY2 - $this->GraphAreaY1) - $Data["Axis"][$AxisID]["Margin"]*2;
       $ScaleHeight = $Data["Axis"][$AxisID]["ScaleMax"] - $Data["Axis"][$AxisID]["ScaleMin"];
       $Step        = $Height / $ScaleHeight;

       if ( $ReturnOnly0Height )
        { foreach($Values as $Key => $Value) { if ( $Value == VOID ) { $Result[] = VOID; } else { $Result[] = $Step * $Value; } } }
       else
        { foreach($Values as $Key => $Value) { if ( $Value == VOID ) { $Result[] = VOID; } else { $Result[] = $this->GraphAreaY2 - $Data["Axis"][$AxisID]["Margin"] - ($Step * ($Value-$Data["Axis"][$AxisID]["ScaleMin"])); } } }
      }
     else
      {
       $Width      = ($this->GraphAreaX2 - $this->GraphAreaX1) - $Data["Axis"][$AxisID]["Margin"]*2;
       $ScaleWidth = $Data["Axis"][$AxisID]["ScaleMax"] - $Data["Axis"][$AxisID]["ScaleMin"];
       $Step       = $Width / $ScaleWidth;

       if ( $ReturnOnly0Height )
        { foreach($Values as $Key => $Value) { if ( $Value == VOID ) { $Result[] = VOID; } else { $Result[] = $Step * $Value; } } }
       else
        { foreach($Values as $Key => $Value) { if ( $Value == VOID ) { $Result[] = VOID; } else { $Result[] = $this->GraphAreaX1 + $Data["Axis"][$AxisID]["Margin"] + ($Step * ($Value-$Data["Axis"][$AxisID]["ScaleMin"])); } } }
      }

     if ( count($Result) == 1 )
      return $Result[0];
     else
      return $Result;
    }

    /* Format the axis values */
    function scaleFormat($Value,$Mode=null,$format=null,$Unit=null)
    {
     if ( $Value == VOID ) { return(""); }

     if ( $Mode == AXIS_FORMAT_CUSTOM )
      { if ( function_exists($format) ) { return(call_user_func($format,$Value)); } }

     if ( $Mode == AXIS_FORMAT_DATE )
      { if ( $format == null ) { $Pattern = "d/m/Y"; } else { $Pattern = $format; } return(date($Pattern,$Value)); }

     if ( $Mode == AXIS_FORMAT_TIME )
      { if ( $format == null ) { $Pattern = "H:i:s"; } else { $Pattern = $format; } return(date($Pattern,$Value)); }

     if ( $Mode == AXIS_FORMAT_CURRENCY )
      { return($format.number_format($Value,2)); }

     if ( $Mode == AXIS_FORMAT_METRIC )
      {
       if (abs($Value) > 1000000000)
        return round($Value/1000000000,$format)."g".$Unit;
       if (abs($Value) > 1000000)
        return round($Value/1000000,$format)."m".$Unit;
       elseif (abs($Value) >= 1000)
        return round($Value/1000,$format)."k".$Unit;

      }
     return $Value.$Unit;
    }

    /* Write Max value on a chart */
    function writeBounds($Type=BOUND_BOTH,$format=null)
    {
     $MaxLabelTxt	= isset($format["MaxLabelTxt"]) ? $format["MaxLabelTxt"] : "max=";
     $MinLabelTxt	= isset($format["MinLabelTxt"]) ? $format["MinLabelTxt"] : "min=";
     $Decimals		= isset($format["Decimals"]) ? $format["Decimals"] : 1;
     $ExcludedSeries	= isset($format["ExcludedSeries"]) ? $format["ExcludedSeries"] : "";
     $DisplayOffset	= isset($format["DisplayOffset"]) ? $format["DisplayOffset"] : 4;
     $DisplayColor	= isset($format["DisplayColor"]) ? $format["DisplayColor"] : DISPLAY_MANUAL;
     $MaxDisplayR	= isset($format["MaxDisplayR"]) ? $format["MaxDisplayR"] : 0;
     $MaxDisplayG	= isset($format["MaxDisplayG"]) ? $format["MaxDisplayG"] : 0;
     $MaxDisplayB	= isset($format["MaxDisplayB"]) ? $format["MaxDisplayB"] : 0;
     $MinDisplayR	= isset($format["MinDisplayR"]) ? $format["MinDisplayR"] : 255;
     $MinDisplayG	= isset($format["MinDisplayG"]) ? $format["MinDisplayG"] : 255;
     $MinDisplayB	= isset($format["MinDisplayB"]) ? $format["MinDisplayB"] : 255;
     $MinLabelPos	= isset($format["MinLabelPos"]) ? $format["MinLabelPos"] : BOUND_LABEL_POS_AUTO;
     $MaxLabelPos	= isset($format["MaxLabelPos"]) ? $format["MaxLabelPos"] : BOUND_LABEL_POS_AUTO;
     $DrawBox		= isset($format["DrawBox"]) ? $format["DrawBox"] : true;
     $DrawBoxBorder	= isset($format["DrawBoxBorder"]) ? $format["DrawBoxBorder"] : false;
     $BorderOffset	= isset($format["BorderOffset"]) ? $format["BorderOffset"] : 5;
     $BoxRounded	= isset($format["BoxRounded"]) ? $format["BoxRounded"] : true;
     $RoundedRadius	= isset($format["RoundedRadius"]) ? $format["RoundedRadius"] : 3;
     $BoxR		= isset($format["BoxR"]) ? $format["BoxR"] : 0;
     $BoxG		= isset($format["BoxG"]) ? $format["BoxG"] : 0;
     $BoxB		= isset($format["BoxB"]) ? $format["BoxB"] : 0;
     $BoxAlpha		= isset($format["BoxAlpha"]) ? $format["BoxAlpha"] : 20;
     $BoxSurrounding	= isset($format["BoxSurrounding"]) ? $format["BoxSurrounding"] : "";
     $BoxBorderR	= isset($format["BoxBorderR"]) ? $format["BoxBorderR"] : 255;
     $BoxBorderG	= isset($format["BoxBorderG"]) ? $format["BoxBorderG"] : 255;
     $BoxBorderB	= isset($format["BoxBorderB"]) ? $format["BoxBorderB"] : 255;
     $BoxBorderAlpha	= isset($format["BoxBorderAlpha"]) ? $format["BoxBorderAlpha"] : 100;

     $CaptionSettings = array("DrawBox"=>$DrawBox,"DrawBoxBorder"=>$DrawBoxBorder,"BorderOffset"=>$BorderOffset,"BoxRounded"=>$BoxRounded,"RoundedRadius"=>$RoundedRadius,
                              "BoxR"=>$BoxR,"BoxG"=>$BoxG,"BoxB"=>$BoxB,"BoxAlpha"=>$BoxAlpha,"BoxSurrounding"=>$BoxSurrounding,
                              "BoxBorderR"=>$BoxBorderR,"BoxBorderG"=>$BoxBorderG,"BoxBorderB"=>$BoxBorderB,"BoxBorderAlpha"=>$BoxBorderAlpha);

     list($XMargin,$XDivs) = $this->scaleGetXSettings();

     $Data = $this->DataSet->getData();
     foreach($Data["Series"] as $serie_name => $serie)
      {
       if ( $serie["isDrawable"] == true && $serie_name != $Data["Abscissa"] && !isset($ExcludedSeries[$serie_name]))
        {
         $R = $serie["Color"]["R"]; $G = $serie["Color"]["G"]; $B = $serie["Color"]["B"]; $Alpha = $serie["Color"]["Alpha"]; $Ticks = $serie["Ticks"];
         if ( $DisplayColor == DISPLAY_AUTO ) { $DisplayR = $R; $DisplayG = $G; $DisplayB = $B; }

         $MinValue = $this->DataSet->getMin($serie_name);
         $MaxValue = $this->DataSet->getMax($serie_name);

         $MinPos = VOID; $MaxPos = VOID;
         foreach($serie["Data"] as $Key => $Value)
          {
           if ( $Value == $MinValue && $MinPos == VOID ) { $MinPos = $Key; }
           if ( $Value == $MaxValue ) { $MaxPos = $Key; }
          }

         $AxisID	= $serie["Axis"];
         $Mode		= $Data["Axis"][$AxisID]["Display"];
         $format	= $Data["Axis"][$AxisID]["Format"];
         $Unit		= $Data["Axis"][$AxisID]["Unit"];

         $PosArray = $this->scaleComputeY($serie["Data"],array("AxisID"=>$serie["Axis"]));

         if ( $Data["Orientation"] == SCALE_POS_LEFTRIGHT )
          {
           $XStep       = ($this->GraphAreaX2-$this->GraphAreaX1-$XMargin*2)/$XDivs;
           $X           = $this->GraphAreaX1 + $XMargin;
           $serieOffset = isset($serie["XOffset"]) ? $serie["XOffset"] : 0;

           if ( $Type == BOUND_MAX || $Type == BOUND_BOTH )
            {
             if ( $MaxLabelPos == BOUND_LABEL_POS_TOP    || ( $MaxLabelPos ==  BOUND_LABEL_POS_AUTO && $MaxValue >= 0) ) { $YPos  = $PosArray[$MaxPos] - $DisplayOffset + 2; $Align = TEXT_ALIGN_BOTTOMMIDDLE; }
             if ( $MaxLabelPos == BOUND_LABEL_POS_BOTTOM || ( $MaxLabelPos ==  BOUND_LABEL_POS_AUTO && $MaxValue < 0) ) { $YPos  = $PosArray[$MaxPos] + $DisplayOffset + 2; $Align = TEXT_ALIGN_TOPMIDDLE; }

             $XPos  = $X + $MaxPos*$XStep + $serieOffset;
             $Label = $MaxLabelTxt.$this->scaleFormat(round($MaxValue,$Decimals),$Mode,$format,$Unit);

             $TxtPos  = $this->getTextBox($XPos,$YPos,$this->FontName,$this->FontSize,0,$Label);
             $XOffset = 0; $YOffset = 0;
             if ( $TxtPos[0]["X"] < $this->GraphAreaX1 ) { $XOffset = (($this->GraphAreaX1 - $TxtPos[0]["X"])/2); }
             if ( $TxtPos[1]["X"] > $this->GraphAreaX2 ) { $XOffset = -(($TxtPos[1]["X"] - $this->GraphAreaX2)/2); }
             if ( $TxtPos[2]["Y"] < $this->GraphAreaY1 ) { $YOffset = $this->GraphAreaY1 - $TxtPos[2]["Y"]; }
             if ( $TxtPos[0]["Y"] > $this->GraphAreaY2 ) { $YOffset = -($TxtPos[0]["Y"] - $this->GraphAreaY2); }

             $CaptionSettings["R"] = $MaxDisplayR; $CaptionSettings["G"] = $MaxDisplayG;
             $CaptionSettings["B"] = $MaxDisplayB; $CaptionSettings["Align"] = $Align;

             $this->drawText($XPos+$XOffset,$YPos+$YOffset,$Label,$CaptionSettings);
            }

           if ( $Type == BOUND_MIN || $Type == BOUND_BOTH )
            {
             if ( $MinLabelPos == BOUND_LABEL_POS_TOP    || ( $MinLabelPos ==  BOUND_LABEL_POS_AUTO && $MinValue >= 0) ) { $YPos  = $PosArray[$MinPos] - $DisplayOffset + 2; $Align = TEXT_ALIGN_BOTTOMMIDDLE; }
             if ( $MinLabelPos == BOUND_LABEL_POS_BOTTOM || ( $MinLabelPos ==  BOUND_LABEL_POS_AUTO && $MinValue < 0) ) { $YPos  = $PosArray[$MinPos] + $DisplayOffset + 2; $Align = TEXT_ALIGN_TOPMIDDLE; }

             $XPos  = $X + $MinPos*$XStep + $serieOffset;
             $Label = $MinLabelTxt.$this->scaleFormat(round($MinValue,$Decimals),$Mode,$format,$Unit);

             $TxtPos  = $this->getTextBox($XPos,$YPos,$this->FontName,$this->FontSize,0,$Label);
             $XOffset = 0; $YOffset = 0;
             if ( $TxtPos[0]["X"] < $this->GraphAreaX1 ) { $XOffset = (($this->GraphAreaX1 - $TxtPos[0]["X"])/2); }
             if ( $TxtPos[1]["X"] > $this->GraphAreaX2 ) { $XOffset = -(($TxtPos[1]["X"] - $this->GraphAreaX2)/2); }
             if ( $TxtPos[2]["Y"] < $this->GraphAreaY1 ) { $YOffset = $this->GraphAreaY1 - $TxtPos[2]["Y"]; }
             if ( $TxtPos[0]["Y"] > $this->GraphAreaY2 ) { $YOffset = -($TxtPos[0]["Y"] - $this->GraphAreaY2); }

             $CaptionSettings["R"] = $MinDisplayR; $CaptionSettings["G"] = $MinDisplayG;
             $CaptionSettings["B"] = $MinDisplayB; $CaptionSettings["Align"] = $Align;

             $this->drawText($XPos+$XOffset,$YPos-$DisplayOffset+$YOffset,$Label,$CaptionSettings);
            }
          }
         else
          {
           $XStep       = ($this->GraphAreaY2-$this->GraphAreaY1-$XMargin*2)/$XDivs;
           $X           = $this->GraphAreaY1 + $XMargin;
           $serieOffset = isset($serie["XOffset"]) ? $serie["XOffset"] : 0;

           if ( $Type == BOUND_MAX || $Type == BOUND_BOTH )
            {
             if ( $MaxLabelPos == BOUND_LABEL_POS_TOP    || ( $MaxLabelPos ==  BOUND_LABEL_POS_AUTO && $MaxValue >= 0) ) { $YPos  = $PosArray[$MaxPos] + $DisplayOffset + 2; $Align = TEXT_ALIGN_MIDDLELEFT; }
             if ( $MaxLabelPos == BOUND_LABEL_POS_BOTTOM || ( $MaxLabelPos ==  BOUND_LABEL_POS_AUTO && $MaxValue < 0) ) { $YPos  = $PosArray[$MaxPos] - $DisplayOffset + 2; $Align = TEXT_ALIGN_MIDDLERIGHT; }

             $XPos  = $X + $MaxPos*$XStep + $serieOffset;
             $Label = $MaxLabelTxt.$this->scaleFormat($MaxValue,$Mode,$format,$Unit);

             $TxtPos  = $this->getTextBox($YPos,$XPos,$this->FontName,$this->FontSize,0,$Label);
             $XOffset = 0; $YOffset = 0;
             if ( $TxtPos[0]["X"] < $this->GraphAreaX1 ) { $XOffset = $this->GraphAreaX1 - $TxtPos[0]["X"]; }
             if ( $TxtPos[1]["X"] > $this->GraphAreaX2 ) { $XOffset = -($TxtPos[1]["X"] - $this->GraphAreaX2); }
             if ( $TxtPos[2]["Y"] < $this->GraphAreaY1 ) { $YOffset = ($this->GraphAreaY1 - $TxtPos[2]["Y"])/2; }
             if ( $TxtPos[0]["Y"] > $this->GraphAreaY2 ) { $YOffset = -(($TxtPos[0]["Y"] - $this->GraphAreaY2)/2);}

             $CaptionSettings["R"] = $MaxDisplayR; $CaptionSettings["G"] = $MaxDisplayG;
             $CaptionSettings["B"] = $MaxDisplayB; $CaptionSettings["Align"] = $Align;

             $this->drawText($YPos+$XOffset,$XPos+$YOffset,$Label,$CaptionSettings);
            }

           if ( $Type == BOUND_MIN || $Type == BOUND_BOTH )
            {
             if ( $MinLabelPos == BOUND_LABEL_POS_TOP    || ( $MinLabelPos ==  BOUND_LABEL_POS_AUTO && $MinValue >= 0) ) { $YPos  = $PosArray[$MinPos] + $DisplayOffset + 2; $Align = TEXT_ALIGN_MIDDLELEFT; }
             if ( $MinLabelPos == BOUND_LABEL_POS_BOTTOM || ( $MinLabelPos ==  BOUND_LABEL_POS_AUTO && $MinValue < 0) ) { $YPos  = $PosArray[$MinPos] - $DisplayOffset + 2; $Align = TEXT_ALIGN_MIDDLERIGHT; }

             $XPos  = $X + $MinPos*$XStep + $serieOffset;
             $Label = $MinLabelTxt.$this->scaleFormat($MinValue,$Mode,$format,$Unit);

             $TxtPos  = $this->getTextBox($YPos,$XPos,$this->FontName,$this->FontSize,0,$Label);
             $XOffset = 0; $YOffset = 0;
             if ( $TxtPos[0]["X"] < $this->GraphAreaX1 ) { $XOffset = $this->GraphAreaX1 - $TxtPos[0]["X"]; }
             if ( $TxtPos[1]["X"] > $this->GraphAreaX2 ) { $XOffset = -($TxtPos[1]["X"] - $this->GraphAreaX2); }
             if ( $TxtPos[2]["Y"] < $this->GraphAreaY1 ) { $YOffset = ($this->GraphAreaY1 - $TxtPos[2]["Y"])/2; }
             if ( $TxtPos[0]["Y"] > $this->GraphAreaY2 ) { $YOffset = -(($TxtPos[0]["Y"] - $this->GraphAreaY2)/2);}

             $CaptionSettings["R"] = $MinDisplayR; $CaptionSettings["G"] = $MinDisplayG;
             $CaptionSettings["B"] = $MinDisplayB; $CaptionSettings["Align"] = $Align;

             $this->drawText($YPos+$XOffset,$XPos+$YOffset,$Label,$CaptionSettings);
            }
          }
        }
      }
    }

    /* Draw a plot chart */
    function drawPlotChart($format=null)
    {
     $PlotSize		= isset($format["PlotSize"]) ? $format["PlotSize"] : null;
     $PlotBorder	= isset($format["PlotBorder"]) ? $format["PlotBorder"] : false;
     $BorderR		= isset($format["BorderR"]) ? $format["BorderR"] : 50;
     $BorderG		= isset($format["BorderG"]) ? $format["BorderG"] : 50;
     $BorderB		= isset($format["BorderB"]) ? $format["BorderB"] : 50;
     $BorderAlpha	= isset($format["BorderAlpha"]) ? $format["BorderAlpha"] : 30;
     $BorderSize	= isset($format["BorderSize"]) ? $format["BorderSize"] : 2;
     $Surrounding	= isset($format["Surrounding"]) ? $format["Surrounding"] : null;
     $DisplayValues	= isset($format["DisplayValues"]) ? $format["DisplayValues"] : false;
     $DisplayOffset	= isset($format["DisplayOffset"]) ? $format["DisplayOffset"] : 4;
     $DisplayColor	= isset($format["DisplayColor"]) ? $format["DisplayColor"] : DISPLAY_MANUAL;
     $DisplayR		= isset($format["DisplayR"]) ? $format["DisplayR"] : 0;
     $DisplayG		= isset($format["DisplayG"]) ? $format["DisplayG"] : 0;
     $DisplayB		= isset($format["DisplayB"]) ? $format["DisplayB"] : 0;
     $RecordImageMap	= isset($format["RecordImageMap"]) ? $format["RecordImageMap"] : false;

     $this->LastChartLayout = CHART_LAST_LAYOUT_REGULAR;

     $Data = $this->DataSet->getData();
     list($XMargin,$XDivs) = $this->scaleGetXSettings();

     foreach($Data["Series"] as $serie_name => $serie)
      {
       if ( $serie["isDrawable"] == true && $serie_name != $Data["Abscissa"] )
        {
         if ( isset($serie["Weight"]) ) { $serieWeight = $serie["Weight"] + 2; } else { $serieWeight = 2; }
         if ( $PlotSize != null ) { $serieWeight = $PlotSize; }

         $R = $serie["Color"]["R"]; $G = $serie["Color"]["G"]; $B = $serie["Color"]["B"]; $Alpha = $serie["Color"]["Alpha"]; $Ticks = $serie["Ticks"];
         if ( $Surrounding != null ) { $BorderR = $R + $Surrounding; $BorderG = $G + $Surrounding; $BorderB = $B + $Surrounding; }
         if ( isset($serie["Picture"]) )
          { $Picture = $serie["Picture"]; list($PicWidth,$PicHeight,$PicType) = $this->getPicInfo($Picture); }
         else { $Picture = null; $PicOffset = 0; }

         if ( $DisplayColor == DISPLAY_AUTO ) { $DisplayR = $R; $DisplayG = $G; $DisplayB = $B; }

         $AxisID	= $serie["Axis"];
         $Shape		= $serie["Shape"];
         $Mode		= $Data["Axis"][$AxisID]["Display"];
         $format	= $Data["Axis"][$AxisID]["Format"];
         $Unit		= $Data["Axis"][$AxisID]["Unit"];

         if (isset($serie["Description"])) { $serieDescription = $serie["Description"]; } else { $serieDescription = $serie_name; }

         $PosArray = $this->scaleComputeY($serie["Data"],array("AxisID"=>$serie["Axis"]));

         $this->DataSet->Data["Series"][$serie_name]["XOffset"] = 0;

         if ( $Data["Orientation"] == SCALE_POS_LEFTRIGHT )
          {
           if ( $XDivs == 0 ) { $XStep = ($this->GraphAreaX2-$this->GraphAreaX1)/4; } else { $XStep = ($this->GraphAreaX2-$this->GraphAreaX1-$XMargin*2)/$XDivs; }
           if ( $Picture != null ) { $PicOffset = $PicHeight / 2; $serieWeight = 0; }
           $X = $this->GraphAreaX1 + $XMargin;

           if ( !is_array($PosArray) ) { $PosArray = [$Value]; }
           foreach($PosArray as $Key => $Y)
            {
             if ( $DisplayValues )
              $this->drawText($X,$Y-$DisplayOffset-$serieWeight-$BorderSize-$PicOffset,$this->scaleFormat($serie["Data"][$Key],$Mode,$format,$Unit),array("R"=>$DisplayR,"G"=>$DisplayG,"B"=>$DisplayB,"Align"=>TEXT_ALIGN_BOTTOMMIDDLE));

             if ( $Y != VOID )
              {
               if ( $RecordImageMap ) { $this->addToImageMap("CIRCLE",floor($X).",".floor($Y).",".$serieWeight,$this->toHTMLColor($R,$G,$B),$serieDescription,$this->scaleFormat($serie["Data"][$Key],$Mode,$format,$Unit)); }

               if ( $Picture != null )
                { $this->drawFromPicture($PicType,$Picture,$X-$PicWidth/2,$Y-$PicHeight/2); }
               else
                { $this->drawShape($X,$Y,$Shape,$serieWeight,$PlotBorder,$BorderSize,$R,$G,$B,$Alpha,$BorderR,$BorderG,$BorderB,$BorderAlpha); }
              }
             $X = $X + $XStep;
            }
          }
         else
          {
           if ( $XDivs == 0 ) { $YStep = ($this->GraphAreaY2-$this->GraphAreaY1)/4; } else { $YStep = ($this->GraphAreaY2-$this->GraphAreaY1-$XMargin*2)/$XDivs; }
           if ( $Picture != null ) { $PicOffset = $PicWidth / 2; $serieWeight = 0; }
           $Y = $this->GraphAreaY1 + $XMargin;

           if ( !is_array($PosArray) ) {$PosArray = [$Value]; }
           foreach($PosArray as $Key => $X)
            {
             if ( $DisplayValues )
              $this->drawText($X+$DisplayOffset+$serieWeight+$BorderSize+$PicOffset,$Y,$this->scaleFormat($serie["Data"][$Key],$Mode,$format,$Unit),array("Angle"=>270,"R"=>$DisplayR,"G"=>$DisplayG,"B"=>$DisplayB,"Align"=>TEXT_ALIGN_BOTTOMMIDDLE));

             if ( $X != VOID )
              {
               if ( $RecordImageMap ) { $this->addToImageMap("CIRCLE",floor($X).",".floor($Y).",".$serieWeight,$this->toHTMLColor($R,$G,$B),$serieDescription,$this->scaleFormat($serie["Data"][$Key],$Mode,$format,$Unit)); }

               if ( $Picture != null )
                { $this->drawFromPicture($PicType,$Picture,$X-$PicWidth/2,$Y-$PicHeight/2); }
               else
                { $this->drawShape($X,$Y,$Shape,$serieWeight,$PlotBorder,$BorderSize,$R,$G,$B,$Alpha,$BorderR,$BorderG,$BorderB,$BorderAlpha); }
              }
             $Y = $Y + $YStep;
            }
          }
        }
      }
    }

    /* Draw a spline chart */
    function drawSplineChart($format=null)
    {
     $BreakVoid		= isset($format["BreakVoid"]) ? $format["BreakVoid"] : true;
     $VoidTicks		= isset($format["VoidTicks"]) ? $format["VoidTicks"] : 4;
     $BreakR		= isset($format["BreakR"]) ? $format["BreakR"] : null; // 234
     $BreakG		= isset($format["BreakG"]) ? $format["BreakG"] : null; // 55
     $BreakB		= isset($format["BreakB"]) ? $format["BreakB"] : null; // 26
     $DisplayValues	= isset($format["DisplayValues"]) ? $format["DisplayValues"] : false;
     $DisplayOffset	= isset($format["DisplayOffset"]) ? $format["DisplayOffset"] : 2;
     $DisplayColor	= isset($format["DisplayColor"]) ? $format["DisplayColor"] : DISPLAY_MANUAL;
     $DisplayR		= isset($format["DisplayR"]) ? $format["DisplayR"] : 0;
     $DisplayG		= isset($format["DisplayG"]) ? $format["DisplayG"] : 0;
     $DisplayB		= isset($format["DisplayB"]) ? $format["DisplayB"] : 0;
     $RecordImageMap	= isset($format["RecordImageMap"]) ? $format["RecordImageMap"] : false;
     $ImageMapPlotSize  = isset($format["ImageMapPlotSize"]) ? $format["ImageMapPlotSize"] : 5;

     $this->LastChartLayout = CHART_LAST_LAYOUT_REGULAR;

     $Data = $this->DataSet->getData();
     list($XMargin,$XDivs) = $this->scaleGetXSettings();
     foreach($Data["Series"] as $serie_name => $serie)
      {
       if ( $serie["isDrawable"] == true && $serie_name != $Data["Abscissa"] )
        {
         $R = $serie["Color"]["R"]; $G = $serie["Color"]["G"]; $B = $serie["Color"]["B"]; $Alpha = $serie["Color"]["Alpha"]; $Ticks = $serie["Ticks"]; $Weight = $serie["Weight"];

         if ( $BreakR == null )
          $BreakSettings = array("R"=>$R,"G"=>$G,"B"=>$B,"Alpha"=>$Alpha,"Ticks"=>$VoidTicks);
         else
          $BreakSettings = array("R"=>$BreakR,"G"=>$BreakG,"B"=>$BreakB,"Alpha"=>$Alpha,"Ticks"=>$VoidTicks,"Weight"=>$Weight);

         if ( $DisplayColor == DISPLAY_AUTO ) { $DisplayR = $R; $DisplayG = $G; $DisplayB = $B; }

         $AxisID	= $serie["Axis"];
         $Mode		= $Data["Axis"][$AxisID]["Display"];
         $format	= $Data["Axis"][$AxisID]["Format"];
         $Unit		= $Data["Axis"][$AxisID]["Unit"];

         if (isset($serie["Description"])) { $serieDescription = $serie["Description"]; } else { $serieDescription = $serie_name; }

         $PosArray = $this->scaleComputeY($serie["Data"],array("AxisID"=>$serie["Axis"]));

         $this->DataSet->Data["Series"][$serie_name]["XOffset"] = 0;

         if ( $Data["Orientation"] == SCALE_POS_LEFTRIGHT )
          {
           if ( $XDivs == 0 ) { $XStep = ($this->GraphAreaX2-$this->GraphAreaX1)/4; } else { $XStep = ($this->GraphAreaX2-$this->GraphAreaX1-$XMargin*2)/$XDivs; }
           $X     = $this->GraphAreaX1 + $XMargin; $WayPoints = "";
           $Force = $XStep / 5;

           if ( !is_array($PosArray) ) { $Value = $PosArray; $PosArray = ""; $PosArray[0] = $Value; }
           $LastGoodY = null; $LastGoodX = null; $LastX = 1; $LastY = 1;
           foreach($PosArray as $Key => $Y)
            {
             if ( $DisplayValues )
              $this->drawText($X,$Y-$DisplayOffset,$this->scaleFormat($serie["Data"][$Key],$Mode,$format,$Unit),array("R"=>$DisplayR,"G"=>$DisplayG,"B"=>$DisplayB,"Align"=>TEXT_ALIGN_BOTTOMMIDDLE));

             if ( $RecordImageMap && $Y != VOID ) { $this->addToImageMap("CIRCLE",floor($X).",".floor($Y).",".$ImageMapPlotSize,$this->toHTMLColor($R,$G,$B),$serieDescription,$this->scaleFormat($serie["Data"][$Key],$Mode,$format,$Unit)); }

             if ( $Y == VOID && $LastY != null )
              { $this->drawSpline($WayPoints,array("Force"=>$Force,"R"=>$R,"G"=>$G,"B"=>$B,"Alpha"=>$Alpha,"Ticks"=>$Ticks,"Weight"=>$Weight)); $WayPoints = ""; }

             if ( $Y != VOID && $LastY == null && $LastGoodY != null && !$BreakVoid )
              { $this->drawLine($LastGoodX,$LastGoodY,$X,$Y,$BreakSettings); }

             if ( $Y != VOID )
              $WayPoints[] = array($X,$Y);

             if ( $Y != VOID ) { $LastGoodY = $Y; $LastGoodX = $X; }
             if ( $Y == VOID ) { $Y = null; }

             $LastX = $X; $LastY = $Y;
             $X = $X + $XStep;
            }
           $this->drawSpline($WayPoints,array("Force"=>$Force,"R"=>$R,"G"=>$G,"B"=>$B,"Alpha"=>$Alpha,"Ticks"=>$Ticks,"Weight"=>$Weight));
          }
         else
          {
           if ( $XDivs == 0 ) { $YStep = ($this->GraphAreaY2-$this->GraphAreaY1)/4; } else { $YStep = ($this->GraphAreaY2-$this->GraphAreaY1-$XMargin*2)/$XDivs; }
           $Y     = $this->GraphAreaY1 + $XMargin; $WayPoints = "";
           $Force = $YStep / 5;

           if ( !is_array($PosArray) ) { $Value = $PosArray; $PosArray = ""; $PosArray[0] = $Value; }
           $LastGoodY = null; $LastGoodX = null; $LastX = 1; $LastY = 1;
           foreach($PosArray as $Key => $X)
            {
             if ( $DisplayValues )
              $this->drawText($X+$DisplayOffset,$Y,$this->scaleFormat($serie["Data"][$Key],$Mode,$format,$Unit),array("Angle"=>270,"R"=>$DisplayR,"G"=>$DisplayG,"B"=>$DisplayB,"Align"=>TEXT_ALIGN_BOTTOMMIDDLE));

             if ( $RecordImageMap && $X != VOID ) { $this->addToImageMap("CIRCLE",floor($X).",".floor($Y).",".$ImageMapPlotSize,$this->toHTMLColor($R,$G,$B),$serieDescription,$this->scaleFormat($serie["Data"][$Key],$Mode,$format,$Unit)); }

             if ( $X == VOID && $LastX != null )
              { $this->drawSpline($WayPoints,array("Force"=>$Force,"R"=>$R,"G"=>$G,"B"=>$B,"Alpha"=>$Alpha,"Ticks"=>$Ticks,"Weight"=>$Weight)); $WayPoints = ""; }

             if ( $X != VOID && $LastX == null && $LastGoodX != null && !$BreakVoid )
              { $this->drawLine($LastGoodX,$LastGoodY,$X,$Y,$BreakSettings); }

             if ( $X != VOID )
              $WayPoints[] = array($X,$Y);

             if ( $X != VOID ) { $LastGoodX = $X; $LastGoodY = $Y; }
             if ( $X == VOID ) { $X = null; }

             $LastX = $X; $LastY = $Y;
             $Y = $Y + $YStep;
            }
           $this->drawSpline($WayPoints,array("Force"=>$Force,"R"=>$R,"G"=>$G,"B"=>$B,"Alpha"=>$Alpha,"Ticks"=>$Ticks,"Weight"=>$Weight));
          }
        }
      }
    }

    /* Draw a filled spline chart */
    function drawFilledSplineChart($format=null)
    {
     $DisplayValues	= isset($format["DisplayValues"]) ? $format["DisplayValues"] : false;
     $DisplayOffset	= isset($format["DisplayOffset"]) ? $format["DisplayOffset"] : 2;
     $DisplayColor	= isset($format["DisplayColor"]) ? $format["DisplayColor"] : DISPLAY_MANUAL;
     $DisplayR		= isset($format["DisplayR"]) ? $format["DisplayR"] : 0;
     $DisplayG		= isset($format["DisplayG"]) ? $format["DisplayG"] : 0;
     $DisplayB		= isset($format["DisplayB"]) ? $format["DisplayB"] : 0;
     $AroundZero	= isset($format["AroundZero"]) ? $format["AroundZero"] : true;
     $Threshold		= isset($format["Threshold"]) ? $format["Threshold"] : null;

     $this->LastChartLayout = CHART_LAST_LAYOUT_REGULAR;

     $Data = $this->DataSet->getData();
     list($XMargin,$XDivs) = $this->scaleGetXSettings();
     foreach($Data["Series"] as $serie_name => $serie)
      {
       if ( $serie["isDrawable"] == true && $serie_name != $Data["Abscissa"] )
        {
         $R = $serie["Color"]["R"]; $G = $serie["Color"]["G"]; $B = $serie["Color"]["B"]; $Alpha = $serie["Color"]["Alpha"]; $Ticks = $serie["Ticks"];
         if ( $DisplayColor == DISPLAY_AUTO ) { $DisplayR = $R; $DisplayG = $G; $DisplayB = $B; }

         $AxisID	= $serie["Axis"];
         $Mode		= $Data["Axis"][$AxisID]["Display"];
         $format	= $Data["Axis"][$AxisID]["Format"];
         $Unit		= $Data["Axis"][$AxisID]["Unit"];

         $PosArray = $this->scaleComputeY($serie["Data"],array("AxisID"=>$serie["Axis"]));
         if ( $AroundZero ) { $YZero = $this->scaleComputeY(0,array("AxisID"=>$serie["Axis"])); }

         if ( $Threshold != null )
          {
           foreach($Threshold as $Key => $Params)
            {
             $Threshold[$Key]["MinX"] = $this->scaleComputeY($Params["Min"],array("AxisID"=>$serie["Axis"]));
             $Threshold[$Key]["MaxX"] = $this->scaleComputeY($Params["Max"],array("AxisID"=>$serie["Axis"]));
            }
          }

         $this->DataSet->Data["Series"][$serie_name]["XOffset"] = 0;

         if ( $Data["Orientation"] == SCALE_POS_LEFTRIGHT )
          {
           if ( $XDivs == 0 ) { $XStep = ($this->GraphAreaX2-$this->GraphAreaX1)/4; } else { $XStep = ($this->GraphAreaX2-$this->GraphAreaX1-$XMargin*2)/$XDivs; }
           $X     = $this->GraphAreaX1 + $XMargin; $WayPoints = "";
           $Force = $XStep / 5;

           if ( !$AroundZero ) { $YZero = $this->GraphAreaY2-1; }
           if ( $YZero > $this->GraphAreaY2-1 ) { $YZero = $this->GraphAreaY2-1; }
           if ( $YZero < $this->GraphAreaY1+1 ) { $YZero = $this->GraphAreaY1+1; }

           $LastX = ""; $LastY = "";
           if ( !is_array($PosArray) ) { $Value = $PosArray; $PosArray = ""; $PosArray[0] = $Value; }
           foreach($PosArray as $Key => $Y)
            {
             if ( $DisplayValues )
              $this->drawText($X,$Y-$DisplayOffset,$this->scaleFormat($serie["Data"][$Key],$Mode,$format,$Unit),array("R"=>$DisplayR,"G"=>$DisplayG,"B"=>$DisplayB,"Align"=>TEXT_ALIGN_BOTTOMMIDDLE));

             if ( $Y == VOID )
              {
               $Area = $this->drawSpline($WayPoints,array("Force"=>$Force,"PathOnly"=>true));

               if ( $Area != "" )
                {
                 foreach ($Area as $key => $points)
                  {
                   $Corners = ""; $Corners[] = $Area[$key][0]["X"]; $Corners[] = $YZero;
                   foreach($points as $subKey => $Point)
                    {
                     if ( $subKey == count($points)-1) { $Corners[] = $Point["X"]-1; } else { $Corners[] = $Point["X"]; }
                     $Corners[] = $Point["Y"]+1;
                    }
                   $Corners[] = $points[$subKey]["X"]-1; $Corners[] = $YZero;

                   $this->drawPolygonChart($Corners,array("R"=>$R,"G"=>$G,"B"=>$B,"Alpha"=>$Alpha/2,"NoBorder"=>true,"Threshold"=>$Threshold));
                  }
                 $this->drawSpline($WayPoints,array("Force"=>$Force,"R"=>$R,"G"=>$G,"B"=>$B,"Alpha"=>$Alpha,"Ticks"=>$Ticks));
                }

               $WayPoints = "";
              }
             else
              $WayPoints[] = array($X,$Y-.5); /* -.5 for AA visual fix */

             $X = $X + $XStep;
            }
           $Area = $this->drawSpline($WayPoints,array("Force"=>$Force,"PathOnly"=>true));

           if ( $Area != "" )
            {
             foreach ($Area as $key => $points)
              {
               $Corners = ""; $Corners[] = $Area[$key][0]["X"]; $Corners[] = $YZero;
               foreach($points as $subKey => $Point)
                {
                 if ( $subKey == count($points)-1) { $Corners[] = $Point["X"]-1; } else { $Corners[] = $Point["X"]; }
                 $Corners[] = $Point["Y"]+1;
                }
               $Corners[] = $points[$subKey]["X"]-1; $Corners[] = $YZero;

               $this->drawPolygonChart($Corners,array("R"=>$R,"G"=>$G,"B"=>$B,"Alpha"=>$Alpha/2,"NoBorder"=>true,"Threshold"=>$Threshold));
              }
             $this->drawSpline($WayPoints,array("Force"=>$Force,"R"=>$R,"G"=>$G,"B"=>$B,"Alpha"=>$Alpha,"Ticks"=>$Ticks));
            }
          }
         else
          {
           if ( $XDivs == 0 ) { $YStep = ($this->GraphAreaY2-$this->GraphAreaY1)/4; } else { $YStep = ($this->GraphAreaY2-$this->GraphAreaY1-$XMargin*2)/$XDivs; }
           $Y     = $this->GraphAreaY1 + $XMargin; $WayPoints = "";
           $Force = $YStep / 5;

           if ( !$AroundZero ) { $YZero = $this->GraphAreaX1+1; }
           if ( $YZero > $this->GraphAreaX2-1 ) { $YZero = $this->GraphAreaX2-1; }
           if ( $YZero < $this->GraphAreaX1+1 ) { $YZero = $this->GraphAreaX1+1; }

           if ( !is_array($PosArray) ) { $Value = $PosArray; $PosArray = ""; $PosArray[0] = $Value; }
           foreach($PosArray as $Key => $X)
            {
             if ( $DisplayValues )
              $this->drawText($X+$DisplayOffset,$Y,$this->scaleFormat($serie["Data"][$Key],$Mode,$format,$Unit),array("Angle"=>270,"R"=>$DisplayR,"G"=>$DisplayG,"B"=>$DisplayB,"Align"=>TEXT_ALIGN_BOTTOMMIDDLE));

             if ( $X == VOID )
              {
               $Area = $this->drawSpline($WayPoints,array("Force"=>$Force,"PathOnly"=>true));

               if ( $Area != "" )
                {
                 foreach ($Area as $key => $points)
                  {
                   $Corners = ""; $Corners[] = $YZero; $Corners[] = $Area[$key][0]["Y"];
                   foreach($points as $subKey => $Point)
                    {
                     if ( $subKey == count($points)-1) { $Corners[] = $Point["X"]-1; } else { $Corners[] = $Point["X"]; }
                     $Corners[] = $Point["Y"];
                    }
                   $Corners[] = $YZero; $Corners[] = $points[$subKey]["Y"]-1;

                   $this->drawPolygonChart($Corners,array("R"=>$R,"G"=>$G,"B"=>$B,"Alpha"=>$Alpha/2,"NoBorder"=>true,"Threshold"=>$Threshold));
                  }
                 $this->drawSpline($WayPoints,array("Force"=>$Force,"R"=>$R,"G"=>$G,"B"=>$B,"Alpha"=>$Alpha,"Ticks"=>$Ticks));
                }

               $WayPoints = "";
              }
             else
              $WayPoints[] = array($X,$Y);

             $Y = $Y + $YStep;
            }
           $Area = $this->drawSpline($WayPoints,array("Force"=>$Force,"PathOnly"=>true));

           if ( $Area != "" )
            {
             foreach ($Area as $key => $points)
              {
               $Corners = ""; $Corners[] = $YZero; $Corners[] = $Area[$key][0]["Y"];
               foreach($points as $subKey => $Point)
                {
                 if ( $subKey == count($points)-1) { $Corners[] = $Point["X"]-1; } else { $Corners[] = $Point["X"]; }
                 $Corners[] = $Point["Y"];
                }
               $Corners[] = $YZero; $Corners[] = $points[$subKey]["Y"]-1;

               $this->drawPolygonChart($Corners,array("R"=>$R,"G"=>$G,"B"=>$B,"Alpha"=>$Alpha/2,"NoBorder"=>true,"Threshold"=>$Threshold));
              }
             $this->drawSpline($WayPoints,array("Force"=>$Force,"R"=>$R,"G"=>$G,"B"=>$B,"Alpha"=>$Alpha,"Ticks"=>$Ticks));
            }

          }
        }
      }
    }

    /* Draw a line chart */
    function drawLineChart($format=null)
    {
     $BreakVoid		= isset($format["BreakVoid"]) ? $format["BreakVoid"] : true;
     $VoidTicks		= isset($format["VoidTicks"]) ? $format["VoidTicks"] : 4;
     $BreakR		= isset($format["BreakR"]) ? $format["BreakR"] : null;
     $BreakG		= isset($format["BreakG"]) ? $format["BreakG"] : null;
     $BreakB		= isset($format["BreakB"]) ? $format["BreakB"] : null;
     $DisplayValues	= isset($format["DisplayValues"]) ? $format["DisplayValues"] : false;
     $DisplayOffset	= isset($format["DisplayOffset"]) ? $format["DisplayOffset"] : 2;
     $DisplayColor	= isset($format["DisplayColor"]) ? $format["DisplayColor"] : DISPLAY_MANUAL;
     $DisplayR		= isset($format["DisplayR"]) ? $format["DisplayR"] : 0;
     $DisplayG		= isset($format["DisplayG"]) ? $format["DisplayG"] : 0;
     $DisplayB		= isset($format["DisplayB"]) ? $format["DisplayB"] : 0;
     $RecordImageMap	= isset($format["RecordImageMap"]) ? $format["RecordImageMap"] : false;
     $ImageMapPlotSize  = isset($format["ImageMapPlotSize"]) ? $format["ImageMapPlotSize"] : 5;
     $ForceColor	= isset($format["ForceColor"]) ? $format["ForceColor"] : false;
     $ForceR		= isset($format["ForceR"]) ? $format["ForceR"] : 0;
     $ForceG		= isset($format["ForceG"]) ? $format["ForceG"] : 0;
     $ForceB		= isset($format["ForceB"]) ? $format["ForceB"] : 0;
     $ForceAlpha	= isset($format["ForceAlpha"]) ? $format["ForceAlpha"] : 100;

     $this->LastChartLayout = CHART_LAST_LAYOUT_REGULAR;

     $Data = $this->DataSet->getData();
     list($XMargin,$XDivs) = $this->scaleGetXSettings();
     foreach($Data["Series"] as $serie_name => $serie)
      {
       if ( $serie["isDrawable"] == true && $serie_name != $Data["Abscissa"] )
        {
         $R = $serie["Color"]["R"]; $G = $serie["Color"]["G"]; $B = $serie["Color"]["B"]; $Alpha = $serie["Color"]["Alpha"]; $Ticks = $serie["Ticks"]; $Weight = $serie["Weight"];

         if ( $ForceColor )
          { $R = $ForceR; $G = $ForceG; $B = $ForceB; $Alpha = $ForceAlpha; }

         if ( $BreakR == null )
          $BreakSettings = array("R"=>$R,"G"=>$G,"B"=>$B,"Alpha"=>$Alpha,"Ticks"=>$VoidTicks,"Weight"=>$Weight);
         else
          $BreakSettings = array("R"=>$BreakR,"G"=>$BreakG,"B"=>$BreakB,"Alpha"=>$Alpha,"Ticks"=>$VoidTicks,"Weight"=>$Weight);

         if ( $DisplayColor == DISPLAY_AUTO ) { $DisplayR = $R; $DisplayG = $G; $DisplayB = $B; }

         $AxisID	= $serie["Axis"];
         $Mode		= $Data["Axis"][$AxisID]["Display"];
         $format	= $Data["Axis"][$AxisID]["Format"];
         $Unit		= $Data["Axis"][$AxisID]["Unit"];

         if (isset($serie["Description"])) { $serieDescription = $serie["Description"]; } else { $serieDescription = $serie_name; }

         $PosArray = $this->scaleComputeY($serie["Data"],array("AxisID"=>$serie["Axis"]));

         $this->DataSet->Data["Series"][$serie_name]["XOffset"] = 0;

         if ( $Data["Orientation"] == SCALE_POS_LEFTRIGHT )
          {
           if ( $XDivs == 0 ) { $XStep = ($this->GraphAreaX2-$this->GraphAreaX1)/4; } else { $XStep = ($this->GraphAreaX2-$this->GraphAreaX1-$XMargin*2)/$XDivs; }
           $X = $this->GraphAreaX1 + $XMargin; $LastX = null; $LastY = null;

           if ( !is_array($PosArray) ) { $Value = $PosArray; $PosArray = ""; $PosArray[0] = $Value; }
           $LastGoodY = null; $LastGoodX = null;
           foreach($PosArray as $Key => $Y)
            {
             if ( $DisplayValues && $serie["Data"][$Key] != VOID )
              {
               if ( $serie["Data"][$Key] > 0 ) { $Align = TEXT_ALIGN_BOTTOMMIDDLE; $Offset = $DisplayOffset; } else { $Align = TEXT_ALIGN_TOPMIDDLE; $Offset = -$DisplayOffset; }
               $this->drawText($X,$Y-$Offset-$Weight,$this->scaleFormat($serie["Data"][$Key],$Mode,$format,$Unit),array("R"=>$DisplayR,"G"=>$DisplayG,"B"=>$DisplayB,"Align"=>$Align));
              }

             if ( $RecordImageMap && $Y != VOID ) { $this->addToImageMap("CIRCLE",floor($X).",".floor($Y).",".$ImageMapPlotSize,$this->toHTMLColor($R,$G,$B),$serieDescription,$this->scaleFormat($serie["Data"][$Key],$Mode,$format,$Unit)); }

             if ( $Y != VOID && $LastX != null && $LastY != null )
              $this->drawLine($LastX,$LastY,$X,$Y,array("R"=>$R,"G"=>$G,"B"=>$B,"Alpha"=>$Alpha,"Ticks"=>$Ticks,"Weight"=>$Weight));

             if ( $Y != VOID && $LastY == null && $LastGoodY != null && !$BreakVoid )
              { $this->drawLine($LastGoodX,$LastGoodY,$X,$Y,$BreakSettings); $LastGoodY = null; }

             if ( $Y != VOID ) { $LastGoodY = $Y; $LastGoodX = $X; }
             if ( $Y == VOID ) { $Y = null; }

             $LastX = $X; $LastY = $Y;
             $X = $X + $XStep;
            }
          }
         else
          {
           if ( $XDivs == 0 ) { $YStep = ($this->GraphAreaY2-$this->GraphAreaY1)/4; } else { $YStep = ($this->GraphAreaY2-$this->GraphAreaY1-$XMargin*2)/$XDivs; }
           $Y = $this->GraphAreaY1 + $XMargin; $LastX = null; $LastY = null;

           if ( !is_array($PosArray) ) { $Value = $PosArray; $PosArray = ""; $PosArray[0] = $Value; }
           $LastGoodY = null; $LastGoodX = null;
           foreach($PosArray as $Key => $X)
            {
             if ( $DisplayValues && $serie["Data"][$Key] != VOID )
              { $this->drawText($X+$DisplayOffset+$Weight,$Y,$this->scaleFormat($serie["Data"][$Key],$Mode,$format,$Unit),array("Angle"=>270,"R"=>$DisplayR,"G"=>$DisplayG,"B"=>$DisplayB,"Align"=>TEXT_ALIGN_BOTTOMMIDDLE)); }

             if ( $RecordImageMap && $X != VOID ) { $this->addToImageMap("CIRCLE",floor($X).",".floor($Y).",".$ImageMapPlotSize,$this->toHTMLColor($R,$G,$B),$serieDescription,$this->scaleFormat($serie["Data"][$Key],$Mode,$format,$Unit)); }

             if ( $X != VOID && $LastX != null && $LastY != null )
              $this->drawLine($LastX,$LastY,$X,$Y,array("R"=>$R,"G"=>$G,"B"=>$B,"Alpha"=>$Alpha,"Ticks"=>$Ticks,"Weight"=>$Weight));

             if ( $X != VOID && $LastX == null && $LastGoodY != null && !$BreakVoid )
              { $this->drawLine($LastGoodX,$LastGoodY,$X,$Y,$BreakSettings); $LastGoodY = null; }

             if ( $X != VOID ) { $LastGoodY = $Y; $LastGoodX = $X; }
             if ( $X == VOID ) { $X = null; }

             $LastX = $X; $LastY = $Y;
             $Y = $Y + $YStep;
            }
          }
        }
      }
    }

    /* Draw a line chart */
    function drawZoneChart($serieA,$serieB,$format=null)
    {
     $AxisID	= isset($format["AxisID"]) ? $format["AxisID"] : 0;
     $LineR	= isset($format["LineR"]) ? $format["LineR"] : 150;
     $LineG	= isset($format["LineG"]) ? $format["LineG"] : 150;
     $LineB	= isset($format["LineB"]) ? $format["LineB"] : 150;
     $LineAlpha	= isset($format["LineAlpha"]) ? $format["LineAlpha"] : 50;
     $LineTicks	= isset($format["LineTicks"]) ? $format["LineTicks"] : 1;
     $AreaR	= isset($format["AreaR"]) ? $format["AreaR"] : 150;
     $AreaG	= isset($format["AreaG"]) ? $format["AreaG"] : 150;
     $AreaB	= isset($format["AreaB"]) ? $format["AreaB"] : 150;
     $AreaAlpha	= isset($format["AreaAlpha"]) ? $format["AreaAlpha"] : 5;

     $this->LastChartLayout = CHART_LAST_LAYOUT_REGULAR;

     $Data	 = $this->DataSet->getData();
     if ( !isset($Data["Series"][$serieA]["Data"]) || !isset($Data["Series"][$serieB]["Data"]) ) { return 0; }
     $serieAData = $Data["Series"][$serieA]["Data"];
     $serieBData = $Data["Series"][$serieB]["Data"];

     list($XMargin,$XDivs) = $this->scaleGetXSettings();

     $Mode	= $Data["Axis"][$AxisID]["Display"];
     $format	= $Data["Axis"][$AxisID]["Format"];
     $Unit	= $Data["Axis"][$AxisID]["Unit"];

     $PosArrayA = $this->scaleComputeY($serieAData,array("AxisID"=>$AxisID));
     $PosArrayB = $this->scaleComputeY($serieBData,array("AxisID"=>$AxisID));
     if ( count($PosArrayA) != count($PosArrayB) ) { return 0; }

     if ( $Data["Orientation"] == SCALE_POS_LEFTRIGHT )
      {
       if ( $XDivs == 0 ) { $XStep = ($this->GraphAreaX2-$this->GraphAreaX1)/4; } else { $XStep = ($this->GraphAreaX2-$this->GraphAreaX1-$XMargin*2)/$XDivs; }
       $X = $this->GraphAreaX1 + $XMargin; $LastX = null; $LastY = null;

       $LastX = null; $LastY1 = null; $LastY2 = null;
       $BoundsA = ""; $BoundsB = "";
       foreach($PosArrayA as $Key => $Y1)
        {
         $Y2 = $PosArrayB[$Key];

         $BoundsA[] = $X; $BoundsA[] = $Y1;
         $BoundsB[] = $X; $BoundsB[] = $Y2;

         $LastX = $X;
         $LastY1 = $Y1; $LastY2 = $Y2;

         $X = $X + $XStep;
        }
       $Bounds = array_merge($BoundsA,$this->reversePlots($BoundsB));
       $this->drawPolygonChart($Bounds,array("R"=>$AreaR,"G"=>$AreaG,"B"=>$AreaB,"Alpha"=>$AreaAlpha));

       for($i=0;$i<=count($BoundsA)-4;$i=$i+2)
        {
         $this->drawLine($BoundsA[$i],$BoundsA[$i+1],$BoundsA[$i+2],$BoundsA[$i+3],array("R"=>$LineR,"G"=>$LineG,"B"=>$LineB,"Alpha"=>$LineAlpha,"Ticks"=>$LineTicks));
         $this->drawLine($BoundsB[$i],$BoundsB[$i+1],$BoundsB[$i+2],$BoundsB[$i+3],array("R"=>$LineR,"G"=>$LineG,"B"=>$LineB,"Alpha"=>$LineAlpha,"Ticks"=>$LineTicks));
        }
      }
     else
      {
       if ( $XDivs == 0 ) { $YStep = ($this->GraphAreaY2-$this->GraphAreaY1)/4; } else { $YStep = ($this->GraphAreaY2-$this->GraphAreaY1-$XMargin*2)/$XDivs; }
       $Y = $this->GraphAreaY1 + $XMargin; $LastX = null; $LastY = null;

       $LastY = null; $LastX1 = null; $LastX2 = null;
       $BoundsA = ""; $BoundsB = "";
       foreach($PosArrayA as $Key => $X1)
        {
         $X2 = $PosArrayB[$Key];

         $BoundsA[] = $X1; $BoundsA[] = $Y;
         $BoundsB[] = $X2; $BoundsB[] = $Y;

         $LastY = $Y;
         $LastX1 = $X1; $LastX2 = $X2;

         $Y = $Y + $YStep;
        }
       $Bounds = array_merge($BoundsA,$this->reversePlots($BoundsB));
       $this->drawPolygonChart($Bounds,array("R"=>$AreaR,"G"=>$AreaG,"B"=>$AreaB,"Alpha"=>$AreaAlpha));

       for($i=0;$i<=count($BoundsA)-4;$i=$i+2)
        {
         $this->drawLine($BoundsA[$i],$BoundsA[$i+1],$BoundsA[$i+2],$BoundsA[$i+3],array("R"=>$LineR,"G"=>$LineG,"B"=>$LineB,"Alpha"=>$LineAlpha,"Ticks"=>$LineTicks));
         $this->drawLine($BoundsB[$i],$BoundsB[$i+1],$BoundsB[$i+2],$BoundsB[$i+3],array("R"=>$LineR,"G"=>$LineG,"B"=>$LineB,"Alpha"=>$LineAlpha,"Ticks"=>$LineTicks));
        }
      }
    }

    /* Draw a step chart */
    function drawStepChart($format=null)
    {
     $BreakVoid		= isset($format["BreakVoid"]) ? $format["BreakVoid"] : false;
     $ReCenter		= isset($format["ReCenter"]) ? $format["ReCenter"] : true;
     $VoidTicks		= isset($format["VoidTicks"]) ? $format["VoidTicks"] : 4;
     $BreakR		= isset($format["BreakR"]) ? $format["BreakR"] : null;
     $BreakG		= isset($format["BreakG"]) ? $format["BreakG"] : null;
     $BreakB		= isset($format["BreakB"]) ? $format["BreakB"] : null;
     $DisplayValues	= isset($format["DisplayValues"]) ? $format["DisplayValues"] :false;
     $DisplayOffset	= isset($format["DisplayOffset"]) ? $format["DisplayOffset"] : 2;
     $DisplayColor	= isset($format["DisplayColor"]) ? $format["DisplayColor"] : DISPLAY_MANUAL;
     $DisplayR		= isset($format["DisplayR"]) ? $format["DisplayR"] : 0;
     $DisplayG		= isset($format["DisplayG"]) ? $format["DisplayG"] : 0;
     $DisplayB		= isset($format["DisplayB"]) ? $format["DisplayB"] : 0;
     $RecordImageMap	= isset($format["RecordImageMap"]) ? $format["RecordImageMap"] : false;
     $ImageMapPlotSize  = isset($format["ImageMapPlotSize"]) ? $format["ImageMapPlotSize"] : 5;

     $this->LastChartLayout = CHART_LAST_LAYOUT_REGULAR;

     $Data = $this->DataSet->getData();
     list($XMargin,$XDivs) = $this->scaleGetXSettings();
     foreach($Data["Series"] as $serie_name => $serie)
      {
       if ( $serie["isDrawable"] == true && $serie_name != $Data["Abscissa"] )
        {
         $R = $serie["Color"]["R"]; $G = $serie["Color"]["G"]; $B = $serie["Color"]["B"]; $Alpha = $serie["Color"]["Alpha"]; $Ticks = $serie["Ticks"]; $Weight = $serie["Weight"];

         if (isset($serie["Description"])) { $serieDescription = $serie["Description"]; } else { $serieDescription = $serie_name; }

         if ( $BreakR == null )
          $BreakSettings = array("R"=>$R,"G"=>$G,"B"=>$B,"Alpha"=>$Alpha,"Ticks"=>$VoidTicks,"Weight"=>$Weight);
         else
          $BreakSettings = array("R"=>$BreakR,"G"=>$BreakG,"B"=>$BreakB,"Alpha"=>$Alpha,"Ticks"=>$VoidTicks,"Weight"=>$Weight);

         if ( $DisplayColor == DISPLAY_AUTO ) { $DisplayR = $R; $DisplayG = $G; $DisplayB = $B; }

         $AxisID	= $serie["Axis"];
         $Mode		= $Data["Axis"][$AxisID]["Display"];
         $format	= $Data["Axis"][$AxisID]["Format"];
         $Unit		= $Data["Axis"][$AxisID]["Unit"];
         $Color		= array("R"=>$R,"G"=>$G,"B"=>$B,"Alpha"=>$Alpha,"Ticks"=>$Ticks,"Weight"=>$Weight);

         $PosArray = $this->scaleComputeY($serie["Data"],array("AxisID"=>$serie["Axis"]));

         $this->DataSet->Data["Series"][$serie_name]["XOffset"] = 0;

         if ( $Data["Orientation"] == SCALE_POS_LEFTRIGHT )
          {
           if ( $XDivs == 0 ) { $XStep = ($this->GraphAreaX2-$this->GraphAreaX1)/4; } else { $XStep = ($this->GraphAreaX2-$this->GraphAreaX1-$XMargin*2)/$XDivs; }
           $X = $this->GraphAreaX1 + $XMargin; $LastX = null; $LastY = null;

           if ( !is_array($PosArray) ) { $Value = $PosArray; $PosArray = ""; $PosArray[0] = $Value; }
           $LastGoodY = null; $LastGoodX = null; $Init = false;
           foreach($PosArray as $Key => $Y)
            {
             if ( $DisplayValues && $serie["Data"][$Key] != VOID )
              {
               if ( $Y <= $LastY ) { $Align = TEXT_ALIGN_BOTTOMMIDDLE; $Offset = $DisplayOffset; } else { $Align = TEXT_ALIGN_TOPMIDDLE; $Offset = -$DisplayOffset; }
               $this->drawText($X,$Y-$Offset-$Weight,$this->scaleFormat($serie["Data"][$Key],$Mode,$format,$Unit),array("R"=>$DisplayR,"G"=>$DisplayG,"B"=>$DisplayB,"Align"=>$Align));
              }

             if ( $Y != VOID && $LastX != null && $LastY != null )
              {
               $this->drawLine($LastX,$LastY,$X,$LastY,$Color);
               $this->drawLine($X,$LastY,$X,$Y,$Color);
               if ( $ReCenter && $X+$XStep < $this->GraphAreaX2 - $XMargin )
                {
                 $this->drawLine($X,$Y,$X+$XStep,$Y,$Color);
                 if ( $RecordImageMap ) { $this->addToImageMap("RECT",floor($X-$ImageMapPlotSize).",".floor($Y-$ImageMapPlotSize).",".floor($X+$XStep+$ImageMapPlotSize).",".floor($Y+$ImageMapPlotSize),$this->toHTMLColor($R,$G,$B),$serieDescription,$this->scaleFormat($serie["Data"][$Key],$Mode,$format,$Unit)); }
                }
               else
                { if ( $RecordImageMap ) { $this->addToImageMap("RECT",floor($LastX-$ImageMapPlotSize).",".floor($LastY-$ImageMapPlotSize).",".floor($X+$ImageMapPlotSize).",".floor($LastY+$ImageMapPlotSize),$this->toHTMLColor($R,$G,$B),$serieDescription,$this->scaleFormat($serie["Data"][$Key],$Mode,$format,$Unit)); } }
              }

             if ( $Y != VOID && $LastY == null && $LastGoodY != null && !$BreakVoid )
              {
               if ( $ReCenter )
                {
                 $this->drawLine($LastGoodX+$XStep,$LastGoodY,$X,$LastGoodY,$BreakSettings);
                 if ( $RecordImageMap ) { $this->addToImageMap("RECT",floor($LastGoodX+$XStep-$ImageMapPlotSize).",".floor($LastGoodY-$ImageMapPlotSize).",".floor($X+$ImageMapPlotSize).",".floor($LastGoodY+$ImageMapPlotSize),$this->toHTMLColor($R,$G,$B),$serieDescription,$this->scaleFormat($serie["Data"][$Key],$Mode,$format,$Unit)); }
                }
               else
                {
                 $this->drawLine($LastGoodX,$LastGoodY,$X,$LastGoodY,$BreakSettings);
                 if ( $RecordImageMap ) { $this->addToImageMap("RECT",floor($LastGoodX-$ImageMapPlotSize).",".floor($LastGoodY-$ImageMapPlotSize).",".floor($X+$ImageMapPlotSize).",".floor($LastGoodY+$ImageMapPlotSize),$this->toHTMLColor($R,$G,$B),$serieDescription,$this->scaleFormat($serie["Data"][$Key],$Mode,$format,$Unit)); }
                }

               $this->drawLine($X,$LastGoodY,$X,$Y,$BreakSettings);
               $LastGoodY = null;
              }
             elseif( !$BreakVoid && $LastGoodY == null && $Y != VOID )
              {
              $this->drawLine($this->GraphAreaX1 + $XMargin,$Y,$X,$Y,$BreakSettings);
               if ( $RecordImageMap ) { $this->addToImageMap("RECT",floor($this->GraphAreaX1+$XMargin-$ImageMapPlotSize).",".floor($Y-$ImageMapPlotSize).",".floor($X+$ImageMapPlotSize).",".floor($Y+$ImageMapPlotSize),$this->toHTMLColor($R,$G,$B),$serieDescription,$this->scaleFormat($serie["Data"][$Key],$Mode,$format,$Unit)); }
              }

             if ( $Y != VOID ) { $LastGoodY = $Y; $LastGoodX = $X; }
             if ( $Y == VOID ) { $Y = null; }

             if ( !$Init && $ReCenter ) { $X = $X - $XStep/2; $Init = true; }
             $LastX = $X; $LastY = $Y;
             if ( $LastX < $this->GraphAreaX1 + $XMargin ) { $LastX = $this->GraphAreaX1 + $XMargin; }
             $X = $X + $XStep;
            }
           if ( $ReCenter )
            {
             $this->drawLine($LastX,$LastY,$this->GraphAreaX2 - $XMargin,$LastY,$Color);
             if ( $RecordImageMap ) { $this->addToImageMap("RECT",floor($LastX-$ImageMapPlotSize).",".floor($LastY-$ImageMapPlotSize).",".floor($this->GraphAreaX2-$XMargin+$ImageMapPlotSize).",".floor($LastY+$ImageMapPlotSize),$this->toHTMLColor($R,$G,$B),$serieDescription,$this->scaleFormat($serie["Data"][$Key],$Mode,$format,$Unit)); }
            }
          }
         else
          {
           if ( $XDivs == 0 ) { $YStep = ($this->GraphAreaY2-$this->GraphAreaY1)/4; } else { $YStep = ($this->GraphAreaY2-$this->GraphAreaY1-$XMargin*2)/$XDivs; }
           $Y = $this->GraphAreaY1 + $XMargin; $LastX = null; $LastY = null;

           if ( !is_array($PosArray) ) { $Value = $PosArray; $PosArray = ""; $PosArray[0] = $Value; }
           $LastGoodY = null; $LastGoodX = null; $Init = false;
           foreach($PosArray as $Key => $X)
            {
             if ( $DisplayValues && $serie["Data"][$Key] != VOID )
              {
               if ( $X >= $LastX ) { $Align = TEXT_ALIGN_MIDDLELEFT; $Offset = $DisplayOffset; } else { $Align = TEXT_ALIGN_MIDDLERIGHT; $Offset = -$DisplayOffset; }
               $this->drawText($X+$Offset+$Weight,$Y,$this->scaleFormat($serie["Data"][$Key],$Mode,$format,$Unit),array("R"=>$DisplayR,"G"=>$DisplayG,"B"=>$DisplayB,"Align"=>$Align));
              }

             if ( $X != VOID && $LastX != null && $LastY != null )
              {
               $this->drawLine($LastX,$LastY,$LastX,$Y,$Color);
               $this->drawLine($LastX,$Y,$X,$Y,$Color);

               if ( $RecordImageMap ) { $this->addToImageMap("RECT",floor($LastX-$ImageMapPlotSize).",".floor($LastY-$ImageMapPlotSize).",".floor($LastX+$XStep+$ImageMapPlotSize).",".floor($Y+$ImageMapPlotSize),$this->toHTMLColor($R,$G,$B),$serieDescription,$this->scaleFormat($serie["Data"][$Key],$Mode,$format,$Unit)); }
              }

             if ( $X != VOID && $LastX == null && $LastGoodY != null && !$BreakVoid )
              {
               $this->drawLine($LastGoodX,$LastGoodY,$LastGoodX,$LastGoodY+$YStep,$Color);
               if ( $RecordImageMap ) { $this->addToImageMap("RECT",floor($LastGoodX-$ImageMapPlotSize).",".floor($LastGoodY-$ImageMapPlotSize).",".floor($LastGoodX+$ImageMapPlotSize).",".floor($LastGoodY+$YStep+$ImageMapPlotSize),$this->toHTMLColor($R,$G,$B),$serieDescription,$this->scaleFormat($serie["Data"][$Key],$Mode,$format,$Unit)); }

               $this->drawLine($LastGoodX,$LastGoodY+$YStep,$LastGoodX,$Y,$BreakSettings);
               if ( $RecordImageMap ) { $this->addToImageMap("RECT",floor($LastGoodX-$ImageMapPlotSize).",".floor($LastGoodY+$YStep-$ImageMapPlotSize).",".floor($LastGoodX+$ImageMapPlotSize).",".floor($YStep+$ImageMapPlotSize),$this->toHTMLColor($R,$G,$B),$serieDescription,$this->scaleFormat($serie["Data"][$Key],$Mode,$format,$Unit)); }

               $this->drawLine($LastGoodX,$Y,$X,$Y,$BreakSettings);
               $LastGoodY = null;
              }
             elseif ( $X != VOID && $LastGoodY == null && !$BreakVoid )
              {
               $this->drawLine($X,$this->GraphAreaY1 + $XMargin,$X,$Y,$BreakSettings);
               if ( $RecordImageMap ) { $this->addToImageMap("RECT",floor($X-$ImageMapPlotSize).",".floor($this->GraphAreaY1+$XMargin-$ImageMapPlotSize).",".floor($X+$ImageMapPlotSize).",".floor($Y+$ImageMapPlotSize),$this->toHTMLColor($R,$G,$B),$serieDescription,$this->scaleFormat($serie["Data"][$Key],$Mode,$format,$Unit)); }
              }

             if ( $X != VOID ) { $LastGoodY = $Y; $LastGoodX = $X; }
             if ( $X == VOID ) { $X = null; }

             if ( !$Init && $ReCenter ) { $Y = $Y - $YStep/2; $Init = true; }
             $LastX = $X; $LastY = $Y;
             if ( $LastY < $this->GraphAreaY1 + $XMargin ) { $LastY = $this->GraphAreaY1 + $XMargin; }
             $Y = $Y + $YStep;
            }
           if ( $ReCenter )
            {
             $this->drawLine($LastX,$LastY,$LastX,$this->GraphAreaY2 - $XMargin,$Color);
             if ( $RecordImageMap ) { $this->addToImageMap("RECT",floor($LastX-$ImageMapPlotSize).",".floor($LastY-$ImageMapPlotSize).",".floor($LastX+$ImageMapPlotSize).",".floor($this->GraphAreaY2-$XMargin+$ImageMapPlotSize),$this->toHTMLColor($R,$G,$B),$serieDescription,$this->scaleFormat($serie["Data"][$Key],$Mode,$format,$Unit)); }
            }
          }
        }
      }
    }

    /* Draw a step chart */
    function drawFilledStepChart($format=null)
    {
     $ReCenter		= isset($format["ReCenter"]) ? $format["ReCenter"] : true;
     $DisplayValues	= isset($format["DisplayValues"]) ? $format["DisplayValues"] :false;
     $DisplayOffset	= isset($format["DisplayOffset"]) ? $format["DisplayOffset"] : 2;
     $DisplayColor	= isset($format["DisplayColor"]) ? $format["DisplayColor"] : DISPLAY_MANUAL;
     $ForceTransparency	= isset($format["ForceTransparency"]) ? $format["ForceTransparency"] : null;
     $DisplayR		= isset($format["DisplayR"]) ? $format["DisplayR"] : 0;
     $DisplayG		= isset($format["DisplayG"]) ? $format["DisplayG"] : 0;
     $DisplayB		= isset($format["DisplayB"]) ? $format["DisplayB"] : 0;
     $AroundZero	= isset($format["AroundZero"]) ? $format["AroundZero"] : true;

     $this->LastChartLayout = CHART_LAST_LAYOUT_REGULAR;

     $Data = $this->DataSet->getData();
     list($XMargin,$XDivs) = $this->scaleGetXSettings();
     foreach($Data["Series"] as $serie_name => $serie)
      {
       if ( $serie["isDrawable"] == true && $serie_name != $Data["Abscissa"] )
        {
         $R = $serie["Color"]["R"]; $G = $serie["Color"]["G"]; $B = $serie["Color"]["B"]; $Alpha = $serie["Color"]["Alpha"]; $Ticks = $serie["Ticks"]; $Weight = $serie["Weight"];

         if ( $DisplayColor == DISPLAY_AUTO ) { $DisplayR = $R; $DisplayG = $G; $DisplayB = $B; }

         $AxisID	= $serie["Axis"];
         $Mode		= $Data["Axis"][$AxisID]["Display"];
         $format	= $Data["Axis"][$AxisID]["Format"];
         $Unit		= $Data["Axis"][$AxisID]["Unit"];

         $Color		= array("R"=>$R,"G"=>$G,"B"=>$B);
         if ( $ForceTransparency != null ) { $Color["Alpha"] = $ForceTransparency; } else { $Color["Alpha"] = $Alpha; }

         $PosArray = $this->scaleComputeY($serie["Data"],array("AxisID"=>$serie["Axis"]));
         $YZero    = $this->scaleComputeY(0,array("AxisID"=>$serie["Axis"]));

         $this->DataSet->Data["Series"][$serie_name]["XOffset"] = 0;

         if ( $Data["Orientation"] == SCALE_POS_LEFTRIGHT )
          {
           if ( $YZero > $this->GraphAreaY2-1 ) { $YZero = $this->GraphAreaY2-1; }
           if ( $YZero < $this->GraphAreaY1+1 ) { $YZero = $this->GraphAreaY1+1; }

           if ( $XDivs == 0 ) { $XStep = ($this->GraphAreaX2-$this->GraphAreaX1)/4; } else { $XStep = ($this->GraphAreaX2-$this->GraphAreaX1-$XMargin*2)/$XDivs; }
           $X = $this->GraphAreaX1 + $XMargin; $LastX = null; $LastY = null;

           if ( !$AroundZero ) { $YZero = $this->GraphAreaY2-1; }

           if ( !is_array($PosArray) ) { $Value = $PosArray; $PosArray = ""; $PosArray[0] = $Value; }
           $LastGoodY = null; $LastGoodX = null; $points = ""; $Init = false;
           foreach($PosArray as $Key => $Y)
            {
             if ( $Y == VOID && $LastX != null && $LastY != null && $points !="" )
              {
               $points[] = $LastX; $points[] = $LastY;
               $points[] = $X; $points[] = $LastY;
               $points[] = $X; $points[] = $YZero;
               $this->drawPolygon($points,$Color);
               $points = "";
              }

             if ( $Y != VOID && $LastX != null && $LastY != null )
              {
               if ( $points == "") { $points[] = $LastX; $points[] = $YZero; }
               $points[] = $LastX; $points[] = $LastY;
               $points[] = $X; $points[] = $LastY;
               $points[] = $X; $points[] = $Y;
              }

             if ( $Y != VOID ) { $LastGoodY = $Y; $LastGoodX = $X; }
             if ( $Y == VOID ) { $Y = null; }

             if ( !$Init && $ReCenter ) { $X = $X - $XStep/2; $Init = true; }
             $LastX = $X; $LastY = $Y;
             if ( $LastX < $this->GraphAreaX1 + $XMargin ) { $LastX = $this->GraphAreaX1 + $XMargin; }
             $X = $X + $XStep;
            }

           if ( $ReCenter )
            {
             $points[] = $LastX+$XStep/2; $points[] = $LastY;
             $points[] = $LastX+$XStep/2; $points[] = $YZero;
            }
           else
            { $points[] = $LastX; $points[] = $YZero; }

           $this->drawPolygon($points,$Color);
          }
         else
          {
           if ( $YZero < $this->GraphAreaX1+1 ) { $YZero = $this->GraphAreaX1+1; }
           if ( $YZero > $this->GraphAreaX2-1 ) { $YZero = $this->GraphAreaX2-1; }

           if ( $XDivs == 0 ) { $YStep = ($this->GraphAreaY2-$this->GraphAreaY1)/4; } else { $YStep = ($this->GraphAreaY2-$this->GraphAreaY1-$XMargin*2)/$XDivs; }
           $Y = $this->GraphAreaY1 + $XMargin; $LastX = null; $LastY = null;

           if ( !is_array($PosArray) ) { $Value = $PosArray; $PosArray = ""; $PosArray[0] = $Value; }
           $LastGoodY = null; $LastGoodX = null; $points = "";
           foreach($PosArray as $Key => $X)
            {
             if ( $X == VOID && $LastX != null && $LastY != null && $points !="" )
              {
               $points[] = $LastX; $points[] = $LastY;
               $points[] = $LastX; $points[] = $Y;
               $points[] = $YZero; $points[] = $Y;
               $this->drawPolygon($points,$Color);
               $points = "";
              }

             if ( $X != VOID && $LastX != null && $LastY != null )
              {
               if ( $points == "") { $points[] = $YZero; $points[] = $LastY; }
               $points[] = $LastX; $points[] = $LastY;
               $points[] = $LastX; $points[] = $Y;
               $points[] = $X; $points[] = $Y;
              }

             if ( $X != VOID ) { $LastGoodY = $Y; $LastGoodX = $X; }
             if ( $X == VOID ) { $X = null; }

             if ( $LastX == null && $ReCenter ) { $Y = $Y - $YStep/2; }
             $LastX = $X; $LastY = $Y;
             if ( $LastY < $this->GraphAreaY1 + $XMargin ) { $LastY = $this->GraphAreaY1 + $XMargin; }
             $Y = $Y + $YStep;
            }

           if ( $ReCenter )
            {
             $points[] = $LastX; $points[] = $LastY+$YStep/2;
             $points[] = $YZero; $points[] = $LastY+$YStep/2;
            }
           else
            { $points[] = $YZero; $points[] = $LastY; }

           $this->drawPolygon($points,$Color);
          }
        }
      }
    }

    /* Draw an area chart */
    function drawAreaChart($format=null)
    {
     $DisplayValues	= isset($format["DisplayValues"]) ? $format["DisplayValues"] : false;
     $DisplayOffset	= isset($format["DisplayOffset"]) ? $format["DisplayOffset"] : 2;
     $DisplayColor	= isset($format["DisplayColor"]) ? $format["DisplayColor"] : DISPLAY_MANUAL;
     $DisplayR		= isset($format["DisplayR"]) ? $format["DisplayR"] : 0;
     $DisplayG		= isset($format["DisplayG"]) ? $format["DisplayG"] : 0;
     $DisplayB		= isset($format["DisplayB"]) ? $format["DisplayB"] : 0;
     $ForceTransparency	= isset($format["ForceTransparency"]) ? $format["ForceTransparency"] : 25;
     $AroundZero	= isset($format["AroundZero"]) ? $format["AroundZero"] : true;
     $Threshold		= isset($format["Threshold"]) ? $format["Threshold"] : null;

     $this->LastChartLayout = CHART_LAST_LAYOUT_REGULAR;

     $Data  = $this->DataSet->getData();
     list($XMargin,$XDivs) = $this->scaleGetXSettings();

     foreach($Data["Series"] as $serie_name => $serie)
      {
       if ( $serie["isDrawable"] == true && $serie_name != $Data["Abscissa"] )
        {
         $R = $serie["Color"]["R"]; $G = $serie["Color"]["G"]; $B = $serie["Color"]["B"]; $Alpha = $serie["Color"]["Alpha"]; $Ticks = $serie["Ticks"];
         if ( $DisplayColor == DISPLAY_AUTO ) { $DisplayR = $R; $DisplayG = $G; $DisplayB = $B; }

         $AxisID	= $serie["Axis"];
         $Mode		= $Data["Axis"][$AxisID]["Display"];
         $format	= $Data["Axis"][$AxisID]["Format"];
         $Unit		= $Data["Axis"][$AxisID]["Unit"];

         $PosArray = $this->scaleComputeY($serie["Data"],array("AxisID"=>$serie["Axis"]));
         $YZero    = $this->scaleComputeY(0,array("AxisID"=>$serie["Axis"]));

         if ( $Threshold != null )
          {
           foreach($Threshold as $Key => $Params)
            {
             $Threshold[$Key]["MinX"] = $this->scaleComputeY($Params["Min"],array("AxisID"=>$serie["Axis"]));
             $Threshold[$Key]["MaxX"] = $this->scaleComputeY($Params["Max"],array("AxisID"=>$serie["Axis"]));
            }
          }

         $this->DataSet->Data["Series"][$serie_name]["XOffset"] = 0;

         if ( $Data["Orientation"] == SCALE_POS_LEFTRIGHT )
          {
           if ( $YZero > $this->GraphAreaY2-1 ) { $YZero = $this->GraphAreaY2-1; }

           $Areas = ""; $AreaID = 0;
           $Areas[$AreaID][] = $this->GraphAreaX1 + $XMargin;
           if ( $AroundZero ) { $Areas[$AreaID][] = $YZero; } else { $Areas[$AreaID][] = $this->GraphAreaY2-1; }

           if ( $XDivs == 0 ) { $XStep = ($this->GraphAreaX2-$this->GraphAreaX1)/4; } else { $XStep = ($this->GraphAreaX2-$this->GraphAreaX1-$XMargin*2)/$XDivs; }
           $X = $this->GraphAreaX1 + $XMargin; $LastX = null; $LastY = null;

           if ( !is_array($PosArray) ) { $Value = $PosArray; $PosArray = ""; $PosArray[0] = $Value; }
           foreach($PosArray as $Key => $Y)
            {
             if ( $DisplayValues && $serie["Data"][$Key] != VOID )
              {
               if ( $serie["Data"][$Key] > 0 ) { $Align = TEXT_ALIGN_BOTTOMMIDDLE; $Offset = $DisplayOffset; } else { $Align = TEXT_ALIGN_TOPMIDDLE; $Offset = -$DisplayOffset; }
               $this->drawText($X,$Y-$Offset,$this->scaleFormat($serie["Data"][$Key],$Mode,$format,$Unit),array("R"=>$DisplayR,"G"=>$DisplayG,"B"=>$DisplayB,"Align"=>$Align));
              }

             if ( $Y == VOID && isset($Areas[$AreaID]) )
              {
               if($LastX == null)
                { $Areas[$AreaID][] = $X; }
               else
                { $Areas[$AreaID][] = $LastX; }

               if ( $AroundZero ) { $Areas[$AreaID][] = $YZero; } else { $Areas[$AreaID][] = $this->GraphAreaY2-1; }
               $AreaID++;
              }
             elseif ($Y != VOID)
              {
               if ( !isset($Areas[$AreaID]) )
                {
                 $Areas[$AreaID][] = $X;
                 if ( $AroundZero ) { $Areas[$AreaID][] = $YZero; } else { $Areas[$AreaID][] = $this->GraphAreaY2-1; }
                }

               $Areas[$AreaID][] = $X;
               $Areas[$AreaID][] = $Y;
              }

             $LastX = $X;
             $X = $X + $XStep;
            }
           $Areas[$AreaID][] = $LastX;
           if ( $AroundZero ) { $Areas[$AreaID][] = $YZero; } else { $Areas[$AreaID][] = $this->GraphAreaY2-1; }

           /* Handle shadows in the areas */
           if ( $this->Shadow )
            {
             $ShadowArea = "";
             foreach($Areas as $Key => $points)
              {
               $ShadowArea[$Key] = "";
               foreach($points as $Key2 => $Value)
                {
                 if ( $Key2 % 2 == 0 )
                  { $ShadowArea[$Key][] = $Value + $this->ShadowX; }
                 else
                  { $ShadowArea[$Key][] = $Value + $this->ShadowY; }
                }
              }

             foreach($ShadowArea as $Key => $points)
              $this->drawPolygonChart($points,array("R"=>$this->ShadowR,"G"=>$this->ShadowG,"B"=>$this->ShadowB,"Alpha"=>$this->Shadowa));
            }

           $Alpha = $ForceTransparency != null ? $ForceTransparency : $Alpha;
           $Color = array("R"=>$R,"G"=>$G,"B"=>$B,"Alpha"=>$Alpha,"Threshold"=>$Threshold);

           foreach($Areas as $Key => $points)
            $this->drawPolygonChart($points,$Color);
          }
         else
          {
           if ( $YZero < $this->GraphAreaX1+1 ) { $YZero = $this->GraphAreaX1+1; }
           if ( $YZero > $this->GraphAreaX2-1 ) { $YZero = $this->GraphAreaX2-1; }

           $Areas = ""; $AreaID = 0;
           if ( $AroundZero ) { $Areas[$AreaID][] = $YZero; } else { $Areas[$AreaID][] = $this->GraphAreaX1+1; }
           $Areas[$AreaID][] = $this->GraphAreaY1 + $XMargin;

           if ( $XDivs == 0 ) { $YStep = ($this->GraphAreaY2-$this->GraphAreaY1)/4; } else { $YStep = ($this->GraphAreaY2-$this->GraphAreaY1-$XMargin*2)/$XDivs; }
           $Y     = $this->GraphAreaY1 + $XMargin; $LastX = null; $LastY = null;

           if ( !is_array($PosArray) ) { $Value = $PosArray; $PosArray = ""; $PosArray[0] = $Value; }
           foreach($PosArray as $Key => $X)
            {
             if ( $DisplayValues && $serie["Data"][$Key] != VOID )
              {
               if ( $serie["Data"][$Key] > 0 ) { $Align = TEXT_ALIGN_BOTTOMMIDDLE; $Offset = $DisplayOffset; } else { $Align = TEXT_ALIGN_TOPMIDDLE; $Offset = -$DisplayOffset; }
               $this->drawText($X+$Offset,$Y,$this->scaleFormat($serie["Data"][$Key],$Mode,$format,$Unit),array("Angle"=>270,"R"=>$DisplayR,"G"=>$DisplayG,"B"=>$DisplayB,"Align"=>$Align));
              }

             if ( $X == VOID && isset($Areas[$AreaID]) )
              {
               if ( $AroundZero ) { $Areas[$AreaID][] = $YZero; } else { $Areas[$AreaID][] = $this->GraphAreaX1+1; }

               if($LastY == null)
                { $Areas[$AreaID][] = $Y; }
               else
                { $Areas[$AreaID][] = $LastY; }

               $AreaID++;
              }
             elseif ($X != VOID)
              {
               if ( !isset($Areas[$AreaID]) )
                {
                 if ( $AroundZero ) { $Areas[$AreaID][] = $YZero; } else { $Areas[$AreaID][] = $this->GraphAreaX1+1; }
                 $Areas[$AreaID][] = $Y;
                }

               $Areas[$AreaID][] = $X;
               $Areas[$AreaID][] = $Y;
              }

             $LastX = $X; $LastY = $Y;
             $Y = $Y + $YStep;
            }
           if ( $AroundZero ) { $Areas[$AreaID][] = $YZero; } else { $Areas[$AreaID][] = $this->GraphAreaX1+1; }
           $Areas[$AreaID][] = $LastY;

           /* Handle shadows in the areas */
           if ( $this->Shadow )
            {
             $ShadowArea = "";
             foreach($Areas as $Key => $points)
              {
               $ShadowArea[$Key] = "";
               foreach($points as $Key2 => $Value)
                {
                 if ( $Key2 % 2 == 0 )
                  { $ShadowArea[$Key][] = $Value + $this->ShadowX; }
                 else
                  { $ShadowArea[$Key][] = $Value + $this->ShadowY; }
                }
              }

             foreach($ShadowArea as $Key => $points)
              $this->drawPolygonChart($points,array("R"=>$this->ShadowR,"G"=>$this->ShadowG,"B"=>$this->ShadowB,"Alpha"=>$this->Shadowa));
            }

           $Alpha = $ForceTransparency != null ? $ForceTransparency : $Alpha;
           $Color = array("R"=>$R,"G"=>$G,"B"=>$B,"Alpha"=>$Alpha,"Threshold"=>$Threshold);

           foreach($Areas as $Key => $points)
            $this->drawPolygonChart($points,$Color);
          }
        }
      }
    }


    /* Draw a bar chart */
    function drawBarChart($format=null)
    {
     $Floating0Serie	= isset($format["Floating0Serie"]) ? $format["Floating0Serie"] : null;
     $Floating0Value	= isset($format["Floating0Value"]) ? $format["Floating0Value"] : null;
     $Draw0Line		= isset($format["Draw0Line"]) ? $format["Draw0Line"] : false;
     $DisplayValues	= isset($format["DisplayValues"]) ? $format["DisplayValues"] : false;
     $DisplayOrientation = isset($format["DisplayOrientation"]) ? $format["DisplayOrientation"] : ORIENTATION_HORIZONTAL;
     $DisplayOffset	= isset($format["DisplayOffset"]) ? $format["DisplayOffset"] : 2;
     $DisplayColor	= isset($format["DisplayColor"]) ? $format["DisplayColor"] : DISPLAY_MANUAL;
     $DisplayFont	= isset($format["DisplaySize"]) ? $format["DisplaySize"] : $this->FontName;
     $DisplaySize	= isset($format["DisplaySize"]) ? $format["DisplaySize"] : $this->FontSize;
     $DisplayPos	= isset($format["DisplayPos"]) ? $format["DisplayPos"] : LABEL_POS_OUTSIDE;
     $DisplayShadow	= isset($format["DisplayShadow"]) ? $format["DisplayShadow"] : true;
     $DisplayR		= isset($format["DisplayR"]) ? $format["DisplayR"] : 0;
     $DisplayG		= isset($format["DisplayG"]) ? $format["DisplayG"] : 0;
     $DisplayB		= isset($format["DisplayB"]) ? $format["DisplayB"] : 0;
     $AroundZero	= isset($format["AroundZero"]) ? $format["AroundZero"] : true;
     $Interleave	= isset($format["Interleave"]) ? $format["Interleave"] : .5;
     $Rounded		= isset($format["Rounded"]) ? $format["Rounded"] : false;
     $RoundRadius	= isset($format["RoundRadius"]) ? $format["RoundRadius"] : 4;
     $Surrounding	= isset($format["Surrounding"]) ? $format["Surrounding"] : null;
     $BorderR		= isset($format["BorderR"]) ? $format["BorderR"] : -1;
     $BorderG		= isset($format["BorderG"]) ? $format["BorderG"] : -1;
     $BorderB		= isset($format["BorderB"]) ? $format["BorderB"] : -1;
     $Gradient		= isset($format["Gradient"]) ? $format["Gradient"] : false;
     $GradientMode	= isset($format["GradientMode"]) ? $format["GradientMode"] : GRADIENT_SIMPLE;
     $GradientAlpha	= isset($format["GradientAlpha"]) ? $format["GradientAlpha"] : 20;
     $GradientStartR	= isset($format["GradientStartR"]) ? $format["GradientStartR"] : 255;
     $GradientStartG	= isset($format["GradientStartG"]) ? $format["GradientStartG"] : 255;
     $GradientStartB	= isset($format["GradientStartB"]) ? $format["GradientStartB"] : 255;
     $GradientEndR	= isset($format["GradientEndR"]) ? $format["GradientEndR"] : 0;
     $GradientEndG	= isset($format["GradientEndG"]) ? $format["GradientEndG"] : 0;
     $GradientEndB	= isset($format["GradientEndB"]) ? $format["GradientEndB"] : 0;
     $TxtMargin		= isset($format["TxtMargin"]) ? $format["TxtMargin"] : 6;
     $OverrideColors	= isset($format["OverrideColors"]) ? $format["OverrideColors"] : null;
     $OverrideSurrounding = isset($format["OverrideSurrounding"]) ? $format["OverrideSurrounding"] : 30;
     $InnerSurrounding	= isset($format["InnerSurrounding"]) ? $format["InnerSurrounding"] : null;
     $InnerBorderR	= isset($format["InnerBorderR"]) ? $format["InnerBorderR"] : -1;
     $InnerBorderG	= isset($format["InnerBorderG"]) ? $format["InnerBorderG"] : -1;
     $InnerBorderB	= isset($format["InnerBorderB"]) ? $format["InnerBorderB"] : -1;
     $RecordImageMap	= isset($format["RecordImageMap"]) ? $format["RecordImageMap"] : false;

     $this->LastChartLayout = CHART_LAST_LAYOUT_REGULAR;

     $Data = $this->DataSet->getData();
     list($XMargin,$XDivs) = $this->scaleGetXSettings();

     if ( $OverrideColors != null )
      {
       $OverrideColors = $this->validatePalette($OverrideColors,$OverrideSurrounding);
       $this->DataSet->saveExtendedData("Palette",$OverrideColors);
      }

     $RestoreShadow = $this->Shadow;

     $seriesCount  = $this->countDrawableSeries();
     $CurrentSerie = 0;
     foreach($Data["Series"] as $serie_name => $serie)
      {
       if ( $serie["isDrawable"] == true && $serie_name != $Data["Abscissa"] )
        {
         $R = $serie["Color"]["R"]; $G = $serie["Color"]["G"]; $B = $serie["Color"]["B"]; $Alpha = $serie["Color"]["Alpha"]; $Ticks = $serie["Ticks"];
         if ( $DisplayColor == DISPLAY_AUTO ) { $DisplayR = $R; $DisplayG = $G; $DisplayB = $B; }
         if ( $Surrounding != null ) { $BorderR = $R+$Surrounding; $BorderG = $G+$Surrounding; $BorderB = $B+$Surrounding; }
         if ( $InnerSurrounding != null ) { $InnerBorderR = $R+$InnerSurrounding; $InnerBorderG = $G+$InnerSurrounding; $InnerBorderB = $B+$InnerSurrounding; }
         if ( $InnerBorderR == -1 ) { $InnerColor = null; } else { $InnerColor = array("R"=>$InnerBorderR,"G"=>$InnerBorderG,"B"=>$InnerBorderB); }
         $Color = array("R"=>$R,"G"=>$G,"B"=>$B,"Alpha"=>$Alpha,"BorderR"=>$BorderR,"BorderG"=>$BorderG,"BorderB"=>$BorderB);

         $AxisID	= $serie["Axis"];
         $Mode		= $Data["Axis"][$AxisID]["Display"];
         $format	= $Data["Axis"][$AxisID]["Format"];
         $Unit		= $Data["Axis"][$AxisID]["Unit"];

         if (isset($serie["Description"])) { $serieDescription = $serie["Description"]; } else { $serieDescription = $serie_name; }

         $PosArray = $this->scaleComputeY($serie["Data"],array("AxisID"=>$serie["Axis"]));

         if ( $Floating0Value != null )
          { $YZero = $this->scaleComputeY($Floating0Value,array("AxisID"=>$serie["Axis"])); }
         else
          { $YZero = $this->scaleComputeY(0,array("AxisID"=>$serie["Axis"])); }

         if ( $Data["Orientation"] == SCALE_POS_LEFTRIGHT )
          {
           if ( $YZero > $this->GraphAreaY2-1 ) { $YZero = $this->GraphAreaY2-1; }
           if ( $YZero < $this->GraphAreaY1+1 ) { $YZero = $this->GraphAreaY1+1; }

           if ( $XDivs == 0 ) { $XStep = 0; } else { $XStep = ($this->GraphAreaX2-$this->GraphAreaX1-$XMargin*2)/$XDivs; }
           $X = $this->GraphAreaX1 + $XMargin;

           if ( $AroundZero ) { $Y1 = $YZero; } else { $Y1 = $this->GraphAreaY2-1; }
           if ( $XDivs == 0 ) { $XSize = ($this->GraphAreaX2-$this->GraphAreaX1)/($seriesCount+$Interleave); } else { $XSize   = ($XStep / ($seriesCount+$Interleave) ); }

           $XOffset = -($XSize*$seriesCount)/2 + $CurrentSerie * $XSize;
           if ( $X + $XOffset <= $this->GraphAreaX1 ) { $XOffset = $this->GraphAreaX1 - $X + 1 ; }

           $this->DataSet->Data["Series"][$serie_name]["XOffset"] = $XOffset + $XSize / 2;

           if ( $Rounded || $BorderR != -1) { $XSpace = 1; } else { $XSpace = 0; }

           if ( !is_array($PosArray) ) { $Value = $PosArray; $PosArray = ""; $PosArray[0] = $Value; }

           $ID = 0;
           foreach($PosArray as $Key => $Y2)
            {
             if ( $Floating0Serie != null )
              {
               if ( isset($Data["Series"][$Floating0Serie]["Data"][$Key]) )
                { $Value = $Data["Series"][$Floating0Serie]["Data"][$Key]; }
               else
                { $Value = 0; }

               $YZero = $this->scaleComputeY($Value,array("AxisID"=>$serie["Axis"]));
               if ( $YZero > $this->GraphAreaY2-1 ) { $YZero = $this->GraphAreaY2-1; }
               if ( $YZero < $this->GraphAreaY1+1 ) { $YZero = $this->GraphAreaY1+1; }

               if ( $AroundZero ) { $Y1 = $YZero; } else { $Y1 = $this->GraphAreaY2-1; }
              }

             if ( $OverrideColors != null )
              { if ( isset($OverrideColors[$ID]) ) { $Color = array("R"=>$OverrideColors[$ID]["R"],"G"=>$OverrideColors[$ID]["G"],"B"=>$OverrideColors[$ID]["B"],"Alpha"=>$OverrideColors[$ID]["Alpha"],"BorderR"=>$OverrideColors[$ID]["BorderR"],"BorderG"=>$OverrideColors[$ID]["BorderG"],"BorderB"=>$OverrideColors[$ID]["BorderB"]); } else { $Color = $this->getRandomColor(); } }

             if ( $Y2 != VOID )
              {
               $BarHeight = $Y1 - $Y2;

               if ( $serie["Data"][$Key] == 0 )
                {
                 $this->drawLine($X+$XOffset+$XSpace,$Y1,$X+$XOffset+$XSize-$XSpace,$Y1,$Color);
                 if ( $RecordImageMap ) { $this->addToImageMap("RECT",floor($X+$XOffset+$XSpace).",".floor($Y1-1).",".floor($X+$XOffset+$XSize-$XSpace).",".floor($Y1+1),$this->toHTMLColor($R,$G,$B),$serieDescription,$this->scaleFormat($serie["Data"][$Key],$Mode,$format,$Unit)); }
                }
               else
                {
                 if ( $RecordImageMap ) { $this->addToImageMap("RECT",floor($X+$XOffset+$XSpace).",".floor($Y1).",".floor($X+$XOffset+$XSize-$XSpace).",".floor($Y2),$this->toHTMLColor($R,$G,$B),$serieDescription,$this->scaleFormat($serie["Data"][$Key],$Mode,$format,$Unit)); }

                 if ( $Rounded )
                  $this->drawRoundedFilledRectangle($X+$XOffset+$XSpace,$Y1,$X+$XOffset+$XSize-$XSpace,$Y2,$RoundRadius,$Color);
                 else
                  {
                   $this->drawFilledRectangle($X+$XOffset+$XSpace,$Y1,$X+$XOffset+$XSize-$XSpace,$Y2,$Color);

                   if ( $InnerColor != null ) { $this->drawRectangle($X+$XOffset+$XSpace+1,min($Y1,$Y2)+1,$X+$XOffset+$XSize-$XSpace-1,max($Y1,$Y2)-1,$InnerColor); }

                   if ( $Gradient )
                    {
                     $this->Shadow  = false;

                     if ( $GradientMode == GRADIENT_SIMPLE )
                      {
                       if ( $serie["Data"][$Key] >= 0 )
                        $GradienColor = array("StartR"=>$GradientStartR,"StartG"=>$GradientStartG,"StartB"=>$GradientStartB,"EndR"=>$GradientEndR,"EndG"=>$GradientEndG,"EndB"=>$GradientEndB,"Alpha"=>$GradientAlpha);
                       else
                        $GradienColor = array("StartR"=>$GradientEndR,"StartG"=>$GradientEndG,"StartB"=>$GradientEndB,"EndR"=>$GradientStartR,"EndG"=>$GradientStartG,"EndB"=>$GradientStartB,"Alpha"=>$GradientAlpha);

                       $this->drawGradientArea($X+$XOffset+$XSpace,$Y1,$X+$XOffset+$XSize-$XSpace,$Y2,DIRECTION_VERTICAL,$GradienColor);
                      }
                     elseif ( $GradientMode == GRADIENT_EFFECT_CAN )
                      {
                       $GradienColor1 = array("StartR"=>$GradientEndR,"StartG"=>$GradientEndG,"StartB"=>$GradientEndB,"EndR"=>$GradientStartR,"EndG"=>$GradientStartG,"EndB"=>$GradientStartB,"Alpha"=>$GradientAlpha);
                       $GradienColor2 = array("StartR"=>$GradientStartR,"StartG"=>$GradientStartG,"StartB"=>$GradientStartB,"EndR"=>$GradientEndR,"EndG"=>$GradientEndG,"EndB"=>$GradientEndB,"Alpha"=>$GradientAlpha);
                       $XSpan = floor($XSize / 3);

                       $this->drawGradientArea($X+$XOffset+$XSpace,$Y1,$X+$XOffset+$XSpan-$XSpace,$Y2,DIRECTION_HORIZONTAL,$GradienColor1);
                       $this->drawGradientArea($X+$XOffset+$XSpan+$XSpace,$Y1,$X+$XOffset+$XSize-$XSpace,$Y2,DIRECTION_HORIZONTAL,$GradienColor2);
                      }
                     $this->Shadow = $RestoreShadow;
                    }
                  }

                 if ( $Draw0Line )
                  {
                   $Line0Color = array("R"=>0,"G"=>0,"B"=>0,"Alpha"=>20);

                   if ( abs($Y1 - $Y2) > 3 ) { $Line0Width = 3; } else { $Line0Width = 1; }
                   if ( $Y1 - $Y2 < 0 ) { $Line0Width = -$Line0Width; }

                   $this->drawFilledRectangle($X+$XOffset+$XSpace,floor($Y1),$X+$XOffset+$XSize-$XSpace,floor($Y1)-$Line0Width,$Line0Color);
                   $this->drawLine($X+$XOffset+$XSpace,floor($Y1),$X+$XOffset+$XSize-$XSpace,floor($Y1),$Line0Color);
                  }
                }

               if ( $DisplayValues && $serie["Data"][$Key] != VOID )
                {
                 if ( $DisplayShadow ) { $this->Shadow = true; }

                 $Caption    = $this->scaleFormat($serie["Data"][$Key],$Mode,$format,$Unit);
                 $TxtPos     = $this->getTextBox(0,0,$DisplayFont,$DisplaySize,90,$Caption);
                 $TxtHeight  = $TxtPos[0]["Y"] - $TxtPos[1]["Y"] + $TxtMargin;

                 if ( $DisplayPos == LABEL_POS_INSIDE && abs($TxtHeight) < abs($BarHeight) )
                  {
                   $CenterX = (($X+$XOffset+$XSize-$XSpace)-($X+$XOffset+$XSpace))/2 + $X+$XOffset+$XSpace;
                   $CenterY = ($Y2-$Y1)/2 + $Y1;

                   $this->drawText($CenterX,$CenterY,$Caption,array("R"=>$DisplayR,"G"=>$DisplayG,"B"=>$DisplayB,"Align"=>TEXT_ALIGN_MIDDLEMIDDLE,"FontSize"=>$DisplaySize,"Angle"=>90));
                  }
                 else
                  {
                   if ( $serie["Data"][$Key] >= 0 ) { $Align = TEXT_ALIGN_BOTTOMMIDDLE; $Offset = $DisplayOffset; } else { $Align = TEXT_ALIGN_TOPMIDDLE; $Offset = -$DisplayOffset; }
                   $this->drawText($X+$XOffset+$XSize/2,$Y2-$Offset,$this->scaleFormat($serie["Data"][$Key],$Mode,$format,$Unit),array("R"=>$DisplayR,"G"=>$DisplayG,"B"=>$DisplayB,"Align"=>$Align,"FontSize"=>$DisplaySize));
                  }

                 $this->Shadow = $RestoreShadow;
                }
              }

             $X = $X + $XStep;
             $ID++;
            }
          }
         else
          {
           if ( $YZero < $this->GraphAreaX1+1 ) { $YZero = $this->GraphAreaX1+1; }
           if ( $YZero > $this->GraphAreaX2-1 ) { $YZero = $this->GraphAreaX2-1; }

           if ( $XDivs == 0 ) { $YStep = 0; } else { $YStep = ($this->GraphAreaY2-$this->GraphAreaY1-$XMargin*2)/$XDivs; }

           $Y = $this->GraphAreaY1 + $XMargin;

           if ( $AroundZero ) { $X1 = $YZero; } else { $X1 = $this->GraphAreaX1+1; }
           if ( $XDivs == 0 ) { $YSize = ($this->GraphAreaY2-$this->GraphAreaY1)/($seriesCount+$Interleave); } else { $YSize   = ($YStep / ($seriesCount+$Interleave) ); }

           $YOffset = -($YSize*$seriesCount)/2 + $CurrentSerie * $YSize;
           if ( $Y + $YOffset <= $this->GraphAreaY1 ) { $YOffset = $this->GraphAreaY1 - $Y + 1 ; }

           $this->DataSet->Data["Series"][$serie_name]["XOffset"] = $YOffset + $YSize / 2;

           if ( $Rounded || $BorderR != -1 ) { $YSpace = 1; } else { $YSpace = 0; }

           if ( !is_array($PosArray) ) { $Value = $PosArray; $PosArray = ""; $PosArray[0] = $Value; }

           $ID = 0 ;
           foreach($PosArray as $Key => $X2)
            {
             if ( $Floating0Serie != null )
              {
               if ( isset($Data["Series"][$Floating0Serie]["Data"][$Key]) )
                $Value = $Data["Series"][$Floating0Serie]["Data"][$Key];
               else { $Value = 0; }

               $YZero = $this->scaleComputeY($Value,array("AxisID"=>$serie["Axis"]));
               if ( $YZero < $this->GraphAreaX1+1 ) { $YZero = $this->GraphAreaX1+1; }
               if ( $YZero > $this->GraphAreaX2-1 ) { $YZero = $this->GraphAreaX2-1; }
               if ( $AroundZero ) { $X1 = $YZero; } else { $X1 = $this->GraphAreaX1+1; }
              }

             if ( $OverrideColors != null )
              { if ( isset($OverrideColors[$ID]) ) { $Color = array("R"=>$OverrideColors[$ID]["R"],"G"=>$OverrideColors[$ID]["G"],"B"=>$OverrideColors[$ID]["B"],"Alpha"=>$OverrideColors[$ID]["Alpha"],"BorderR"=>$OverrideColors[$ID]["BorderR"],"BorderG"=>$OverrideColors[$ID]["BorderG"],"BorderB"=>$OverrideColors[$ID]["BorderB"]); } else { $Color = $this->getRandomColor(); } }

             if ( $X2 != VOID )
              {
               $BarWidth = $X2 - $X1;

               if ( $serie["Data"][$Key] == 0 )
                {
                 $this->drawLine($X1,$Y+$YOffset+$YSpace,$X1,$Y+$YOffset+$YSize-$YSpace,$Color);
                 if ( $RecordImageMap ) { $this->addToImageMap("RECT",floor($X1-1).",".floor($Y+$YOffset+$YSpace).",".floor($X1+1).",".floor($Y+$YOffset+$YSize-$YSpace),$this->toHTMLColor($R,$G,$B),$serieDescription,$this->scaleFormat($serie["Data"][$Key],$Mode,$format,$Unit)); }
                }
               else
                {
                 if ( $RecordImageMap ) { $this->addToImageMap("RECT",floor($X1).",".floor($Y+$YOffset+$YSpace).",".floor($X2).",".floor($Y+$YOffset+$YSize-$YSpace),$this->toHTMLColor($R,$G,$B),$serieDescription,$this->scaleFormat($serie["Data"][$Key],$Mode,$format,$Unit)); }

                 if ( $Rounded )
                  $this->drawRoundedFilledRectangle($X1+1,$Y+$YOffset+$YSpace,$X2,$Y+$YOffset+$YSize-$YSpace,$RoundRadius,$Color);
                 else
                  {
                   $this->drawFilledRectangle($X1,$Y+$YOffset+$YSpace,$X2,$Y+$YOffset+$YSize-$YSpace,$Color);

                   if ( $InnerColor != null ) { $this->drawRectangle(min($X1,$X2)+1,$Y+$YOffset+$YSpace+1,max($X1,$X2)-1,$Y+$YOffset+$YSize-$YSpace-1,$InnerColor); }

                   if ( $Gradient )
                    {
                     $this->Shadow  = false;

                     if ( $GradientMode == GRADIENT_SIMPLE )
                      {
                       if ( $serie["Data"][$Key] >= 0 )
                        $GradienColor = array("StartR"=>$GradientStartR,"StartG"=>$GradientStartG,"StartB"=>$GradientStartB,"EndR"=>$GradientEndR,"EndG"=>$GradientEndG,"EndB"=>$GradientEndB,"Alpha"=>$GradientAlpha);
                       else
                        $GradienColor = array("StartR"=>$GradientEndR,"StartG"=>$GradientEndG,"StartB"=>$GradientEndB,"EndR"=>$GradientStartR,"EndG"=>$GradientStartG,"EndB"=>$GradientStartB,"Alpha"=>$GradientAlpha);

                       $this->drawGradientArea($X1,$Y+$YOffset+$YSpace,$X2,$Y+$YOffset+$YSize-$YSpace,DIRECTION_HORIZONTAL,$GradienColor);
                      }
                     elseif ( $GradientMode == GRADIENT_EFFECT_CAN )
                      {
                       $GradienColor1 = array("StartR"=>$GradientEndR,"StartG"=>$GradientEndG,"StartB"=>$GradientEndB,"EndR"=>$GradientStartR,"EndG"=>$GradientStartG,"EndB"=>$GradientStartB,"Alpha"=>$GradientAlpha);
                       $GradienColor2 = array("StartR"=>$GradientStartR,"StartG"=>$GradientStartG,"StartB"=>$GradientStartB,"EndR"=>$GradientEndR,"EndG"=>$GradientEndG,"EndB"=>$GradientEndB,"Alpha"=>$GradientAlpha);
                       $YSpan = floor($YSize / 3);

                       $this->drawGradientArea($X1,$Y+$YOffset+$YSpace,$X2,$Y+$YOffset+$YSpan-$YSpace,DIRECTION_VERTICAL,$GradienColor1);
                       $this->drawGradientArea($X1,$Y+$YOffset+$YSpan,$X2,$Y+$YOffset+$YSize-$YSpace,DIRECTION_VERTICAL,$GradienColor2);
                      }
                     $this->Shadow = $RestoreShadow;
                    }
                  }

                 if ( $Draw0Line )
                  {
                   $Line0Color = array("R"=>0,"G"=>0,"B"=>0,"Alpha"=>20);

                   if ( abs($X1 - $X2) > 3 ) { $Line0Width = 3; } else { $Line0Width = 1; }
                   if ( $X2 - $X1 < 0 ) { $Line0Width = -$Line0Width; }

                   $this->drawFilledRectangle(floor($X1),$Y+$YOffset+$YSpace,floor($X1)+$Line0Width,$Y+$YOffset+$YSize-$YSpace,$Line0Color);
                   $this->drawLine(floor($X1),$Y+$YOffset+$YSpace,floor($X1),$Y+$YOffset+$YSize-$YSpace,$Line0Color);
                  }
                }

               if ( $DisplayValues && $serie["Data"][$Key] != VOID )
                {
                 if ( $DisplayShadow ) { $this->Shadow = true; }

                 $Caption   = $this->scaleFormat($serie["Data"][$Key],$Mode,$format,$Unit);
                 $TxtPos    = $this->getTextBox(0,0,$DisplayFont,$DisplaySize,0,$Caption);
                 $TxtWidth  = $TxtPos[1]["X"] - $TxtPos[0]["X"] + $TxtMargin;

                 if ( $DisplayPos == LABEL_POS_INSIDE && abs($TxtWidth) < abs($BarWidth) )
                  {
                   $CenterX = ($X2-$X1)/2 + $X1;
                   $CenterY = (($Y+$YOffset+$YSize-$YSpace)-($Y+$YOffset+$YSpace))/2 + ($Y+$YOffset+$YSpace);

                   $this->drawText($CenterX,$CenterY,$Caption,array("R"=>$DisplayR,"G"=>$DisplayG,"B"=>$DisplayB,"Align"=>TEXT_ALIGN_MIDDLEMIDDLE,"FontSize"=>$DisplaySize));
                  }
                 else
                  {
                   if ( $serie["Data"][$Key] >= 0 ) { $Align = TEXT_ALIGN_MIDDLELEFT; $Offset = $DisplayOffset; } else { $Align = TEXT_ALIGN_MIDDLERIGHT; $Offset = -$DisplayOffset; }
                   $this->drawText($X2+$Offset,$Y+$YOffset+$YSize/2,$Caption,array("R"=>$DisplayR,"G"=>$DisplayG,"B"=>$DisplayB,"Align"=>$Align,"FontSize"=>$DisplaySize));
                  }

                 $this->Shadow = $RestoreShadow;
                }
              }
             $Y = $Y + $YStep;
             $ID++;
            }
          }
         $CurrentSerie++;
        }
      }
    }

    /* Draw a bar chart */
    function drawStackedBarChart($format=null)
    {
     $DisplayValues	= isset($format["DisplayValues"]) ? $format["DisplayValues"] : false;
     $DisplayOrientation = isset($format["DisplayOrientation"]) ? $format["DisplayOrientation"] : ORIENTATION_AUTO;
     $DisplayRound      = isset($format["DisplayRound"]) ? $format["DisplayRound"] : 0;
     $DisplayColor	= isset($format["DisplayColor"]) ? $format["DisplayColor"] : DISPLAY_MANUAL;
     $DisplayFont	= isset($format["DisplayFont"]) ? $format["DisplayFont"] : $this->FontName;
     $DisplaySize	= isset($format["DisplaySize"]) ? $format["DisplaySize"] : $this->FontSize;
     $DisplayR		= isset($format["DisplayR"]) ? $format["DisplayR"] : 0;
     $DisplayG		= isset($format["DisplayG"]) ? $format["DisplayG"] : 0;
     $DisplayB		= isset($format["DisplayB"]) ? $format["DisplayB"] : 0;
     $Interleave	= isset($format["Interleave"]) ? $format["Interleave"] : .5;
     $Rounded		= isset($format["Rounded"]) ? $format["Rounded"] : false;
     $RoundRadius	= isset($format["RoundRadius"]) ? $format["RoundRadius"] : 4;
     $Surrounding	= isset($format["Surrounding"]) ? $format["Surrounding"] : null;
     $BorderR		= isset($format["BorderR"]) ? $format["BorderR"] : -1;
     $BorderG		= isset($format["BorderG"]) ? $format["BorderG"] : -1;
     $BorderB		= isset($format["BorderB"]) ? $format["BorderB"] : -1;
     $Gradient		= isset($format["Gradient"]) ? $format["Gradient"] : false;
     $GradientMode	= isset($format["GradientMode"]) ? $format["GradientMode"] : GRADIENT_SIMPLE;
     $GradientAlpha	= isset($format["GradientAlpha"]) ? $format["GradientAlpha"] : 20;
     $GradientStartR	= isset($format["GradientStartR"]) ? $format["GradientStartR"] : 255;
     $GradientStartG	= isset($format["GradientStartG"]) ? $format["GradientStartG"] : 255;
     $GradientStartB	= isset($format["GradientStartB"]) ? $format["GradientStartB"] : 255;
     $GradientEndR	= isset($format["GradientEndR"]) ? $format["GradientEndR"] : 0;
     $GradientEndG	= isset($format["GradientEndG"]) ? $format["GradientEndG"] : 0;
     $GradientEndB	= isset($format["GradientEndB"]) ? $format["GradientEndB"] : 0;
     $InnerSurrounding	= isset($format["InnerSurrounding"]) ? $format["InnerSurrounding"] : null;
     $InnerBorderR	= isset($format["InnerBorderR"]) ? $format["InnerBorderR"] : -1;
     $InnerBorderG	= isset($format["InnerBorderG"]) ? $format["InnerBorderG"] : -1;
     $InnerBorderB	= isset($format["InnerBorderB"]) ? $format["InnerBorderB"] : -1;
     $RecordImageMap	= isset($format["RecordImageMap"]) ? $format["RecordImageMap"] : false;
     $FontFactor	= isset($format["FontFactor"]) ? $format["FontFactor"] : 8;

     $this->LastChartLayout = CHART_LAST_LAYOUT_STACKED;

     $Data = $this->DataSet->getData();
     list($XMargin,$XDivs) = $this->scaleGetXSettings();

     $RestoreShadow = $this->Shadow;

     $LastX = ""; $LastY = "";
     foreach($Data["Series"] as $serie_name => $serie)
      {
       if ( $serie["isDrawable"] == true && $serie_name != $Data["Abscissa"] )
        {
         $R = $serie["Color"]["R"]; $G = $serie["Color"]["G"]; $B = $serie["Color"]["B"]; $Alpha = $serie["Color"]["Alpha"]; $Ticks = $serie["Ticks"];
         if ( $DisplayColor == DISPLAY_AUTO ) { $DisplayR = 255; $DisplayG = 255; $DisplayB = 255; }
         if ( $Surrounding != null ) { $BorderR = $R+$Surrounding; $BorderG = $G+$Surrounding; $BorderB = $B+$Surrounding; }
         if ( $InnerSurrounding != null ) { $InnerBorderR = $R+$InnerSurrounding; $InnerBorderG = $G+$InnerSurrounding; $InnerBorderB = $B+$InnerSurrounding; }
         if ( $InnerBorderR == -1 ) { $InnerColor = null; } else { $InnerColor = array("R"=>$InnerBorderR,"G"=>$InnerBorderG,"B"=>$InnerBorderB); }

         $AxisID	= $serie["Axis"];
         $Mode		= $Data["Axis"][$AxisID]["Display"];
         $format	= $Data["Axis"][$AxisID]["Format"];
         $Unit		= $Data["Axis"][$AxisID]["Unit"];

         if (isset($serie["Description"])) { $serieDescription = $serie["Description"]; } else { $serieDescription = $serie_name; }

         $PosArray = $this->scaleComputeY($serie["Data"],array("AxisID"=>$serie["Axis"]),true);
         $YZero    = $this->scaleComputeY(0,array("AxisID"=>$serie["Axis"]));

         $this->DataSet->Data["Series"][$serie_name]["XOffset"] = 0;

         $Color = array("TransCorner"=>true,"R"=>$R,"G"=>$G,"B"=>$B,"Alpha"=>$Alpha,"BorderR"=>$BorderR,"BorderG"=>$BorderG,"BorderB"=>$BorderB);

         if ( $Data["Orientation"] == SCALE_POS_LEFTRIGHT )
          {
           if ( $YZero > $this->GraphAreaY2-1 ) { $YZero = $this->GraphAreaY2-1; }
           if ( $YZero > $this->GraphAreaY2-1 ) { $YZero = $this->GraphAreaY2-1; }

           if ( $XDivs == 0 ) { $XStep = ($this->GraphAreaX2-$this->GraphAreaX1)/4; } else { $XStep = ($this->GraphAreaX2-$this->GraphAreaX1-$XMargin*2)/$XDivs; }
           $X     = $this->GraphAreaX1 + $XMargin;

           $XSize   = ($XStep / (1+$Interleave) );
           $XOffset = -($XSize/2);

           if ( !is_array($PosArray) ) { $Value = $PosArray; $PosArray = ""; $PosArray[0] = $Value; }
           foreach($PosArray as $Key => $Height)
            {
             if ( $Height != VOID && $serie["Data"][$Key] != 0 )
              {
               if ( $serie["Data"][$Key] > 0 ) { $Pos = "+"; } else { $Pos = "-"; }

               if ( !isset($LastY[$Key] ) ) { $LastY[$Key] = ""; }
               if ( !isset($LastY[$Key][$Pos] ) ) { $LastY[$Key][$Pos] = $YZero; }

               $Y1 = $LastY[$Key][$Pos];
               $Y2 = $Y1 - $Height;

               if ( ($Rounded || $BorderR != -1) && ($Pos == "+" && $Y1 != $YZero) ) { $YSpaceUp = 1; } else { $YSpaceUp = 0; }
               if ( ($Rounded || $BorderR != -1) && ($Pos == "-" && $Y1 != $YZero) ) { $YSpaceDown = 1; } else { $YSpaceDown = 0; }

               if ( $RecordImageMap ) { $this->addToImageMap("RECT",floor($X+$XOffset).",".floor($Y1-$YSpaceUp+$YSpaceDown).",".floor($X+$XOffset+$XSize).",".floor($Y2),$this->toHTMLColor($R,$G,$B),$serieDescription,$this->scaleFormat($serie["Data"][$Key],$Mode,$format,$Unit)); }

               if ( $Rounded )
                $this->drawRoundedFilledRectangle($X+$XOffset,$Y1-$YSpaceUp+$YSpaceDown,$X+$XOffset+$XSize,$Y2,$RoundRadius,$Color);
               else
                {
                 $this->drawFilledRectangle($X+$XOffset,$Y1-$YSpaceUp+$YSpaceDown,$X+$XOffset+$XSize,$Y2,$Color);

                 if ( $InnerColor != null ) { $RestoreShadow = $this->Shadow; $this->Shadow = false; $this->drawRectangle(min($X+$XOffset+1,$X+$XOffset+$XSize),min($Y1-$YSpaceUp+$YSpaceDown,$Y2)+1,max($X+$XOffset+1,$X+$XOffset+$XSize)-1,max($Y1-$YSpaceUp+$YSpaceDown,$Y2)-1,$InnerColor); $this->Shadow = $RestoreShadow;}

                 if ( $Gradient )
                  {
                   $this->Shadow  = false;

                   if ( $GradientMode == GRADIENT_SIMPLE )
                    {
                     $GradientColor = array("StartR"=>$GradientStartR,"StartG"=>$GradientStartG,"StartB"=>$GradientStartB,"EndR"=>$GradientEndR,"EndG"=>$GradientEndG,"EndB"=>$GradientEndB,"Alpha"=>$GradientAlpha);
                     $this->drawGradientArea($X+$XOffset,$Y1-1-$YSpaceUp+$YSpaceDown,$X+$XOffset+$XSize,$Y2+1,DIRECTION_VERTICAL,$GradientColor);
                    }
                   elseif ( $GradientMode == GRADIENT_EFFECT_CAN )
                    {
                     $GradientColor1 = array("StartR"=>$GradientEndR,"StartG"=>$GradientEndG,"StartB"=>$GradientEndB,"EndR"=>$GradientStartR,"EndG"=>$GradientStartG,"EndB"=>$GradientStartB,"Alpha"=>$GradientAlpha);
                     $GradientColor2 = array("StartR"=>$GradientStartR,"StartG"=>$GradientStartG,"StartB"=>$GradientStartB,"EndR"=>$GradientEndR,"EndG"=>$GradientEndG,"EndB"=>$GradientEndB,"Alpha"=>$GradientAlpha);
                     $XSpan = floor($XSize / 3);

                     $this->drawGradientArea($X+$XOffset-.5,$Y1-.5-$YSpaceUp+$YSpaceDown,$X+$XOffset+$XSpan,$Y2+.5,DIRECTION_HORIZONTAL,$GradientColor1);
                     $this->drawGradientArea($X+$XSpan+$XOffset-.5,$Y1-.5-$YSpaceUp+$YSpaceDown,$X+$XOffset+$XSize,$Y2+.5,DIRECTION_HORIZONTAL,$GradientColor2);
                    }
                   $this->Shadow = $RestoreShadow;
                  }
                }

               if ( $DisplayValues )
                {
                 $BarHeight = abs($Y2-$Y1)-2;
                 $BarWidth  = $XSize+($XOffset/2)-$FontFactor;

                 $Caption   = $this->scaleFormat(round($serie["Data"][$Key],$DisplayRound),$Mode,$format,$Unit);
                 $TxtPos    = $this->getTextBox(0,0,$DisplayFont,$DisplaySize,0,$Caption);
                 $TxtHeight = abs($TxtPos[2]["Y"] - $TxtPos[0]["Y"]);
                 $TxtWidth  = abs($TxtPos[1]["X"] - $TxtPos[0]["X"]);

                 $XCenter = ( ($X+$XOffset+$XSize) - ($X+$XOffset) ) / 2 + $X+$XOffset;
                 $YCenter = ( ($Y2) - ($Y1-$YSpaceUp+$YSpaceDown) ) / 2 + $Y1-$YSpaceUp+$YSpaceDown;

                 $Done = false;
                 if ( $DisplayOrientation == ORIENTATION_HORIZONTAL || $DisplayOrientation == ORIENTATION_AUTO )
                  {
                   if ( $TxtHeight < $BarHeight && $TxtWidth < $BarWidth  )
                    {
                     $this->drawText($XCenter,$YCenter,$this->scaleFormat($serie["Data"][$Key],$Mode,$format,$Unit),array("R"=>$DisplayR,"G"=>$DisplayG,"B"=>$DisplayB,"Align"=>TEXT_ALIGN_MIDDLEMIDDLE,"FontSize"=>$DisplaySize,"FontName"=>$DisplayFont));
                     $Done = true;
                    }
                  }

                 if ( $DisplayOrientation == ORIENTATION_VERTICAL || ( $DisplayOrientation == ORIENTATION_AUTO && !$Done) )
                  {
                   if ( $TxtHeight < $BarWidth && $TxtWidth < $BarHeight  )
                    $this->drawText($XCenter,$YCenter,$this->scaleFormat($serie["Data"][$Key],$Mode,$format,$Unit),array("R"=>$DisplayR,"G"=>$DisplayG,"B"=>$DisplayB,"Angle"=>90,"Align"=>TEXT_ALIGN_MIDDLEMIDDLE,"FontSize"=>$DisplaySize,"FontName"=>$DisplayFont));
                  }
                }

               $LastY[$Key][$Pos] = $Y2;
              }

             $X = $X + $XStep;
            }
          }
         else
          {
           if ( $YZero < $this->GraphAreaX1+1 ) { $YZero = $this->GraphAreaX1+1; }
           if ( $YZero > $this->GraphAreaX2-1 ) { $YZero = $this->GraphAreaX2-1; }

           if ( $XDivs == 0 ) { $YStep = ($this->GraphAreaY2-$this->GraphAreaY1)/4; } else { $YStep = ($this->GraphAreaY2-$this->GraphAreaY1-$XMargin*2)/$XDivs; }
           $Y     = $this->GraphAreaY1 + $XMargin;

           $YSize   = $YStep / (1+$Interleave);
           $YOffset = -($YSize/2);

           if ( !is_array($PosArray) ) { $Value = $PosArray; $PosArray = ""; $PosArray[0] = $Value; }
           foreach($PosArray as $Key => $Width)
            {
             if ( $Width != VOID && $serie["Data"][$Key] != 0 )
              {
               if ( $serie["Data"][$Key] > 0 ) { $Pos = "+"; } else { $Pos = "-"; }

               if ( !isset($LastX[$Key] ) ) { $LastX[$Key] = ""; }
               if ( !isset($LastX[$Key][$Pos] ) ) { $LastX[$Key][$Pos] = $YZero; }

               $X1 = $LastX[$Key][$Pos];
               $X2 = $X1 + $Width;

               if ( ($Rounded || $BorderR != -1) && ($Pos == "+" && $X1 != $YZero) ) { $XSpaceLeft = 2; } else { $XSpaceLeft = 0; }
               if ( ($Rounded || $BorderR != -1) && ($Pos == "-" && $X1 != $YZero) ) { $XSpaceRight = 2; } else { $XSpaceRight = 0; }

               if ( $RecordImageMap ) { $this->addToImageMap("RECT",floor($X1+$XSpaceLeft).",".floor($Y+$YOffset).",".floor($X2-$XSpaceRight).",".floor($Y+$YOffset+$YSize),$this->toHTMLColor($R,$G,$B),$serieDescription,$this->scaleFormat($serie["Data"][$Key],$Mode,$format,$Unit)); }

               if ( $Rounded )
                $this->drawRoundedFilledRectangle($X1+$XSpaceLeft,$Y+$YOffset,$X2-$XSpaceRight,$Y+$YOffset+$YSize,$RoundRadius,$Color);
               else
                {
                 $this->drawFilledRectangle($X1+$XSpaceLeft,$Y+$YOffset,$X2-$XSpaceRight,$Y+$YOffset+$YSize,$Color);

                 if ( $InnerColor != null ) { $RestoreShadow = $this->Shadow; $this->Shadow = false; $this->drawRectangle(min($X1+$XSpaceLeft,$X2-$XSpaceRight)+1,min($Y+$YOffset,$Y+$YOffset+$YSize)+1,max($X1+$XSpaceLeft,$X2-$XSpaceRight)-1,max($Y+$YOffset,$Y+$YOffset+$YSize)-1,$InnerColor); $this->Shadow = $RestoreShadow;}

                 if ( $Gradient )
                  {
                   $this->Shadow  = false;

                   if ( $GradientMode == GRADIENT_SIMPLE )
                    {
                     $GradientColor = array("StartR"=>$GradientStartR,"StartG"=>$GradientStartG,"StartB"=>$GradientStartB,"EndR"=>$GradientEndR,"EndG"=>$GradientEndG,"EndB"=>$GradientEndB,"Alpha"=>$GradientAlpha);
                     $this->drawGradientArea($X1+$XSpaceLeft,$Y+$YOffset,$X2-$XSpaceRight,$Y+$YOffset+$YSize,DIRECTION_HORIZONTAL,$GradientColor);
                    }
                   elseif ( $GradientMode == GRADIENT_EFFECT_CAN )
                    {
                     $GradientColor1 = array("StartR"=>$GradientEndR,"StartG"=>$GradientEndG,"StartB"=>$GradientEndB,"EndR"=>$GradientStartR,"EndG"=>$GradientStartG,"EndB"=>$GradientStartB,"Alpha"=>$GradientAlpha);
                     $GradientColor2 = array("StartR"=>$GradientStartR,"StartG"=>$GradientStartG,"StartB"=>$GradientStartB,"EndR"=>$GradientEndR,"EndG"=>$GradientEndG,"EndB"=>$GradientEndB,"Alpha"=>$GradientAlpha);
                     $YSpan = floor($YSize / 3);

                     $this->drawGradientArea($X1+$XSpaceLeft,$Y+$YOffset,$X2-$XSpaceRight,$Y+$YOffset+$YSpan,DIRECTION_VERTICAL,$GradientColor1);
                     $this->drawGradientArea($X1+$XSpaceLeft,$Y+$YOffset+$YSpan,$X2-$XSpaceRight,$Y+$YOffset+$YSize,DIRECTION_VERTICAL,$GradientColor2);
                    }
                   $this->Shadow = $RestoreShadow;
                  }
                }

               if ( $DisplayValues )
                {
                 $BarWidth = abs($X2-$X1)-$FontFactor;
                 $BarHeight = $YSize+($YOffset/2)-$FontFactor/2;
                 $Caption   = $this->scaleFormat(round($serie["Data"][$Key],$DisplayRound),$Mode,$format,$Unit);
                 $TxtPos    = $this->getTextBox(0,0,$DisplayFont,$DisplaySize,0,$Caption);
                 $TxtHeight = abs($TxtPos[2]["Y"] - $TxtPos[0]["Y"]);
                 $TxtWidth  = abs($TxtPos[1]["X"] - $TxtPos[0]["X"]);

                 $XCenter  = ( $X2 - $X1 ) / 2 + $X1;
                 $YCenter  = ( ($Y+$YOffset+$YSize) - ($Y+$YOffset) ) / 2 + $Y+$YOffset;

                 $Done = false;
                 if ( $DisplayOrientation == ORIENTATION_HORIZONTAL || $DisplayOrientation == ORIENTATION_AUTO )
                  {
                   if ( $TxtHeight < $BarHeight && $TxtWidth < $BarWidth  )
                    {
                     $this->drawText($XCenter,$YCenter,$this->scaleFormat($serie["Data"][$Key],$Mode,$format,$Unit),array("R"=>$DisplayR,"G"=>$DisplayG,"B"=>$DisplayB,"Align"=>TEXT_ALIGN_MIDDLEMIDDLE,"FontSize"=>$DisplaySize,"FontName"=>$DisplayFont));
                     $Done = true;
                    }
                  }

                 if ( $DisplayOrientation == ORIENTATION_VERTICAL || ( $DisplayOrientation == ORIENTATION_AUTO && !$Done) )
                  {
                   if ( $TxtHeight < $BarWidth && $TxtWidth < $BarHeight  )
                    $this->drawText($XCenter,$YCenter,$this->scaleFormat($serie["Data"][$Key],$Mode,$format,$Unit),array("R"=>$DisplayR,"G"=>$DisplayG,"B"=>$DisplayB,"Angle"=>90,"Align"=>TEXT_ALIGN_MIDDLEMIDDLE,"FontSize"=>$DisplaySize,"FontName"=>$DisplayFont));
                  }
                }

               $LastX[$Key][$Pos] = $X2;
              }

             $Y = $Y + $YStep;
            }
          }
        }
      }
    }

    /* Draw a stacked area chart */
    function drawStackedAreaChart($format=null)
    {
     $DrawLine		= isset($format["DrawLine"]) ? $format["DrawLine"] : false;
     $LineSurrounding	= isset($format["LineSurrounding"]) ? $format["LineSurrounding"] : null;
     $LineR		= isset($format["LineR"]) ? $format["LineR"] : VOID;
     $LineG		= isset($format["LineG"]) ? $format["LineG"] : VOID;
     $LineB		= isset($format["LineB"]) ? $format["LineB"] : VOID;
     $LineAlpha		= isset($format["LineAlpha"]) ? $format["LineAlpha"] : 100;
     $DrawPlot		= isset($format["DrawPlot"]) ? $format["DrawPlot"] : false;
     $PlotRadius	= isset($format["PlotRadius"]) ? $format["PlotRadius"] : 2;
     $PlotBorder	= isset($format["PlotBorder"]) ? $format["PlotBorder"] : 1;
     $PlotBorderSurrounding = isset($format["PlotBorderSurrounding"]) ? $format["PlotBorderSurrounding"] : null;
     $PlotBorderR	= isset($format["PlotBorderR"]) ? $format["PlotBorderR"] : 0;
     $PlotBorderG	= isset($format["PlotBorderG"]) ? $format["PlotBorderG"] : 0;
     $PlotBorderB	= isset($format["PlotBorderB"]) ? $format["PlotBorderB"] : 0;
     $PlotBorderAlpha	= isset($format["PlotBorderAlpha"]) ? $format["PlotBorderAlpha"] : 50;
     $ForceTransparency	= isset($format["ForceTransparency"]) ? $format["ForceTransparency"] : null;

     $this->LastChartLayout = CHART_LAST_LAYOUT_STACKED;

     $Data = $this->DataSet->getData();
     list($XMargin,$XDivs) = $this->scaleGetXSettings();

     $RestoreShadow = $this->Shadow;
     $this->Shadow  = false;

     /* Build the offset data series */
     $OffsetData    = "";
     $OverallOffset = "";
     $serieOrder    = "";
     foreach($Data["Series"] as $serie_name => $serie)
      {
       if ( $serie["isDrawable"] == true && $serie_name != $Data["Abscissa"] )
        {
         $serieOrder[] = $serie_name;

         foreach($serie["Data"] as $Key => $Value)
          {
           if ( $Value == VOID ) { $Value = 0; }
           if ($Value >= 0) { $Sign = "+"; } else { $Sign = "-"; }
           if ( !isset($OverallOffset[$Key]) || !isset($OverallOffset[$Key][$Sign]) ) { $OverallOffset[$Key][$Sign] = 0; }

           if ( $Sign == "+" )
            { $Data["Series"][$serie_name]["Data"][$Key] = $Value + $OverallOffset[$Key][$Sign]; }
           else
            { $Data["Series"][$serie_name]["Data"][$Key] = $Value - $OverallOffset[$Key][$Sign]; }

           $OverallOffset[$Key][$Sign] = $OverallOffset[$Key][$Sign] + abs($Value);
          }
        }
      }
     $serieOrder = array_reverse($serieOrder);

     $LastX = ""; $LastY = "";
     foreach($serieOrder as $Key => $serie_name)
      {
       $serie = $Data["Series"][$serie_name];
       if ( $serie["isDrawable"] == true && $serie_name != $Data["Abscissa"] )
        {
         $R = $serie["Color"]["R"]; $G = $serie["Color"]["G"]; $B = $serie["Color"]["B"]; $Alpha = $serie["Color"]["Alpha"]; $Ticks = $serie["Ticks"];
         if ( $ForceTransparency != null ) { $Alpha = $ForceTransparency; }

         $Color = array("R"=>$R,"G"=>$G,"B"=>$B,"Alpha"=>$Alpha);

         if ( $LineSurrounding != null )
          $LineColor = array("R"=>$R+$LineSurrounding,"G"=>$G+$LineSurrounding,"B"=>$B+$LineSurrounding,"Alpha"=>$Alpha);
         elseif ( $LineR != VOID )
          $LineColor = array("R"=>$LineR,"G"=>$LineG,"B"=>$LineB,"Alpha"=>$LineAlpha);
         else
          $LineColor = $Color;

         if ( $PlotBorderSurrounding != null )
          $PlotBorderColor = array("R"=>$R+$PlotBorderSurrounding,"G"=>$G+$PlotBorderSurrounding,"B"=>$B+$PlotBorderSurrounding,"Alpha"=>$PlotBorderAlpha);
         else
          $PlotBorderColor = array("R"=>$PlotBorderR,"G"=>$PlotBorderG,"B"=>$PlotBorderB,"Alpha"=>$PlotBorderAlpha);

         $AxisID	= $serie["Axis"];
         $Mode		= $Data["Axis"][$AxisID]["Display"];
         $format	= $Data["Axis"][$AxisID]["Format"];
         $Unit		= $Data["Axis"][$AxisID]["Unit"];

         $PosArray = $this->scaleComputeY($serie["Data"],array("AxisID"=>$serie["Axis"]),true);
         $YZero    = $this->scaleComputeY(0,array("AxisID"=>$serie["Axis"]));

         $this->DataSet->Data["Series"][$serie_name]["XOffset"] = 0;

         if ( $Data["Orientation"] == SCALE_POS_LEFTRIGHT )
          {
           if ( $YZero < $this->GraphAreaY1+1 ) { $YZero = $this->GraphAreaY1+1; }
           if ( $YZero > $this->GraphAreaY2-1 ) { $YZero = $this->GraphAreaY2-1; }

           if ( $XDivs == 0 ) { $XStep = ($this->GraphAreaX2-$this->GraphAreaX1)/4; } else { $XStep = ($this->GraphAreaX2-$this->GraphAreaX1-$XMargin*2)/$XDivs; }
           $X = $this->GraphAreaX1 + $XMargin;

           if ( !is_array($PosArray) ) { $Value = $PosArray; $PosArray = ""; $PosArray[0] = $Value; }

           $Plots = ""; $Plots[] = $X; $Plots[] = $YZero;
           foreach($PosArray as $Key => $Height)
            {
             if ( $Height != VOID ) { $Plots[] = $X; $Plots[] = $YZero-$Height; }
             $X = $X + $XStep;
            }
           $Plots[] = $X-$XStep; $Plots[] = $YZero;

           $this->drawPolygon($Plots,$Color);

           $this->Shadow = $RestoreShadow;
           if ( $DrawLine ) { for($i=2; $i<=count($Plots)-6; $i=$i+2) { $this->drawLine($Plots[$i],$Plots[$i+1],$Plots[$i+2],$Plots[$i+3],$LineColor); } }
           if ( $DrawPlot )
            {
             for($i=2; $i<=count($Plots)-4; $i=$i+2)
              {
               if ( $PlotBorder != 0 )
                { $this->drawFilledCircle($Plots[$i],$Plots[$i+1],$PlotRadius+$PlotBorder,$PlotBorderColor); }

               $this->drawFilledCircle($Plots[$i],$Plots[$i+1],$PlotRadius,$Color);
              }
            }
           $this->Shadow = false;
          }
         elseif ( $Data["Orientation"] == SCALE_POS_TOPBOTTOM )
          {
           if ( $YZero < $this->GraphAreaX1+1 ) { $YZero = $this->GraphAreaX1+1; }
           if ( $YZero > $this->GraphAreaX2-1 ) { $YZero = $this->GraphAreaX2-1; }

           if ( $XDivs == 0 ) { $YStep = ($this->GraphAreaY2-$this->GraphAreaY1)/4; } else { $YStep = ($this->GraphAreaY2-$this->GraphAreaY1-$XMargin*2)/$XDivs; }
           $Y = $this->GraphAreaY1 + $XMargin;

           if ( !is_array($PosArray) ) { $Value = $PosArray; $PosArray = ""; $PosArray[0] = $Value; }

           $Plots = ""; $Plots[] = $YZero; $Plots[] = $Y;
           foreach($PosArray as $Key => $Height)
            {
             if ( $Height != VOID ) { $Plots[] = $YZero+$Height; $Plots[] = $Y; }
             $Y = $Y + $YStep;
            }
           $Plots[] = $YZero; $Plots[] = $Y-$YStep;

           $this->drawPolygon($Plots,$Color);

           $this->Shadow = $RestoreShadow;
           if ( $DrawLine ) { for($i=2; $i<=count($Plots)-6; $i=$i+2) { $this->drawLine($Plots[$i],$Plots[$i+1],$Plots[$i+2],$Plots[$i+3],$LineColor); } }
           if ( $DrawPlot )
            {
             for($i=2; $i<=count($Plots)-4; $i=$i+2)
              {
               if ( $PlotBorder != 0 )
                { $this->drawFilledCircle($Plots[$i],$Plots[$i+1],$PlotRadius+$PlotBorder,$PlotBorderColor); }

               $this->drawFilledCircle($Plots[$i],$Plots[$i+1],$PlotRadius,$Color);
              }
            }
           $this->Shadow = false;
          }
        }
      }
     $this->Shadow = $RestoreShadow;
    }

    /* Returns a random color */
    function getRandomColor($Alpha=100)
    { return(array("R"=>rand(0,255),"G"=>rand(0,255),"B"=>rand(0,255),"Alpha"=>$Alpha)); }

    /* Validate a palette */
    function validatePalette($Colors,$Surrounding=null)
    {
     $Result = "";

     if ( !is_array($Colors) ) { return($this->getRandomColor()); }

     foreach($Colors as $Key => $Values)
      {
       if ( isset($Values["R"]) ) { $Result[$Key]["R"] = $Values["R"]; } else { $Result[$Key]["R"] = rand(0,255); }
       if ( isset($Values["G"]) ) { $Result[$Key]["G"] = $Values["G"]; } else { $Result[$Key]["G"] = rand(0,255); }
       if ( isset($Values["B"]) ) { $Result[$Key]["B"] = $Values["B"]; } else { $Result[$Key]["B"] = rand(0,255); }
       if ( isset($Values["Alpha"]) ) { $Result[$Key]["Alpha"] = $Values["Alpha"]; } else { $Result[$Key]["Alpha"] = 100; }

       if ( $Surrounding != null )
        {
         $Result[$Key]["BorderR"] = $Result[$Key]["R"] + $Surrounding;
         $Result[$Key]["BorderG"] = $Result[$Key]["G"] + $Surrounding;
         $Result[$Key]["BorderB"] = $Result[$Key]["B"] + $Surrounding;
        }
       else
        {
         if ( isset($Values["BorderR"]) )     { $Result[$Key]["BorderR"] = $Values["BorderR"]; } else { $Result[$Key]["BorderR"] = $Result[$Key]["R"]; }
         if ( isset($Values["BorderG"]) )     { $Result[$Key]["BorderG"] = $Values["BorderG"]; } else { $Result[$Key]["BorderG"] = $Result[$Key]["G"]; }
         if ( isset($Values["BorderB"]) )     { $Result[$Key]["BorderB"] = $Values["BorderB"]; } else { $Result[$Key]["BorderB"] = $Result[$Key]["B"]; }
         if ( isset($Values["BorderAlpha"]) ) { $Result[$Key]["BorderAlpha"] = $Values["BorderAlpha"]; } else { $Result[$Key]["BorderAlpha"] = $Result[$Key]["Alpha"]; }
        }
      }

     return $Result;
    }

    /* Draw the derivative chart associated to the data series */
    function drawDerivative($format=null)
    {
     $Offset		= isset($format["Offset"]) ? $format["Offset"] : 10;
     $serieSpacing	= isset($format["SerieSpacing"]) ? $format["SerieSpacing"] : 3;
     $DerivativeHeight	= isset($format["DerivativeHeight"]) ? $format["DerivativeHeight"] : 4;
     $ShadedSlopeBox	= isset($format["ShadedSlopeBox"]) ? $format["ShadedSlopeBox"] : false;
     $DrawBackground	= isset($format["DrawBackground"]) ? $format["DrawBackground"] : true;
     $BackgroundR	= isset($format["BackgroundR"]) ? $format["BackgroundR"] : 255;
     $BackgroundG	= isset($format["BackgroundG"]) ? $format["BackgroundG"] : 255;
     $BackgroundB	= isset($format["BackgroundB"]) ? $format["BackgroundB"] : 255;
     $BackgroundAlpha	= isset($format["BackgroundAlpha"]) ? $format["BackgroundAlpha"] : 20;
     $DrawBorder	= isset($format["DrawBorder"]) ? $format["DrawBorder"] : true;
     $BorderR		= isset($format["BorderR"]) ? $format["BorderR"] : 0;
     $BorderG		= isset($format["BorderG"]) ? $format["BorderG"] : 0;
     $BorderB		= isset($format["BorderB"]) ? $format["BorderB"] : 0;
     $BorderAlpha	= isset($format["BorderAlpha"]) ? $format["BorderAlpha"] : 100;
     $Caption		= isset($format["Caption"]) ? $format["Caption"] : true;
     $CaptionHeight	= isset($format["CaptionHeight"]) ? $format["CaptionHeight"] : 10;
     $CaptionWidth	= isset($format["CaptionWidth"]) ? $format["CaptionWidth"] : 20;
     $CaptionMargin	= isset($format["CaptionMargin"]) ? $format["CaptionMargin"] : 4;
     $CaptionLine	= isset($format["CaptionLine"]) ? $format["CaptionLine"] : false;
     $CaptionBox	= isset($format["CaptionBox"]) ? $format["CaptionBox"] : false;
     $CaptionBorderR	= isset($format["CaptionBorderR"]) ? $format["CaptionBorderR"] : 0;
     $CaptionBorderG	= isset($format["CaptionBorderG"]) ? $format["CaptionBorderG"] : 0;
     $CaptionBorderB	= isset($format["CaptionBorderB"]) ? $format["CaptionBorderB"] : 0;
     $CaptionFillR	= isset($format["CaptionFillR"]) ? $format["CaptionFillR"] : 255;
     $CaptionFillG	= isset($format["CaptionFillG"]) ? $format["CaptionFillG"] : 255;
     $CaptionFillB	= isset($format["CaptionFillB"]) ? $format["CaptionFillB"] : 255;
     $CaptionFillAlpha	= isset($format["CaptionFillAlpha"]) ? $format["CaptionFillAlpha"] : 80;
     $PositiveSlopeStartR	= isset($format["PositiveSlopeStartR"]) ? $format["PositiveSlopeStartR"] : 184;
     $PositiveSlopeStartG	= isset($format["PositiveSlopeStartG"]) ? $format["PositiveSlopeStartG"] : 234;
     $PositiveSlopeStartB	= isset($format["PositiveSlopeStartB"]) ? $format["PositiveSlopeStartB"] : 88;
     $PositiveSlopeEndR		= isset($format["PositiveSlopeStartR"]) ? $format["PositiveSlopeStartR"] : 239;
     $PositiveSlopeEndG		= isset($format["PositiveSlopeStartG"]) ? $format["PositiveSlopeStartG"] : 31;
     $PositiveSlopeEndB		= isset($format["PositiveSlopeStartB"]) ? $format["PositiveSlopeStartB"] : 36;
     $NegativeSlopeStartR	= isset($format["NegativeSlopeStartR"]) ? $format["NegativeSlopeStartR"] : 184;
     $NegativeSlopeStartG	= isset($format["NegativeSlopeStartG"]) ? $format["NegativeSlopeStartG"] : 234;
     $NegativeSlopeStartB	= isset($format["NegativeSlopeStartB"]) ? $format["NegativeSlopeStartB"] : 88;
     $NegativeSlopeEndR		= isset($format["NegativeSlopeStartR"]) ? $format["NegativeSlopeStartR"] : 67;
     $NegativeSlopeEndG		= isset($format["NegativeSlopeStartG"]) ? $format["NegativeSlopeStartG"] : 124;
     $NegativeSlopeEndB		= isset($format["NegativeSlopeStartB"]) ? $format["NegativeSlopeStartB"] : 227;

     $Data = $this->DataSet->getData();

     list($XMargin,$XDivs) = $this->scaleGetXSettings();

     if ( $Data["Orientation"] == SCALE_POS_LEFTRIGHT )
      $YPos = $this->DataSet->Data["GraphArea"]["Y2"] + $Offset;
     else
      $XPos = $this->DataSet->Data["GraphArea"]["X2"] + $Offset;

     foreach($Data["Series"] as $serie_name => $serie)
      {
       if ( $serie["isDrawable"] == true && $serie_name != $Data["Abscissa"] )
        {
         $R = $serie["Color"]["R"]; $G = $serie["Color"]["G"]; $B = $serie["Color"]["B"]; $Alpha = $serie["Color"]["Alpha"]; $Ticks = $serie["Ticks"]; $Weight = $serie["Weight"];

         $AxisID   = $serie["Axis"];
         $PosArray = $this->scaleComputeY($serie["Data"],array("AxisID"=>$serie["Axis"]));

         if ( $Data["Orientation"] == SCALE_POS_LEFTRIGHT )
          {
           if ( $Caption )
            {
             if ( $CaptionLine )
              {
               $StartX = floor($this->GraphAreaX1-$CaptionWidth+$XMargin-$CaptionMargin);
               $EndX   = floor($this->GraphAreaX1-$CaptionMargin+$XMargin);

               $CaptionSettings = array("R"=>$R,"G"=>$G,"B"=>$B,"Alpha"=>$Alpha,"Ticks"=>$Ticks,"Weight"=>$Weight);
               if ( $CaptionBox ) { $this->drawFilledRectangle($StartX,$YPos,$EndX,$YPos+$CaptionHeight,array("R"=>$CaptionFillR,"G"=>$CaptionFillG,"B"=>$CaptionFillB,"BorderR"=>$CaptionBorderR,"BorderG"=>$CaptionBorderG,"BorderB"=>$CaptionBorderB,"Alpha"=>$CaptionFillAlpha)); }
               $this->drawLine($StartX+2,$YPos+($CaptionHeight/2),$EndX-2,$YPos+($CaptionHeight/2),$CaptionSettings);
              }
             else
              $this->drawFilledRectangle($this->GraphAreaX1-$CaptionWidth+$XMargin-$CaptionMargin,$YPos,$this->GraphAreaX1-$CaptionMargin+$XMargin,$YPos+$CaptionHeight,array("R"=>$R,"G"=>$G,"B"=>$B,"BorderR"=>$CaptionBorderR,"BorderG"=>$CaptionBorderG,"BorderB"=>$CaptionBorderB));
            }

           if ( $XDivs == 0 ) { $XStep = ($this->GraphAreaX2-$this->GraphAreaX1)/4; } else { $XStep = ($this->GraphAreaX2-$this->GraphAreaX1-$XMargin*2)/$XDivs; }
           $X = $this->GraphAreaX1 + $XMargin;

           $TopY    = $YPos + ($CaptionHeight/2) - ($DerivativeHeight/2);
           $BottomY = $YPos + ($CaptionHeight/2) + ($DerivativeHeight/2);

           $StartX  = floor($this->GraphAreaX1+$XMargin);
           $EndX    = floor($this->GraphAreaX2-$XMargin);

           if ( $DrawBackground ) { $this->drawFilledRectangle($StartX-1,$TopY-1,$EndX+1,$BottomY+1,array("R"=>$BackgroundR,"G"=>$BackgroundG,"B"=>$BackgroundB,"Alpha"=>$BackgroundAlpha)); }
           if ( $DrawBorder ) { $this->drawRectangle($StartX-1,$TopY-1,$EndX+1,$BottomY+1,array("R"=>$BorderR,"G"=>$BorderG,"B"=>$BorderB,"Alpha"=>$BorderAlpha)); }

           if ( !is_array($PosArray) ) { $Value = $PosArray; $PosArray = ""; $PosArray[0] = $Value; }

           $RestoreShadow = $this->Shadow;
           $this->Shadow  = false;

           /* Determine the Max slope index */
           $LastX = null; $LastY = null; $MinSlope = 0; $MaxSlope = 1;
           foreach($PosArray as $Key => $Y)
            {
             if ( $Y != VOID && $LastX != null )
              { $Slope = ($LastY - $Y); if ( $Slope > $MaxSlope ) { $MaxSlope = $Slope; } if ( $Slope < $MinSlope ) { $MinSlope = $Slope; } }

             if ( $Y == VOID )
              { $LastX = null; $LastY = null; }
             else
              { $LastX = $X; $LastY = $Y; }
            }

           $LastX = null; $LastY = null; $LastColor = null;
           foreach($PosArray as $Key => $Y)
            {
             if ( $Y != VOID && $LastY != null )
              {
               $Slope = ($LastY - $Y);

               if ( $Slope >= 0 )
                {
                 $SlopeIndex = (100 / $MaxSlope) * $Slope;
                 $R = (($PositiveSlopeEndR - $PositiveSlopeStartR)/100)*$SlopeIndex+$PositiveSlopeStartR;
                 $G = (($PositiveSlopeEndG - $PositiveSlopeStartG)/100)*$SlopeIndex+$PositiveSlopeStartG;
                 $B = (($PositiveSlopeEndB - $PositiveSlopeStartB)/100)*$SlopeIndex+$PositiveSlopeStartB;
                }
               elseif ( $Slope < 0 )
                {
                 $SlopeIndex = (100 / abs($MinSlope)) * abs($Slope);
                 $R = (($NegativeSlopeEndR - $NegativeSlopeStartR)/100)*$SlopeIndex+$NegativeSlopeStartR;
                 $G = (($NegativeSlopeEndG - $NegativeSlopeStartG)/100)*$SlopeIndex+$NegativeSlopeStartG;
                 $B = (($NegativeSlopeEndB - $NegativeSlopeStartB)/100)*$SlopeIndex+$NegativeSlopeStartB;
                }

               $Color = array("R"=>$R,"G"=>$G,"B"=>$B);

               if ( $ShadedSlopeBox && $LastColor != null ) // && $Slope != 0
                {
                 $GradientSettings = array("StartR"=>$LastColor["R"],"StartG"=>$LastColor["G"],"StartB"=>$LastColor["B"],"EndR"=>$R,"EndG"=>$G,"EndB"=>$B);
                 $this->drawGradientArea($LastX,$TopY,$X,$BottomY,DIRECTION_HORIZONTAL,$GradientSettings);
                }
               elseif ( !$ShadedSlopeBox || $LastColor == null ) // || $Slope == 0
                $this->drawFilledRectangle(floor($LastX),$TopY,floor($X),$BottomY,$Color);

               $LastColor = $Color;
              }

             if ( $Y == VOID )
              { $LastY = null; }
             else
              { $LastX = $X; $LastY = $Y; }

             $X = $X + $XStep;
            }

           $YPos = $YPos + $CaptionHeight + $serieSpacing;
          }
         else
          {
           if ( $Caption )
            {
             $StartY = floor($this->GraphAreaY1-$CaptionWidth+$XMargin-$CaptionMargin);
             $EndY   = floor($this->GraphAreaY1-$CaptionMargin+$XMargin);
             if ( $CaptionLine )
              {
               $CaptionSettings = array("R"=>$R,"G"=>$G,"B"=>$B,"Alpha"=>$Alpha,"Ticks"=>$Ticks,"Weight"=>$Weight);
               if ( $CaptionBox ) { $this->drawFilledRectangle($XPos,$StartY,$XPos+$CaptionHeight,$EndY,array("R"=>$CaptionFillR,"G"=>$CaptionFillG,"B"=>$CaptionFillB,"BorderR"=>$CaptionBorderR,"BorderG"=>$CaptionBorderG,"BorderB"=>$CaptionBorderB,"Alpha"=>$CaptionFillAlpha)); }
               $this->drawLine($XPos+($CaptionHeight/2),$StartY+2,$XPos+($CaptionHeight/2),$EndY-2,$CaptionSettings);
              }
             else
              $this->drawFilledRectangle($XPos,$StartY,$XPos+$CaptionHeight,$EndY,array("R"=>$R,"G"=>$G,"B"=>$B,"BorderR"=>$CaptionBorderR,"BorderG"=>$CaptionBorderG,"BorderB"=>$CaptionBorderB));
            }


           if ( $XDivs == 0 ) { $XStep = ($this->GraphAreaY2-$this->GraphAreaY1)/4; } else { $XStep = ($this->GraphAreaY2-$this->GraphAreaY1-$XMargin*2)/$XDivs; }
           $Y = $this->GraphAreaY1 + $XMargin;

           $TopX    = $XPos + ($CaptionHeight/2) - ($DerivativeHeight/2);
           $BottomX = $XPos + ($CaptionHeight/2) + ($DerivativeHeight/2);

           $StartY  = floor($this->GraphAreaY1+$XMargin);
           $EndY    = floor($this->GraphAreaY2-$XMargin);

           if ( $DrawBackground ) { $this->drawFilledRectangle($TopX-1,$StartY-1,$BottomX+1,$EndY+1,array("R"=>$BackgroundR,"G"=>$BackgroundG,"B"=>$BackgroundB,"Alpha"=>$BackgroundAlpha)); }
           if ( $DrawBorder ) { $this->drawRectangle($TopX-1,$StartY-1,$BottomX+1,$EndY+1,array("R"=>$BorderR,"G"=>$BorderG,"B"=>$BorderB,"Alpha"=>$BorderAlpha)); }

           if ( !is_array($PosArray) ) { $Value = $PosArray; $PosArray = ""; $PosArray[0] = $Value; }

           $RestoreShadow = $this->Shadow;
           $this->Shadow  = false;

           /* Determine the Max slope index */
           $LastX = null; $LastY = null; $MinSlope = 0; $MaxSlope = 1;
           foreach($PosArray as $Key => $X)
            {
             if ( $X != VOID && $LastX != null )
              { $Slope = ($X - $LastX); if ( $Slope > $MaxSlope ) { $MaxSlope = $Slope; } if ( $Slope < $MinSlope ) { $MinSlope = $Slope; } }

             if ( $X == VOID )
              { $LastX = null; }
             else
              { $LastX = $X; }
            }

           $LastX = null; $LastY = null; $LastColor = null;
           foreach($PosArray as $Key => $X)
            {
             if ( $X != VOID && $LastX != null )
              {
               $Slope = ($X - $LastX);

               if ( $Slope >= 0 )
                {
                 $SlopeIndex = (100 / $MaxSlope) * $Slope;
                 $R = (($PositiveSlopeEndR - $PositiveSlopeStartR)/100)*$SlopeIndex+$PositiveSlopeStartR;
                 $G = (($PositiveSlopeEndG - $PositiveSlopeStartG)/100)*$SlopeIndex+$PositiveSlopeStartG;
                 $B = (($PositiveSlopeEndB - $PositiveSlopeStartB)/100)*$SlopeIndex+$PositiveSlopeStartB;
                }
               elseif ( $Slope < 0 )
                {
                 $SlopeIndex = (100 / abs($MinSlope)) * abs($Slope);
                 $R = (($NegativeSlopeEndR - $NegativeSlopeStartR)/100)*$SlopeIndex+$NegativeSlopeStartR;
                 $G = (($NegativeSlopeEndG - $NegativeSlopeStartG)/100)*$SlopeIndex+$NegativeSlopeStartG;
                 $B = (($NegativeSlopeEndB - $NegativeSlopeStartB)/100)*$SlopeIndex+$NegativeSlopeStartB;
                }

               $Color = array("R"=>$R,"G"=>$G,"B"=>$B);

               if ( $ShadedSlopeBox && $LastColor != null )
                {
                 $GradientSettings = array("StartR"=>$LastColor["R"],"StartG"=>$LastColor["G"],"StartB"=>$LastColor["B"],"EndR"=>$R,"EndG"=>$G,"EndB"=>$B);

                 $this->drawGradientArea($TopX,$LastY,$BottomX,$Y,DIRECTION_VERTICAL,$GradientSettings);
                }
               elseif ( !$ShadedSlopeBox || $LastColor == null )
                $this->drawFilledRectangle($TopX,floor($LastY),$BottomX,floor($Y),$Color);

               $LastColor = $Color;
              }

             if ( $X == VOID )
              { $LastX = null; }
             else
              { $LastX = $X; $LastY = $Y; }

             $Y = $Y + $XStep;
            }

           $XPos = $XPos + $CaptionHeight + $serieSpacing;
          }

         $this->Shadow = $RestoreShadow;
        }
      }
    }

    /* Draw the line of best fit */
    function drawBestFit($format="")
    {
     $OverrideTicks	= isset($format["Ticks"]) ? $format["Ticks"] : null;
     $OverrideR		= isset($format["R"]) ? $format["R"] : VOID;
     $OverrideG		= isset($format["G"]) ? $format["G"] : VOID;
     $OverrideB		= isset($format["B"]) ? $format["B"] : VOID;
     $OverrideAlpha	= isset($format["Alpha"]) ? $format["Alpha"] : VOID;

     $Data = $this->DataSet->getData();
     list($XMargin,$XDivs) = $this->scaleGetXSettings();

     foreach($Data["Series"] as $serie_name => $serie)
      {
       if ( $serie["isDrawable"] == true && $serie_name != $Data["Abscissa"] )
        {
         if ( $OverrideR != VOID && $OverrideG != VOID && $OverrideB != VOID ) { $R = $OverrideR; $G = $OverrideG; $B = $OverrideB; } else { $R = $serie["Color"]["R"]; $G = $serie["Color"]["G"]; $B = $serie["Color"]["B"]; }
         if ( $OverrideTicks == null ) { $Ticks = $serie["Ticks"]; } else { $Ticks = $OverrideTicks; }
         if ( $OverrideAlpha == VOID ) { $Alpha = $serie["Color"]["Alpha"]; } else { $Alpha = $OverrideAlpha; }

         $Color = array("R"=>$R,"G"=>$G,"B"=>$B,"Alpha"=>$Alpha,"Ticks"=>$Ticks);

         $AxisID   = $serie["Axis"];
         $PosArray = $this->scaleComputeY($serie["Data"],array("AxisID"=>$serie["Axis"]));

         if ( $Data["Orientation"] == SCALE_POS_LEFTRIGHT )
          {
           if ( $XDivs == 0 ) { $XStep = ($this->GraphAreaX2-$this->GraphAreaX1)/4; } else { $XStep = ($this->GraphAreaX2-$this->GraphAreaX1-$XMargin*2)/$XDivs; }
           $X = $this->GraphAreaX1 + $XMargin;

           if ( !is_array($PosArray) ) { $Value = $PosArray; $PosArray = ""; $PosArray[0] = $Value; }
           $Sxy = 0; $Sx = 0; $Sy = 0; $Sxx = 0;
           foreach($PosArray as $Key => $Y)
            {
             if ( $Y != VOID )
              {
               $Sxy = $Sxy + $X*$Y;
               $Sx  = $Sx + $X;
               $Sy  = $Sy + $Y;
               $Sxx = $Sxx + $X*$X;
              }

             $X = $X + $XStep;
            }
           $n = count($this->DataSet->stripVOID($PosArray)); //$n = count($PosArray);
           $M = (($n*$Sxy)-($Sx*$Sy)) / (($n*$Sxx)-($Sx*$Sx));
           $B = (($Sy)-($M*$Sx))/($n);

           $X1 = $this->GraphAreaX1 + $XMargin;
           $Y1 = $M * $X1 + $B;
           $X2 = $this->GraphAreaX2 - $XMargin;
           $Y2 = $M * $X2 + $B;

           if ( $Y1 < $this->GraphAreaY1 ) { $X1 = $X1 + ($this->GraphAreaY1-$Y1); $Y1 = $this->GraphAreaY1; }
           if ( $Y1 > $this->GraphAreaY2 ) { $X1 = $X1 + ($Y1-$this->GraphAreaY2); $Y1 = $this->GraphAreaY2; }
           if ( $Y2 < $this->GraphAreaY1 ) { $X2 = $X2 - ($this->GraphAreaY1-$Y2); $Y2 = $this->GraphAreaY1; }
           if ( $Y2 > $this->GraphAreaY2 ) { $X2 = $X2 - ($Y2-$this->GraphAreaY2); $Y2 = $this->GraphAreaY2; }

           $this->drawLine($X1,$Y1,$X2,$Y2,$Color);
          }
         else
          {
           if ( $XDivs == 0 ) { $YStep = ($this->GraphAreaY2-$this->GraphAreaY1)/4; } else { $YStep = ($this->GraphAreaY2-$this->GraphAreaY1-$XMargin*2)/$XDivs; }
           $Y = $this->GraphAreaY1 + $XMargin;

           if ( !is_array($PosArray) ) { $Value = $PosArray; $PosArray = ""; $PosArray[0] = $Value; }
           $Sxy = 0; $Sx = 0; $Sy = 0; $Sxx = 0;
           foreach($PosArray as $Key => $X)
            {
             if ( $X != VOID )
              {
               $Sxy = $Sxy + $X*$Y;
               $Sx  = $Sx + $Y;
               $Sy  = $Sy + $X;
               $Sxx = $Sxx + $Y*$Y;
              }

             $Y = $Y + $YStep;
            }
           $n = count($this->DataSet->stripVOID($PosArray)); //$n = count($PosArray);
           $M = (($n*$Sxy)-($Sx*$Sy)) / (($n*$Sxx)-($Sx*$Sx));
           $B = (($Sy)-($M*$Sx))/($n);

           $Y1 = $this->GraphAreaY1 + $XMargin;
           $X1 = $M * $Y1 + $B;
           $Y2 = $this->GraphAreaY2 - $XMargin;
           $X2 = $M * $Y2 + $B;

           if ( $X1 < $this->GraphAreaX1 ) { $Y1 = $Y1 + ($this->GraphAreaX1-$X1); $X1 = $this->GraphAreaX1; }
           if ( $X1 > $this->GraphAreaX2 ) { $Y1 = $Y1 + ($X1-$this->GraphAreaX2); $X1 = $this->GraphAreaX2; }
           if ( $X2 < $this->GraphAreaX1 ) { $Y2 = $Y2 - ($this->GraphAreaY1-$X2); $X2 = $this->GraphAreaX1; }
           if ( $X2 > $this->GraphAreaX2 ) { $Y2 = $Y2 - ($X2-$this->GraphAreaX2); $X2 = $this->GraphAreaX2; }

           $this->drawLine($X1,$Y1,$X2,$Y2,$Color);
          }
        }
      }
    }

    /* Write labels */
    function writeLabel($seriesName,$Indexes,$format="")
    {
     $OverrideTitle	= isset($format["OverrideTitle"]) ? $format["OverrideTitle"] : null;
     $ForceLabels	= isset($format["ForceLabels"]) ? $format["ForceLabels"] : null;
     $DrawPoint		= isset($format["DrawPoint"]) ? $format["DrawPoint"] : LABEL_POINT_BOX;
     $DrawVerticalLine	= isset($format["DrawVerticalLine"]) ? $format["DrawVerticalLine"] : false;
     $VerticalLineR	= isset($format["VerticalLineR"]) ? $format["VerticalLineR"] : 0;
     $VerticalLineG	= isset($format["VerticalLineG"]) ? $format["VerticalLineG"] : 0;
     $VerticalLineB	= isset($format["VerticalLineB"]) ? $format["VerticalLineB"] : 0;
     $VerticalLineAlpha	= isset($format["VerticalLineAlpha"]) ? $format["VerticalLineAlpha"] : 40;
     $VerticalLineTicks	= isset($format["VerticalLineTicks"]) ? $format["VerticalLineTicks"] : 2;

     $Data = $this->DataSet->getData();
     list($XMargin,$XDivs) = $this->scaleGetXSettings();

     if ( !is_array($Indexes) )    { $Index = $Indexes; $Indexes = ""; $Indexes[] = $Index; }
     if ( !is_array($seriesName) ) { $serie_name = $seriesName; $seriesName = ""; $seriesName[] = $serie_name; }
     if ( $ForceLabels != null && !is_array($ForceLabels) ) { $ForceLabel = $ForceLabels; $ForceLabels = ""; $ForceLabels[] = $ForceLabel; }

     foreach ($Indexes as $Key => $Index)
      {
       $series = "";

       if ( $Data["Orientation"] == SCALE_POS_LEFTRIGHT )
        {
         if ( $XDivs == 0 ) { $XStep = ($this->GraphAreaX2-$this->GraphAreaX1)/4; } else { $XStep = ($this->GraphAreaX2-$this->GraphAreaX1-$XMargin*2)/$XDivs; }
         $X = $this->GraphAreaX1 + $XMargin + $Index * $XStep;

         if ( $DrawVerticalLine ) { $this->drawLine($X,$this->GraphAreaY1+$Data["YMargin"],$X,$this->GraphAreaY2-$Data["YMargin"],array("R"=>$VerticalLineR,"G"=>$VerticalLineG,"B"=>$VerticalLineB,"Alpha"=>$VerticalLineAlpha,"Ticks"=>$VerticalLineTicks)); }

         $MinY = $this->GraphAreaY2;
         foreach ($seriesName as $iKey => $serie_name)
          {
           if ( isset($Data["Series"][$serie_name]["Data"][$Index]) )
            {
             $AxisID      = $Data["Series"][$serie_name]["Axis"];

             if ( $OverrideTitle != null)
              $Description = $OverrideTitle;
             elseif ( count($seriesName) == 1 )
              {
               if ( isset($Data["Abscissa"]) && isset($Data["Series"][$Data["Abscissa"]]["Data"][$Index]) )
                $Description = $Data["Series"][$serie_name]["Description"]." - ".$Data["Series"][$Data["Abscissa"]]["Data"][$Index];
               else
                $Description = $Data["Series"][$serie_name]["Description"];
              }
             elseif ( isset($Data["Abscissa"]) && isset($Data["Series"][$Data["Abscissa"]]["Data"][$Index]) )
              $Description = $Data["Series"][$Data["Abscissa"]]["Data"][$Index];

             $AxisMode     = $Data["Axis"][$AxisID]["Display"];
             $AxisFormat   = $Data["Axis"][$AxisID]["Format"];
             $AxisUnit     = $Data["Axis"][$AxisID]["Unit"];

             $serie = "";
             $serie["R"] = $Data["Series"][$serie_name]["Color"]["R"];
             $serie["G"] = $Data["Series"][$serie_name]["Color"]["G"];
             $serie["B"] = $Data["Series"][$serie_name]["Color"]["B"];
             $serie["Alpha"] = $Data["Series"][$serie_name]["Color"]["Alpha"];

             if ( count($seriesName) == 1 && isset($Data["Series"][$serie_name]["XOffset"]) )
              $serieOffset = $Data["Series"][$serie_name]["XOffset"];
             else
              $serieOffset = 0;

             $Value = $Data["Series"][$serie_name]["Data"][$Index];
             if ( $Value == VOID ) { $Value = "NaN"; }

             if ( $ForceLabels != null )
              $Caption = isset($ForceLabels[$Key]) ? $ForceLabels[$Key] : "Not set";
             else
              $Caption = $this->scaleFormat($Value,$AxisMode,$AxisFormat,$AxisUnit);

             if ( $this->LastChartLayout == CHART_LAST_LAYOUT_STACKED )
              {
               if ( $Value >=0 ) { $LookFor = "+"; } else { $LookFor = "-"; }

               $Value = 0; $Done = false;
               foreach($Data["Series"] as $Name => $serieLookup)
                {
                 if ( $serieLookup["isDrawable"] == true && $Name != $Data["Abscissa"] && !$Done )
                  {
                   if ( isset($Data["Series"][$Name]["Data"][$Index]) && $Data["Series"][$Name]["Data"][$Index] != VOID )
                    {
                     if ($Data["Series"][$Name]["Data"][$Index] >= 0 && $LookFor == "+" ) { $Value = $Value + $Data["Series"][$Name]["Data"][$Index]; }
                     if ($Data["Series"][$Name]["Data"][$Index] < 0 && $LookFor == "-" )  { $Value = $Value - $Data["Series"][$Name]["Data"][$Index]; }
                     if ($Name == $serie_name ) { $Done = true; }
                    }
                  }
                }
              }

             $X = floor($this->GraphAreaX1 + $XMargin + $Index * $XStep + $serieOffset);
             $Y = floor($this->scaleComputeY($Value,array("AxisID"=>$AxisID)));

             if ($Y < $MinY) { $MinY = $Y; }

             if ( $DrawPoint == LABEL_POINT_CIRCLE )
              $this->drawFilledCircle($X,$Y,3,array("R"=>255,"G"=>255,"B"=>255,"BorderR"=>0,"BorderG"=>0,"BorderB"=>0));
             elseif ( $DrawPoint == LABEL_POINT_BOX )
              $this->drawFilledRectangle($X-2,$Y-2,$X+2,$Y+2,array("R"=>255,"G"=>255,"B"=>255,"BorderR"=>0,"BorderG"=>0,"BorderB"=>0));

             $series[] = array("Format"=>$serie,"Caption"=>$Caption);
            }
          }
         $this->drawLabelBox($X,$MinY-3,$Description,$series,$format);

        }
       else
        {
         if ( $XDivs == 0 ) { $XStep = ($this->GraphAreaY2-$this->GraphAreaY1)/4; } else { $XStep = ($this->GraphAreaY2-$this->GraphAreaY1-$XMargin*2)/$XDivs; }
         $Y = $this->GraphAreaY1 + $XMargin + $Index * $XStep;

         if ( $DrawVerticalLine ) { $this->drawLine($this->GraphAreaX1+$Data["YMargin"],$Y,$this->GraphAreaX2-$Data["YMargin"],$Y,array("R"=>$VerticalLineR,"G"=>$VerticalLineG,"B"=>$VerticalLineB,"Alpha"=>$VerticalLineAlpha,"Ticks"=>$VerticalLineTicks)); }

         $MinX = $this->GraphAreaX2;
         foreach ($seriesName as $Key => $serie_name)
          {
           if ( isset($Data["Series"][$serie_name]["Data"][$Index]) )
            {
             $AxisID      = $Data["Series"][$serie_name]["Axis"];

             if ( $OverrideTitle != null)
              $Description = $OverrideTitle;
             elseif ( count($seriesName) == 1 )
              {
               if ( isset($Data["Abscissa"]) && isset($Data["Series"][$Data["Abscissa"]]["Data"][$Index]) )
                $Description = $Data["Series"][$serie_name]["Description"]." - ".$Data["Series"][$Data["Abscissa"]]["Data"][$Index];
               else
                $Description = $Data["Series"][$serie_name]["Description"];
              }
             elseif ( isset($Data["Abscissa"]) && isset($Data["Series"][$Data["Abscissa"]]["Data"][$Index]) )
              $Description = $Data["Series"][$Data["Abscissa"]]["Data"][$Index];

             $AxisMode     = $Data["Axis"][$AxisID]["Display"];
             $AxisFormat   = $Data["Axis"][$AxisID]["Format"];
             $AxisUnit     = $Data["Axis"][$AxisID]["Unit"];

             $serie = "";
             if ( isset($Data["Extended"]["Palette"][$Index] ) )
              {
               $serie["R"] = $Data["Extended"]["Palette"][$Index]["R"];
               $serie["G"] = $Data["Extended"]["Palette"][$Index]["G"];
               $serie["B"] = $Data["Extended"]["Palette"][$Index]["B"];
               $serie["Alpha"] = $Data["Extended"]["Palette"][$Index]["Alpha"];
              }
             else
              {
               $serie["R"] = $Data["Series"][$serie_name]["Color"]["R"];
               $serie["G"] = $Data["Series"][$serie_name]["Color"]["G"];
               $serie["B"] = $Data["Series"][$serie_name]["Color"]["B"];
               $serie["Alpha"] = $Data["Series"][$serie_name]["Color"]["Alpha"];
              }

             if ( count($seriesName) == 1 && isset($Data["Series"][$serie_name]["XOffset"]) )
              $serieOffset = $Data["Series"][$serie_name]["XOffset"];
             else
              $serieOffset = 0;

             $Value = $Data["Series"][$serie_name]["Data"][$Index];
             if ( $ForceLabels != null )
              $Caption = isset($ForceLabels[$Key]) ? $ForceLabels[$Key] : "Not set";
             else
              $Caption = $this->scaleFormat($Value,$AxisMode,$AxisFormat,$AxisUnit);
             if ( $Value == VOID ) { $Value = "NaN"; }

             if ( $this->LastChartLayout == CHART_LAST_LAYOUT_STACKED )
              {
               if ( $Value >=0 ) { $LookFor = "+"; } else { $LookFor = "-"; }

               $Value = 0; $Done = false;
               foreach($Data["Series"] as $Name => $serieLookup)
                {
                 if ( $serieLookup["isDrawable"] == true && $Name != $Data["Abscissa"] && !$Done )
                  {
                   if ( isset($Data["Series"][$Name]["Data"][$Index]) && $Data["Series"][$Name]["Data"][$Index] != VOID )
                    {
                     if ($Data["Series"][$Name]["Data"][$Index] >= 0 && $LookFor == "+" ) { $Value = $Value + $Data["Series"][$Name]["Data"][$Index]; }
                     if ($Data["Series"][$Name]["Data"][$Index] < 0 && $LookFor == "-" )  { $Value = $Value - $Data["Series"][$Name]["Data"][$Index]; }
                     if ($Name == $serie_name ) { $Done = true; }
                    }
                  }
                }
              }

             $X = floor($this->scaleComputeY($Value,array("AxisID"=>$AxisID)));
             $Y = floor($this->GraphAreaY1 + $XMargin + $Index * $XStep + $serieOffset);

             if ($X < $MinX) { $MinX = $X; }

             if ( $DrawPoint == LABEL_POINT_CIRCLE )
              $this->drawFilledCircle($X,$Y,3,array("R"=>255,"G"=>255,"B"=>255,"BorderR"=>0,"BorderG"=>0,"BorderB"=>0));
             elseif ( $DrawPoint == LABEL_POINT_BOX )
              $this->drawFilledRectangle($X-2,$Y-2,$X+2,$Y+2,array("R"=>255,"G"=>255,"B"=>255,"BorderR"=>0,"BorderG"=>0,"BorderB"=>0));

             $series[] = array("Format"=>$serie,"Caption"=>$Caption);
            }
          }
         $this->drawLabelBox($MinX,$Y-3,$Description,$series,$format);

        }
      }
    }

    /* Draw a label box */
    function drawLabelBox($X,$Y,$Title,$Captions,$format="")
    {
     $NoTitle		= isset($format["NoTitle"]) ? $format["NoTitle"] : null;
     $BoxWidth		= isset($format["BoxWidth"]) ? $format["BoxWidth"] : 50;
     $DrawSerieColor	= isset($format["DrawSerieColor"]) ? $format["DrawSerieColor"] : true;
     $serieR		= isset($format["SerieR"]) ? $format["SerieR"] : null;
     $serieG		= isset($format["SerieG"]) ? $format["SerieG"] : null;
     $serieB		= isset($format["SerieB"]) ? $format["SerieB"] : null;
     $serieAlpha	= isset($format["SerieAlpha"]) ? $format["SerieAlpha"] : null;
     $serieBoxSize	= isset($format["SerieBoxSize"]) ? $format["SerieBoxSize"] : 6;
     $serieBoxSpacing	= isset($format["SerieBoxSpacing"]) ? $format["SerieBoxSpacing"] : 4;
     $VerticalMargin	= isset($format["VerticalMargin"]) ? $format["VerticalMargin"] : 10;
     $HorizontalMargin	= isset($format["HorizontalMargin"]) ? $format["HorizontalMargin"] : 8;
     $R			= isset($format["R"]) ? $format["R"] : $this->FontColorR;
     $G			= isset($format["G"]) ? $format["G"] : $this->FontColorG;
     $B			= isset($format["B"]) ? $format["B"] : $this->FontColorB;
     $Alpha		= isset($format["Alpha"]) ? $format["Alpha"] : $this->FontColorA;
     $FontName		= isset($format["FontName"]) ? $format["FontName"] : $this->FontName;
     $FontSize		= isset($format["FontSize"]) ? $format["FontSize"] : $this->FontSize;
     $TitleMode		= isset($format["TitleMode"]) ? $format["TitleMode"] : LABEL_TITLE_NOBACKGROUND;
     $TitleR		= isset($format["TitleR"]) ? $format["TitleR"] : $R;
     $TitleG		= isset($format["TitleG"]) ? $format["TitleG"] : $G;
     $TitleB		= isset($format["TitleB"]) ? $format["TitleB"] : $B;
     $TitleBackgroundR	= isset($format["TitleBackgroundR"]) ? $format["TitleBackgroundR"] : 0;
     $TitleBackgroundG	= isset($format["TitleBackgroundG"]) ? $format["TitleBackgroundG"] : 0;
     $TitleBackgroundB	= isset($format["TitleBackgroundB"]) ? $format["TitleBackgroundB"] : 0;
     $GradientStartR	= isset($format["GradientStartR"]) ? $format["GradientStartR"] : 255;
     $GradientStartG	= isset($format["GradientStartG"]) ? $format["GradientStartG"] : 255;
     $GradientStartB	= isset($format["GradientStartB"]) ? $format["GradientStartB"] : 255;
     $GradientEndR	= isset($format["GradientEndR"]) ? $format["GradientEndR"] : 220;
     $GradientEndG	= isset($format["GradientEndG"]) ? $format["GradientEndG"] : 220;
     $GradientEndB	= isset($format["GradientEndB"]) ? $format["GradientEndB"] : 220;

     if ( !$DrawSerieColor ) { $serieBoxSize = 0; $serieBoxSpacing = 0; }

     $TxtPos      = $this->getTextBox($X,$Y,$FontName,$FontSize,0,$Title);
     $TitleWidth  = ($TxtPos[1]["X"] - $TxtPos[0]["X"])+$VerticalMargin*2;
     $TitleHeight = ($TxtPos[0]["Y"] - $TxtPos[2]["Y"]);

     if ( $NoTitle ) { $TitleWidth = 0; $TitleHeight = 0; }

     $CaptionWidth = 0; $CaptionHeight = -$HorizontalMargin;
     foreach($Captions as $Key =>$Caption)
      {
       $TxtPos        = $this->getTextBox($X,$Y,$FontName,$FontSize,0,$Caption["Caption"]);
       $CaptionWidth  = max($CaptionWidth,($TxtPos[1]["X"] - $TxtPos[0]["X"])+$VerticalMargin*2);
       $CaptionHeight = $CaptionHeight + max(($TxtPos[0]["Y"] - $TxtPos[2]["Y"]),($serieBoxSize+2)) + $HorizontalMargin;
      }

     if ( $CaptionHeight <= 5 ) { $CaptionHeight = $CaptionHeight + $HorizontalMargin/2; }

     if ( $DrawSerieColor ) { $CaptionWidth = $CaptionWidth + $serieBoxSize + $serieBoxSpacing; }

     $BoxWidth = max($BoxWidth,$TitleWidth,$CaptionWidth);

     $XMin = $X - 5 - floor(($BoxWidth-10) / 2);
     $XMax = $X + 5 + floor(($BoxWidth-10) / 2);

     $RestoreShadow = $this->Shadow;
     if ( $this->Shadow == true )
      {
       $this->Shadow = false;

       $Poly = "";
       $Poly[] = $X+$this->ShadowX; $Poly[] = $Y+$this->ShadowX;
       $Poly[] = $X+5+$this->ShadowX; $Poly[] = $Y-5+$this->ShadowX;
       $Poly[] = $XMax+$this->ShadowX; $Poly[] = $Y-5+$this->ShadowX;

       if ( $NoTitle )
        {
         $Poly[] = $XMax+$this->ShadowX; $Poly[] = $Y-5-$TitleHeight-$CaptionHeight-$HorizontalMargin*2+$this->ShadowX;
         $Poly[] = $XMin+$this->ShadowX; $Poly[] = $Y-5-$TitleHeight-$CaptionHeight-$HorizontalMargin*2+$this->ShadowX;
        }
       else
        {
         $Poly[] = $XMax+$this->ShadowX; $Poly[] = $Y-5-$TitleHeight-$CaptionHeight-$HorizontalMargin*3+$this->ShadowX;
         $Poly[] = $XMin+$this->ShadowX; $Poly[] = $Y-5-$TitleHeight-$CaptionHeight-$HorizontalMargin*3+$this->ShadowX;
        }

       $Poly[] = $XMin+$this->ShadowX; $Poly[] = $Y-5+$this->ShadowX;
       $Poly[] = $X-5+$this->ShadowX; $Poly[] = $Y-5+$this->ShadowX;
       $this->drawPolygon($Poly,array("R"=>$this->ShadowR,"G"=>$this->ShadowG,"B"=>$this->ShadowB,"Alpha"=>$this->Shadowa));
      }

     /* Draw the background */
     $GradientSettings = array("StartR"=>$GradientStartR,"StartG"=>$GradientStartG,"StartB"=>$GradientStartB,"EndR"=>$GradientEndR,"EndG"=>$GradientEndG,"EndB"=>$GradientEndB);
     if ( $NoTitle )
      $this->drawGradientArea($XMin,$Y-5-$TitleHeight-$CaptionHeight-$HorizontalMargin*2,$XMax,$Y-6,DIRECTION_VERTICAL,$GradientSettings);
     else
      $this->drawGradientArea($XMin,$Y-5-$TitleHeight-$CaptionHeight-$HorizontalMargin*3,$XMax,$Y-6,DIRECTION_VERTICAL,$GradientSettings);
     $Poly = ""; $Poly[] = $X; $Poly[] = $Y; $Poly[] = $X-5; $Poly[] = $Y-5; $Poly[] = $X+5; $Poly[] = $Y-5;
     $this->drawPolygon($Poly,array("R"=>$GradientEndR,"G"=>$GradientEndG,"B"=>$GradientEndB,"NoBorder"=>true));

     /* Outer border */
     $OuterBorderColor = $this->allocateColor($this->Picture,100,100,100,100);
     imageline($this->Picture,$XMin,$Y-5,$X-5,$Y-5,$OuterBorderColor);
     imageline($this->Picture,$X,$Y,$X-5,$Y-5,$OuterBorderColor);
     imageline($this->Picture,$X,$Y,$X+5,$Y-5,$OuterBorderColor);
     imageline($this->Picture,$X+5,$Y-5,$XMax,$Y-5,$OuterBorderColor);
     if ( $NoTitle )
      {
       imageline($this->Picture,$XMin,$Y-5-$TitleHeight-$CaptionHeight-$HorizontalMargin*2,$XMin,$Y-5,$OuterBorderColor);
       imageline($this->Picture,$XMax,$Y-5-$TitleHeight-$CaptionHeight-$HorizontalMargin*2,$XMax,$Y-5,$OuterBorderColor);
       imageline($this->Picture,$XMin,$Y-5-$TitleHeight-$CaptionHeight-$HorizontalMargin*2,$XMax,$Y-5-$TitleHeight-$CaptionHeight-$HorizontalMargin*2,$OuterBorderColor);
      }
     else
      {
       imageline($this->Picture,$XMin,$Y-5-$TitleHeight-$CaptionHeight-$HorizontalMargin*3,$XMin,$Y-5,$OuterBorderColor);
       imageline($this->Picture,$XMax,$Y-5-$TitleHeight-$CaptionHeight-$HorizontalMargin*3,$XMax,$Y-5,$OuterBorderColor);
       imageline($this->Picture,$XMin,$Y-5-$TitleHeight-$CaptionHeight-$HorizontalMargin*3,$XMax,$Y-5-$TitleHeight-$CaptionHeight-$HorizontalMargin*3,$OuterBorderColor);
      }

     /* Inner border */
     $InnerBorderColor = $this->allocateColor($this->Picture,255,255,255,100);
     imageline($this->Picture,$XMin+1,$Y-6,$X-5,$Y-6,$InnerBorderColor);
     imageline($this->Picture,$X,$Y-1,$X-5,$Y-6,$InnerBorderColor);
     imageline($this->Picture,$X,$Y-1,$X+5,$Y-6,$InnerBorderColor);
     imageline($this->Picture,$X+5,$Y-6,$XMax-1,$Y-6,$InnerBorderColor);
     if ( $NoTitle )
      {
       imageline($this->Picture,$XMin+1,$Y-4-$TitleHeight-$CaptionHeight-$HorizontalMargin*2,$XMin+1,$Y-6,$InnerBorderColor);
       imageline($this->Picture,$XMax-1,$Y-4-$TitleHeight-$CaptionHeight-$HorizontalMargin*2,$XMax-1,$Y-6,$InnerBorderColor);
       imageline($this->Picture,$XMin+1,$Y-4-$TitleHeight-$CaptionHeight-$HorizontalMargin*2,$XMax-1,$Y-4-$TitleHeight-$CaptionHeight-$HorizontalMargin*2,$InnerBorderColor);
      }
     else
      {
       imageline($this->Picture,$XMin+1,$Y-4-$TitleHeight-$CaptionHeight-$HorizontalMargin*3,$XMin+1,$Y-6,$InnerBorderColor);
       imageline($this->Picture,$XMax-1,$Y-4-$TitleHeight-$CaptionHeight-$HorizontalMargin*3,$XMax-1,$Y-6,$InnerBorderColor);
       imageline($this->Picture,$XMin+1,$Y-4-$TitleHeight-$CaptionHeight-$HorizontalMargin*3,$XMax-1,$Y-4-$TitleHeight-$CaptionHeight-$HorizontalMargin*3,$InnerBorderColor);
      }

     /* Draw the separator line */
     if ( $TitleMode == LABEL_TITLE_NOBACKGROUND && !$NoTitle )
      {
       $YPos    = $Y-7-$CaptionHeight-$HorizontalMargin-$HorizontalMargin/2;
       $XMargin = $VerticalMargin / 2;
       $this->drawLine($XMin+$XMargin,$YPos+1,$XMax-$XMargin,$YPos+1,array("R"=>$GradientEndR,"G"=>$GradientEndG,"B"=>$GradientEndB));
       $this->drawLine($XMin+$XMargin,$YPos,$XMax-$XMargin,$YPos,array("R"=>$GradientStartR,"G"=>$GradientStartG,"B"=>$GradientStartB));
      }
     elseif ( $TitleMode == LABEL_TITLE_BACKGROUND )
      {
       $this->drawFilledRectangle($XMin,$Y-5-$TitleHeight-$CaptionHeight-$HorizontalMargin*3,$XMax,$Y-5-$TitleHeight-$CaptionHeight-$HorizontalMargin+$HorizontalMargin/2,array("R"=>$TitleBackgroundR,"G"=>$TitleBackgroundG,"B"=>$TitleBackgroundB));
       imageline($this->Picture,$XMin+1,$Y-5-$TitleHeight-$CaptionHeight-$HorizontalMargin+$HorizontalMargin/2+1,$XMax-1,$Y-5-$TitleHeight-$CaptionHeight-$HorizontalMargin+$HorizontalMargin/2+1,$InnerBorderColor);
      }

     /* Write the description */
     if ( !$NoTitle )
      $this->drawText($XMin+$VerticalMargin,$Y-7-$CaptionHeight-$HorizontalMargin*2,$Title,array("Align"=>TEXT_ALIGN_BOTTOMLEFT,"R"=>$TitleR,"G"=>$TitleG,"B"=>$TitleB));

     /* Write the value */
     $YPos = $Y-5-$HorizontalMargin; $XPos = $XMin+$VerticalMargin+$serieBoxSize+$serieBoxSpacing;
     foreach($Captions as $Key => $Caption)
      {
       $CaptionTxt    = $Caption["Caption"];
       $TxtPos        = $this->getTextBox($XPos,$YPos,$FontName,$FontSize,0,$CaptionTxt);
       $CaptionHeight = ($TxtPos[0]["Y"] - $TxtPos[2]["Y"]);

       /* Write the serie color if needed */
       if ( $DrawSerieColor )
        {
         $BoxSettings = array("R"=>$Caption["Format"]["R"],"G"=>$Caption["Format"]["G"],"B"=>$Caption["Format"]["B"],"Alpha"=>$Caption["Format"]["Alpha"],"BorderR"=>0,"BorderG"=>0,"BorderB"=>0);
         $this->drawFilledRectangle($XMin+$VerticalMargin,$YPos-$serieBoxSize,$XMin+$VerticalMargin+$serieBoxSize,$YPos,$BoxSettings);
        }

       $this->drawText($XPos,$YPos,$CaptionTxt,array("Align"=>TEXT_ALIGN_BOTTOMLEFT));

       $YPos = $YPos - $CaptionHeight - $HorizontalMargin;
      }

     $this->Shadow = $RestoreShadow;
    }

    /* Draw a basic shape */
    function drawShape($X,$Y,$Shape,$PlotSize,$PlotBorder,$BorderSize,$R,$G,$B,$Alpha,$BorderR,$BorderG,$BorderB,$BorderAlpha)
    {
     if ( $Shape == SERIE_SHAPE_FILLEDCIRCLE )
      {
       if ( $PlotBorder ) { $this->drawFilledCircle($X,$Y,$PlotSize+$BorderSize,array("R"=>$BorderR,"G"=>$BorderG,"B"=>$BorderB,"Alpha"=>$BorderAlpha)); }
       $this->drawFilledCircle($X,$Y,$PlotSize,array("R"=>$R,"G"=>$G,"B"=>$B,"Alpha"=>$Alpha));
      }
     elseif ( $Shape == SERIE_SHAPE_FILLEDSQUARE )
      {
       if ( $PlotBorder ) { $this->drawFilledRectangle($X-$PlotSize-$BorderSize,$Y-$PlotSize-$BorderSize,$X+$PlotSize+$BorderSize,$Y+$PlotSize+$BorderSize,array("R"=>$BorderR,"G"=>$BorderG,"B"=>$BorderB,"Alpha"=>$BorderAlpha)); }
       $this->drawFilledRectangle($X-$PlotSize,$Y-$PlotSize,$X+$PlotSize,$Y+$PlotSize,array("R"=>$R,"G"=>$G,"B"=>$B,"Alpha"=>$Alpha));
      }
     elseif ( $Shape == SERIE_SHAPE_FILLEDTRIANGLE )
      {
       if ( $PlotBorder )
        {
         $Pos = ""; $Pos[]=$X; $Pos[]=$Y-$PlotSize-$BorderSize; $Pos[]=$X-$PlotSize-$BorderSize; $Pos[]=$Y+$PlotSize+$BorderSize; $Pos[]=$X+$PlotSize+$BorderSize; $Pos[]=$Y+$PlotSize+$BorderSize;
         $this->drawPolygon($Pos,array("R"=>$BorderR,"G"=>$BorderG,"B"=>$BorderB,"Alpha"=>$BorderAlpha));
        }

       $Pos = ""; $Pos[]=$X; $Pos[]=$Y-$PlotSize; $Pos[]=$X-$PlotSize; $Pos[]=$Y+$PlotSize; $Pos[]=$X+$PlotSize; $Pos[]=$Y+$PlotSize;
       $this->drawPolygon($Pos,array("R"=>$R,"G"=>$G,"B"=>$B,"Alpha"=>$Alpha));
      }
     elseif ( $Shape == SERIE_SHAPE_TRIANGLE )
      {
       $this->drawLine($X,$Y-$PlotSize,$X-$PlotSize,$Y+$PlotSize,array("R"=>$R,"G"=>$G,"B"=>$B,"Alpha"=>$Alpha));
       $this->drawLine($X-$PlotSize,$Y+$PlotSize,$X+$PlotSize,$Y+$PlotSize,array("R"=>$R,"G"=>$G,"B"=>$B,"Alpha"=>$Alpha));
       $this->drawLine($X+$PlotSize,$Y+$PlotSize,$X,$Y-$PlotSize,array("R"=>$R,"G"=>$G,"B"=>$B,"Alpha"=>$Alpha));
      }
     elseif ( $Shape == SERIE_SHAPE_SQUARE )
      $this->drawRectangle($X-$PlotSize,$Y-$PlotSize,$X+$PlotSize,$Y+$PlotSize,array("R"=>$R,"G"=>$G,"B"=>$B,"Alpha"=>$Alpha));
     elseif ( $Shape == SERIE_SHAPE_CIRCLE )
      $this->drawCircle($X,$Y,$PlotSize,$PlotSize,array("R"=>$R,"G"=>$G,"B"=>$B,"Alpha"=>$Alpha));
     elseif ( $Shape == SERIE_SHAPE_DIAMOND )
      {
       $Pos = ""; $Pos[]=$X-$PlotSize; $Pos[]=$Y; $Pos[]=$X; $Pos[]=$Y-$PlotSize; $Pos[]=$X+$PlotSize; $Pos[]=$Y; $Pos[]=$X; $Pos[]=$Y+$PlotSize;
       $this->drawPolygon($Pos,array("NoFill"=>true,"BorderR"=>$R,"BorderG"=>$G,"BorderB"=>$B,"BorderAlpha"=>$Alpha));
      }
     elseif ( $Shape == SERIE_SHAPE_FILLEDDIAMOND )
      {
       if ( $PlotBorder )
        {
         $Pos = ""; $Pos[]=$X-$PlotSize-$BorderSize; $Pos[]=$Y; $Pos[]=$X; $Pos[]=$Y-$PlotSize-$BorderSize; $Pos[]=$X+$PlotSize+$BorderSize; $Pos[]=$Y; $Pos[]=$X; $Pos[]=$Y+$PlotSize+$BorderSize;
         $this->drawPolygon($Pos,array("R"=>$BorderR,"G"=>$BorderG,"B"=>$BorderB,"Alpha"=>$BorderAlpha));
        }

       $Pos = ""; $Pos[]=$X-$PlotSize; $Pos[]=$Y; $Pos[]=$X; $Pos[]=$Y-$PlotSize; $Pos[]=$X+$PlotSize; $Pos[]=$Y; $Pos[]=$X; $Pos[]=$Y+$PlotSize;
       $this->drawPolygon($Pos,array("R"=>$R,"G"=>$G,"B"=>$B,"Alpha"=>$Alpha));
      }
    }

    function drawPolygonChart($points,$format="")
    {
     $R			= isset($format["R"]) ? $format["R"] : 0;
     $G			= isset($format["G"]) ? $format["G"] : 0;
     $B			= isset($format["B"]) ? $format["B"] : 0;
     $Alpha		= isset($format["Alpha"]) ? $format["Alpha"] : 100;
     $NoFill		= isset($format["NoFill"]) ? $format["NoFill"] : false;
     $NoBorder		= isset($format["NoBorder"]) ? $format["NoBorder"] : false;
     $BorderR		= isset($format["BorderR"]) ? $format["BorderR"] : $R;
     $BorderG		= isset($format["BorderG"]) ? $format["BorderG"] : $G;
     $BorderB		= isset($format["BorderB"]) ? $format["BorderB"] : $B;
     $BorderAlpha 	= isset($format["BorderAlpha"]) ? $format["BorderAlpha"] : $Alpha / 2;
     $Surrounding	= isset($format["Surrounding"]) ? $format["Surrounding"] : null;
     $Threshold         = isset($format["Threshold"]) ? $format["Threshold"] : null;

     if ( $Surrounding != null ) { $BorderR = $R+$Surrounding; $BorderG = $G+$Surrounding; $BorderB = $B+$Surrounding; }

     $RestoreShadow = $this->Shadow;
     $this->Shadow = false;

     $AllIntegers = true;
     for($i=0;$i<=count($points)-2;$i=$i+2)
      { if ( $this->getFirstDecimal($points[$i+1]) != 0 ) { $AllIntegers = false; } }

     /* Convert polygon to segments */
     $Segments = "";
     for($i=2;$i<=count($points)-2;$i=$i+2)
      { $Segments[] = array("X1"=>$points[$i-2],"Y1"=>$points[$i-1],"X2"=>$points[$i],"Y2"=>$points[$i+1]); }
     $Segments[] = array("X1"=>$points[$i-2],"Y1"=>$points[$i-1],"X2"=>$points[0],"Y2"=>$points[1]);

     /* Simplify straight lines */
     $Result = ""; $inHorizon = false; $LastX = VOID;
     foreach($Segments as $Key => $Pos)
      {
       if ( $Pos["Y1"] != $Pos["Y2"] )
        {
         if ( $inHorizon ) { $inHorizon = false; $Result[] = array("X1"=>$LastX,"Y1"=>$Pos["Y1"],"X2"=>$Pos["X1"],"Y2"=>$Pos["Y1"]); }

         $Result[] = array("X1"=>$Pos["X1"],"Y1"=>$Pos["Y1"],"X2"=>$Pos["X2"],"Y2"=>$Pos["Y2"]);
        }
       else { if ( !$inHorizon ) { $inHorizon = true; $LastX = $Pos["X1"];} }
      }
     $Segments = $Result;

     /* Do we have something to draw */
     if ( $Segments == "" ) { return 0; }

     /* For segments debugging purpose */
     //foreach($Segments as $Key => $Pos)
     // echo $Pos["X1"].",".$Pos["Y1"].",".$Pos["X2"].",".$Pos["Y2"]."\r\n";

     /* Find out the min & max Y boundaries */
     $MinY = OUT_OF_SIGHT; $MaxY = OUT_OF_SIGHT;
     foreach($Segments as $Key => $Coords)
      {
       if ( $MinY == OUT_OF_SIGHT || $MinY > min($Coords["Y1"],$Coords["Y2"]) ) { $MinY = min($Coords["Y1"],$Coords["Y2"]); }
       if ( $MaxY == OUT_OF_SIGHT || $MaxY < max($Coords["Y1"],$Coords["Y2"]) ) { $MaxY = max($Coords["Y1"],$Coords["Y2"]); }
      }

     if ( $AllIntegers ) { $YStep = 1; } else { $YStep = .5; }

     $MinY = floor($MinY); $MaxY = floor($MaxY);

     /* Scan each Y lines */
     $DefaultColor = $this->allocateColor($this->Picture,$R,$G,$B,$Alpha);
     $DebugLine = 0; $DebugColor = $this->allocateColor($this->Picture,255,0,0,100);

     $MinY = floor($MinY); $MaxY = floor($MaxY); $YStep = 1;

     if ( !$NoFill )
      {
       //if ( $DebugLine ) { $MinY = $DebugLine; $MaxY = $DebugLine; }
       for($Y=$MinY;$Y<=$MaxY;$Y=$Y+$YStep)
        {
         $Intersections = ""; $LastSlope = null; $RestoreLast = "-";
         foreach($Segments as $Key => $Coords)
          {
           $X1 = $Coords["X1"]; $X2 = $Coords["X2"]; $Y1 = $Coords["Y1"]; $Y2 = $Coords["Y2"];

           if ( min($Y1,$Y2) <= $Y && max($Y1,$Y2) >= $Y )
            {
             if ( $Y1 == $Y2 )
              { $X = $X1; }
             else
              { $X = $X1 + ( ($Y-$Y1)*$X2 - ($Y-$Y1)*$X1 ) / ($Y2-$Y1); }

             $X = floor($X);

             if ( $X2 == $X1 )
              { $Slope = "!"; }
             else
              {
               $SlopeC = ($Y2 - $Y1) / ($X2 - $X1);
               if( $SlopeC == 0 )
                { $Slope = "="; }
               elseif( $SlopeC > 0 )
                { $Slope = "+"; }
               elseif ( $SlopeC < 0 )
                { $Slope = "-"; }
              }

             if ( !is_array($Intersections) )
              { $Intersections[] = $X; }
             elseif( !in_array($X,$Intersections) )
              { $Intersections[] = $X; }
             elseif( in_array($X,$Intersections) )
              {
               if ($Y == $DebugLine) { echo $Slope."/".$LastSlope."(".$X.") "; }

               if ( $Slope == "=" && $LastSlope == "-"  )                             { $Intersections[] = $X; }
               if ( $Slope != $LastSlope && $LastSlope != "!" && $LastSlope != "=" )  { $Intersections[] = $X; }
               if ( $Slope != $LastSlope && $LastSlope == "!" && $Slope == "+" )      { $Intersections[] = $X; }
              }

             if ( is_array($Intersections) && in_array($X,$Intersections) && $LastSlope == "=" && ($Slope == "-" )) { $Intersections[] = $X; }

             $LastSlope = $Slope;
            }
          }
         if ( $RestoreLast != "-" ) { $Intersections[] = $RestoreLast; echo "@".$Y."\r\n"; }

         if ( is_array($Intersections) )
          {
           sort($Intersections);

           if ($Y == $DebugLine) { print_r($Intersections); }

           /* Remove null plots */
           $Result = "";
           for($i=0;$i<=count($Intersections)-1;$i=$i+2)
            {
             if ( isset($Intersections[$i+1]) )
              { if ( $Intersections[$i] != $Intersections[$i+1] ) { $Result[] = $Intersections[$i]; $Result[] = $Intersections[$i+1]; } }
            }

           if ( is_array($Result) )
            {
             $Intersections = $Result;

             $LastX = OUT_OF_SIGHT;
             foreach($Intersections as $Key => $X)
              {
               if ( $LastX == OUT_OF_SIGHT )
                $LastX = $X;
               elseif ( $LastX != OUT_OF_SIGHT )
                {
                 if ( $this->getFirstDecimal($LastX) > 1 ) { $LastX++; }

                 $Color = $DefaultColor;
                 if ( $Threshold != null )
                  {
                   foreach($Threshold as $Key => $Parameters)
                    {
                     if ( $Y <= $Parameters["MinX"] && $Y >= $Parameters["MaxX"])
                      {
                       if ( isset($Parameters["R"]) ) { $R = $Parameters["R"]; } else { $R = 0; }
                       if ( isset($Parameters["G"]) ) { $G = $Parameters["G"]; } else { $G = 0; }
                       if ( isset($Parameters["B"]) ) { $B = $Parameters["B"]; } else { $B = 0; }
                       if ( isset($Parameters["Alpha"]) ) { $Alpha = $Parameters["Alpha"]; } else { $Alpha = 100; }
                       $Color = $this->allocateColor($this->Picture,$R,$G,$B,$Alpha);
                      }
                    }
                  }

                 imageline($this->Picture,$LastX,$Y,$X,$Y,$Color);

                 if ( $Y == $DebugLine) { imageline($this->Picture,$LastX,$Y,$X,$Y,$DebugColor); }

                 $LastX = OUT_OF_SIGHT;
                }
              }
            }
          }
        }
      }

     /* Draw the polygon border, if required */
     if ( !$NoBorder)
      {
       foreach($Segments as $Key => $Coords)
        $this->drawLine($Coords["X1"],$Coords["Y1"],$Coords["X2"],$Coords["Y2"],array("R"=>$BorderR,"G"=>$BorderG,"B"=>$BorderB,"Alpha"=>$BorderAlpha,"Threshold"=>$Threshold));
      }

     $this->Shadow = $RestoreShadow;
    }

    /* Return the abscissa margin */
    function getAbscissaMargin($Data)
    {
     foreach($Data["Axis"] as $AxisID => $Values) { if ( $Values["Identity"] == AXIS_X ) { return($Values["Margin"]); } }
     return 0;
    }
}