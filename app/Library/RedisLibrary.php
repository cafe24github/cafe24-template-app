<?php

namespace App\Library;

use Illuminate\Support\Facades\Redis;

class RedisLibrary
{
    /*
     * @var $sTable
     */
    private $sTable;

    /**
     * libRedis constructor.
     * @param $sTable
     */
    public function initializeTable($sTable)
    {
        $this->sTable = $sTable;
    }

    /**
     * method to get current primary key in redis
     *
     * @return int
     */
    protected function getPk()
    {
        return (int)Redis::incr($this->sTable . ':pk');
    }

    /**
     * method to insert data in redis
     *
     * @param $aArray
     * @return int
     */
    public function insert($aArray)
    {
        $aArray['seq'] = $this->getPk();
        return Redis::rPush($this->sTable, $this->processInput($aArray));
    }

    /**
     * method to get count of a list saved in redis
     *
     * @return int
     */
    public function getListLength()
    {
        return Redis::lLen($this->sTable);
    }

    /**
     * function to get a list in redis
     *
     * @param int $iLimit
     * @param int $iOffset
     * @return array
     */
    public function getList(int $iLimit = 0, int $iOffset = 0)
    {
        $iLimit--;
        $iStart = $iOffset;
        $iEnd = $iStart + $iLimit;

        $aReturn = Redis::lRange($this->sTable, $iStart, $iEnd);

        $aList = array();
        foreach ($aReturn as $mReturn) {
            $aList[] = $this->processOutput($mReturn);
        }
        return $aList;
    }

    /**
     * function to get all list in redis
     *
     * @return array
     */
    public function getAllList()
    {
        $aReturn = $this->getList($this->getListLength());
        return $aReturn;
    }

    /**
     * method to get list in redis in descending order
     *
     * @param int $iRows
     * @param int $iPage
     * @param int $iTotalCount
     * @return array
     */
    public function getListDesc($iRows = 10, $iPage = 1, $iTotalCount = 0)
    {
        $iStart = $iTotalCount - ($iPage * $iRows);
        $iEnd = $iStart + ($iRows - 1);
        $iStart = ($iStart < 0) ? 0 : $iStart;

        $aResponse = Redis::lRange($this->sTable, $iStart, $iEnd);

        $aList = array();
        foreach ($aResponse as $mRow) {
            $aList[] = $this->processOutput($mRow);
        }

        $aList = is_array($aList) ? array_reverse($aList) : $aList;

        return $aList;
    }

    /**
     * method to get list in redis in descending order like getList
     *
     * @param     $iLimit
     * @param int $iOffset
     * @return array
     */
    public function getListDescV2($iLimit, $iOffset = 0)
    {
        $iOffset++;
        $iEnd = $this->getListLength() - $iOffset;
        $iStart = $iEnd - $iLimit + 1 < 0 ? 0 : $iEnd - $iLimit + 1;

        $aResponse = Redis::lRange($this->sTable, $iStart, $iEnd);

        $aList = array();
        foreach ($aResponse as $mRow) {
            $aList[] = $this->processOutput($mRow);
        }

        $aList = is_array($aList) ? array_reverse($aList) : $aList;

        return $aList;
    }

    /**
     * method to get data of an element with specific index in redis
     *
     * @param $iIndex
     * @return mixed
     */
    public function getIndexData($iIndex)
    {
        $aResponse = Redis::lIndex($this->sTable, $iIndex);
        return $this->processOutput($aResponse);
    }

    /**
     * method to get index of an element with filter in redis
     *
     * @param $aFilter
     * @return bool|int
     */
    public function getIndex($aFilter)
    {
        $mData = $this->getFilteredList($aFilter);
        if ($mData === false || is_array($mData) !== true || count($mData) === 0) {
            return false;
        }
        $aIndex = array_keys($mData);
        return $aIndex[0];
    }

