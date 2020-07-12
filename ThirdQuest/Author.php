<?php


abstract class Author
{
    private $id;
    private $documentList=[];

    public abstract function addDocumnet(Document $doc);

    /**
     * @return array
     */
    public function getDocumentList(): array
    {
        return $this->documentList;
    }




}