<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}
global $USER;

?>

<? if (!empty($arResult)): ?>
    <ul class="nav nav-sidebar nav-b2bcabinet" data-nav-type="accordion">
        <?
        $previousLevel = 0;
        foreach ($arResult as $key => $arItem):
            if (!empty($arItem["PERMISSION"]) && $arItem["PERMISSION"] <= "D") {
                continue;
            }
            ?>
            <?
            if ($key === 'PERSONAL_MANAGER_ID' || $key === 'CATALOG_MENU' || $key === 'OPEN_CATALOG') {
                continue;
            }
            ?>
            <? if ($arItem["IS_PARENT"] || $arItem["PARAMS"]["IS_PARENT"]):?>

                <li class="nav-item-header">
                    <div class="text-uppercase text-nowrap fs-sm lh-lg"><?= $arItem["TEXT"] ?></div>
                    <i class="ph-dots-three sidebar-resize-show"></i>
                </li>
            <? elseif ($arItem["PARAMS"]["IS_CATALOG"] == "Y" && $arResult["CATALOG_MENU"]):?>
                <?
                $TOP_DEPTH = 0;
                $CURRENT_DEPTH = $TOP_DEPTH;
                $CURRENT_OPEN = $arResult["OPEN_CATALOG"] == "Y" ? true : false;

                if (str_contains($APPLICATION->GetCurPage(), $arItem["LINK"]) != false) {
                    $arItem["SELECTED"] = "Y";
                    $CURRENT_OPEN = "Y";
                }
                ?>
                <li class="nav-item nav-item-submenu catalog-wrapper <?=$CURRENT_OPEN == "Y" ? "nav-item-open" : ""?>">
                    <a href="<?= $arItem["LINK"] ?>"
                    class="nav-link text-nowrap<? if ($arItem["SELECTED"]):?> active<? endif ?>"
                    title="<?= $arItem["TEXT"] ?>">
                        <i class="<?= $arItem['PARAMS']['ICON_CLASS'] ?>"></i>
                        <span><?= $arItem["TEXT"] ?></span>
                    </a>
                        <?
                            foreach ($arResult["CATALOG_MENU"] as $section) {
                                $selected = '';
                                if (str_contains($APPLICATION->GetCurPage(), $section[1]) != false) {
                                    $CURRENT_OPEN = "Y";
                                    $section["PARAMS"]["OPEN"] = "Y";
                                }

                                $subOpen = 'collapse';
                                if ($CURRENT_OPEN == 'Y') {
                                    $subOpen .= ' show';
                                }

                                if ($CURRENT_DEPTH < $section[3]["DEPTH_LEVEL"]) {
                                    echo '<ul class="nav-group-sub '.$subOpen.'">';
                                } elseif($CURRENT_DEPTH == $section[3]["DEPTH_LEVEL"]){
                                    echo "</li>";
                                } else {
                                    while($CURRENT_DEPTH > $section[3]["DEPTH_LEVEL"]) {
                                        echo "</li></ul>";
                                        $CURRENT_DEPTH--;
                                    }
                                    echo "</li>";
                                }

                                $navOpen = $section["PARAMS"]["OPEN"] == "Y" ? ' nav-item-open' : '';
                                $submenu = $section[3]["IS_PARENT"] == true ? ' nav-item-submenu' : '';

                                if ($section[1] == $APPLICATION->GetCurPage()) {
                                    $selected = " active";
                                }
                                if (str_contains($APPLICATION->GetCurPage(), $section[1]) != false) {
                                    if ($CURRENT_DEPTH == $section[3]["DEPTH_LEVEL"]) {
                                        $selected = " active";
                                    }
                                }
                                if ($USER->IsAdmin()){
                                    $btn = '<button class="favorite_btn"></button>';
                                    $link = '<a class="nav-link' . $selected . '" href="'. $section[1] .'">'.  $btn .'<span>'.$section[0].'</span></a>';
                                }else{
                                    $link = '<a class="nav-link' . $selected . '" href="'. $section[1] .'">' .'<span>'.$section[0].'</span></a>';
                                }
//                                $btn = '<button class="favorite_btn"></button>';
//                                $link = '<a class="nav-link' . $selected . '" href="'. $section[1] .'">'.  $btn .'<span>'.$section[0].'</span></a>';


                                echo '<li class="nav-item ' . $submenu . $navOpen .'" >'  . $link;
                                $CURRENT_DEPTH = $section[3]["DEPTH_LEVEL"];
                                $CURRENT_OPEN = $section["PARAMS"]["OPEN"] == "Y";
                            }

                        while($CURRENT_DEPTH > $TOP_DEPTH)
                        {
                            echo "</li>";
                            echo "</ul>";
                            $CURRENT_DEPTH--;
                        }
                    ?>
                </li>
                <!--Избранное меню-->
                <? if ($USER->IsAdmin()):?>
                <li class="nav-item nav-item-submenu favorite-wrapper <?if(!empty($sectionData)):?> active <?endif;?>">
                    <a href="#" title="Избранный каталог" class="nav-link text-nowrap" id="fav_toggler">
                        <i class="ph-folder-star"></i>
                        <span><?=GetMessage('FAVORITE_TITLE')?></span>
                    </a>

                    <ul id="ajax_menu" class="nav-group-sub collapse">


                    </ul>
                    <button id="close_ajax_menu" title="Закрыть"></button>
                </li>

                <?endif;?>
                <!--Избранное меню-->
                    <? else:?>

                <? if ($arItem["PERMISSION"] > "D"):?>
                    <li class="nav-item">

                        <a href="<?= $arItem["LINK"] ?>"
                        class="nav-link text-nowrap<? if ($arItem["SELECTED"]):?> active<? endif ?>"
                        title="<?= $arItem["TEXT"] ?>">
                            <? if (isset($arItem['PARAMS']['ICON_CLASS'])): ?>
                                <i class="<?= $arItem['PARAMS']['ICON_CLASS'] ?>"></i>
                            <? else: ?>
                                <i class="ph ph-list"></i>
                            <? endif; ?>
                            <span><?= $arItem["TEXT"] ?></span>
                        </a>
                    </li>
                <? endif ?>
            <? endif ?>
            <? $previousLevel = $arItem["DEPTH_LEVEL"]; ?>
        <? endforeach ?>
    </ul>
<? endif ?>




