<?php
class Forum extends Widget
{

    public function __construct()
    {
        parent::__construct(__CLASS__);
    }
    public function Main()
    {
        if($this->CheckCache(__CLASS__))
            return $this->GetCache(__CLASS__);

        $ResultArr = array();
        if (@fopen($this->Config['Link'], "r")) {
            $forum = simplexml_load_file($this->Config['Link']);

            $i=0;
            foreach ($forum->channel->item as $article) {
                if ($i == $this->Config['CountPost'])
                    break;
                /*foreach ($article as $key => $art_property)
                    $ResultArr[$i][$key] = $art_property;*/
                $ResultArr[$i]['title'] = $article->title;
                $ResultArr[$i]['link'] = $article->link;
                $ResultArr[$i]['date'] = $article->pubDate;
                if (property_exists($article, 'photo' ))
                    $ResultArr[$i]['photo'] = $article->photo;
                if (property_exists($article, 'author' ))
                    $ResultArr[$i]['author'] = $article->author;
                $i++;
            }
        }

        $content = $this->render("Forum.tpl", $ResultArr);
        $this->WriteCache(__CLASS__, $content);
        return $content;
    }

}