<?php
/**
 * 分析师业务管理类
 * author: xcy
 */
class Model_AnalystsOpinion extends Model_Base
{

    const DB_NAME = 'cric_xf';

    const TABLE_NAME = 'Analysts_Opinion';

    /*
     * 获取相关分析师id的opinion
     * return array
     */
    public static function getAnalystsOpinion($p_aIds, $p_aUnitIds) {	
        $aParam = [
        	'where' => [ 
                'AnalystsID in' => $p_aIds,
                'UnitID in' => implode(',', $p_aUnitIds),
            ],
        	'limit' => 50,
        	'order' => 'UpdateTime desc'
        ];

        return self::getOrm()->fetchAll($aParam);
    }
}