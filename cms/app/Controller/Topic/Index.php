<?php

/**
 * Created by PhpStorm.
 * User: yaobiqing
 * Date: 15/1/12
 * Time: 上午9:05
 */
class Controller_Topic_Index extends Controller_Base
{

    const FILE_EXISTS     = 1;
    const SFTP_CONTENT_FAILED  = 2;
    const FILE_NOT_EXISTS = 3;
    const ZIPFILE_NOT_EXISTS = 4;
    const DESDIR_EXISTS = 5;

    private $sTopicPath = '';

    public function actionBefore()
    {
        parent::actionBefore();
        $this->sTopicPath = Yaf_G::getConf('path', 'topic');
    }
    /**
     * 专题列表
     */
    public function listAction()
    {
        $aParam                   = array();
        $aParam['sTitle']         = $this->getParam('sTitle');
        $aParam['sOrder']         = $this->getParam('sOrder');
        $aParam['iPublishStatus'] = $this->getParam('iPublishStatus');

        $this->assign('aParam', $aParam);

        $iPage  = intval($this->getParam('page'));
        $aWhere = array(
            'iStatus' => 1,
            'iCityID IN' => [0, $this->iCurrCityID]
        );

        if (isset($aParam['iPublishStatus']) && $aParam['iPublishStatus'] != -1) {
            $aWhere['iPublishStatus'] = $aParam['iPublishStatus'];
        }

        if (!empty($aParam['sTitle'])) {
            $aWhere['sTitle LIKE'] = '%' . $aParam['sTitle'] . '%';
        }

        $sOrder = '';
        if (!empty($aParam['sOrder'])) {
            $sOrder = $aParam['sOrder'];
        }
        $aList = Model_Topic::getList($aWhere, $iPage, $sOrder);
        $this->assign('aList', $aList);

    }

    private function _cityDispatch($sUrl, $sLastUrl, $oldDir, $newDir)
    {
        $sMkdir = '';
        $sSourceDir = '';
        $sDesDir = '';
        if ($oldDir != $newDir) {
            $sTmpUrl = str_replace($oldDir.'/', '', $sUrl);
            $sBaseDir = substr($sTmpUrl, 0, strpos($sTmpUrl, '/'));
            $sSourceDir = $oldDir.'/'.$sBaseDir;
            $sDesDir = $newDir.'/'.$sBaseDir;
            $sMkdir = $newDir;
        }


        if ($sMkdir) {
            $this->_mkDir($this->sTopicPath.$sMkdir);
        }
        if ($sSourceDir && $sDesDir) {
            $this->_mvFile($this->sTopicPath.$sSourceDir, $this->sTopicPath.$sDesDir);
        }
        $sUrl = str_replace($sSourceDir, $sDesDir, $sUrl);
        $sLastUrl = str_replace($sSourceDir, $sDesDir, $sLastUrl);
        return['sUrl' => $sUrl, 'sLastUrl' => $sLastUrl];
    }

    /**
     * 专题添加
     */
    public function addAction()
    {
        if ($this->isPost()) {
            $aTopic = $this->_checkData();
            if (empty($aTopic)) {
                return null;
            }
            $aTopic['iCreateUserID'] = $aTopic['iUpdateUserID'] = $this->aCurrUser['iUserID'];
            if ($aTopic['iCityID'] > 0) {
                $aCity = Model_City::getDetail($aTopic['iCityID']);
                $aRet = $this->_cityDispatch($aTopic['sUrl'], $aTopic['sLastUrl'], 'tmp' , $aCity['sFullPinyin']);

            } else {
                $aRet = $this->_cityDispatch($aTopic['sUrl'], $aTopic['sLastUrl'], 'tmp' , 'china');
            }
            $aTopic['sLastUrl'] = $aRet['sLastUrl'];
            $aTopic['sUrl'] = $aRet['sUrl'];

            if (Model_Topic::addData($aTopic) > 0) {
                return $this->showMsg('专题增加成功！', true);
            } else {
                return $this->showMsg('专题增加失败！', false);
            }
        } else {
            $this->assign('sUploadUrl', Yaf_G::getConf('upload', 'url'));
        }
    }

