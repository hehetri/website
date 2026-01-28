<?php
class ServerInfo extends Widget
{
    private $SelectDB;
    public function __construct($SelectDB)
    {
        $this->SelectDB = $SelectDB;
        parent::__construct(__CLASS__);
        App::$Smarty->assign('WidgetDb', $this->SelectDB);
    }

    /**
     * @return array
     */
    public function Main()
    {
        if($this->CheckCacheDb(__CLASS__, $this->SelectDB))
            return $this->GetCacheDb(__CLASS__, $this->SelectDB);
        $ResultArr = array();


        $ResultArr['Online'] = App::$DBs[$this->SelectDB]
            ->select('memb___id')
            ->from('MEMB_STAT AS ms')
            ->where('ConnectStat', '1')
            ->count_all_results();
        //->count_all_results();*/
        // = count($acconline->result_array());
        App::$FullServerOnline += $ResultArr['Online'];


        if ($this->Config['TopPlayer']) {
            $topplayer = App::$DBs[$this->SelectDB]
                ->select('Name')
                ->where('CtlCode != ', '1')
                ->where('CtlCode != ', '1')
                ->order_by(App::$DBs[$_SESSION[$this->SelectDB]]->column_names['GrandResets'],'DESC')
                ->order_by(App::$DBs[$_SESSION[$this->SelectDB]]->column_names['Resets'],'DESC')
                ->order_by('cLevel','DESC')
                ->order_by('Name','ASC')
                ->limit(1)
                ->from('Character')
                ->get();
            $ResultArr['TopPlayer'] = $topplayer->result_array();
            $ResultArr['TopPlayer'] = $ResultArr['TopPlayer'][0]['Name'];
           
        }
        if ($this->Config['TopGuild']) {

            $topguild = App::$DBs[$this->SelectDB]
                ->select('Guild.G_Name, COUNT(GuildMember.Name) AS CountMember')
                ->from('Guild')
                ->join('GuildMember', 'GuildMember.G_Name = Guild.G_Name')
                ->group_by('Guild.G_Name')
                ->order_by('CountMember', 'DESC')
                ->limit(1)
                ->get();
            $topguild = $topguild->result_array();
            if ($topguild[0]['G_Name'] == null)
                $ResultArr['TopGuild'] = "Гилдия не найдена";
            else
                $ResultArr['TopGuild'] = $topguild[0]['G_Name'];

        }

        if ($this->Config['Castle']) {

            $castle = App::$DBs[$this->SelectDB]
                ->select('OWNER_GUILD')
                ->from('MuCastle_DATA')
                ->limit(1)
                ->get();
            $castle = $castle->result_array();
            if ($castle[0]['OWNER_GUILD'] == '' || $castle[0]['OWNER_GUILD'] == ' ')
                $ResultArr['Castle'] = "Не Захвачен";
            else
                $ResultArr['Castle'] = $castle[0]['OWNER_GUILD'];
        }
        if ($this->Config['Accounts']) {
            $ResultArr['Accounts'] = App::$DBs[$this->SelectDB]->count_all('MEMB_INFO');
        }
        if ($this->Config['Players']) {
            $ResultArr['Players'] = App::$DBs[$this->SelectDB]->count_all('Character');
        }

        $ResultArr['Guilds'] = App::$DBs[$this->SelectDB]->count_all('Guild');

        $ResultArr['Crywolf'] = App::$DBs[$this->SelectDB]->select()->from(App::$DBs[$this->SelectDB]->column_names['CrywolfTable'])->get()->result_array();
        
        $ResultArr['Crywolf'] = $ResultArr['Crywolf'][0]['CRYWOLF_STATE'];

        if ($ResultArr['Crywolf'] == '' || empty($ResultArr['Crywolf'])) {
            $ResultArr['Crywolf'] = 'Не защищена';
        }
        
        $content = $this->render("ServerInfo.tpl", $ResultArr);
        $this->WriteCacheDb(__CLASS__,$this->SelectDB, $content);
        return $content;
    }

}