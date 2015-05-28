<?php

/**
 * Created by PhpStorm.
 * User: cjj
 * Date: 15/4/7
 * Time: 下午1:44
 */
class Model_BatchTypes
{


    // 最新批次信息
    public static  function newestBatchTypes($unitID)
    {
        $result = Sdk_Cms_Newhouse::newestBatchTypes($unitID);
        if ($result['status'] == 1) {
            return $result['data'];
        }
        return null;
    }

    public static function getGroupBatchType($batchType)
    {
        $groupArr = array();
        if (!empty($batchType)) {
            foreach ($batchType as $item) {
                $groupArr[$item['BatchId']][] = $item;
            }
        }
        return $groupArr;
    }

    public static function getBatchType($groupData)
    {
        $batchType = array();
        if (empty($groupData)) {
            return $batchType;
        }
        foreach ($groupData as $item) {
            foreach ($item as $kc => $vc) {
                $batchType[] = $vc;
                break;
            }
        }
        return $batchType;
    }

    public static function getBatchTypeInfo(&$batchTypes){
       /* for (int i = 11; i >= 0; i--)
    {
        var temBatchType = batchTypes.Where(x => x.MarketDate >= curMonth.AddMonths(-i) && x.MarketDate < curMonth.AddMonths(-i + 1)).FirstOrDefault();

        if (temBatchType != null)
        {
            salesPrice = temBatchType.SalePrice.ToString();
            batchTypeNew = temBatchType;
        }
        temBatchData.Add(salesPrice);
        batchTypeList.Add(batchTypeNew);
    }*/
        $batchTypeList = array();
        $salesPrice = array();
        for ( $i = 11; $i >= 0; $i--) {
            $temp1 = null;
            $temp2 = 'null';
            if (!empty($batchTypes)) {
                foreach ($batchTypes as $item) {
                    $s = "-{$i}";
                    $t = (-$i) + 1;
                    if ($t < 0) {
                        $e = "-{$t}";
                    } else {
                        $e = "+{$t}";
                    }
                    if (strtotime($item['MarketDate']) >= strtotime("$s month") && strtotime($item['MarketDate']) < strtotime("$e month")) {
                        $temp1 = $item;
                        $temp2 = $item['SalePrice'];
                        break;
                    }
                }
            }
            $batchTypeList[] = $temp1;
            $salesPrice[] = $temp2;
        }
        return array('batchTypeList'=> $batchTypeList,'salesPrice' => $salesPrice);
    }

    public static function getBatchTypeBlock($unitID)
    {
        $result = Sdk_Cms_Newhouse::getBatchTypeBlock($unitID);
        if ($result['status'] == 1 && !empty($result['data'])) {
            return $result['data'];
        }
        return  null;
    }

    public static function getBlockIdAndNumbers(&$batchTypeBlock,$BatchId)
    {
        $blockIdAndNumbers  = array();
        if (empty($batchTypeBlock)) {
            return $blockIdAndNumbers;
        }
        foreach($batchTypeBlock as $item) {
            $temp = array();
            if (strtoupper($item['Batch_Id']) == strtoupper($BatchId)) {
                $temp[0] = $item['Block'];
                $temp[1] = $item['block_id'];
            }
            if (!empty($temp)) {
                $blockIdAndNumbers[] = $temp;
            }
        }
        return $blockIdAndNumbers;
    }


    public static function getBatchTypeNewList($homeID)
    {
        $result = Sdk_Cms_Newhouse::getBatchTypeNewList($homeID);
        if ($result['status'] && !empty($result['data'])) {
            return $result['data'];
        }
        return array();
    }

    public static  function filterBatchTypeFullNews(&$batchTypeFullNews,$homeID,$batchId)
    {
        $data = array();
        if (empty($batchTypeFullNews)) {
            return $data;
        }
        foreach ($batchTypeFullNews as $item) {
            if ($item['BuildingID'] == $homeID && $item['BlockGroupID'] == $batchId) {
                $data = $item;
                break;
            }
        }
        return $data;
    }


    //判断是否是别墅
    public static function isCottage($data)
    {
        if(!empty($data)){
            foreach($data as $key=>$value){
                if( $value['BatchTypeCode'] == '花园洋房' || $value['BatchTypeCode'] == '联排' || $value['BatchTypeCode'] == '双拼'
                    || $value['BatchTypeCode'] == '独栋' || $value['BatchTypeCode'] == '叠加')
                {
                    return true;
                }
            }
        }
         return false;
    }
}