    /**
     * 专题编辑
     */
    public function editAction()
    {
        if ($this->isPost()) {
            $aTopic = $this->_checkData();
            if (empty($aTopic)) {
                return null;
            }
            $aTopic['iUpdateUserID'] = $this->aCurrUser['iUserID'];
            $aTopic['iTopicID'] = intval($this->getParam('iTopicID'));

            $aTopicInfo = Model_Topic::getDetail($aTopic['iTopicID']);
            $aOldCity = Model_City::getDetail($aTopicInfo['iCityID']);

            $aNewCity = Model_City::getDetail($aTopic['iCityID']);
            if (isset($aTopic['sUrl'])) {
                if ($aTopic['iCityID'] > 0) {
                    $aRet = $this->_cityDispatch($aTopic['sUrl'], $aTopic['sLastUrl'], 'tmp', $aNewCity['sFullPinyin']);
                } else {
                    $aRet = $this->_cityDispatch($aTopic['sUrl'], $aTopic['sLastUrl'], 'tmp', 'china');
                }
                $aTopic['sLastUrl'] = $aRet['sLastUrl'];
                $aTopic['sUrl'] = $aRet['sUrl'];

            } else {
                unset($aTopic['sZipFile']);
                if ($aTopic['iCityID'] != $aTopicInfo['iCityID']) {
                    $sOldCity = 'china';
                    $sNewCity = 'china';
                    if ($aOldCity) {
                        $sOldCity = $aOldCity['sFullPinyin'];
                    }
                    if ($aNewCity) {
                        $sNewCity = $aNewCity['sFullPinyin'];
                    }
                    $aRet = $this->_cityDispatch($aTopicInfo['sUrl'], $aTopicInfo['sLastUrl'], $sOldCity, $sNewCity);
                }
                $aTopic['sLastUrl'] = $aRet['sLastUrl'];
                $aTopic['sUrl'] = $aRet['sUrl'];

            }

            if (1 == Model_Topic::updData($aTopic)) {
                return $this->showMsg('专题信息更新成功！', true);
            } else {
                return $this->showMsg('专题信息更新失败！', false);
            }
        } else {
            $this->_response->setHeader('Access-Control-Allow-Origin', '*');

            $iTopicID = intval($this->getParam('id'));
            $aTopic = Model_Topic::getDetail($iTopicID);
            if (!$aTopic) {
                $this->redirect('/topic/list.html');
            }
            $this->assign('aTopic', $aTopic);
            $this->assign('sUploadUrl', Yaf_G::getConf('upload', 'url'));
        }
    }

    /**
     * 专题下架
     */
    public function offlineAction()
    {
        $iTopicID = intval($this->getParam('id'));
        $aTopic   = Model_Topic::getDetail($iTopicID);
        if (empty($aTopic)) {
            return $this->showMsg('发布的专题不存在！', false);
        }

        if (0 == $aTopic['iPublishStatus']) {
            return $this->showMsg('专题下架成功！', true);
        }
        $sLastUrl = dirname($aTopic['sUrl']) . '/' . Util_Tools::passwdGen(
                10,
                Util_Tools::FLAG_ALPHANUMERIC
            ) . '.html';

        $aRow = array(
            'iTopicID'       => $iTopicID,
            'iPublishStatus' => 0,
            'sLastUrl'       => $sLastUrl
        );

        $this->_mvFile($this->sTopicPath.$aTopic['sUrl'], $this->sTopicPath.$sLastUrl);
        $iRet = Model_Topic::updData($aRow);
        if ($iRet == 1) {
            return $this->showMsg('专题下架成功！', true);
        } else {
            return $this->showMsg('专题下架失败！', false);
        }
    }

