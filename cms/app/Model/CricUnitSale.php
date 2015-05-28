<?php

class Model_CricUnitSale extends Model_Base
{

    const DB_NAME = 'cric_xf';

    const TABLE_NAME = 'Unit_Sale';

    public static function getUnitSale($unitID)
    {
        if (empty($unitID)) {
            return null;
        }
        return self::getAll(
            array(
                'where' => array('UnitID' => $unitID),
                'order' => 'UpdateTime DESC',
            )
        );
    }

    //房价点评网(价目表->房价走势功能)
    public static function getUnitSaleData($unitID)
    {
        return self::getRow(array(
            'where' => array('UnitID' => $unitID),
            'order' => 'SaleEndTime DESC',
        ));
    }
}