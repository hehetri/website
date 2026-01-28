<?php

class TopGuilds extends Widget
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
        $ResultArr = App::$DBs[$this->SelectDB]
            ->select('Guild.G_Name, G_Score, G_Mark, G_Master, G_Union, Number, COUNT(GuildMember.Name) AS CountMember')
            //->order_by('G_Score','DESC')
            //->order_by('Number','DESC')
            //order_by('CountMember','DESC')
            //->limit(10)
            ->from('Guild')
            ->join('GuildMember','GuildMember.G_Name = Guild.G_Name')
            ->group_by('Guild.G_Name, G_Score, G_Mark, G_Master, G_Union, Number')
            ->order_by('CountMember', 'DESC')
            ->limit($this->Config['CountGuild'])
            ->get();
        $ResultArr = $ResultArr->result_array();
        for($i = 0; $i < count($ResultArr); $i++){
            $ResultArr[$i]['G_Mark'] = Guild::Logo($ResultArr[$i]['G_Mark'],$ResultArr[$i]['G_Name'],24);
        }

        $content = $this->render("TopGuilds.tpl", $ResultArr);
        $this->WriteCacheDb(__CLASS__,$this->SelectDB, $content);
        return $content;
    }
}