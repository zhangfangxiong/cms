<?php

/**
 * CricIn数据同步
 * @author xiejinci@gmail.com
 *
 */
class Model_CricIn
{

    private $msdbh;

    private $mydbh;

    private $iPingTime;

    private $sDefaultDB = 'CricXinFang';
    
    private $iMaxProcess = 10;

    private $aType = array(
        'nchar' => 'char',
        'char' => 'char',
        'text' => 'text',
        'varbinary' => 'varbinary',
        'tinyint' => 'tinyint',
        'varchar' => 'varchar',
        'nvarchar' => 'varchar',
        'float' => 'float',
        'datetime' => 'datetime',
        'timestamp' => 'timestamp',
        'money' => 'float',
        'int' => 'int',
        'bigint' => 'bigint',
        'uniqueidentifier' => 'varchar',
        'bit' => 'tinyint',
        'geography' => 'varchar(30)',
        'geometry' => 'varchar(30)'
    );

    private $aNeedSync = array(
        'DB_HOUSE_PRICE_USER.TB_CITY_LIST',
        'Analysts',
        'Analysts_Opinion',
        'Unit',
        'BatchTypeFull',
        'CricNews',
        'City',
        'Region',
        'Block',
        'District',
        'UnitRecommended',
        'RoomType',
        'CityMap',
        'RoomAssessSpecific',
        'RoomTypeRemaining',
        'UnitZhuanti0114',
        'UnitZhuanti0114_Detail',
        'Unit_Picture',
        'Room',
    );

    private $aFilterSync = array(
        'visit',
        'bak',
        'temp',
        'tmp',
        'yesterday',
        'test',
        'log',
        'session',
        'search',
        'keyword',
        'hanzi',
        'PinYin',
        'Source'
    );

    private $aNoSync = array(
        'ADSource',
        'AdProject',
        'AdRelation',
        'AdResourcesCategory',
        'AdVisitedLog',
        'AgencyPrice',
        'AuthorizeFailedLog',
        'Analysts_Recommended',
        'BaiduPinZhuan',
        'BlockAssessSpecific',
        'City_PriceRtypeProject',
        'Ctiy_PriceSection',
        'CricNews',
        'ESFMostPopUnit',
        'QuestionCollect',
        'ShareInfo',
        'TB_MAP_POI_MAIN',
        'TouchWebUser',
        'UnitInfoAddition',
        'UnitNear',
        'UnitNearBy',
        'UnitPK',
        'Unit_Comment_Mobile',
        'Unit_Geo',
        'Unit_Geo_App',
        'Unit_Interest_List',
        'Unit_PK_List',
        'Unit_Surround',
        'UrlUnitID',
        'UserAccount',
        'UserActivedLink',
        'UserHelpAction',
        'UserMouseMove',
        'UserTripOut',
        'User_Feedback_Mobile',
        
        'XinFangUnitDJByDay'
    );

    public function __construct ()
    {
        $this->iPingTime = time();
        $this->msdbh = Util_Common::getMsSQLDB('cric_xf', 'master');
        $this->mydbh = Util_Common::getDb('cric_xf');

        $this->aNoSync = array_flip($this->aNoSync);
    }

    public function syncAll ($iMaxProcess = 10)
    {
        $this->iMaxProcess = $iMaxProcess;
        $aTable = $this->getTables();
        $this->syncTable($aTable);
        $this->syncCity();
    }

    public function syncNeed ($iMaxProcess = 10)
    {
        $this->iMaxProcess = $iMaxProcess;
        $aTable = $this->aNeedSync;
        $this->syncTable($aTable);
        $this->syncCity();
    }
    
    public function syncStat ($aTable) 
    {
        if ($aTable == 'need') {
            $aTable = $this->aNeedSync;
        } elseif ($aTable == 'all') {
            $aTable = $this->getTables();
        }
        foreach ($aTable as $sRealTable) {
            $sTable = $this->parseTable($sRealTable);
            $iMsCnt = $this->msdbh->getOne('SELECT COUNT(*) FROM ' . $sTable);
            $iMyCnt = $this->getMySQLOne('SELECT COUNT(*) FROM ' . $sTable);
            
            echo "$sRealTable : $iMsCnt == $iMyCnt\n";
        }
    }
    
