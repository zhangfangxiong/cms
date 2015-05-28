<?php
/**
 * Created by PhpStorm.
 * User: yaobiqing
 * Date: 15/4/7
 * Time: 下午1:44
 */


class Model_Pricecompare
{
    /**
     * 进行比较
     * @param $price1
     * @param $price2
     */
    public static function Compare($price1,$price2) {
        if ($price1 > 0 && $price2 > 0)
        {
             $rate = $price1 / $price2;
				if ($rate > 1)
                {
                    $rate = $rate - 1;
                    $Result = 1;
                }
                else
                {
                    $rate = 1 - $rate;
                    $Result = -1;
                }
				if ($rate < 0.00005)
                {
                    $rate = '';
                    $Result = 0;
                    $realRate = '';
                }
                else
                {
                    $realRate = sprintf("%01.4f",$rate)*100;
                }
                return array('OrigionRate'=>$rate,'Price1'=>$price1,'Price2'=>$price2,'Result'=>$Result,'realRate' => $realRate);
			}
    }

    /**
     * 按月比较
     */
    public static function CompareLastMonth($PriceList){
        if(!empty($PriceList)){
            $newPriceList = explode(',',$PriceList);
            $count = count($newPriceList);
            if($count > 5){
                return self::Compare($newPriceList[$count-1],$newPriceList[$count-5]);
            }
        }else{
            return null;
        }
    }

}