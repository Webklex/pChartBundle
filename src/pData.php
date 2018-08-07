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

 /* Replacement to the PHP null keyword */
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

     /**
      * @var array $palette
      */
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
             'XAxisFormat'  => null,
             'XAxisName'    => null,
             'XAxisUnit'    => null,
             'Abscissa'     => null,
             'AbsicssaPosition' => AXIS_POSITION_BOTTOM,
             'Axis' => [[
                "Display"  => AXIS_FORMAT_DEFAULT,
                "Position" => AXIS_POSITION_LEFT,
                "Identity" => AXIS_Y,
             ]]
         ];
     }

     /**
      * @param mixed $values
      * @param string $serie
      *
      * @return int
      */
     function addPoints($values, $serie = "Serie1") {
         if (!isset($this->data["Series"][$serie])) {
             $this->initialise($serie);
         }

         if ( is_array($values) ) {
             foreach($values as $key => $value) {
                 $this->data["Series"][$serie]["Data"][] = $value;
             }
         } else {
             $this->data["Series"][$serie]["Data"][] = $values;
         }

         if ( $values != VOID ) {
             $StrippedData = $this->stripVOID($this->data["Series"][$serie]["Data"]);
             if ( empty($StrippedData) ) {
                 $this->data["Series"][$serie]["Max"] = 0;
                 $this->data["Series"][$serie]["Min"] =0;
                 return 0;
             }
             $this->data["Series"][$serie]["Max"] = max($StrippedData);
             $this->data["Series"][$serie]["Min"] = min($StrippedData);
        }

        return 1;
    }

     /**
      * Strip VOID values
      * @param mixed $values
      *
      * @return array
      */
     function stripVOID($values) {
         if (!is_array($values)) {
             return [];
         }

         $result = [];
         foreach($values as $key => $value) {
             if ( $value != VOID ) {
                 $result[] = $value;
             }
         }

         return $result;
     }

     /**
      * Return the number of values contained in a given serie
      * @param string $serie
      *
      * @return int
      */
     function getSerieCount($serie) {
         if (isset($this->data["Series"][$serie]["Data"])){
             return sizeof($this->data["Series"][$serie]["Data"]);
         }

         return 0;
     }

     /**
      * Remove a serie from the pData object
      * @param $series
      */
     function removeSerie($series) {
         if ( !is_array($series) ) {
             $series = $this->convertToArray($series);
         }

         foreach($series as $serie) {
             if (isset($this->data["Series"][$serie])) {
                 unset($this->data["Series"][$serie]);
             }
         }
     }

     /**
      * Return a value from given serie & index
      * @param $serie
      * @param int $index
      *
      * @return null|int
      */
     function getValueAt($serie, $index = 0){
         if (isset($this->data["Series"][$serie]["Data"][$index])) {
             return $this->data["Series"][$serie]["Data"][$index];
         }

         return null;
     }

     /**
      * Return the values array
      * @param $serie
      *
      * @return null|array
      */
     function getValues($serie) {
         if (isset($this->data["Series"][$serie]["Data"])) {
             return $this->data["Series"][$serie]["Data"];
         }

         return null;
     }

     /**
      * Reverse the values in the given serie
      * @param $series
      */
     function reverseSerie($series) {
         if ( !is_array($series) ) { $series = $this->convertToArray($series); }
         foreach($series as $serie) {
             if (isset($this->data["Series"][$serie]["Data"])) {
                 $this->data["Series"][$serie]["Data"] = array_reverse($this->data["Series"][$serie]["Data"]);
             }
         }
     }

     /**
      * Return the sum of the serie values
      * @param $serie
      *
      * @return float|null
      */
     function getSum($serie) {
         if (isset($this->data["Series"][$serie])) {
             return array_sum($this->data["Series"][$serie]["Data"]);
         }

         return null;
     }

     /**
      * Return the max value of a given serie
      * @param $serie
      *
      * @return null|float
      */
     function getMax($serie) {
         if (isset($this->data["Series"][$serie]["Max"])) {
             return $this->data["Series"][$serie]["Max"];
         }

         return null;
     }

     /**
      * Return the min value of a given serie
      * @param $serie
      *
      * @return null|float
      */
     function getMin($serie) {
         if (isset($this->data["Series"][$serie]["Min"])) {
             return $this->data["Series"][$serie]["Min"];
         }

         return null;
     }

     /**
      * Set the description of a given serie
      * @param $series
      * @param int $shape
      */
     function setSerieShape($series, $shape = SERIE_SHAPE_FILLEDCIRCLE) {
         if ( !is_array($series) ) {
             $series = $this->convertToArray($series);
         }

         foreach($series as $serie) {
             if (isset($this->data["Series"][$serie]) ) {
                 $this->data["Series"][$serie]["Shape"] = $shape;
             }
         }
     }

     /**
      * Set the description of a given serie
      * @param $series
      * @param string $description
      */
     function setSerieDescription($series, $description = "My serie") {
         if ( !is_array($series) ) {
             $series = $this->convertToArray($series);
         }

         foreach($series as $serie) {
             if (isset($this->data["Series"][$serie]) ) {
                 $this->data["Series"][$serie]["Description"] = $description;
             }
         }
     }

     /**
      * Set a serie as "drawable" while calling a rendering function
      * @param $series
      * @param bool $drawable
      */
     function setSerieDrawable($series, $drawable = true) {
         if ( !is_array($series) ) {
             $series = $this->convertToArray($series);
         }

         foreach($series as $serie) {
             if (isset($this->data["Series"][$serie]) ) {
                 $this->data["Series"][$serie]["isDrawable"] = $drawable;
             }
         }
     }

     /**
      * Set the icon associated to a given serie
      * @param $series
      * @param null $picture
      */
     function setSeriePicture($series, $picture = null) {
         if ( !is_array($series) ) { 
             $series = $this->convertToArray($series); 
         }
         
         foreach($series as $serie) { 
             if (isset($this->data["Series"][$serie]) ) { 
                 $this->data["Series"][$serie]["Picture"] = $picture; 
             } 
         }
     }

     /**
      * Set the name of the X Axis
      * @param $name
      */
     function setXAxisName($name){ 
         $this->data["XAxisName"] = $name; 
     }

     /**
      * Set the display mode of the    X Axis
      * @param $mode
      * @param null $format
      */
     function setXAxisDisplay($mode, $format = null) { 
         $this->data["XAxisDisplay"] = $mode; 
         $this->data["XAxisFormat"]    = $format; 
     }

     /**
      * Set the unit that will be displayed on the X axis
      * @param $unit
      */
     function setXAxisUnit($unit) { 
         $this->data["XAxisUnit"] = $unit; 
     }

     /**
      * Set the serie that will be used as abscissa
      * @param $serie
      */
     function setAbscissa($serie) { 
         if (isset($this->data["Series"][$serie])) { 
             $this->data["Abscissa"] = $serie; 
         } 
     }

     /**
      * @param int $position
      */
     function setAbsicssaPosition($position = AXIS_POSITION_BOTTOM) { 
         $this->data["AbsicssaPosition"] = $position; 
     }

     /**
      * Set the name of the abscissa axis
      * @param $name
      */
     function setAbscissaName($name) { 
         $this->data["AbscissaName"] = $name; 
     }

     /**
      * Create a scatter group specifyin X and Y data series
      * @param $serie_x
      * @param $serie_y
      * @param int $id
      */
     function setScatterSerie($serie_x, $serie_y, $id = 0) { 
         if (isset($this->data["Series"][$serie_x]) && isset($this->data["Series"][$serie_y]) ) { 
             $this->initScatterSerie($id); 
             $this->data["ScatterSeries"][$id]["X"] = $serie_x;
             $this->data["ScatterSeries"][$id]["Y"] = $serie_y;
         } 
     }

     /**
      * Set the shape of a given sctatter serie
      * @param $id
      * @param int $shape
      */
     function setScatterSerieShape($id, $shape = SERIE_SHAPE_FILLEDCIRCLE) { 
         if (isset($this->data["ScatterSeries"][$id]) ) { 
             $this->data["ScatterSeries"][$id]["Shape"] = $shape; 
         } 
     }

     /**
      * Set the description of a given scatter serie
      * @param $id
      * @param string $description
      */
     function setScatterSerieDescription($id, $description = "My serie") { 
         if (isset($this->data["ScatterSeries"][$id]) ) { 
             $this->data["ScatterSeries"][$id]["Description"] = $description; 
         } 
     }

     /**
      * Set the icon associated to a given scatter serie
      * @param $id
      * @param null $picture
      */
     function setScatterSeriePicture($id, $picture = null) { 
         if (isset($this->data["ScatterSeries"][$id]) ) { 
             $this->data["ScatterSeries"][$id]["Picture"] = $picture; 
         } 
     }

     /**
      * Set a scatter serie as "drawable" while calling a rendering function
      * @param $id
      * @param bool $drawable
      */
     function setScatterSerieDrawable($id ,$drawable = true) { 
         if (isset($this->data["ScatterSeries"][$id]) ) { 
             $this->data["ScatterSeries"][$id]["isDrawable"] = $drawable; 
         } 
     }

     /**
      * Define if a scatter serie should be draw with ticks
      * @param $id
      * @param int $width
      */
     function setScatterSerieTicks($id, $width = 0) { 
         if ( isset($this->data["ScatterSeries"][$id]) ) { 
             $this->data["ScatterSeries"][$id]["Ticks"] = $width; 
         } 
     }

     /**
      * Define if a scatter serie should be draw with a special weight
      * @param $id
      * @param int $weight
      */
     function setScatterSerieWeight($id, $weight = 0) { 
         if ( isset($this->data["ScatterSeries"][$id]) ) { 
             $this->data["ScatterSeries"][$id]["Weight"] = $weight; 
         } 
     }

     /**
      * Associate a color to a scatter serie
      * @param $id
      * @param $format
      */
     function setScatterSerieColor($id, $format) {
         $r	        = isset($format["R"]) ? $format["R"] : 0;
         $g	        = isset($format["G"]) ? $format["G"] : 0;
         $b	        = isset($format["B"]) ? $format["B"] : 0;
         $alpha = isset($format["Alpha"]) ? $format["Alpha"] : 100;

         if ( isset($this->data["ScatterSeries"][$id]) ) {
             $this->data["ScatterSeries"][$id]["Color"]["R"] = $r;
             $this->data["ScatterSeries"][$id]["Color"]["G"] = $g;
             $this->data["ScatterSeries"][$id]["Color"]["B"] = $b;
             $this->data["ScatterSeries"][$id]["Color"]["Alpha"] = $alpha;
         }
     }

     /**
      * Compute the series limits for an individual and global point of view
      * 
      * @return array
      */
     function limits() {
         $global_min = ABSOLUTE_MAX;
         $global_max = ABSOLUTE_MIN;

         foreach($this->data["Series"] as $key => $value) {
             if ( $this->data["Abscissa"] != $key && $this->data["Series"][$key]["isDrawable"] == true) {
                 if ( $global_min > $this->data["Series"][$key]["Min"] ) { 
                     $global_min = $this->data["Series"][$key]["Min"]; 
                 }
                 if ( $global_max < $this->data["Series"][$key]["Max"] ) { 
                     $global_max = $this->data["Series"][$key]["Max"]; 
                 }
             }
         }
         
         $this->data["Min"] = $global_min;
         $this->data["Max"] = $global_max;

         return [$global_min, $global_max];
     }

     /**
      * Mark all series as drawable
      */
     function drawAll() { 
         foreach($this->data["Series"] as $key => $value) { 
             if ( $this->data["Abscissa"] != $key ) { 
                 $this->data["Series"][$key]["isDrawable"] = true; 
             } 
         } 
     }

     /**
      * Return the average value of the given serie
      * @param $serie
      * 
      * @return float|null
      */
     function getSerieAverage($serie) {
         if ( isset($this->data["Series"][$serie]) ) {
             $serie_data = $this->stripVOID($this->data["Series"][$serie]["Data"]);
             
             return array_sum($serie_data) / sizeof($serie_data);
         }
         
         return null;
     }

     /**
      * Return the geometric mean of the given serie
      * @param $serie
      * 
      * @return float|null
      */
     function getGeometricMean($serie) {
         if ( isset($this->data["Series"][$serie]) ) {
             $serie_data = $this->stripVOID($this->data["Series"][$serie]["Data"]);
             $seriesum    = 1; 
             
             foreach($serie_data as $value) { 
                 $seriesum = $seriesum * $value; 
             }
             
             return pow($seriesum,1 / sizeof($serie_data));
         }
         
         return null;
     }

     /**
      * Return the harmonic mean of the given serie
      * @param $serie
      * 
      * @return float|null
      */
     function getHarmonicMean($serie) {
         if ( isset($this->data["Series"][$serie]) ) {
             $serie_data = $this->stripVOID($this->data["Series"][$serie]["Data"]);
             $seriesum    = 0; 
             
             foreach($serie_data as $key => $value) { 
                 $seriesum = $seriesum + 1 / $value; 
             }
             
             return sizeof($serie_data) / $seriesum;
         }
     
         return null;
     }

     /**
      * Return the standard deviation of the given serie
      * @param $serie
      * 
      * @return float|null
      */
     function getStandardDeviation($serie) {
         if ( isset($this->data["Series"][$serie]) ) {
             $average     = $this->getSerieAverage($serie);
             $serie_data = $this->stripVOID($this->data["Series"][$serie]["Data"]);

             $deviation_sum = 0;
             foreach($serie_data as $value) {
                 $deviation_sum = $deviation_sum + ($value - $average) * ($value - $average);
             }

             $deviation = sqrt($deviation_sum / count($serie_data));

             return $deviation;
         }
         
         return null;
     }

     /**
      * Return the Coefficient of variation of the given serie
      * @param $serie
      * 
      * @return float|null
      */
     function getCoefficientOfVariation($serie) {
         if ( isset($this->data["Series"][$serie]) ) {
             $average                     = $this->getSerieAverage($serie);
             $standard_deviation = $this->getStandardDeviation($serie);

             if ( $standard_deviation != 0 ){
                 return $standard_deviation/$average;
             }
             
         }
         
         return null;
     }

     /**
      * Return the median value of the given serie
      * @param $serie
      * 
      * @return mixed|null
      */
     function getSerieMedian($serie) {
         if ( isset($this->data["Series"][$serie]) ) {
             $serie_data = $this->stripVOID($this->data["Series"][$serie]["Data"]);
             sort($serie_data);
             $serie_center = floor(sizeof($serie_data) / 2);

             if ( isset($serie_data[(string)$serie_center]) ){
                 return $serie_data[(string)$serie_center];
             }
         }
         
         return null;
     }

     /**
      * Return the x th percentil of the given serie
      * @param string $serie
      * @param int $percentil
      * 
      * @return null
      */
     function getSeriePercentile($serie = "Serie1", $percentil = 95) {
         if (!isset($this->data["Series"][$serie]["Data"])) { 
             return null; 
         }

         $values = count($this->data["Series"][$serie]["Data"]) - 1;
         if ( $values < 0 ) { 
             $values = 0; 
         }

         $percentil_id    = floor(($values / 100) * $percentil + .5);
         $SortedValues = $this->data["Series"][$serie]["Data"];
         sort($SortedValues);

         if ( is_numeric($SortedValues[(string)$percentil_id]) ) {
             return $SortedValues[(string)$percentil_id];
         }
         
         return null;
     }

     /**
      * Add random values to a given serie
      * @param string $serie_name
      * @param array $options
      */
     function addRandomValues($serie_name = "Serie1", $options = []) {
         $values        = isset($options["Values"]) ? $options["Values"] : 20;
         $Min             = isset($options["Min"]) ? $options["Min"] : 0;
         $Max             = isset($options["Max"]) ? $options["Max"] : 100;
         $withFloat = isset($options["withFloat"]) ? $options["withFloat"] : false;

         for ($i = 0; $i <= $values; $i++) {
             if ( $withFloat ) { 
                 $value = rand($Min*100,$Max*100)/100; 
             } else { 
                 $value = rand($Min,$Max); 
             }
     
             $this->addPoints($value, $serie_name);
         }
     }

     /**
      * Test if we have valid data
      * 
      * @return bool
      */
     function containsData() {
         if (!isset($this->data["Series"])) { 
             return false; 
         }

         $result = false;
         foreach($this->data["Series"] as $key => $value) { 
             if ( $this->data["Abscissa"] != $key && $this->data["Series"][$key]["isDrawable"]==true) { 
                 $result = true; 
             } 
         }
         
         return $result;
     }

     /**
      * Set the display mode of an Axis
      * @param $axis_id
      * @param int $mode
      * @param null $format
      */
     function setAxisDisplay($axis_id, $mode = AXIS_FORMAT_DEFAULT, $format = null) {
         if ( isset($this->data["Axis"][$axis_id] ) ) {
             $this->data["Axis"][$axis_id]["Display"] = $mode;
             if ( $format != null ) { 
                 $this->data["Axis"][$axis_id]["Format"] = $format; 
             }
         }
     }

     /**
      * Set the position of an Axis
      * @param $axis_id
      * @param int $position
      */
     function setAxisPosition($axis_id, $position = AXIS_POSITION_LEFT) { 
         if ( isset($this->data["Axis"][$axis_id] ) ) { 
             $this->data["Axis"][$axis_id]["Position"] = $position; 
         } 
     }

     /**
      * Associate an unit to an axis
      * @param $axis_id
      * @param $unit
      */
     function setAxisUnit($axis_id, $unit) { 
         if ( isset($this->data["Axis"][$axis_id] ) ) { 
             $this->data["Axis"][$axis_id]["Unit"] = $unit; 
         } 
     }

     /**
      * Associate a name to an axis
      * @param $axis_id
      * @param $name
      */
     function setAxisName($axis_id, $name) { 
         if ( isset($this->data["Axis"][$axis_id] ) ) { 
             $this->data["Axis"][$axis_id]["Name"] = $name; 
         } 
     }

     /**
      * Associate a color to an axis
      * @param $axis_id
      * @param $format
      */
     function setAxisColor($axis_id, $format) {
         $r	        = isset($format["R"]) ? $format["R"] : 0;
         $g	        = isset($format["G"]) ? $format["G"] : 0;
         $b	        = isset($format["B"]) ? $format["B"] : 0;
         $alpha = isset($format["Alpha"]) ? $format["Alpha"] : 100;

         if ( isset($this->data["Axis"][$axis_id] ) ) {
             $this->data["Axis"][$axis_id]["Color"]["R"] = $r;
             $this->data["Axis"][$axis_id]["Color"]["G"] = $g;
             $this->data["Axis"][$axis_id]["Color"]["B"] = $b;
             $this->data["Axis"][$axis_id]["Color"]["Alpha"] = $alpha;
         }
     }


     /**
      * Design an axis as X or Y member
      * @param $axis_id
      * @param int $identity
      */
     function setAxisXY($axis_id, $identity = AXIS_Y) { 
         if ( isset($this->data["Axis"][$axis_id] ) ) { 
             $this->data["Axis"][$axis_id]["Identity"] = $identity; 
         } 
     }

     /**
      * Associate one data serie with one axis
      * @param $series
      * @param $axis_id
      */
     function setSerieOnAxis($series, $axis_id) {
         if ( !is_array($series) ) { 
             $series = $this->convertToArray($series); 
         }
         
         foreach($series as $key => $serie) {
             $previous_axis = $this->data["Series"][$serie]["Axis"];

             /* Create missing axis */
             if ( !isset($this->data["Axis"][$axis_id] ) ) { 
                 $this->data["Axis"][$axis_id]["Position"] = AXIS_POSITION_LEFT; 
                 $this->data["Axis"][$axis_id]["Identity"] = AXIS_Y;
             }

             $this->data["Series"][$serie]["Axis"] = $axis_id;

             /* Cleanup unused axis */
             $found = false;
             foreach($this->data["Series"] as $serie_name => $values) { 
                 if ( $values["Axis"] == $previous_axis ) { 
                     $found = true;
                     break;
                 } 
             }
             
             if (!$found) { 
                 unset($this->data["Axis"][$previous_axis]); 
             }
         }
     }

     /**
      * Define if a serie should be draw with ticks
      * @param $series
      * @param int $width
      */
     function setSerieTicks($series, $width = 0) {
         if ( !is_array($series) ) { 
             $series = $this->convertToArray($series); 
         }
         
         foreach($series as $key => $serie) { 
             if ( isset($this->data["Series"][$serie]) ) { 
                 $this->data["Series"][$serie]["Ticks"] = $width; 
             } 
         }
     }

     /**
      * Define if a serie should be draw with a special weight
      * @param $series
      * @param int $weight
      */
     function setSerieWeight($series, $weight = 0) {
         if ( !is_array($series) ) { 
             $series = $this->convertToArray($series); 
         }
         
         foreach($series as $key => $serie) { 
             if ( isset($this->data["Series"][$serie]) ) { 
                 $this->data["Series"][$serie]["Weight"] = $weight; 
             } 
         }
     }

     /**
      * Returns the palette of the given serie
      * @param $serie
      * 
      * @return array|null
      */
     function getSeriePalette($serie) {
         if ( !isset($this->data["Series"][$serie]) ) { 
             return null; 
         }
         
         return [
             "R" => $this->data["Series"][$serie]["Color"]["R"],
             "G" => $this->data["Series"][$serie]["Color"]["G"],
             "B" => $this->data["Series"][$serie]["Color"]["B"],
             "Alpha" => $this->data["Series"][$serie]["Color"]["Alpha"],
         ];
     }

     /**
      * Set the color of one serie
      * @param $series
      * @param null $format
      */
     function setPalette($series, $format = null) {
         if ( !is_array($series) ) { 
             $series = $this->convertToArray($series); 
         }

         foreach($series as $serie) {
             $r	        = isset($format["R"]) ? $format["R"] : 0;
             $g	        = isset($format["G"]) ? $format["G"] : 0;
             $b	        = isset($format["B"]) ? $format["B"] : 0;
             $alpha = isset($format["Alpha"]) ? $format["Alpha"] : 100;

             if ( isset($this->data["Series"][$serie]) ) {
                 $OldR = $this->data["Series"][$serie]["Color"]["R"]; $OldG = $this->data["Series"][$serie]["Color"]["G"]; $OldB = $this->data["Series"][$serie]["Color"]["B"];
                 $this->data["Series"][$serie]["Color"]["R"] = $r;
                 $this->data["Series"][$serie]["Color"]["G"] = $g;
                 $this->data["Series"][$serie]["Color"]["B"] = $b;
                 $this->data["Series"][$serie]["Color"]["Alpha"] = $alpha;

                 /* Do reverse processing on the internal palette array */
                 foreach ($this->palette as $key => $value) { 
                     if ($value["R"] == $OldR && $value["G"] == $OldG && $value["B"] == $OldB) { 
                         $this->palette[$key]["R"] = $r; 
                         $this->palette[$key]["G"] = $g; 
                         $this->palette[$key]["B"] = $b; 
                         $this->palette[$key]["Alpha"] = $alpha;
                     } 
                 }
             }
         }
     }

     /**
      * Load a palette file
      * @param $file_name
      * @param bool $overwrite
      * 
      * @return int
      */
     function loadPalette($file_name, $overwrite = false) {
         if ( !file_exists($file_name) ) { 
             return -1; 
         }
         
         if ( $overwrite ) { 
             $this->palette = ""; 
         }

         $fileHandle = @fopen($file_name, "r");
         if (!$fileHandle) { 
             return -1; 
         }
         
         while (!feof($fileHandle)) {
             $buffer = fgets($fileHandle, 4096);
             if ( preg_match("/,/",$buffer) ) {
                 list($r,$g,$b,$alpha) = preg_split("/,/",$buffer);
                 if ( $this->palette == "" ) { 
                     $id = 0; 
                 } else { 
                     $id = count($this->palette); 
                 }
                 $this->palette[$id] = [
                     "R" => $r,
                     "G" => $g,
                     "B" => $b,
                     "Alpha" => $alpha
                 ];
             }
         }
         fclose($fileHandle);

         /* Apply changes to current series */
         $id = 0;
         if ( isset($this->data["Series"])) {
             foreach($this->data["Series"] as $key => $value) {
                 if ( !isset($this->palette[$id]) ){
                     $this->data["Series"][$key]["Color"] = [
                         "R" => 0,
                         "G" => 0,
                         "B" => 0,
                         "Alpha" => 0
                     ];
                 } else {
                     $this->data["Series"][$key]["Color"] = $this->palette[$id];
                 }
                 
                 $id++;
             }
         }
         
         return 1;
     }

     /**
      * Initialise a given scatter serie
      * @param $id
      * 
      * @return int
      */
     function initScatterSerie($id) {
         if ( isset($this->data["ScatterSeries"][$id]) ) {
             return 0;
         }

         $this->data["ScatterSeries"][$id]["Description"]	= "Scatter ".$id;
         $this->data["ScatterSeries"][$id]["isDrawable"]	= true;
         $this->data["ScatterSeries"][$id]["Picture"]	= null;
         $this->data["ScatterSeries"][$id]["Ticks"]		= 0;
         $this->data["ScatterSeries"][$id]["Weight"]	= 0;

         if ( isset($this->palette[$id]) ) {
             $this->data["ScatterSeries"][$id]["Color"] = $this->palette[$id];
         } else {
             $this->data["ScatterSeries"][$id]["Color"]["R"] = rand(0,255);
             $this->data["ScatterSeries"][$id]["Color"]["G"] = rand(0,255);
             $this->data["ScatterSeries"][$id]["Color"]["B"] = rand(0,255);
             $this->data["ScatterSeries"][$id]["Color"]["Alpha"] = 100;
         }

         return 1;
     }

     /**
      * Initialise a given serie
      * @param $serie
      */
     function initialise($serie){
         if ( isset($this->data["Series"]) ) { $id = count($this->data["Series"]); } else { $id = 0; }

         $this->data["Series"][$serie]["Description"]	= $serie;
         $this->data["Series"][$serie]["isDrawable"]	= true;
         $this->data["Series"][$serie]["Picture"]		= null;
         $this->data["Series"][$serie]["Max"]		= null;
         $this->data["Series"][$serie]["Min"]		= null;
         $this->data["Series"][$serie]["Axis"]		= 0;
         $this->data["Series"][$serie]["Ticks"]		= 0;
         $this->data["Series"][$serie]["Weight"]		= 0;
         $this->data["Series"][$serie]["Shape"]		= SERIE_SHAPE_FILLEDCIRCLE;

         if ( isset($this->palette[$id]) ){
             $this->data["Series"][$serie]["Color"] = $this->palette[$id];
         } else {
             $this->data["Series"][$serie]["Color"]["R"] = rand(0,255);
             $this->data["Series"][$serie]["Color"]["G"] = rand(0,255);
             $this->data["Series"][$serie]["Color"]["B"] = rand(0,255);
             $this->data["Series"][$serie]["Color"]["Alpha"] = 100;
         }
     }

     /**
      * @param int $normalization_factor
      * @param null $unit_change
      * @param int $round
      */
     function normalize($normalization_factor = 100, $unit_change = null, $round = 1) {
         $abscissa = $this->data["Abscissa"];

         $selected_series = [];
         $max_val                 = 0;
         foreach($this->data["Axis"] as $axis_id => $axis) {
             if ( $unit_change != null ) { 
                 $this->data["Axis"][$axis_id]["Unit"] = $unit_change; 
             }

             foreach($this->data["Series"] as $serie_name => $serie) {
                 if ($serie["Axis"] == $axis_id && $serie["isDrawable"] == true && $serie_name != $abscissa) {
                     $selected_series[$serie_name] = $serie_name;

                     if ( count($serie["Data"] ) > $max_val ) { 
                         $max_val = count($serie["Data"]); 
                     }
                 }
             }
         }

         for($i=0; $i <= $max_val - 1; $i++) {
             $factor = 0;
             foreach ($selected_series as $key => $serie_name ) {
                 $value = $this->data["Series"][$serie_name]["Data"][$i];
                 if ( $value != VOID ) {
                     $factor = $factor + abs($value);
                 }
             }

             if ( $factor != 0 ) {
                 $factor = $normalization_factor / $factor;

                 foreach ($selected_series as $key => $serie_name ) {
                     $value = $this->data["Series"][$serie_name]["Data"][$i];

                     if ( $value != VOID && $factor != $normalization_factor ) {
                         $this->data["Series"][$serie_name]["Data"][$i] = round(abs($value)*$factor,$round);
                     } elseif ( $value == VOID || $value == 0 ) {
                         $this->data["Series"][$serie_name]["Data"][$i] = VOID;
                     } elseif ( $factor == $normalization_factor ) {
                         $this->data["Series"][$serie_name]["Data"][$i] = $normalization_factor;
                     }
                 }
             }
         }

         foreach ($selected_series as $key => $serie_name ) {
             $this->data["Series"][$serie_name]["Max"] = max($this->stripVOID($this->data["Series"][$serie_name]["Data"]));
             $this->data["Series"][$serie_name]["Min"] = min($this->stripVOID($this->data["Series"][$serie_name]["Data"]));
         }
     }

     /**
      * Load data from a CSV (or similar) data source
      * @param $file_name
      * @param array $options
      */
     function importFromCSV($file_name, $options = []) {
         $delimiter		= isset($options["Delimiter"]) ? $options["Delimiter"] : ",";
         $got_header		= isset($options["GotHeader"]) ? $options["GotHeader"] : false;
         $skip_columns	= isset($options["SkipColumns"]) ? $options["SkipColumns"] : array(-1);
         $default_serie_name	= isset($options["DefaultSerieName"]) ? $options["DefaultSerieName"] : "Serie";

         $handle = @fopen($file_name,"r");
         if ($handle) {
             $header_parsed = false; 
             $serie_names = "";
             
             while (!feof($handle)) {
                 $buffer = fgets($handle, 4096);
                 $buffer = str_replace(chr(10),"",$buffer);
                 $buffer = str_replace(chr(13),"",$buffer);
                 $values = preg_split("/".$delimiter."/",$buffer);

                 if ( $buffer != "" ) {
                     if ( $got_header && !$header_parsed ) {
                         foreach($values as $key => $name) { 
                             if ( !in_array($key,$skip_columns) ) { 
                                 $serie_names[$key] = $name; 
                             } 
                         }
                         
                         $header_parsed = true;
                     } else {
                         if ($serie_names === "" ) { 
                             foreach($values as $key => $name) {    
                                 if ( !in_array($key, $skip_columns) ) { 
                                     $serie_names[$key] = $default_serie_name.$key; 
                                 } 
                             } 
                         }
                         
                         foreach($values as $key => $value) {    
                             if ( !in_array($key, $skip_columns) ) { 
                                 $this->addPoints($value, $serie_names[$key]); 
                             } 
                         }
                     }
                 }
             }
             fclose($handle);
         
         }
     }

     /**
      * Create a dataset based on a formula
      * @param $serie_name
      * @param string $formula
      * @param array $options
      * 
      * @return int
      */
     function createFunctionSerie($serie_name, $formula = "", $options = []) {
         $min_x		= isset($options["MinX"]) ? $options["MinX"] : -10;
         $max_x		= isset($options["MaxX"]) ? $options["MaxX"] : 10;
         $x_step		= isset($options["XStep"]) ? $options["XStep"] : 1;
         $auto_description	= isset($options["AutoDescription"]) ? $options["AutoDescription"] : false;
         $record_abscissa	= isset($options["RecordAbscissa"]) ? $options["RecordAbscissa"] : false;
         $abscissa_serie	= isset($options["AbscissaSerie"]) ? $options["AbscissaSerie"] : "Abscissa";

         if ( $formula == "" ) { 
             return 0; 
         }

         $result = [];
         $return = VOID;
         $abscissa = [];
         for($i = $min_x; $i <= $max_x; $i = $i + $x_step ) {
             $expression = "\$return = '!'.(".str_replace("z",$i,$formula).");";

             if ( @eval($expression) === false ) {
                 $return = VOID;
             }
             if ( $return == "!" ) { 
                 $return = VOID; 
             } else { 
                 $return = $this->right($return,strlen($return) -1 ); 
             }
             
             if ( $return == "NAN" || $return == "INF" || $return == "-INF" ) { 
                 $return = VOID; 
             }

             $abscissa[] = $i;
             $result[]     = $return;
         }

         $this->addPoints($result, $serie_name);
         
         if ( $auto_description ) { 
             $this->setSerieDescription($serie_name, $formula); 
         }
         
         if ( $record_abscissa ) { 
             $this->addPoints($abscissa, $abscissa_serie); 
         }
         
         return 1;
     }

     /**
      * @param $series
      */
     function negateValues($series) {
         if ( !is_array($series) ) {
             $series = $this->convertToArray($series);
         }

         foreach($series as $serie) {
             if (isset($this->data["Series"][$serie])) {

                 $data = [];
                 foreach($this->data["Series"][$serie]["Data"] as $value) {
                     if ( $value == VOID ) {
                         $data[] = VOID;
                     } else {
                         $data[] = -$value;
                     }
                 }

                 $this->data["Series"][$serie]["Data"] = $data;

                 $this->data["Series"][$serie]["Max"] = max($this->stripVOID($this->data["Series"][$serie]["Data"]));
                 $this->data["Series"][$serie]["Min"] = min($this->stripVOID($this->data["Series"][$serie]["Data"]));
                }
            }
     }

     /**
      * Return the data & configuration of the series
      *
      * @return array|string
      */
     function getData() {
         return $this->data;
     }

     /**
      * Save a palette element
      * @param $id
      * @param $color
      */
     function savePalette($id, $color) {
         $this->palette[$id] = $color;
     }

     /**
      * Return the palette of the series
      *
      * @return array
      */
     function getPalette(){
         return $this->palette;
     }

     /**
      * Called by the scaling algorithm to save the config
      * @param $axis
      */
     function saveAxisConfig($axis) {
         $this->data["Axis"] = $axis;
     }

     /**
      * Save the Y Margin if set
      * @param $value
      */
     function saveYMargin($value) {
         $this->data["YMargin"] = $value;
     }

     /**
      * Save extended configuration to the pData object
      * @param $tag
      * @param $values
      */
     function saveExtendedData($tag, $values) {
         $this->data["Extended"][$tag] = $values;
     }

     /**
      * Called by the scaling algorithm to save the orientation of the scale
      * @param $orientation
      */
     function saveOrientation($orientation) {
         $this->data["Orientation"] = $orientation;
     }

     /**
      * Convert a string to a single elements array
      * @param $value
      * 
      * @return array
      */
     function convertToArray($value) {
         return [$value];
     }

     /**
      * @return string
      */
     function __toString(){
         return "pData object.";
     }

     /**
      * @param $value
      * @param $nb_char
      *
      * @return bool|string
      */
     function left($value, $nb_char) {
         return substr($value,0, $nb_char);
     }

     /**
      * @param $value
      * @param $nb_char
      *
      * @return bool|string
      */
     function right($value, $nb_char)	{
         return substr($value,strlen($value) - $nb_char, $nb_char);
     }

     /**
      * @param $value
      * @param $depart
      * @param $nb_char
      *
      * @return bool|string
      */
     function mid($value, $depart, $nb_char) {
         return substr($value,$depart-1, $nb_char);
     }
 }