    public function dropTable($sKeyword)
    {
        if (empty($sKeyword)) {
            return true;
        }
        $sSQL = 'SHOW TABLES LIKE "%' . $sKeyword . '%"';
        $sST = $this->mydbh->query($sSQL);
        $sST->setFetchMode(PDO::FETCH_NUM);
        $aSyncTable = $sST->fetchAll();
        if (empty($aSyncTable)) {
            $aList = array();
        }
        foreach ($aSyncTable as $aTable) {
            $this->mydbh->query('DROP TABLE IF EXISTS ' . $aTable[0]);
            echo "Drop table ${aTable[0]}\n";
        }
    }

    public function syncCity()
    {
        $sSQL = "
            REPLACE INTO permission.t_city(iCityID,sCityName,sPinyin,sFullPinyin,sLJCode)
            SELECT ID,CityName,EJUCityCode,CityCode,LEJUCityCode FROM cric_xf.City
        ";
        $this->mydbh->query($sSQL);
    }

    public function getAllTable ()
    {
        $sSQL = "SELECT * FROM sys.tables ORDER BY name";
        $aTableList = $this->msdbh->getAll($sSQL);
        $aTable = array();
        foreach ($aTableList as $aRow) {
            $aTable[] = $aRow['name'];
        }
        return $aTable;
    }

    public function countData ()
    {
        $aTable = $this->getAllTable();
        foreach ($aTable as $sTable) {
            $iCnt = $this->msdbh->getOne('SELECT COUNT(*) FROM ' . $sTable);
            echo "$sTable => $iCnt \n";
        }
    }

    public function getUpdateField ($aTableInfo)
    {
        $aUpdateFiled = array(
            'updatetime' => 1,
            'updatedate' => 1,
            'update_time' => 1,
            'update_date' => 1,
            'createtime' => 1,
            'createdate' => 1,
            'create_time' => 1,
            'create_date' => 1
        );
        foreach ($aTableInfo as $sField => $aRow) {
            if (isset($aUpdateFiled[strtolower($sField)]) && $aRow['type'] == 'datetime') {
                return $sField;
            }
        }

        return '';
    }
    
    public function syncTable ($aTable)
    {
        $oRedis = Util_Common::getRedis('orm');
        foreach ($aTable as $sRealTable) {
            $sTable = $this->parseTable($sRealTable);
            $sMyTable = $sTable . '_SYNC';
            if (isset($this->aNoSync[$sTable])) {
                continue;
            }
            
            // 同步数据结构
            $this->syncStructure($sRealTable);

            $this->mydbh->query('TRUNCATE TABLE `' . $sMyTable . '`');
            $iCnt = $this->msdbh->getOne('SELECT COUNT(*) FROM ' . $sTable);
            if ($iCnt > 3000) {
                $oRedis->del('DB_SYNC_' . $sTable);
                $iLimit = max(ceil($iCnt / $this->iMaxProcess), 3000);
                for ($i = 0; $i < $this->iMaxProcess; $i++) {
                    $iOffset = $i * $iLimit;
                    Util_Cron::newProcess('/cmd/cricin/syncmuti/table/' . $sRealTable . '/offset/' . $iOffset . '/limit/' . $iLimit, $i);
                    if ($iOffset >= $iCnt) {
                        break;
                    }
                }
                while (true) {
                    $cmd = 'ps -efww | grep "/cmd/cricin/syncmuti/table/' . $sRealTable . '/offset/"|grep -v grep|wc -l';
                    $out = '';
                    exec($cmd, $out);
                    $exec_num = isset($out[0]) ? $out[0] : 0;
                    if ($exec_num == 0) {
                        break;
                    }
                    $aSyncCnt = $oRedis->hVals('DB_SYNC_' . $sTable);
                    $iOkCnt = array_sum($aSyncCnt);
                    echo "$sMyTable finished $iOkCnt/$iCnt\n";
                    sleep(1);
                }
            } else {
                $this->syncMutiData($sRealTable, 0, $iCnt, 0);
            }
            
            $this->mydbh->query('DROP TABLE IF EXISTS `' . $sTable . '`');
            $this->mydbh->query('RENAME TABLE `' . $sMyTable . '` TO `' . $sTable . '`');
            
            // 清除缓存
            $oRedis->clear('DB_*:*:' . $sTable);
            $oRedis->clear('DB_SYNC_' . $sTable);

            echo "$sTable finished\n\n";
        }

        $this->dropTable('_SYNC');
        $this->syncStat($aTable);
    }
    
