<?php

/**
 * Created by PhpStorm.
 * User: 
 * Date: 29.08.2016
 * Time: 2:52
 */
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
        
        $ResultArr = array();


        $ResultArr = App::$DBs[$this->SelectDB]
            ->select()
            ->order_by('time','DESC')
            ->from('ETW_MARKET_CHARACTER')
            ->get();


        $ResultArr = $ResultArr->result_array();

        for ($i = 0; $i < count($ResultArr); $i++) {
  
        }
        $content = $this->render("TopSellerChars.tpl", $ResultArr);
        $this->WriteCacheDb(__CLASS__,$this->SelectDB, $content);
        return $content;
    }
}