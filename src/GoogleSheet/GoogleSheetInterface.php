<?php

namespace Letov\Flycatcher\GoogleSheet;

interface GoogleSheetInterface
{
    function __construct(string $sheetsJsonFilePath, array $scope, string $spreadsheetId);
    function getListData(string $listName): array;
    function setListData(string $listName, array $data);
}