    public function syncMutiData($sTable, $iOffset, $iLimit, $iPid)
    {
        $this->syncStructure($sTable, true);
        $sTable = $this->parseTable($sTable);
        if ($iPid > 0) {
            $sMyTable = $sTable . '_SYNC_' . $iPid;
            $this->mydbh->query('CREATE TABLE IF NOT EXISTS `' . $sMyTable . '` LIKE `' . $sTable . '`');
        } else {
            $sMyTable = $sTable . '_SYNC';
        }
        $this->mydbh->query('TRUNCATE TABLE `' . $sMyTable . '`');
        $oRedis = Util_Common::getRedis('orm');
        $aTableInfo = $this->getTableInfo($sTable);
        $aOField = array();
        foreach ($aTableInfo as $aField) {
            if ($aField['type'] == 'uniqueidentifier') {
                $aOField[] = "CAST([" . $aField['name'] . "] AS varchar(38)) as '" . $aField['name'] . "'";
            } elseif ($aField['type'] == 'datetime') {
                $aOField[] = "CONVERT(varchar(30),[" . $aField['name'] . "],120) as '" . $aField['name'] . "'";
            } elseif ($aField['type'] == 'geography') {
                $aOField[] = "CAST([" . $aField['name'] . "] AS varchar(38)) as '" . $aField['name'] . "'";
            } elseif ($aField['type'] == 'geometry') {
                $aOField[] = "CAST([" . $aField['name'] . "] AS varchar(38)) as '" . $aField['name'] . "'";
            } else {
                $aOField[] = '[' . $aField['name'] . ']';
            }
        }
        $sOField = join(',', $aOField);
        $aRow = $this->msdbh->getRow('SELECT TOP 1 * FROM ' . $sTable);
        $iCnt = $this->msdbh->getOne('SELECT COUNT(*) FROM ' . $sTable);
        $iLimit = min($iLimit, $iCnt);
        if (empty($aRow)) {
            return true;;
        }
        $sField = key($aRow);
        $iSize = 1000;
        $iFinishCnt = 0;
        $iOkCnt = 0;
        while (true) {
            $iSize = min(1000, $iLimit - $iFinishCnt);
            $sSQL = "
                SELECT TOP $iSize * FROM (
                    SELECT $sOField, ROW_NUMBER() OVER (ORDER BY [$sField]) AS RowNo
                    FROM $sTable AS B
                ) AS A
                WHERE RowNo > $iOffset AND RowNo <= " . ($iOffset + $iSize) . "
            ";
        
            // echo $sSQL . "\n\n";exit;
            for ($i = 0; $i < 10; $i++) {
                try {
                    $aList = $this->msdbh->getAll($sSQL);
                } catch (Exception $e) {
                    echo $e->getMessage() . "\n";
                }
                //print_r($aList);exit;
                if (! empty($aList)) {
                    break;
                } else {
                    sleep(1);
                }
            }
            $iOkCnt += $this->insertRows($sMyTable, $aList, $aTableInfo);
            $iOffset += $iSize;
            $iFinishCnt += $iSize;
            $oRedis->hSet('DB_SYNC_' . $sTable, $iPid, $iOkCnt);
            echo "$sMyTable finished $iOffset/$iOkCnt/$iFinishCnt/$iLimit\n";
            
            if ($iFinishCnt >= $iLimit) {
                break;
            }
        
            if (time() - $this->iPingTime > 300) {
                $this->iPingTime = time();
                $this->mydbh = null;
                $this->mydbh = Util_Common::getDb('cric_xf');
            }
        }
        if ($iPid > 0) {
            $this->mydbh->query("REPLACE INTO ${sTable}_SYNC SELECT * FROM $sMyTable");
            $this->mydbh->query("DROP TABLE IF EXISTS $sMyTable");
        }
    }

