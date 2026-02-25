<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */
$this->setFrameMode(true);
?>


<div class="serv_list">
    <?foreach ($arResult['ITEMS'] as $arItem):?>
        <div class="serv_item">
            <img src="<?=$arItem['PREVIEW_PICTURE']['SRC']?>" alt="">
            <div class="serv_item--list">
               <div class="item">
                   <?
                   foreach ($arItem['PROPERTIES']['SERV_LIST']['VALUE'] as $servItemId):
                       // Получаем связанный элемент по его ID
                       $servItem = CIBlockElement::GetByID($servItemId)->GetNext();
                       if ($servItem):
                           // Выводим свойства элемента
                           echo "<div class='item'>";
                           echo "Название: " . $servItem['NAME'] . "<br>";

                           // Получаем и выводим свойства
                           $propertiesToDisplay = array("CITY", "COMPANY", "ADRESS", "PHONE", "E_MAIL", "TEXT_LINK", "URL_LINK");
                           foreach ($propertiesToDisplay as $propertyCode) {
                               $rsProp = CIBlockElement::GetProperty(
                                   $servItem['IBLOCK_ID'],
                                   $servItem['ID'],
                                   "sort",
                                   "asc",
                                   array("CODE" => $propertyCode)
                               );
                               if ($arProp = $rsProp->Fetch()) {
                                   echo $arProp['NAME'] . ": " . $arProp['VALUE'] . "<br>";
                               }
                           }

                           echo "</div>";
                       endif;
                   endforeach;
                   ?>
               </div>
            </div>
        </div>
    <?endforeach;?>
</div>
