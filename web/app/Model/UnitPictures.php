<?php

/**
 * Created by PhpStorm.
 * User: cjj
 * Date: 15/4/7
 * Time: 下午1:44
 */
class Model_UnitPictures
{


    // cric 楼盘图片
    public static function getUnitPictures($unitID)
    {
        $data = array();
        $result = Sdk_Cms_Newhouse::unitPictures($unitID);
        if ($result['status'] == 1) {
            foreach ($result['data'] as $key => $item) {
                // 社区实景
                if ($item['PictureType'] == 'sjt') {
                    $data['sjt'][] = $item;
                }
                // 效果图
                if ($item['PictureType'] == 'xgt') {
                    $data['xgt'][] = $item;
                }
                // 总平图
                if ($item['PictureType'] == 'zpt') {
                    $data['zpt'][] = $item;
                }
                // 楼盘周边
                if ($item['PictureType'] == 'ybft') {
                    $data['ybft'][] = $item;
                }
            }
        }
        return $data;
    }

    public static function getTypeImages(&$unitPictures, $type)
    {
        $data = array();
        if (!empty($unitPictures)) {
            foreach ($unitPictures as $item) {
                if ($item['Remark'] == $type) {
                    $data[] = $item;
                }
            }
        }
        return $data;
    }

    public static function formatUnitImageInfo($data)
    {
        $ImageArr = array();
        foreach ($data as $item) {
            $ImageArr[] = array('ImageUrl' =>preg_replace('/600X450/','639X480',$item['sImage']));
        }
        $firstImg = preg_replace('/639X480/','220X160',$ImageArr[0]['ImageUrl']);
        $imageUrls = json_encode($ImageArr,JSON_UNESCAPED_SLASHES);
        $imageUrls = preg_replace('/\"/','&quot;',$imageUrls);
        return array('imageUrls' => $imageUrls,'firstImg' => $firstImg);
    }
}