<?php

/**
 * Created by PhpStorm.
 * User: Андрей
 * Date: 18.04.2016
 * Time: 2:52
 */
class Strongest extends Widget
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

        $ParamArr = array();
        $ParamArr[] = "Name";
        $ParamArr[] = "HideInfoTime";
        if ($this->Config['Reset'])
            $ParamArr[] = App::$DBs[$this->SelectDB]->column_names['Resets'];
        if ($this->Config['Level'])
            $ParamArr[] = "cLevel";
        if ($this->Config['GrandReset'])
            $ParamArr[] = App::$DBs[$this->SelectDB]->column_names['GrandResets'];

        $ResultArr = App::$DBs[$this->SelectDB]
            ->select($ParamArr)
            ->where('CtlCode != ','1')
            ->where('CtlCode != ','17')
            ->order_by(App::$DBs[$this->SelectDB]->column_names['GrandResets'], 'DESC')
            ->order_by(App::$DBs[$this->SelectDB]->column_names['Resets'],'DESC')
            ->order_by('cLevel','DESC')
            ->order_by('Name','DESC')
            ->limit($this->Config['CountPlayer'])
            ->from('Character')
            ->get();


        $ResultArr = $ResultArr->result_array();

        for ($i = 0; $i < count($ResultArr); $i++) {
          if (App::$DBs[$this->SelectDB]->column_names['Resets'] != 'Resets')
              $ResultArr[$i]['Resets'] = $ResultArr[$i][App::$DBs[$this->SelectDB]->column_names['Resets']];
          
          if (App::$DBs[$this->SelectDB]->column_names['GrandResets'] != 'GrandResets')
              $ResultArr[$i]['GrandResets'] = $ResultArr[$i][App::$DBs[$this->SelectDB]->column_names['GrandResets']];
          
          if ($ResultArr[$i]['HideInfoTime'] > time())
              $ResultArr[$i]['Hide'] = true;
            else
                $ResultArr[$i]['Hide'] = false;

        }
        $content = $this->render("Strongest.tpl", $ResultArr);
        $this->WriteCacheDb(__CLASS__,$this->SelectDB, $content);
        return $content;
    }
}