    /**
     * method to get elements in a list of with specific filter
     *
     * @param     $aFilters
     * @param int $iLimit
     * @param int $iOffset
     * @return array
     */
    public function getFilteredList($aFilters, $iLimit = 0, $iOffset = 0)
    {
        $iStart = $iAdded = 0;
        $iEnd = $this->getListLength();
        $aResponse = Redis::lRange($this->sTable, $iStart, $iEnd);
        $iLimit = !$iLimit ? $iEnd : $iLimit + $iOffset;
        $aList = array();
        for ($iKey = 0; $iKey < $iEnd; $iKey++) {
            $aResponse[$iKey] = $this->processOutput($aResponse[$iKey]);
            if (array_key_exists('seq', $aFilters) === true && (int)$aFilters['seq'] === (int)$aResponse[$iKey]['seq']) {
                $aList[$iKey] = $aResponse[$iKey];
            } else if (is_array($aResponse[$iKey]) === true) {
                ++$iAdded;
                if (($iAdded <= $iLimit) && $aFilters === array_intersect_assoc($aResponse[$iKey], $aFilters)) {
                    $aList[$iKey] = $aResponse[$iKey];
                }

            }
        }
        return $aList;
    }

    /**
     * method to delete elements of a list in redis with specific filter
     *
     * @param $aFilters
     * @return array
     */
    public function removeByFilter($aFilters)
    {
        $aList = $this->getFilteredList($aFilters);
        $aResult = array();
        if (count($aList) > 0) {
            foreach ($aList as $aItem) {
                $aResult[] = $this->removeFromList($aItem);
            }
        }
        return $aResult;
    }

    /**
     * method to delete an element in a list with a specific index
     *
     * @param     $mValue
     * @param int $iCount
     * @return int
     */
    public function removeFromList($mValue, $iCount = 0)
    {
        return Redis::lRem($this->sTable, $iCount, $this->processInput($mValue));
    }

    /**
     * method to update a list in redis
     *
     * @param $iIndex
     * @param $aData
     * @return bool
     */
    public function update($iIndex, $aData)
    {
        return Redis::lSet($this->sTable, $iIndex, $this->processInput($aData));
    }

    /**
     * method to get last element inserted in a list
     *
     * @return mixed
     */
    public function getLastRow()
    {
        return $this->getIndexData(-1);
    }

    /**
     * method to get first element inserted in a list
     *
     * @return mixed
     */
    public function getFirstRow()
    {
        return $this->getIndexData(0);
    }

    /**
     * method to remove the first element inserted in a list and return it
     *
     * @return mixed
     */
    public function firstPop()
    {
        $sReturn = Redis::lPop($this->sTable);
        return $this->processOutput($sReturn);
    }

    /**
     * method to remove the last element inserted in a list and return it
     *
     * @return mixed
     */
    public function lastPop()
    {
        $sReturn = Redis::rPop($this->sTable);
        return $this->processOutput($sReturn);
    }

    /**
     * method to delete a list in redis
     *
     * @return mixed
     */
    public function delData()
    {
        return Redis::del($this->sTable);
    }

    /**
     * method to set expiration of the key values
     *
     * @param $iTtl
     * @return bool
     */
    public function setExpire($iTtl)
    {
        return Redis::expire($this->sTable, $iTtl);
    }

    /**
     * method to set expiration of the key values
     *
     * @return int
     */
    public function getExpire()
    {
        return Redis::ttl($this->sTable);
    }

    /**
     * method to get available keys saved in redis
     *
     * @param string $sKeys
     * @return mixed
     */
    public function getKeys($sKeys = '*')
    {
        return Redis::keys($sKeys);
    }

    /**
     * method to unserialize return values from redis
     *
     * @param string $sReturn
     * @return mixed
     */
    private function processOutput(string $sReturn)
    {
        return unserialize($sReturn);
    }

    /**
     * method to serialize input values to redis
     *
     * @param array $aInput
     * @return string
     */
    private function processInput(array $aInput)
    {
        return serialize($aInput);
    }
}
