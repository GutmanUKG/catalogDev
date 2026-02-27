<?
foreach($arResult['SEARCH']as &$arTag)
{
    foreach($arResult['TAGS_CHAIN'] as $arChainTag)
    {
        if($arTag['NAME']===$arChainTag['TAG_NAME'])
        {
            $arTag['TAG_WITHOUT']=$arChainTag['TAG_WITHOUT'];
            $arTag['IN_CHAIN']=true;
            break;
        }
        else
        {
            $arTag['IN_CHAIN']=false; 
        }

    }
}
