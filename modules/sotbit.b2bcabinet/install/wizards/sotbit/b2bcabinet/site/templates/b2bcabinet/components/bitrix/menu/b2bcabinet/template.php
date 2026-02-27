<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}
?>

<? if (!empty($arResult)): ?>
    <ul class="nav nav-sidebar" data-nav-type="accordion">
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
                <div class="text-uppercase font-size-xs line-height-xs"><?= $arItem["TEXT"] ?></div>
                <i class="icon-menu" title="Forms"></i>
            </li>
        <? elseif ($arItem["PARAMS"]["IS_CATALOG"] == "Y" && $arResult["CATALOG_MENU"]):?>
            <?
                $TOP_DEPTH = 0;
                $CURRENT_DEPTH = $TOP_DEPTH;
                $CURRENT_OPEN = $arResult["OPEN_CATALOG"] == "Y" ? true : false;
                ?>
            <li class="nav-item nav-item-submenu <?=$CURRENT_OPEN == "Y" ? "nav-item-open" : ""?>">
                <a href="<?= $arItem["LINK"] ?>"
                   class="nav-link<? if ($arItem["SELECTED"] == true):?> active<? endif ?>"
                   title="<?= $arItem["TEXT"] ?>">
                    <i class="<?= $arItem['PARAMS']['ICON_CLASS'] ?>"></i>
                    <span><?= $arItem["TEXT"] ?></span>
                </a>
                    <?
                        foreach ($arResult["CATALOG_MENU"] as $section) {

                            $subOpen = '';
                            if ($CURRENT_OPEN == 'Y') {
                                $subOpen = 'style="display: block;"';
                            }

                            if ($CURRENT_DEPTH < $section[3]["DEPTH_LEVEL"]) {
                                echo '<ul class="nav nav-group-sub" '.$subOpen.'>';
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
                            $link = '<a class="nav-link" href="'. $section[1] .'"><span>'.$section[0].'</span></a>';
                            echo '<li class="nav-item' . $submenu . $navOpen .'" >' . $link;
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
        <? else:?>
            <? if ($arItem["PERMISSION"] > "D"):?>
                <li class="nav-item">
                    <a href="<?= $arItem["LINK"] ?>"
                       class="nav-link<? if ($arItem["SELECTED"] == true):?> active<? endif ?>"
                       title="<?= $arItem["TEXT"] ?>">
                        <? if (isset($arItem['PARAMS']['ICON_CLASS'])): ?>
                            <i class="<?= $arItem['PARAMS']['ICON_CLASS'] ?>"></i>
                        <? else: ?>
                            <i class="icon-menu6"></i>
                        <? endif; ?>
                        <span><?= $arItem["TEXT"] ?></span>
                    </a>
                </li>
            <? endif ?>
        <? endif ?>
            <? $previousLevel = $arItem["DEPTH_LEVEL"]; ?>
        <? endforeach ?>
        <?
        if (!empty($arResult['PERSONAL_MANAGER_ID'])) {
            $APPLICATION->IncludeComponent(
                "sotbit:sotbit.personal.manager",
                "b2bcabinet_manager",
                array(
                    "MANAGER_ID" => $arResult['PERSONAL_MANAGER_ID'],
                    "COMPONENT_TEMPLATE" => "b2bcabinet_manager",
                    "SHOW_FIELDS" => array(
                        0 => "NAME",
                        1 => "PERSONAL_PHOTO",
                        2 => "WORK_PHONE",
                    ),
                    "USER_PROPERTY" => array(
                        0 => "UF_ORGANIZATION",
                        1 => "UF_P_MANAGER_ID",
                    )
                ),
                false
            );
        }
        ?>
    </ul>
<? endif ?>

<style>
    .nav-item-submenu .nav-group-sub {
        display: none;
    }

    li.nav-item-open ul {
        display: block;
    }

    ul.nav-group-sub-open {
        display: block !important;
    }
</style>
