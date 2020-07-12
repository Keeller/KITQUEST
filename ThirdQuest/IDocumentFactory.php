<?php
/**
 *Паттерн фабрика так как могут быть различные типы документов
 */

interface IDocumentFactory
{
    public static function create(array $paramList):Document;

}