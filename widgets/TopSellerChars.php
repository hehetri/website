<?php

/**
 * Created by PhpStorm.
 * User: 
 * Date: 29.08.2016
 * Time: 2:52
 */
function cmpTopSellerChars($b, $a)
{
    return strnatcmp($a["time"], $b["time"]);
}
class TopSellerChars extends Widget
{
    private $SelectDB;
    public function __construct($SelectDB)
    {
        $this->SelectDB = $SelectDB;
        parent::__construct(__CLASS__);
        App::$Smarty->assign('WidgetDb', $this->SelectDB);
    }

    public function Main()
    {
        if($this->CheckCacheDb(__CLASS__, $this->SelectDB))
            return $this->GetCacheDb(__CLASS__, $this->SelectDB);
        
        $ParamArr = array();
        $ParamArr[] = "Name";
        $ParamArr[] = App::$DBs[$this->SelectDB]->column_names['Resets'];

        $ParamArr[] = "cLevel";
        $ParamArr[] = App::$DBs[$this->SelectDB]->column_names['GrandResets'];

        $ParamArr[] = 'nickname';
        $ParamArr[] = 'time';
        $ResultArr = App::$DBs[$this->SelectDB]
            ->select($ParamArr)
            ->from('ETW_MARKET_CHARACTER')
            ->join('Character','Character.Name = ETW_MARKET_CHARACTER.nickname COLLATE DATABASE_DEFAULT', 'left', false)
            ->get();


        $ResultArr = $ResultArr->result_array();
        for ($i = 0; $i < count($ResultArr); $i++) {
            $ResultArr[$i]['time'] = strtotime($ResultArr[$i]['time']);
        }

        usort($ResultArr, "cmpTopSellerChars");
        $content = $this->render("topsellerchars.tpl", $ResultArr);
        $this->WriteCacheDb(__CLASS__,$this->SelectDB, $content);
        return $content;
    }
}