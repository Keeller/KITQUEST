<?php


 abstract class Document
{

    private $id;
    private $author;

     /**
      * @return mixed
      */
     public function getAuthor()
     {
         return $this->author;
     }

     /**
      * @param mixed $author
      */
     public function setAuthor($author)
     {
         $this->author = $author;
     }



}