    public function insertRows ($sTable, $aRows, $aTableInfo)
    {
        if (empty($aRows)) {
            return 0;
        }
        
        $cols = array();
        $vals = array();
        $n = 0;
        foreach ($aRows as $row) {
            unset($row['RowNo']);
            $arr = array();
            foreach ($row as $col => $val) {
                if (0 == $n) {
                    $cols[] = $col;
                }
                $arr[] = $this->quote($val, isset($aTableInfo[$col]) ? $aTableInfo[$col]['type'] : 'char');
            }
            $vals[] = '(' . join(',', $arr) . ')';
            $n ++;
        }
        $sSQL = 'REPLACE INTO `' . $sTable . '`(`' . join('`,`', $cols) . '`) VALUES' . join(',', $vals);
        $bRet = $this->mydbh->query($sSQL);
        if ($bRet === false) {
            echo "Error: REPLACE INTO $sTable \n";
            echo $sSQL . "\n";
            exit();
        }

        return count($aRows);
    }

    public function getMySQLOne ($sSQL)
    {
        $sST = $this->mydbh->query($sSQL);
        return $sST->fetchColumn();
    }

    public function getMySQLAll ($sSQL)
    {
        $sST = $this->mydbh->query($sSQL);
        $sST->setFetchMode(PDO::FETCH_ASSOC);
        $aList = $sST->fetchAll();
        if (empty($aList)) {
            $aList = array();
        }

        return $aList;
    }

    public function quote ($value, $type = 'char')
    {
        switch ($type) {
            case 'datetime':
                return "'" . (empty($value) ? '1970-01-01 08:00:00' : $value) . "'";
            case 'money':
            case 'float':
                return floatval($value);
            case 'tinyint':
            case 'int':
            case 'bigint':
            case 'bit':
            case 'timestamp':
                return intval($value);
            case 'nchar':
            case 'char':
            case 'varchar':
            case 'nvarchar':
            case 'geography':
            case 'geometry':
            case 'varbinary':
            case 'uniqueidentifie':
            default:
                return "'" . addcslashes($value, "\000\n\r\\'\"\032") . "'";
        }
    }

    public function syncStructure ($sTable, $bCheckHas = false)
    {
        $sTable = $this->parseTable($sTable);
        $sMyTable = $sTable . '_SYNC';
        if ($bCheckHas && $this->getMySQLOne('SHOW TABLES LIKE "' . $sMyTable . '"')) {
            return true;
        }
        // $aTableInfo = $this->getTableInfo('Unit');print_r($aTableInfo);exit;
        // $sTable = 'Unit';
        $aTableInfo = $this->getTableInfo($sTable);
        
        // print_r($aTableInfo);exit;
        $sSQL = 'DROP TABLE IF EXISTS `' . $sMyTable . '`';
        $this->mydbh->query($sSQL);
        $sSQL = $this->makeCreate($sTable, $sMyTable, $aTableInfo);
        $bRet = $this->mydbh->query($sSQL);
        if ($bRet === false) {
            print_r($aTableInfo);
            echo $sSQL . "\n";
            exit();
        }
        $sSQL = $this->makeIndex($sTable, $sMyTable, $aTableInfo);
        // echo $sSQL;exit;
        $this->mydbh->query($sSQL);
        echo "CREATE TABLE IF NOT EXISTS $sMyTable\n";
    }

    public function getTables ()
    {
        $sSQL = "SELECT * FROM sys.tables ORDER BY name";
        $aTableList = $this->msdbh->getAll($sSQL);
        $aTable = array();
        //$aNeedSync = array_flip($this->aNeedSync);
        $aNeedSync = array();
        foreach ($aTableList as $aRow) {
            if (isset($this->aNoSync[$aRow['name']]) || isset($aNeedSync[$aRow['name']])) {
                continue;
            }

            $bFind = false;
            foreach ($this->aFilterSync as $v) {
                if (false !== stripos($aRow['name'], $v)) {
                    $bFind = true;
                    break;
                }
            }
            if ($bFind) {
                continue;
            }
            if (preg_match('/\d+$/', $aRow['name'])) {
                continue;
            }
            $aTable[] = $aRow['name'];
        }
        return $aTable;
    }

