<?php

require_once("Spyc.php");


class Programm

{
 public $id;
    private $content;
    public $title;
    public $author;
public $date;
    public $intro;
    public $text;

 function __construct($path)

    {

 $temp = new Spyc();

        $this->content = $temp->loadFile($path);


        $this->id = $this->content["id"];

        $this->title = $this->content["title"];

        $this->author = $this->content["author"];
        $this->date = $this->content["date"];
        $this->intro = $this->content["intro"];
        $this->text = $this->content["text"];

    }

}