    /**
     * 专题发布
     */
    public function onlineAction()
    {
        $iTopicID = intval($this->getParam('id'));
        $aTopic   = Model_Topic::getDetail($iTopicID);
        if (empty($aTopic)) {
            return $this->showMsg('发布的专题不存在！', false);
        }

        if (1 == $aTopic['iPublishStatus']) {
            return $this->showMsg('专题上架成功！', true);
        }

        $aRow = array(
            'iTopicID'       => $iTopicID,
            'iPublishStatus' => 1,
            'sLastUrl'       => $aTopic['sUrl']
        );
        $this->_mvFile($this->sTopicPath.$aTopic['sLastUrl'], $this->sTopicPath.$aTopic['sUrl']);
        $iRet = Model_Topic::updData($aRow);
        if ($iRet == 1) {
            return $this->showMsg('专题上架成功！', true);
        } else {
            return $this->showMsg('专题上架失败！', false);
        }
    }

    /**
     * 专题删除
     */
    public function delAction()
    {
        $iTopicID   = intval($this->getParam('id'));
        $aTopicInfo = Model_Topic::getDetail($iTopicID);
        if ($aTopicInfo) {
            $iRet = Model_Topic::delData($iTopicID);

            if ($iRet) {
                //针对文件删除的操作
                $this->_mvFile(
                    $this->sTopicPath.$aTopicInfo['sUrl'],
                    dirname($aTopicInfo['sUrl']) . '/' . Util_Tools::passwdGen(
                        10,
                        Util_Tools::FLAG_ALPHANUMERIC
                    ) . '.html'
                );

                return $this->showMsg('专题删除成功！', true);
            } else {
                return $this->showMsg('专题删除失败！', false);
            }

        } else {
            return $this->showMsg('专题删除失败！', false);
        }
    }

