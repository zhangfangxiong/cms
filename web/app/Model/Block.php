<?php

/**
 * Created by PhpStorm.
 * User: cjj
 * Date: 15/4/7
 * Time: 下午1:44
 */
class Model_Block
{



    public static  function filterBlocks(&$Blocks,$BlockID)
    {
        $data = array();
        if (empty($Blocks)) {
            return $data;
        }
        foreach($Blocks as $item) {
            if (strtoupper($item['BlockID']) == strtoupper($BlockID)) {
                $data = $item;
                break;
            }
        }
        return $data;
    }

    public static function getBlockExtension(&$Blocks,$rooms)
    {
        $blockExtension  = array();
        foreach ($Blocks as $index => $block) {
            $roomType  = array();
            foreach ($rooms as $key => $item) {
                if ($item['UnitId'] == $block['UnitID'] && $item['BlockId'] == $block['BlockID']) {
                    if (!in_array($item['TypeName'],$roomType)) {
                        $roomType[] = $item['TypeName'];
                    }
                    unset($rooms[$key]);
                }
            }
            $blockExtension[$index] = array(
                  'BlockID' => $block['BlockID'],
                  'BlockAddress' => $block['BlockAddress'],
                  'BlockNumber' => $block['BlockNumber'],
                  'BLOCK' => $block['Block'],
                  'RoomType' => empty($roomType)? '':implode(';',$roomType),
            );
        }
        return $blockExtension;
    }

}