    public function getFieldType ($aField, $sTable)
    {
        if (isset($this->aType[$aField['type']])) {
            $type = $this->aType[$aField['type']];
        } else {
            echo $sTable . "\n";
            print_r($aField);
            echo "\n";
            exit();
        }
        if (strstr($aField['type'], 'char') && $aField['length'] > 1000) {
            $type = 'text';
            $length = '';
        } elseif ($aField['type'] == 'uniqueidentifier') {
            $type = 'varchar(38)';
            $length = '';
        } elseif ($aField['type'] == 'nvarchar' || $aField['type'] == 'nchar') {
            $length = '(' . ($aField['length'] / 2) . ')';
        } elseif (in_array($aField['type'], array(
                'text',
                'datetime',
                'geography',
                'geometry',
                'timestamp',
                'int',
                'float',
                'tinyint',
                'bigint'
            ))) {
            $length = '';
        } else {
            $length = $aField['length'] > 0 ? '(' . $aField['length'] . ')' : '';
        }

        return $type . $length;
    }

    public function makeCreate ($sTable, $sMyTable, $aTableInfo)
    {
        $aCreate = array();
        foreach ($aTableInfo as $aField) {
            $type = $this->getFieldType($aField, $sTable);

            $isnullable = ' NOT NULL';
            if ($aField['isnullable']) {
                $isnullable = ' ';
            }

            $comment = '';
            if (! empty($aField['comment'])) {
                $comment = ' COMMENT ' . $this->quote($aField['comment'], 'char') . '';
            }

            $aCreate[] = '`' . $aField['name'] . '` ' . $type . $isnullable . $comment;
        }

//         if (! $this->hasPrimary($sTable, $aTableInfo)) {
//             $aCreate[] = '`AutoID` INT NOT NULL AUTO_INCREMENT, PRIMARY KEY (`AutoID`)';
//         }
        
        //$aCreate[] = 'sHashCode varchar(32) NOT NULL';
        return 'CREATE TABLE `' . $sMyTable . '` (' . join(',', $aCreate) . ') ENGINE=MyISAM DEFAULT CHARSET=utf8';
    }
    
    public function hasPrimary($sTable, $aTableInfo)
    {
        $aIndexList = $this->getTableIndex($sTable, $aTableInfo);
        foreach ($aIndexList as $sIndex => $aIndex) {
            if ($aIndex['type'] == 'primary') {
                return true;
            }
        }
        
        return false;
    }

    public function makeIndex ($sTable, $sMyTable, $aTableInfo)
    {
        $aIndexList = $this->getTableIndex($sTable, $aTableInfo);
        $aCreate = array();
        foreach ($aIndexList as $sIndex => $aIndex) {
            if ($aIndex['length'] >= 500) {
                continue;
            }
            if ($aIndex['type'] == 'primary') {
                $aCreate[] = 'ADD PRIMARY KEY (' . join(',', $aIndex['columns']) . ')';
            } elseif ($aIndex['type'] == 'unique') {
                $aCreate[] = 'ADD UNIQUE ' . $sIndex . '(' . join(',', $aIndex['columns']) . ')';
            } elseif ($aIndex['type'] == 'index') {
                $aCreate[] = 'ADD INDEX ' . $sIndex . '(' . join(',', $aIndex['columns']) . ')';
            }
        }

        // 唯一索引
        //$aCreate[] = 'ADD UNIQUE sHashCode(sHashCode)';

        return 'ALTER TABLE ' . $sMyTable . ' ' . join(',', $aCreate);
    }

    public function isChangeTableInfo ($sTable, $aTableInfo)
    {
        return true;
        // 判断表是否存在
        $sSQL = "SHOW TABLES LIKE '$sTable'";
        $sSt = $this->mydbh->query($sSQL);
        if ($sSt->rowCount() == 0) {
            return true;
        }

        // 判断表结构是否改变
        $sSQL = "SHOW COLUMNS FROM $sTable";
        $sSt = $this->mydbh->query($sSQL);
        $aField0 = array();
        foreach ($sSt as $row) {
            if ($row['Field'] == 'sHashCode') {
                continue;
            }
            if (substr($row['Type'], 0, 3) == 'bit') {
                return true;
            }
            $aField0[] = $row['Field'];
        }
        $aField1 = array();
        foreach ($aTableInfo as $row) {
            $aField1[] = $row['name'];
        }
        sort($aField0);
        sort($aField1);
        if (join(',', $aField0) == join(',', $aField1)) {
            return false;
        }
        return true;
    }