    /**
     * 上传zip文件并解压缩
     */
    public function uploadAction()
    {
        $aResult = [];

        if (isset($_FILES['sZip']) && $_FILES['sZip']['error'] == 0) {
            $aFileInfo = $_FILES['sZip'];
            if (in_array($aFileInfo['type'], ['application/octet-stream', 'application/zip'])) {
                if (is_uploaded_file($aFileInfo['tmp_name'])) {
                    $sTmpPath = $this->sTopicPath.'tmp';
                    if (!$this->_mkDir($sTmpPath)) {
                        $aResult['status']  = false;
                        $aResult['message'] = '目录创建失败';
                    } else {
                        $result = $this->_unzipFile($aFileInfo['tmp_name'], $sTmpPath.'/'.basename($aFileInfo['name'], '.zip'));
                        //echo $result;exit;
                        switch ($result) {
                            case self::ZIPFILE_NOT_EXISTS:
                                $aResult['status']  = false;
                                $aResult['message'] = 'zip压缩包上传失败！';

                                break;
                            case self::DESDIR_EXISTS:
                                $aResult['status']  = false;
                                $aResult['message'] = '压缩包解压失败，请联系管理员';
                                break;

                            default:
                                $aResult['status']   = true;
                                $aResult['sZipFile'] = $aFileInfo['name'];
                                /**
                                 * 获取第一个解压出来的html文件相对路径
                                 */
                                $TmpRet          = explode('.html', $result);
                                if (count($TmpRet) >= 2) {
                                    $sHtmlUrl        = trim(
                                        substr($TmpRet[0], strrpos($TmpRet[0], 'inflating:') + strlen('inflating:'))
                                    );
                                    $aResult['sUrl'] = str_replace(
                                        $this->sTopicPath,
                                        '',
                                        $sHtmlUrl . '.html'
                                    );
                                } else {
                                    $aResult['status']   = false;
                                    $aResult['message']   = '压缩包中未包含html文件！';
                                }

                                break;
                        }
                    }
                } else {
                    $aResult['status']  = false;
                    $aResult['message'] = '上传失败，请稍后重试！';

                }
            } else {
                $aResult['status']  = false;
                $aResult['message'] = '当前只支持zip压缩包，您上传的格式不正确！';
            }
        } else {
            $aResult['status']  = false;
            $aResult['message'] = '上传失败，请稍后重试！';
        }
        $body     = "<script>
        document.domain='".Yaf_G::getConf('cms', 'domain')."';
        var uploadResult = " . json_encode(
                $aResult
            ) . "; window.parent.zipUploadCallBack(uploadResult);</script>";
        $response = $this->getResponse();
        $response->appendBody($body);
        $this->autoRender(false);
    }

    /**
     * 检测参数
     */
    private function _checkData()
    {
        $iTopicID = intval($this->getParam('iTopicID'));

        $sTitle       = $this->getParam('sTitle');
        $iCityID      = intval($this->getParam('iCityID'));
        $iPublishTime = $this->getParam('iPublishTime');
        $sImage       = $this->getParam('sImage');
        $sZipFile     = $this->getParam('sZipFile');
        $sUrl         = $this->getParam('sUrl');

        $row = Model_Topic::getRow(
            [
                'where' => [
                    'iStatus' => 1,
                    'iCityID IN' => [$iCityID, 0],
                    'sZipFile' => $sZipFile
                ]
            ]
        );
        if (!Util_Validate::isLength($sTitle, 2, 50)) {
            return $this->showMsg('专题标题长度范围为2到50个字！', false);
        }

        if (!$sZipFile && 0 == $iTopicID) {
            return $this->showMsg('请先上传压缩包！！', false);
        }

        if (empty($sImage)) {
            return $this->showMsg('请选择一张默认图片！', false);
        }

        if ($row && $row['iTopicID'] != $iTopicID) {
            return $this->showMsg('对应的专题已经存在！', false);
        }

        if (empty($iPublishTime)) {
            $iPublishTime = time();
        } else {
            $iPublishTime = strtotime($iPublishTime);
        }

        $aRow = array(
            'sTitle'       => $sTitle,
            'iCityID'      => $iCityID,
            'sImage'       => $sImage,
            'iPublishTime' => $iPublishTime,
            'sZipFile'     => $sZipFile,

        );

        if ($sUrl) {
            $aRow['sLastUrl'] = $aRow['sUrl'] = $sUrl;
        }

        return $aRow;

    }

    /**
     * sftp上传文件并解压缩
     * @param $sSourceFile
     * @param $sDesFile
     * @return bool|int|string
     */
    private function _unzipFile($zipFile, $desDir)
    {

        if (!file_exists($zipFile)) {
            return self::ZIPFILE_NOT_EXISTS;
        }

        if (file_exists($desDir)) {
            $this->_delDir($desDir);
        }
        $ret = exec("unzip $zipFile -d $desDir");
        error_log($ret);
        return $ret;
    }

    private function _mkDir($sPath)
    {
        error_log($sPath);
        if (!file_exists($sPath)) {
            return Util_File::tryMakeDir($sPath, 0755);
        }
        return true;
    }
    /**
     * 重命名上存储的专题html文件
     * @param $sSourceFile
     * @param $sDesFile
     * @return bool|int|string
     */
    private function _mvFile($sSourceFile, $sDesFile)
    {

        if (file_exists($sDesFile)) {
            return self::FILE_EXISTS;
        }
        if (!file_exists($sSourceFile)) {
            return self::FILE_NOT_EXISTS;
        }

        return exec("mv $sSourceFile $sDesFile");
    }

    function _delDir($dir) {
        return Util_File::tryDeleteDir($dir);

        //先删除目录下的文件：
        $dh=opendir($dir);
        while ($file=readdir($dh)) {
            if($file!="." && $file!="..") {
                $fullpath=$dir."/".$file;
                if(!is_dir($fullpath)) {
                    unlink($fullpath);
                } else {
                    deldir($fullpath);
                }
            }
        }

        closedir($dh);
        //删除当前文件夹：
        if(rmdir($dir)) {
            return true;
        } else {
            return false;
        }
    }
}