<?php

namespace App\Library;
/**
 * ERP에 사용되는 페이징 데이터를 관리한다.
 *
 * @package program/lib
 * @author jsyang < jsyang@cafe24corp.com >
 * @since 2014. 2. 18.
 * @version 1.0
 */
class libPaging
{
    const BLOCK_COUNT = 10;
    /**
     * dataHtml 함수와 동일한데 $sParams 이 필요 없음
     *
     * @static
     * @param integer $iPage            current page
     * @param integer $iTotalCount      total count
     * @param integer $iLimit           limit
     * @return string
     */
    public static function getHtml($iPage, $iTotalCount, $iLimit, $aParams)
    {
        $aPaging = self::initPage($iPage, $iTotalCount, $iLimit);
        return self::buildHtml($aPaging, self::getParamData($aParams), $iLimit, $aParams);
    }

    /**
     * 페이징을 처리를 위한 페이징 데이터를 얻어옵니다.
     *
     * @static
     * @param integer $iPage
     * @param integer $iTotalCount
     * @param integer $iLimit
     * @param integer $iBlockSize
     * @return array
     */
    private static function initPage($iPage=1, $iTotalCount=0, $iLimit=10, $iBlockSize=10)
    {
        $iCurrPage  = $iPage;

        if (intval($iLimit) === 0) {
            $iTotalPage = 1;
        } else {
            $iTotalPage = ceil((int)$iTotalCount / (int)$iLimit );
        }

        $iFirst = 1;
        $iLast  = $iTotalPage;

        $iEnd   = (int)(ceil($iCurrPage / $iBlockSize) * $iBlockSize );
        $iEnd   = ( $iEnd > $iTotalPage ) ? $iTotalPage : $iEnd;

        $iStart = (int)($iEnd - $iBlockSize) + 1;
        $iStart = ( $iStart <= 0 ) ? 1 : $iStart;

        $iNext  = $iStart + $iBlockSize;
        $iNext  = ( $iNext > $iTotalPage ) ? $iTotalPage : $iNext;

        $iPrev  = $iStart - 1;
        $iPrev  = ( $iPrev <= 0 ) ? 1 : $iPrev;

        $aData = array();

        for ($i = $iStart; $i <= $iEnd; ++$i) {
            $aData['num'][]  = $i;
        }
        $aData['first'] = $iFirst;
        $aData['last']  = $iLast;
        $aData['next']  = $iNext;
        $aData['prev']  = $iPrev;
        $aData['page']  = $iCurrPage;
        $aData['total_page'] = $iTotalPage;
        $aData['total_count'] = $iTotalCount;

        return $aData;
    }



    /**
     * 검색 파라미터 값얻기
     * @param array $aSearch 검색조건 파라미터
     * @return string
     */
    private static function buildParam($aSearch)
    {
        $aFilterKey = array('page', 'offset');

        $sHtml = "";
        foreach ($aSearch as $k => $v) {
            if (empty($v) === false && in_array($k, $aFilterKey)  === false ) {
                $sHtml .= "&" . $k ."=" .$v;
            }
        }
        return $sHtml;
    }

    /**
     * 검색 데이터 param
     * @param array $aSearchData  검색정보
     * @return string             get방식 검색데이터
     */
    private static function getParamData($aSearchData)
    {
        $sParam = '';

        foreach ($aSearchData as $sKey => $mValue) {
            if ($sKey == 'page') continue;
            if ($sKey === 'limit') continue;

            if (is_array($mValue) === true) {
                foreach ($mValue as $vd) {
                    $sParam .= "&".$sKey."[]=".$vd;
                }
            } else if ($mValue !== null) {
                $sParam .= "&".$sKey."=".$mValue;
            }
        }
        return $sParam;
    }

    /**
     * 페이지 HTML 얻어오기
     *
     * @static
     * @param string $aPaging
     * @param string $sParams 추가미터
     * @return string
     */
    private static function buildHtml($aPaging, $sParams='', $iLimit, $aParams)
    {
        $sHtml = "";
        $iCount = count($aPaging['num']);
        if ( $iCount <= 0 ) return;
        $sUri = '?page=__page__&' . $sParams;
        $sHtml .= '<div class="mPaginate">';

        if ($aPaging['page'] > 1) {
            $sHtml .= '<li><a class="prev" href="' . str_replace('__page__', $aPaging['prev'], $sUri) . '&limit=' . $iLimit . '" ><span> <<</span></a></li>';
        }
        $sHtml .= "<ol>";
        for ($i = 0; $i < $iCount; $i++) {
            $cls = ( $i === 0 ) ? "fst" : "";
            if ( intval($aPaging['page']) === intval($aPaging['num'][$i]) ) {
                $sHtml .= '<li class="' . $cls . '"><strong>' . trim($aPaging['num'][$i]) .'</strong></li>';
            } else {
                $aHref = str_replace('__page__', $aPaging['num'][$i], $sUri) . '&limit=' . $iLimit;
                $sHtml .= '<li><a class="'.$cls.'" href="' . $aHref . '" title="' . trim($aPaging['num'][$i]) .'">' . trim($aPaging['num'][$i]) . '</a></li>';
            }
        }
        $sHtml .= "</ol>";
        if ($aPaging['page'] < $aPaging['total_page']) {
            $sHtml .= '<li><a class="next" href="' . str_replace('__page__', $aPaging['next'], $sUri) . '&limit=' . $iLimit . '" ><span> >> </span></a></li>';
        }
        $sHtml .= '</div>';
        return $sHtml;
    }
}