    public function parseTable ($sTable) {
        $aTable = explode('.', $sTable);
        if (count($aTable) > 1) {
            $sTable = $aTable[1];
            $this->msdbh->query('USE ' . $aTable[0]);
        } else {
            $sTable = $aTable[0];
            $this->msdbh->query('USE ' . $this->sDefaultDB);
        }

        return $sTable;
    }

    public function getTableInfo ($sTable)
    {
        $sSQL = "
            SELECT b.name as name,
                b.max_length as length,
                b.is_nullable as isnullable,
                c.name as type,
                CAST(d.[value] as varchar(30)) as comment
            FROM sys.tables AS a
            LEFT JOIN sys.columns AS b ON b.object_id = a.object_id
            LEFT JOIN sys.types AS c ON c.user_type_id=b.user_type_id
            LEFT JOIN sys.extended_properties AS d ON a.object_id=d.major_id AND b.column_id=d.minor_id
            WHERE a.name='$sTable'
            ORDER BY b.column_id
        ";
        $aTableInfo = $this->msdbh->getAll($sSQL);
        $aRet = array();
        foreach ($aTableInfo as $aField) {
            $aRet[$aField['name']] = array(
                'name' => $aField['name'],
                'type' => $aField['type'],
                'length' => $aField['length'] == - 1 ? 2000 : $aField['length'],
                'isnullable' => $aField['isnullable'],
                'comment' => isset($aField['comment']) ? $aField['comment'] : null
            );
        }
        return $aRet;
    }

    public function getTableIndex ($sTable, $aTableInfo)
    {
        $sSQL = "
            SELECT a.object_id
    		,b.name AS schema_name
    		,a.name AS table_name
    		,c.name as ix_name
    		,c.is_unique AS ix_unique
    		,c.type_desc AS ix_type_desc
    		,d.index_column_id
    		,d.is_included_column
    		,e.name AS column_name
    		,f.name AS fg_name
    		,d.is_descending_key AS is_descending_key
    		,c.is_primary_key
    		,c.is_unique_constraint
            FROM sys.tables AS a
            INNER JOIN sys.schemas AS b            ON a.schema_id = b.schema_id AND a.is_ms_shipped = 0
            INNER JOIN sys.indexes AS c            ON a.object_id = c.object_id
            INNER JOIN sys.index_columns AS d      ON d.object_id = c.object_id AND d.index_id = c.index_id
            INNER JOIN sys.columns AS e            ON e.object_id = d.object_id AND e.column_id = d.column_id
            INNER JOIN sys.data_spaces AS f        ON f.data_space_id = c.data_space_id
            WHERE a.name='$sTable'
            ORDER BY c.name,d.index_column_id
        ";
        $aIndexList = $this->msdbh->getAll($sSQL);

        $aRet = array();
        foreach ($aIndexList as $aIndex) {
            if (isset($aErr[$aIndex['ix_name']])) {
                continue;
            }

            if (in_array($aTableInfo[$aIndex['column_name']]['type'], array(
                    'text',
                    'geography',
                    'geometry'
                ))) {
                $aRet[$aIndex['ix_name']]['length'] = 1000;
            }

            if (! isset($aRet[$aIndex['ix_name']])) {
                $type = 'index';
                if ($aIndex['is_primary_key'] == 1) {
                    $type = 'primary';
                } elseif ($aIndex['ix_unique']) {
                    $type = 'unique';
                }
                $aRet[$aIndex['ix_name']]['type'] = $type;
                $aRet[$aIndex['ix_name']]['columns'] = array();
                $aRet[$aIndex['ix_name']]['length'] = 0;
            }

            $aRet[$aIndex['ix_name']]['columns'][] = $aIndex['column_name'];
            $aRet[$aIndex['ix_name']]['length'] += $aTableInfo[$aIndex['column_name']]['length'];
        }
        return $aRet;
    }
}