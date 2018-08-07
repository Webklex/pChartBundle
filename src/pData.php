<?php
 /*
         pDraw - class to manipulate data arrays

         Version         : 2.1.3
         Made by         : Jean-Damien POGOLOTTI
         Last Update : 09/09/11

         This file can be distributed under the license you can find at :

                                             http://www.pchart.net/license

         You can find the whole class documentation on the pChart web site.
 */

namespace Webklex\pChart;

 /* Axis configuration */
 define("AXIS_FORMAT_DEFAULT"    , 680001);
 define("AXIS_FORMAT_TIME"         , 680002);
 define("AXIS_FORMAT_DATE"         , 680003);
 define("AXIS_FORMAT_METRIC"     , 680004);
 define("AXIS_FORMAT_CURRENCY" , 680005);
 define("AXIS_FORMAT_CUSTOM"     , 680006);

 /* Axis position */
 define("AXIS_POSITION_LEFT"	, 681001);
 define("AXIS_POSITION_RIGHT"	, 681002);
 define("AXIS_POSITION_TOP"		, 681001);
 define("AXIS_POSITION_BOTTOM"	, 681002);

 /* Families of data points */
 define("SERIE_SHAPE_FILLEDCIRCLE"	 , 681011);
 define("SERIE_SHAPE_FILLEDTRIANGLE" , 681012);
 define("SERIE_SHAPE_FILLEDSQUARE"	 , 681013);
 define("SERIE_SHAPE_FILLEDDIAMOND"	 , 681017);
 define("SERIE_SHAPE_CIRCLE"		 , 681014);
 define("SERIE_SHAPE_TRIANGLE"		 , 681015);
 define("SERIE_SHAPE_SQUARE"		 , 681016);
 define("SERIE_SHAPE_DIAMOND"		 , 681018);

 /* Axis position */
 define("AXIS_X" , 682001);
 define("AXIS_Y" , 682002);

 /* Define value limits */
 define("ABSOLUTE_MIN" , -10000000000000);
 define("ABSOLUTE_MAX" , 10000000000000);

 /* Replacement to the PHP NULL keyword */
 define("VOID" , 0.123456789);

 /* Euro symbol for GD fonts */
 define("EURO_SYMBOL" , utf8_encode("&#8364;"));

