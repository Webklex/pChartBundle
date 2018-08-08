<?php
/*
 pStock - class to draw stock charts

 Version     : 2.1.3
 Made by     : Jean-Damien POGOLOTTI
 Last Update : 09/09/11

 This file can be distributed under the license you can find at :

                   http://www.pchart.net/license

 You can find the whole class documentation on the pChart web site.
*/

namespace Webklex\pChart;

define("STOCK_MISSING_SERIE"	, 180001);

/**
* Class pStock
*
* @package Webklex\pChart
*/
class pStock {
    
    /** @var pDraw|pImage $pChartObject */
    var $pChartObject;
    
    /** @var pData $pDataObject */
    var $pDataObject;
    
    /**
     * pStock constructor.
     * @param $pChartObject
     * @param $pDataObject
     */
    function __construct($pChartObject, $pDataObject) {
        $this->pChartObject = $pChartObject;
        $this->pDataObject  = $pDataObject;
    }

    /**
     * Draw a stock chart
     * @param array $format
     *
     * @return null|integer
     */
    function drawStockChart($format = []) {
        $default = [
            "SerieOpen" => "Open",
            "SerieClose" => "Close",
            "SerieMin" => "Min",
            "SerieMax" => "Max",
            "SerieMedian" => null,
            "LineWidth" => 1,
            "LineR" => 0,
            "LineG" => 0,
            "LineB" => 0,
            "LineAlpha" => 100,
            "ExtremityWidth" => 1,
            "ExtremityLength" => 3,
            "ExtremityR" => 0,
            "ExtremityG" => 0,
            "ExtremityB" => 0,
            "ExtremityAlpha" => 100,
            "BoxWidth" => 8,
            "BoxUpR" => 188,
            "BoxUpG" => 224,
            "BoxUpB" => 46,
            "BoxUpAlpha" => 100,
            "BoxUpSurrounding" => null,
            "BoxUpBorderR" => 188 - 20,
            "BoxUpBorderG" => 224 - 20,
            "BoxUpBorderB" => 46 - 20,
            "BoxUpBorderAlpha" => 100,
            "BoxDownR" => 224,
            "BoxDownG" => 100,
            "BoxDownB" => 46,
            "BoxDownAlpha" => 100,
            "BoxDownSurrounding" => null,
            "BoxDownBorderR" => 188 - 20,
            "BoxDownBorderG" => 224 - 20,
            "BoxDownBorderB" => 46 - 20,
            "BoxDownBorderAlpha" => 100,
            "ShadowOnBoxesOnly" => true,
            "MedianR" => 255,
            "MedianG" => 0,
            "MedianB" => 0,
            "MedianAlpha" => 100,
            "RecordImageMap" => false,
            "ImageMapTitle" => "Stock Chart"
        ];
        
        $format = array_merge($default, $format);
        
        if ( $format['BoxUpSurrounding'] != null ){
            $format['BoxUpBorderR'] = $format['BoxUpR'] + $format['BoxUpSurrounding'];
            $format['BoxUpBorderG'] = $format['BoxUpG'] + $format['BoxUpSurrounding'];
            $format['BoxUpBorderB'] = $format['BoxUpB'] + $format['BoxUpSurrounding'];
        }
        
        if ( $format['BoxDownSurrounding'] != null )	{
            $format['BoxDownBorderR'] = $format['BoxDownR'] + $format['BoxDownSurrounding'];
            $format['BoxDownBorderG'] = $format['BoxDownG'] + $format['BoxDownSurrounding'];
            $format['BoxDownBorderB'] = $format['BoxDownB'] + $format['BoxDownSurrounding'];
        }
        
        if ( $format['LineWidth'] != 1 ) {
            $format['LineOffset'] = $format['LineWidth'] / 2;
        }

        $format['BoxOffset'] = $format['BoxWidth'] / 2;
        
        $data = $this->pChartObject->DataSet->getData();
        list($x_margin, $x_divs) = $this->pChartObject->scaleGetXSettings();

        $serie_open = $format['SerieOpen'];
        $serie_close = $format['SerieClose'];
        $serie_min = $format['SerieMin'];
        $serie_max = $format['SerieMax'];
        $serie_median = $format['SerieMedian'];
        
        if ( !isset($data["Series"][$serie_open]) || !isset($data["Series"][$serie_close]) || !isset($data["Series"][$serie_min]) || !isset($data["Series"][$serie_max]) ){
            return STOCK_MISSING_SERIE;
        }
        
        $plots = [];
        foreach($data["Series"][$serie_open]["Data"] as $key => $value) {
            $point = [];
            if ( isset($data["Series"][$serie_close]["Data"][$key]) || isset($data["Series"][$serie_min]["Data"][$key]) || isset($data["Series"][$serie_max]["Data"][$key]) ){
                $point = [
                    $value,
                    $data["Series"][$serie_close]["Data"][$key],
                    $data["Series"][$serie_min]["Data"][$key],
                    $data["Series"][$serie_max]["Data"][$key]
                ];
            }
            if ( $serie_median != null && isset($data["Series"][$serie_median]["Data"][$key]) ){
                $point[] = $data["Series"][$serie_median]["Data"][$key];
            }
        
            $plots[] = $point;
        }
        
        $axis_id	= $data["Series"][$serie_open]["Axis"];
        
        $y_zero	= $this->pChartObject->scaleComputeY(0, ["AxisID" => $axis_id]);
        
        $x = $this->pChartObject->GraphAreaX1 + $x_margin;
        $y = $this->pChartObject->GraphAreaY1 + $x_margin;
        
        $line_settings = [
            "R" => $format['LineR'],
            "G" => $format['LineG'],
            "B" => $format['LineB'],
            "Alpha" => $format['LineAlpha']
        ];
        $extremity_settings	= [
            "R" => $format['ExtremityR'],
            "G" => $format['ExtremityG'],
            "B" => $format['ExtremityB'],
            "Alpha" => $format['ExtremityAlpha']
        ];
        $box_up_settings = [
            "R" => $format['BoxUpR'],
            "G" => $format['BoxUpG'],
            "B" => $format['BoxUpB'],
            "Alpha" => $format['BoxUpAlpha'],
            "BorderR" => $format['BoxUpBorderR'],
            "BorderG" => $format['BoxUpBorderG'],
            "BorderB" => $format['BoxUpBorderB'],
            "BorderAlpha" => $format['BoxUpBorderAlpha']
        ];
        $box_down_settings = [
            "R" => $format['BoxDownR'],
            "G" => $format['BoxDownG'],
            "B" => $format['BoxDownB'],
            "Alpha" => $format['BoxDownAlpha'],
            "BorderR" => $format['BoxDownBorderR'],
            "BorderG" => $format['BoxDownBorderG'],
            "BorderB" => $format['BoxDownBorderB'],
            "BorderAlpha" => $format['BoxDownBorderAlpha']
        ];
        $median_settings = [
            "R" => $format['MedianR'],
            "G" => $format['MedianG'],
            "B" => $format['MedianB'],
            "Alpha" => $format['MedianAlpha']
        ];
        
        foreach($plots as $key => $points) {
            $pos_array = $this->pChartObject->scaleComputeY($points, ["AxisID" => $axis_id]);

            $values = "Open :".$data["Series"][$serie_open]["Data"][$key].
                "<BR>Close : ".$data["Series"][$serie_close]["Data"][$key].
                "<BR>Min : ".$data["Series"][$serie_min]["Data"][$key].
                "<BR>Max : ".$data["Series"][$serie_max]["Data"][$key]."<BR>";

            if ( $serie_median != null ) {
                $values = $values."Median : ".$data["Series"][$serie_median]["Data"][$key]."<BR>";
            }

            if ( $pos_array[0] > $pos_array[1] ) {
                $format['ImageMapColor'] = $this->pChartObject->toHTMLColor($format['BoxUpR'], $format['BoxUpG'], $format['BoxUpB']);
            } else {
                $format['ImageMapColor'] = $this->pChartObject->toHTMLColor($format['BoxDownR'], $format['BoxDownG'], $format['BoxDownB']);
            }

            if ( $data["Orientation"] == SCALE_POS_LEFTRIGHT ) {
                if ( $y_zero > $this->pChartObject->GraphAreaY2 - 1 ) { 
                    $y_zero = $this->pChartObject->GraphAreaY2 - 1; 
                }
                if ( $y_zero < $this->pChartObject->GraphAreaY1 + 1 ) { 
                    $y_zero = $this->pChartObject->GraphAreaY1 + 1; 
                }
    
                if ( $x_divs == 0 ) { 
                    $x_step = 0; 
                } else { 
                    $x_step = ($this->pChartObject->GraphAreaX2 - $this->pChartObject->GraphAreaX1 - $x_margin * 2) / $x_divs; 
                }
    
                if ( $format['ShadowOnBoxesOnly'] ) {
                    $format['RestoreShadow'] = $this->pChartObject->Shadow; 
                    $this->pChartObject->Shadow = false; 
                }
    
                if ( $format['LineWidth'] == 1 ){
                    $this->pChartObject->drawLine($x, $pos_array[2], $x, $pos_array[3], $line_settings);
                } else {
                    $this->pChartObject->drawFilledRectangle($x - $format['LineOffset'], $pos_array[2],$x + $format['LineOffset'], $pos_array[3], $line_settings);
                }
    
                if ( $format['ExtremityWidth'] == 1 ) {
                    $this->pChartObject->drawLine($x - $format['ExtremityLength'], $pos_array[2],$x + $format['ExtremityLength'], $pos_array[2], $extremity_settings);
                    $this->pChartObject->drawLine($x - $format['ExtremityLength'], $pos_array[3],$x + $format['ExtremityLength'], $pos_array[3], $extremity_settings);
    
                    if ( $format['RecordImageMap'] ) { 
                        $this->pChartObject->addToImageMap(
                            "RECT",
                            floor($x - $format['ExtremityLength']).",".
                                    floor($pos_array[2]).",".
                                    floor($x + $format['ExtremityLength']).",".
                                    floor($pos_array[3]), $format['ImageMapColor'], $format['ImageMapTitle'], $values); 
                    }
                } else {
                    $this->pChartObject->drawFilledRectangle($x - $format['ExtremityLength'], $pos_array[2],$x + $format['ExtremityLength'],$pos_array[2] - $format['ExtremityWidth'], $extremity_settings);
                    $this->pChartObject->drawFilledRectangle($x - $format['ExtremityLength'], $pos_array[3],$x + $format['ExtremityLength'],$pos_array[3] + $format['ExtremityWidth'], $extremity_settings);
        
                    if ( $format['RecordImageMap'] ) { 
                        $this->pChartObject->addToImageMap(
                            "RECT",
                            floor($x - $format['ExtremityLength']).",".
                                    floor($pos_array[2] - $format['ExtremityWidth']).",".
                                    floor($x + $format['ExtremityLength']).",".
                                    floor($pos_array[3] + $format['ExtremityWidth']), $format['ImageMapColor'], $format['ImageMapTitle'], $values); 
                    }
                }
    
                if ( $format['ShadowOnBoxesOnly'] ) { 
                    $this->pChartObject->Shadow = $format['RestoreShadow']; 
                }
    
                if ( $pos_array[0] > $pos_array[1] ) {
                    $this->pChartObject->drawFilledRectangle($x - $format['BoxOffset'], $pos_array[0],$x + $format['BoxOffset'], $pos_array[1], $box_up_settings);
                } else {
                    $this->pChartObject->drawFilledRectangle($x - $format['BoxOffset'], $pos_array[0],$x + $format['BoxOffset'], $pos_array[1], $box_down_settings);
                }
    
                if ( isset($pos_array[4]) ) {
                    $this->pChartObject->drawLine($x - $format['ExtremityLength'], $pos_array[4],$x + $format['ExtremityLength'], $pos_array[4], $median_settings);
                }
    
                $x = $x + $x_step;
                
            } elseif ( $data["Orientation"] == SCALE_POS_TOPBOTTOM ) {
                
                if ( $y_zero > $this->pChartObject->GraphAreaX2 - 1 ) { 
                    $y_zero = $this->pChartObject->GraphAreaX2 - 1; 
                }
                if ( $y_zero < $this->pChartObject->GraphAreaX1 + 1 ) { 
                    $y_zero = $this->pChartObject->GraphAreaX1 + 1; 
                }
    
                if ( $x_divs == 0 ) { 
                    $x_step = 0; 
                } else { 
                    $x_step = ($this->pChartObject->GraphAreaY2 - $this->pChartObject->GraphAreaY1 - $x_margin * 2) / $x_divs; 
                }

                if ( $format['LineWidth'] == 1 ) {
                    $this->pChartObject->drawLine($pos_array[2], $y, $pos_array[3], $y, $line_settings);
                } else {
                    $this->pChartObject->drawFilledRectangle($pos_array[2],$y - $format['LineOffset'], $pos_array[3],$y + $format['LineOffset'], $line_settings);
                }

                if ( $format['ShadowOnBoxesOnly'] ) {
                    $format['RestoreShadow'] = $this->pChartObject->Shadow;
                    $this->pChartObject->Shadow = false;
                }
    
                if ( $format['ExtremityWidth'] == 1 ) {
                    $this->pChartObject->drawLine($pos_array[2],$y - $format['ExtremityLength'], $pos_array[2],$y + $format['ExtremityLength'], $extremity_settings);
                    $this->pChartObject->drawLine($pos_array[3],$y - $format['ExtremityLength'], $pos_array[3],$y + $format['ExtremityLength'], $extremity_settings);

                    if ( $format['RecordImageMap'] ) {
                        $this->pChartObject->addToImageMap(
                            "RECT",
                            floor($pos_array[2]).",".
                                    floor($y - $format['ExtremityLength']).",".
                                    floor($pos_array[3]).",".
                                    floor($y + $format['ExtremityLength']), $format['ImageMapColor'], $format['ImageMapTitle'], $values);
                    }
                } else {
                    $this->pChartObject->drawFilledRectangle($pos_array[2],$y - $format['ExtremityLength'],$pos_array[2] - $format['ExtremityWidth'],$y + $format['ExtremityLength'], $extremity_settings);
                    $this->pChartObject->drawFilledRectangle($pos_array[3],$y - $format['ExtremityLength'],$pos_array[3] + $format['ExtremityWidth'],$y + $format['ExtremityLength'], $extremity_settings);

                    if ( $format['RecordImageMap'] ) {
                        $this->pChartObject->addToImageMap(
                            "RECT",
                            floor($pos_array[2] - $format['ExtremityWidth']).",".
                                    floor($y - $format['ExtremityLength']).",".
                                    floor($pos_array[3] + $format['ExtremityWidth']).",".
                                    floor($y + $format['ExtremityLength']), $format['ImageMapColor'], $format['ImageMapTitle'], $values);
                    }
                }
    
                if ( $format['ShadowOnBoxesOnly'] ) {
                    $this->pChartObject->Shadow = $format['RestoreShadow'];
                }
    
                if ( $pos_array[0] < $pos_array[1] ){
                    $this->pChartObject->drawFilledRectangle($pos_array[0],$y - $format['BoxOffset'], $pos_array[1],$y + $format['BoxOffset'], $box_up_settings);
                } else {
                    $this->pChartObject->drawFilledRectangle($pos_array[0],$y - $format['BoxOffset'], $pos_array[1],$y + $format['BoxOffset'], $box_down_settings);
                }

                if ( isset($pos_array[4]) ) {
                    $this->pChartObject->drawLine($pos_array[4],$y - $format['ExtremityLength'], $pos_array[4],$y + $format['ExtremityLength'], $median_settings);
                }

                $y = $y + $x_step;
            }
        }

        return null;
    }
}