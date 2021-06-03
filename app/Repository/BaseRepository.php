<?php

namespace App\Repository;

use App\Library\RedisLibrary;
use Illuminate\Support\Facades\DB;

/**
 * Class BaseRepository
 * @package App\Repository
 *
 * @author joven <joven@cafe24corp.com>
 * @version 1.0
 * @date 11/27/2020 10:26 AM
 */
class BaseRepository
{
    /**
     * Initialize Redis
     * @var $oRedis
     */
    protected $oRedis;

    /**
     * SaveAccessTokenRepository constructor.
     * @param RedisLibrary $oRedisLibrary
     */
    public function __construct(RedisLibrary $oRedisLibrary)
    {
        $this->oRedis = $oRedisLibrary;
    }

    /**
     * Initialize Redis
     * @param $sTable
     * @return bool
     */
    protected function initializeRedis($sTable)
    {
        $this->oRedis->initializeTable($sTable);
        return true;
    }

    /**
     * Use Table Base Repository
     * @param $sData
     * @return \Illuminate\Database\Query\Builder
     */
    protected function useTable(string $sData)
    {
        return DB::table($sData);
    }

    /**
     * Table Join
     * Function used for table joins
     * @param $sTable1
     * @param $sTable2
     * @param $sId
     * @return array
     */
    protected  function joinTable(string $sTable1, string $sTable2, string $sId)
    {
        return [
            $sTable1 . '.' . $sId,
            $sTable2 . '.' . $sId,
        ];
    }
}