/**
 * Class pData
 *
 * @package Webklex\pChart
 */
 class pData {

     /**
      * @var string $data
      */
     var $data;

     var $palette = [
         "0" => [
             "R" => 188,
             "G" => 224,
             "B" => 46,
             "Alpha" => 100
         ], 
         "1" => [
             "R" => 224,
             "G" => 100,
             "B" => 46,
             "Alpha" => 100
         ], 
         "2" => [
             "R" => 224,
             "G" => 214,
             "B" => 46,
             "Alpha" => 100
         ], 
         "3" => [
             "R" => 46,
             "G" => 151,
             "B" => 224,
             "Alpha" => 100
         ], 
         "4" => [
             "R" => 176,
             "G" => 46,
             "B" => 224,
             "Alpha" => 100
         ], 
         "5" => [
             "R" => 224,
             "G" => 46,
             "B" => 117,
             "Alpha" => 100
         ], 
         "6" => [
             "R" => 92,
             "G" => 224,
             "B" => 46,
             "Alpha" => 100
         ], 
         "7" => [
             "R" => 224,
             "G" => 176,
             "B" => 46,
             "Alpha" => 10
         ]
     ];

     /**
      * pData constructor.
      */
     function __construct() {
         $this->data = [
             'XAxisDisplay' => AXIS_FORMAT_DEFAULT,
             'XAxisFormat'  => NULL,
             'XAxisName'    => NULL,
             'XAxisUnit'    => NULL,
             'Abscissa'     => NULL,
             'AbsicssaPosition' => AXIS_POSITION_BOTTOM,
             'Axis' => [[
                "Display"  => AXIS_FORMAT_DEFAULT,
                "Position" => AXIS_POSITION_LEFT,
                "Identity" => AXIS_Y,
             ]]
         ];
     }

     /**
      * @param $Values
      * @param string $SerieName
      *
      * @return int
      */
     function addPoints($Values,$SerieName="Serie1")
        {
         if (!isset($this->data["Series"][$SerieName]))
            $this->initialise($SerieName);

         if ( is_array($Values) )
            {
             foreach($Values as $Key => $Value)
                { $this->data["Series"][$SerieName]["Data"][] = $Value; }
            }
         else
            $this->data["Series"][$SerieName]["Data"][] = $Values;

         if ( $Values != VOID )
            {
             $StrippedData = $this->stripVOID($this->data["Series"][$SerieName]["Data"]);
             if ( empty($StrippedData) ) { $this->data["Series"][$SerieName]["Max"] = 0; $this->data["Series"][$SerieName]["Min"] =0; return(0); }
             $this->data["Series"][$SerieName]["Max"] = max($StrippedData);
             $this->data["Series"][$SerieName]["Min"] = min($StrippedData);
            }
        }

     /* Strip VOID values */
     function stripVOID($Values)
        { if (!is_array($Values)) { return(array()); } $Result = array(); foreach($Values as $Key => $Value) { if ( $Value != VOID ) { $Result[] = $Value; } } return($Result); }

     /* Return the number of values contained in a given serie */
     function getSerieCount($Serie)
        { if (isset($this->data["Series"][$Serie]["Data"])) { return(sizeof($this->data["Series"][$Serie]["Data"])); } else { return(0); } }

     /* Remove a serie from the pData object */
     function removeSerie($Series)
        {
         if ( !is_array($Series) ) { $Series = $this->convertToArray($Series); }
         foreach($Series as $Key => $Serie) { if (isset($this->data["Series"][$Serie])) { unset($this->data["Series"][$Serie]); } }
        }

     /* Return a value from given serie & index */
     function getValueAt($Serie,$Index=0)
        { if (isset($this->data["Series"][$Serie]["Data"][$Index])) { return($this->data["Series"][$Serie]["Data"][$Index]); } else { return(NULL); } }

     /* Return the values array */
     function getValues($Serie)
        { if (isset($this->data["Series"][$Serie]["Data"])) { return($this->data["Series"][$Serie]["Data"]); } else { return(NULL); } }

     /* Reverse the values in the given serie */
     function reverseSerie($Series)
        {
         if ( !is_array($Series) ) { $Series = $this->convertToArray($Series); }
         foreach($Series as $Key => $Serie) { if (isset($this->data["Series"][$Serie]["Data"])) { $this->data["Series"][$Serie]["Data"] = array_reverse($this->data["Series"][$Serie]["Data"]); } }
        }

     /* Return the sum of the serie values */
     function getSum($Serie)
        { if (isset($this->data["Series"][$Serie])) { return(array_sum($this->data["Series"][$Serie]["Data"])); } else { return(NULL); } }

     /* Return the max value of a given serie */
     function getMax($Serie)
        { if (isset($this->data["Series"][$Serie]["Max"])) { return($this->data["Series"][$Serie]["Max"]); } else { return(NULL); } }

     /* Return the min value of a given serie */
     function getMin($Serie)
        { if (isset($this->data["Series"][$Serie]["Min"])) { return($this->data["Series"][$Serie]["Min"]); } else { return(NULL); } }

     /* Set the description of a given serie */
     function setSerieShape($Series,$Shape=SERIE_SHAPE_FILLEDCIRCLE)
        {
         if ( !is_array($Series) ) { $Series = $this->convertToArray($Series); }
         foreach($Series as $Key => $Serie) { if (isset($this->data["Series"][$Serie]) ) { $this->data["Series"][$Serie]["Shape"] = $Shape; } }
        }

     /* Set the description of a given serie */
     function setSerieDescription($Series,$Description="My serie")
        {
         if ( !is_array($Series) ) { $Series = $this->convertToArray($Series); }
         foreach($Series as $Key => $Serie) { if (isset($this->data["Series"][$Serie]) ) { $this->data["Series"][$Serie]["Description"] = $Description; } }
        }

     /* Set a serie as "drawable" while calling a rendering function */
     function setSerieDrawable($Series,$Drawable=TRUE)
        {
         if ( !is_array($Series) ) { $Series = $this->convertToArray($Series); }
         foreach($Series as $Key => $Serie) { if (isset($this->data["Series"][$Serie]) ) { $this->data["Series"][$Serie]["isDrawable"] = $Drawable; } }
        }

     /* Set the icon associated to a given serie */
     function setSeriePicture($Series,$Picture=NULL)
        {
         if ( !is_array($Series) ) { $Series = $this->convertToArray($Series); }
         foreach($Series as $Key => $Serie) { if (isset($this->data["Series"][$Serie]) ) { $this->data["Series"][$Serie]["Picture"] = $Picture; } }
        }

     /* Set the name of the X Axis */
     function setXAxisName($Name)
        { $this->data["XAxisName"] = $Name; }

     /* Set the display mode of the    X Axis */
     function setXAxisDisplay($Mode,$Format=NULL)
        { $this->data["XAxisDisplay"] = $Mode; $this->data["XAxisFormat"]    = $Format; }

     /* Set the unit that will be displayed on the X axis */
     function setXAxisUnit($Unit)
        { $this->data["XAxisUnit"] = $Unit; }

     /* Set the serie that will be used as abscissa */
     function setAbscissa($Serie)
        { if (isset($this->data["Series"][$Serie])) { $this->data["Abscissa"] = $Serie; } }

     function setAbsicssaPosition($Position = AXIS_POSITION_BOTTOM)
        { $this->data["AbsicssaPosition"] = $Position; }

     /* Set the name of the abscissa axis */
     function setAbscissaName($Name)
        { $this->data["AbscissaName"] = $Name; }

     /* Create a scatter group specifyin X and Y data series */
     function setScatterSerie($SerieX,$SerieY,$ID=0)
        { if (isset($this->data["Series"][$SerieX]) && isset($this->data["Series"][$SerieY]) ) { $this->initScatterSerie($ID); $this->data["ScatterSeries"][$ID]["X"] = $SerieX; $this->data["ScatterSeries"][$ID]["Y"] = $SerieY; } }

     /* Set the shape of a given sctatter serie */
     function setScatterSerieShape($ID,$Shape=SERIE_SHAPE_FILLEDCIRCLE)
        { if (isset($this->data["ScatterSeries"][$ID]) ) { $this->data["ScatterSeries"][$ID]["Shape"] = $Shape; } }

     /* Set the description of a given scatter serie */
     function setScatterSerieDescription($ID,$Description="My serie")
        { if (isset($this->data["ScatterSeries"][$ID]) ) { $this->data["ScatterSeries"][$ID]["Description"] = $Description; } }

     /* Set the icon associated to a given scatter serie */
     function setScatterSeriePicture($ID,$Picture=NULL)
        { if (isset($this->data["ScatterSeries"][$ID]) ) { $this->data["ScatterSeries"][$ID]["Picture"] = $Picture; } }

     /* Set a scatter serie as "drawable" while calling a rendering function */
     function setScatterSerieDrawable($ID ,$Drawable=TRUE)
        { if (isset($this->data["ScatterSeries"][$ID]) ) { $this->data["ScatterSeries"][$ID]["isDrawable"] = $Drawable; } }

     /* Define if a scatter serie should be draw with ticks */
     function setScatterSerieTicks($ID,$Width=0)
        { if ( isset($this->data["ScatterSeries"][$ID]) ) { $this->data["ScatterSeries"][$ID]["Ticks"] = $Width; } }

     /* Define if a scatter serie should be draw with a special weight */
     function setScatterSerieWeight($ID,$Weight=0)
        { if ( isset($this->data["ScatterSeries"][$ID]) ) { $this->data["ScatterSeries"][$ID]["Weight"] = $Weight; } }

     /* Associate a color to a scatter serie */
     function setScatterSerieColor($ID,$Format)
        {
         $R	        = isset($Format["R"]) ? $Format["R"] : 0;
         $G	        = isset($Format["G"]) ? $Format["G"] : 0;
         $B	        = isset($Format["B"]) ? $Format["B"] : 0;
         $Alpha = isset($Format["Alpha"]) ? $Format["Alpha"] : 100;

         if ( isset($this->data["ScatterSeries"][$ID]) )
            {
             $this->data["ScatterSeries"][$ID]["Color"]["R"] = $R;
             $this->data["ScatterSeries"][$ID]["Color"]["G"] = $G;
             $this->data["ScatterSeries"][$ID]["Color"]["B"] = $B;
             $this->data["ScatterSeries"][$ID]["Color"]["Alpha"] = $Alpha;
            }
        }

     /* Compute the series limits for an individual and global point of view */
     function limits()
        {
         $GlobalMin = ABSOLUTE_MAX;
         $GlobalMax = ABSOLUTE_MIN;

         foreach($this->data["Series"] as $Key => $Value)
            {
             if ( $this->data["Abscissa"] != $Key && $this->data["Series"][$Key]["isDrawable"] == TRUE)
                {
                 if ( $GlobalMin > $this->data["Series"][$Key]["Min"] ) { $GlobalMin = $this->data["Series"][$Key]["Min"]; }
                 if ( $GlobalMax < $this->data["Series"][$Key]["Max"] ) { $GlobalMax = $this->data["Series"][$Key]["Max"]; }
                }
            }
         $this->data["Min"] = $GlobalMin;
         $this->data["Max"] = $GlobalMax;

         return(array($GlobalMin,$GlobalMax));
        }

     /* Mark all series as drawable */
     function drawAll()
        { foreach($this->data["Series"] as $Key => $Value) { if ( $this->data["Abscissa"] != $Key ) { $this->data["Series"][$Key]["isDrawable"]=TRUE; } } }        

     /* Return the average value of the given serie */
     function getSerieAverage($Serie)
        {
         if ( isset($this->data["Series"][$Serie]) )
            {
             $SerieData = $this->stripVOID($this->data["Series"][$Serie]["Data"]);
             return(array_sum($SerieData)/sizeof($SerieData));
            }
         else
            return(NULL);
        }

     /* Return the geometric mean of the given serie */
     function getGeometricMean($Serie)
        {
         if ( isset($this->data["Series"][$Serie]) )
            {
             $SerieData = $this->stripVOID($this->data["Series"][$Serie]["Data"]);
             $Seriesum    = 1; foreach($SerieData as $Key => $Value) { $Seriesum = $Seriesum * $Value; }
             return(pow($Seriesum,1/sizeof($SerieData)));
            }
         else
            return(NULL);
        }

     /* Return the harmonic mean of the given serie */
     function getHarmonicMean($Serie)
        {
         if ( isset($this->data["Series"][$Serie]) )
            {
             $SerieData = $this->stripVOID($this->data["Series"][$Serie]["Data"]);
             $Seriesum    = 0; foreach($SerieData as $Key => $Value) { $Seriesum = $Seriesum + 1/$Value; }
             return(sizeof($SerieData)/$Seriesum);
            }
         else
            return(NULL);
        }

     /* Return the standard deviation of the given serie */
     function getStandardDeviation($Serie)
        {
         if ( isset($this->data["Series"][$Serie]) )
            {
             $Average     = $this->getSerieAverage($Serie);
             $SerieData = $this->stripVOID($this->data["Series"][$Serie]["Data"]);

             $DeviationSum = 0;
             foreach($SerieData as $Key => $Value)
                $DeviationSum = $DeviationSum + ($Value-$Average)*($Value-$Average);

             $Deviation = sqrt($DeviationSum/count($SerieData));

             return($Deviation);
            }
         else
            return(NULL);
        }

     /* Return the Coefficient of variation of the given serie */
     function getCoefficientOfVariation($Serie)
        {
         if ( isset($this->data["Series"][$Serie]) )
            {
             $Average                     = $this->getSerieAverage($Serie);
             $StandardDeviation = $this->getStandardDeviation($Serie);

             if ( $StandardDeviation != 0 )
                return($StandardDeviation/$Average);
             else
                return(NULL);
            }
         else
            return(NULL);
        }

     /* Return the median value of the given serie */
     function getSerieMedian($Serie)
        {
         if ( isset($this->data["Series"][$Serie]) )
            {
             $SerieData = $this->stripVOID($this->data["Series"][$Serie]["Data"]);
             sort($SerieData);
             $SerieCenter = floor(sizeof($SerieData)/2);

             if ( isset($SerieData[$SerieCenter]) )
                return($SerieData[$SerieCenter]);
             else
                return(NULL);
            }
         else
            return(NULL);
        }

     /* Return the x th percentil of the given serie */
     function getSeriePercentile($Serie="Serie1",$Percentil=95)
        {
         if (!isset($this->data["Series"][$Serie]["Data"])) { return(NULL); }

         $Values = count($this->data["Series"][$Serie]["Data"])-1;
         if ( $Values < 0 ) { $Values = 0; }

         $PercentilID    = floor(($Values/100)*$Percentil+.5);
         $SortedValues = $this->data["Series"][$Serie]["Data"];
         sort($SortedValues);

         if ( is_numeric($SortedValues[$PercentilID]) )
            return($SortedValues[$PercentilID]);
         else
            return(NULL);
        }

     /* Add random values to a given serie */
     function addRandomValues($SerieName="Serie1",$Options="")
        {
         $Values        = isset($Options["Values"]) ? $Options["Values"] : 20;
         $Min             = isset($Options["Min"]) ? $Options["Min"] : 0;
         $Max             = isset($Options["Max"]) ? $Options["Max"] : 100;
         $withFloat = isset($Options["withFloat"]) ? $Options["withFloat"] : FALSE;

         for ($i=0;$i<=$Values;$i++)
            {
             if ( $withFloat ) { $Value = rand($Min*100,$Max*100)/100; } else { $Value = rand($Min,$Max); }
             $this->addPoints($Value,$SerieName);
            }
        }

     /* Test if we have valid data */
     function containsData()
        {
         if (!isset($this->data["Series"])) { return(FALSE); }

         $Result = FALSE;
         foreach($this->data["Series"] as $Key => $Value)
            { if ( $this->data["Abscissa"] != $Key && $this->data["Series"][$Key]["isDrawable"]==TRUE) { $Result=TRUE; } }
         return($Result);
        }

     /* Set the display mode of an Axis */
     function setAxisDisplay($AxisID,$Mode=AXIS_FORMAT_DEFAULT,$Format=NULL)
        {
         if ( isset($this->data["Axis"][$AxisID] ) )
            {
             $this->data["Axis"][$AxisID]["Display"] = $Mode;
             if ( $Format != NULL ) { $this->data["Axis"][$AxisID]["Format"] = $Format; }
            }
        }

     /* Set the position of an Axis */
     function setAxisPosition($AxisID,$Position=AXIS_POSITION_LEFT)
        { if ( isset($this->data["Axis"][$AxisID] ) ) { $this->data["Axis"][$AxisID]["Position"] = $Position; } }

     /* Associate an unit to an axis */
     function setAxisUnit($AxisID,$Unit)
        { if ( isset($this->data["Axis"][$AxisID] ) ) { $this->data["Axis"][$AxisID]["Unit"] = $Unit; } }

     /* Associate a name to an axis */
     function setAxisName($AxisID,$Name)
        { if ( isset($this->data["Axis"][$AxisID] ) ) { $this->data["Axis"][$AxisID]["Name"] = $Name; } }

     /* Associate a color to an axis */
     function setAxisColor($AxisID,$Format)
        {
         $R	        = isset($Format["R"]) ? $Format["R"] : 0;
         $G	        = isset($Format["G"]) ? $Format["G"] : 0;
         $B	        = isset($Format["B"]) ? $Format["B"] : 0;
         $Alpha = isset($Format["Alpha"]) ? $Format["Alpha"] : 100;

         if ( isset($this->data["Axis"][$AxisID] ) )
            {
             $this->data["Axis"][$AxisID]["Color"]["R"] = $R;
             $this->data["Axis"][$AxisID]["Color"]["G"] = $G;
             $this->data["Axis"][$AxisID]["Color"]["B"] = $B;
             $this->data["Axis"][$AxisID]["Color"]["Alpha"] = $Alpha;
            }
        }


     /* Design an axis as X or Y member */
     function setAxisXY($AxisID,$Identity=AXIS_Y)
        { if ( isset($this->data["Axis"][$AxisID] ) ) { $this->data["Axis"][$AxisID]["Identity"] = $Identity; } }

     /* Associate one data serie with one axis */
     function setSerieOnAxis($Series,$AxisID)
        {
         if ( !is_array($Series) ) { $Series = $this->convertToArray($Series); }
         foreach($Series as $Key => $Serie)
            {
             $PreviousAxis = $this->data["Series"][$Serie]["Axis"];

             /* Create missing axis */
             if ( !isset($this->data["Axis"][$AxisID] ) )
                { $this->data["Axis"][$AxisID]["Position"] = AXIS_POSITION_LEFT; $this->data["Axis"][$AxisID]["Identity"] = AXIS_Y;}

             $this->data["Series"][$Serie]["Axis"] = $AxisID;

             /* Cleanup unused axis */
             $Found = FALSE;
             foreach($this->data["Series"] as $SerieName => $Values) { if ( $Values["Axis"] == $PreviousAxis ) { $Found = TRUE; } }
             if (!$Found) { unset($this->data["Axis"][$PreviousAxis]); }
            }
        }

     /* Define if a serie should be draw with ticks */
     function setSerieTicks($Series,$Width=0)
        {
         if ( !is_array($Series) ) { $Series = $this->convertToArray($Series); }
         foreach($Series as $Key => $Serie) { if ( isset($this->data["Series"][$Serie]) ) { $this->data["Series"][$Serie]["Ticks"] = $Width; } }
        }

     /* Define if a serie should be draw with a special weight */
     function setSerieWeight($Series,$Weight=0)
        {
         if ( !is_array($Series) ) { $Series = $this->convertToArray($Series); }
         foreach($Series as $Key => $Serie) { if ( isset($this->data["Series"][$Serie]) ) { $this->data["Series"][$Serie]["Weight"] = $Weight; } }
        }

     /* Returns the palette of the given serie */
     function getSeriePalette($Serie)
        {
         if ( !isset($this->data["Series"][$Serie]) ) { return(NULL); }

         $Result = "";
         $Result["R"] = $this->data["Series"][$Serie]["Color"]["R"];
         $Result["G"] = $this->data["Series"][$Serie]["Color"]["G"];
         $Result["B"] = $this->data["Series"][$Serie]["Color"]["B"];
         $Result["Alpha"] = $this->data["Series"][$Serie]["Color"]["Alpha"];

         return($Result);
        }

     /* Set the color of one serie */
     function setPalette($Series,$Format=NULL)
        {
         if ( !is_array($Series) ) { $Series = $this->convertToArray($Series); }

         foreach($Series as $Key => $Serie)
            {
             $R	        = isset($Format["R"]) ? $Format["R"] : 0;
             $G	        = isset($Format["G"]) ? $Format["G"] : 0;
             $B	        = isset($Format["B"]) ? $Format["B"] : 0;
             $Alpha = isset($Format["Alpha"]) ? $Format["Alpha"] : 100;

             if ( isset($this->data["Series"][$Serie]) )
                {
                 $OldR = $this->data["Series"][$Serie]["Color"]["R"]; $OldG = $this->data["Series"][$Serie]["Color"]["G"]; $OldB = $this->data["Series"][$Serie]["Color"]["B"];
                 $this->data["Series"][$Serie]["Color"]["R"] = $R;
                 $this->data["Series"][$Serie]["Color"]["G"] = $G;
                 $this->data["Series"][$Serie]["Color"]["B"] = $B;
                 $this->data["Series"][$Serie]["Color"]["Alpha"] = $Alpha;

                 /* Do reverse processing on the internal palette array */
                 foreach ($this->palette as $Key => $Value)
                    { if ($Value["R"] == $OldR && $Value["G"] == $OldG && $Value["B"] == $OldB) { $this->palette[$Key]["R"] = $R; $this->palette[$Key]["G"] = $G; $this->palette[$Key]["B"] = $B; $this->palette[$Key]["Alpha"] = $Alpha;} }
                }
            }
        }

     /* Load a palette file */
     function loadPalette($FileName,$Overwrite=FALSE)
        {
         if ( !file_exists($FileName) ) { return(-1); }
         if ( $Overwrite ) { $this->palette = ""; }

         $fileHandle = @fopen($FileName, "r");
         if (!$fileHandle) { return(-1); }
         while (!feof($fileHandle))
            {
             $buffer = fgets($fileHandle, 4096);
             if ( preg_match("/,/",$buffer) )
                {
                 list($R,$G,$B,$Alpha) = preg_split("/,/",$buffer);
                 if ( $this->palette == "" ) { $ID = 0; } else { $ID = count($this->palette); }
                 $this->palette[$ID] = array("R"=>$R,"G"=>$G,"B"=>$B,"Alpha"=>$Alpha);
                }
            }
         fclose($fileHandle);

         /* Apply changes to current series */
         $ID = 0;
         if ( isset($this->data["Series"]))
            {
             foreach($this->data["Series"] as $Key => $Value)
                {
                 if ( !isset($this->palette[$ID]) )
                    $this->data["Series"][$Key]["Color"] = array("R"=>0,"G"=>0,"B"=>0,"Alpha"=>0);
                 else
                    $this->data["Series"][$Key]["Color"] = $this->palette[$ID];
                 $ID++;
                }
            }
        }

     /* Initialise a given scatter serie */
     function initScatterSerie($ID)
        {
         if ( isset($this->data["ScatterSeries"][$ID]) ) { return(0); }

         $this->data["ScatterSeries"][$ID]["Description"]	= "Scatter ".$ID;
         $this->data["ScatterSeries"][$ID]["isDrawable"]	= TRUE;
         $this->data["ScatterSeries"][$ID]["Picture"]	= NULL;
         $this->data["ScatterSeries"][$ID]["Ticks"]		= 0;
         $this->data["ScatterSeries"][$ID]["Weight"]	= 0;

         if ( isset($this->palette[$ID]) )
            $this->data["ScatterSeries"][$ID]["Color"] = $this->palette[$ID];
         else
            {
             $this->data["ScatterSeries"][$ID]["Color"]["R"] = rand(0,255);
             $this->data["ScatterSeries"][$ID]["Color"]["G"] = rand(0,255);
             $this->data["ScatterSeries"][$ID]["Color"]["B"] = rand(0,255);
             $this->data["ScatterSeries"][$ID]["Color"]["Alpha"] = 100;
            }
        }

     /* Initialise a given serie */
     function initialise($Serie)
        {
         if ( isset($this->data["Series"]) ) { $ID = count($this->data["Series"]); } else { $ID = 0; }

         $this->data["Series"][$Serie]["Description"]	= $Serie;
         $this->data["Series"][$Serie]["isDrawable"]	= TRUE;
         $this->data["Series"][$Serie]["Picture"]		= NULL;
         $this->data["Series"][$Serie]["Max"]		= NULL;
         $this->data["Series"][$Serie]["Min"]		= NULL;
         $this->data["Series"][$Serie]["Axis"]		= 0;
         $this->data["Series"][$Serie]["Ticks"]		= 0;
         $this->data["Series"][$Serie]["Weight"]		= 0;
         $this->data["Series"][$Serie]["Shape"]		= SERIE_SHAPE_FILLEDCIRCLE;

         if ( isset($this->palette[$ID]) )
            $this->data["Series"][$Serie]["Color"] = $this->palette[$ID];
         else
            {
             $this->data["Series"][$Serie]["Color"]["R"] = rand(0,255);
             $this->data["Series"][$Serie]["Color"]["G"] = rand(0,255);
             $this->data["Series"][$Serie]["Color"]["B"] = rand(0,255);
             $this->data["Series"][$Serie]["Color"]["Alpha"] = 100;
            }
        }
         
     function normalize($NormalizationFactor=100,$UnitChange=NULL,$Round=1)
        {
         $Abscissa = $this->data["Abscissa"];

         $SelectedSeries = "";
         $MaxVal                 = 0;
         foreach($this->data["Axis"] as $AxisID => $Axis)
            {
             if ( $UnitChange != NULL ) { $this->data["Axis"][$AxisID]["Unit"] = $UnitChange; }

             foreach($this->data["Series"] as $SerieName => $Serie)
                {
                 if ($Serie["Axis"] == $AxisID && $Serie["isDrawable"] == TRUE && $SerieName != $Abscissa)
                    {
                     $SelectedSeries[$SerieName] = $SerieName;

                     if ( count($Serie["Data"] ) > $MaxVal ) { $MaxVal = count($Serie["Data"]); }
                    }
                }
            }

         for($i=0;$i<=$MaxVal-1;$i++)
            {
             $Factor = 0;
             foreach ($SelectedSeries as $Key => $SerieName )
                {
                 $Value = $this->data["Series"][$SerieName]["Data"][$i];
                 if ( $Value != VOID )
                    $Factor = $Factor + abs($Value);
                }

             if ( $Factor != 0 )
                {
                 $Factor = $NormalizationFactor / $Factor;

                 foreach ($SelectedSeries as $Key => $SerieName )
                    {
                     $Value = $this->data["Series"][$SerieName]["Data"][$i];

                     if ( $Value != VOID && $Factor != $NormalizationFactor )
                        $this->data["Series"][$SerieName]["Data"][$i] = round(abs($Value)*$Factor,$Round);
                     elseif ( $Value == VOID || $Value == 0 )
                        $this->data["Series"][$SerieName]["Data"][$i] = VOID;
                     elseif ( $Factor == $NormalizationFactor )
                        $this->data["Series"][$SerieName]["Data"][$i] = $NormalizationFactor;
                    }
                }
            }

         foreach ($SelectedSeries as $Key => $SerieName )
            {
             $this->data["Series"][$SerieName]["Max"] = max($this->stripVOID($this->data["Series"][$SerieName]["Data"]));
             $this->data["Series"][$SerieName]["Min"] = min($this->stripVOID($this->data["Series"][$SerieName]["Data"]));
            }
        }

     /* Load data from a CSV (or similar) data source */
     function importFromCSV($FileName,$Options="")
        {
         $Delimiter		= isset($Options["Delimiter"]) ? $Options["Delimiter"] : ",";
         $GotHeader		= isset($Options["GotHeader"]) ? $Options["GotHeader"] : FALSE;
         $SkipColumns	= isset($Options["SkipColumns"]) ? $Options["SkipColumns"] : array(-1);
         $DefaultSerieName	= isset($Options["DefaultSerieName"]) ? $Options["DefaultSerieName"] : "Serie";

         $Handle = @fopen($FileName,"r");
         if ($Handle)
            {
             $HeaderParsed = FALSE; $SerieNames = "";
             while (!feof($Handle))
                {
                 $Buffer = fgets($Handle, 4096);
                 $Buffer = str_replace(chr(10),"",$Buffer);
                 $Buffer = str_replace(chr(13),"",$Buffer);
                 $Values = preg_split("/".$Delimiter."/",$Buffer);

                 if ( $Buffer != "" )
                    {
                     if ( $GotHeader && !$HeaderParsed )
                        {
                         foreach($Values as $Key => $Name) { if ( !in_array($Key,$SkipColumns) ) { $SerieNames[$Key] = $Name; } }
                         $HeaderParsed = TRUE;
                        }
                     else
                        {
                         if ($SerieNames == "" ) { foreach($Values as $Key => $Name) {    if ( !in_array($Key,$SkipColumns) ) { $SerieNames[$Key] = $DefaultSerieName.$Key; } } }
                         foreach($Values as $Key => $Value) {    if ( !in_array($Key,$SkipColumns) ) { $this->addPoints($Value,$SerieNames[$Key]); } }
                        }
                    }
                }
             fclose($Handle);
            }
        }

     /* Create a dataset based on a formula */
     function createFunctionSerie($SerieName,$Formula="",$Options="")
        {
         $MinX		= isset($Options["MinX"]) ? $Options["MinX"] : -10;
         $MaxX		= isset($Options["MaxX"]) ? $Options["MaxX"] : 10;
         $XStep		= isset($Options["XStep"]) ? $Options["XStep"] : 1;
         $AutoDescription	= isset($Options["AutoDescription"]) ? $Options["AutoDescription"] : FALSE;
         $RecordAbscissa	= isset($Options["RecordAbscissa"]) ? $Options["RecordAbscissa"] : FALSE;
         $AbscissaSerie	= isset($Options["AbscissaSerie"]) ? $Options["AbscissaSerie"] : "Abscissa";

         if ( $Formula == "" ) { return(0); }

         $Result = ""; $Abscissa = "";
         for($i=$MinX; $i<=$MaxX; $i=$i+$XStep)
            {
             $Expression = "\$return = '!'.(".str_replace("z",$i,$Formula).");";
             if ( @eval($Expression) === FALSE ) { $return = VOID; }
             if ( $return == "!" ) { $return = VOID; } else { $return = $this->right($return,strlen($return)-1); }
             if ( $return == "NAN" ) { $return = VOID; }
             if ( $return == "INF" ) { $return = VOID; }
             if ( $return == "-INF" ) { $return = VOID; }

             $Abscissa[] = $i;
             $Result[]     = $return;
            }

         $this->addPoints($Result,$SerieName);
         if ( $AutoDescription ) { $this->setSerieDescription($SerieName,$Formula); }
         if ( $RecordAbscissa ) { $this->addPoints($Abscissa,$AbscissaSerie); }
        }

     function negateValues($Series)
        {
         if ( !is_array($Series) ) { $Series = $this->convertToArray($Series); }
         foreach($Series as $Key => $SerieName)
            {
             if (isset($this->data["Series"][$SerieName]))
                {
                 $data = "";
                 foreach($this->data["Series"][$SerieName]["Data"] as $Key => $Value)
                    { if ( $Value == VOID ) { $data[] = VOID; } else { $data[] = -$Value; } }
                 $this->data["Series"][$SerieName]["Data"] = $data;

                 $this->data["Series"][$SerieName]["Max"] = max($this->stripVOID($this->data["Series"][$SerieName]["Data"]));
                 $this->data["Series"][$SerieName]["Min"] = min($this->stripVOID($this->data["Series"][$SerieName]["Data"]));
                }
            }
        }

     /* Return the data & configuration of the series */
     function getData()
        { return($this->data); }

     /* Save a palette element */
     function savePalette($ID,$Color)
        { $this->palette[$ID] = $Color; }

     /* Return the palette of the series */
     function getPalette()
        { return($this->palette); }

     /* Called by the scaling algorithm to save the config */
     function saveAxisConfig($Axis) { $this->data["Axis"]=$Axis; }

     /* Save the Y Margin if set */
     function saveYMargin($Value) { $this->data["YMargin"]=$Value; }

     /* Save extended configuration to the pData object */
     function saveExtendedData($Tag,$Values) { $this->data["Extended"][$Tag]=$Values; }

     /* Called by the scaling algorithm to save the orientation of the scale */
     function saveOrientation($Orientation) { $this->data["Orientation"]=$Orientation; }

     /**
      * Convert a string to a single elements array
      * @param $value
      * 
      * @return array
      */
     function convertToArray($value) {
         return [$value];
     }

     /* Class string wrapper */
     function __toString()
        { return("pData object."); }

     function left($value,$NbChar)	{ return substr($value,0,$NbChar); }    
     function right($value,$NbChar)	{ return substr($value,strlen($value)-$NbChar,$NbChar); }    
     function mid($value,$Depart,$NbChar)	{ return substr($value,$Depart-1,$NbChar); }    
    }
?>
