<?php

class Model_CricBlock extends Model_Base
{
    const DB_NAME = 'cric_xf';

    const TABLE_NAME = 'Block';



    public static function getBlockByUnitID($sUnitID)
    {
        return self::getAll(
            array(
                'where' => array( 'UnitID' => $sUnitID,'State' => 1),
                'order' => 'BlockNumber asc',
            )
        );
    }

    //获取block表信息(type1代表根据条件获取一条数据，type2代表根据条件获取所有数据)
    public static function getBlock($sUnitID,$type)
    {
        if($type == 1) {
            $sql = "SELECT * FROM " . self::TABLE_NAME . " WHERE UnitID = '$sUnitID' AND State = 1 AND HEIGHT > 0 AND WIDTH > 0 ORDER BY BlockSort ASC LIMIT 1";
            return self::query($sql);
        }
        if($type == 2){
            $asql = "SELECT * FROM " . self::TABLE_NAME . " WHERE UnitID = '$sUnitID' AND State = 1 AND (X > 0 OR Y > 0) ORDER BY BlockNumber ASC";
            return self::query($asql);
        }
    }

    public static function getBlockRoomInfo($sUnitID)
    {
        $blockInfo = self::getAll(
            [
                'where' => [
                    'UnitID' => $sUnitID,
                    'State' => 1
                ],
                'order' => 'BlockSort ASC, BlockNumber ASC'
            ]
        );
        $blockExtension = [];
        if ($blockInfo) {
            foreach($blockInfo as $key => $value) {
                $where = [
                    'UnitID' => $sUnitID,
                    'BlockID' => $value['BlockID'],
                    'State' => 1,
                ];
                $minArea = Model_CricRoom::getRow(
                    [
                        'where' => $where,
                        'order' => 'Area Asc'
                    ]

                );

                $maxArea = Model_CricRoom::getRow(
                    [
                        'where' => $where,
                        'order' => 'Area DESC'
                    ]
                );

                $typeName = Model_CricRoom::getPair(
                    [
                        'where' => $where,
                        'group' => 'TypeName'
                    ],
                    'RoomID',
                    'TypeName'
                );
                $tmp = [
                    'BlockID' => $value['BlockID'], 'BlockAddress' => $value['BlockAddress'], 'BlockNumber' => $value['BlockNumber'],
                    'BLOCK' => $value['Block'],'MinArea' => $minArea['Area'],'MaxArea' => $maxArea['Area'],'RoomType' => implode(';', array_values($typeName))
                ];
                array_push($blockExtension,$tmp);
            }
        }
        return $blockExtension;
    }
}