<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}
use Bitrix\Main\Localization\Loc;
?>

<div class="d-flex w-100 w-xl-auto overflow-auto overflow-xl-visible scrollbar-hidden border-top border-top-xl-0 order-1 order-xl-0 b2bcabinet-navbar-2__topmenu">

    <? if (!empty($arResult)): ?>
        <ul class="navbar-nav navbar-nav-underline flex-row text-nowrap mx-auto">
            <?

            $previousLevel = 0;
            foreach ($arResult as $key => $arItem):
                if ($key === 'PERSONAL_MANAGER_ID' || $key === 'CATALOG_MENU' || $key === 'OPEN_CATALOG') {
                    continue;
                }
                ?>
                <? if ($arItem["DEPTH_LEVEL"] === 1): ?>
                <? if (($arItem["IS_PARENT"] || $arItem["PARAMS"]["IS_PARENT"]) && $arItem["PARAMS"]["IS_CATALOG"] != "Y"): ?>
                    <li class="nav-item dropdown nav-item-dropdown-xl">
                    <span
                            class="navbar-nav-link dropdown-toggle <?= ($arItem["SELECTED"] || $arItem["CHILD_SELECTED"] === true) ? 'active' : '' ?>"
                            data-hover="dropdown"
                    >
                        <i class="<?= $arItem["PARAMS"]["ICON_CLASS"] ?: "" ?> mr-2"></i>
                        <?= $arItem["TEXT"] ?>
                    </span>

                    <div class="dropdown-menu dropdown-menu-right dropdown-scrollable-xl">
                <? elseif ($arItem["PARAMS"]["IS_CATALOG"] == "Y" && $arResult["CATALOG_MENU"]): ?>
                    <?
                    $TOP_DEPTH = 0;
                    $CURRENT_DEPTH = $TOP_DEPTH;
                    $CURRENT_OPEN = $arResult["OPEN_CATALOG"] == "Y" ? true : false;
                    ?>
                    <li class="nav-item dropdown nav-item-dropdown-xl">
                        <a
                                href="<?=$arItem["LINK"]?>"
                                class="navbar-nav-link dropdown-toggle <?= $arItem["SELECTED"] ? 'active' : '' ?>"
                                data-hover="dropdown"
                        >
                            <i class="<?= $arItem["PARAMS"]["ICON_CLASS"] ?: "" ?> mr-2"></i>
                            <?= $arItem["TEXT"] ?>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right dropdown-scrollable-xl">
                            <?
                            foreach ($arResult["CATALOG_MENU"] as $section) {
                                if ($CURRENT_DEPTH < $section[3]["DEPTH_LEVEL"]) {
                                    echo '<div class="dropdown-submenu">';
                                } elseif ($CURRENT_DEPTH == $section[3]["DEPTH_LEVEL"]) {
                                    echo "</div>";
                                    echo "</div>";
                                    echo '<div class="dropdown-submenu">';

                                } else {
                                    while ($CURRENT_DEPTH > $section[3]["DEPTH_LEVEL"]) {
                                        echo "</div></div>";
                                        $CURRENT_DEPTH--;
                                    }
                                    echo '</div></div>' . '<div class="dropdown-submenu">';
                                }

                                $submenu = $section[3]["IS_PARENT"] === false ? ' dropdown-last-item' : '';
                                $link = '<a class="dropdown-item '. $section['ACTIVE'] . $submenu . '" href="' . $section[1] . '"><span>' . $section[0] . '</span></a>';
                                if ($section[3]["IS_PARENT"]) {
                                    echo $link . '<div class="dropdown-menu">';
                                } else {
                                    echo $link . '<div>';
                                }
                                $CURRENT_DEPTH = $section[3]["DEPTH_LEVEL"];
                            }

                            while ($CURRENT_DEPTH > $TOP_DEPTH) {
                                echo "</div>";
                                echo "</div>";
                                $CURRENT_DEPTH--;
                            }
                            ?>
                        </div>
                    </li>
                <? else: ?>
                    <li class="nav-item">
                        <a href="<?= $arItem["LINK"] ?>"
                           class="navbar-nav-link <?= $arItem["SELECTED"] ? 'active' : '' ?>">
                            <i class="<?= $arItem["PARAMS"]["ICON_CLASS"] ?: "" ?> mr-2"></i>
                            <?= $arItem["TEXT"] ?>
                        </a>
                    </li>
                <? endif; ?>
            <? else: ?>
                <?if ($arItem["PERMISSION"] > "D"):?>
                    <a href="<?= $arItem["LINK"] ?>" class="dropdown-item <?= $arItem["SELECTED"] ? 'active' : '' ?>"><i
                                class="<?= $arItem["PARAMS"]["ICON_CLASS"] ?>"></i>
                        <?= $arItem["TEXT"] ?>
                    </a>
                <?else:?>
                    <span class="dropdown-item disabled"><i
                                class="<?= $arItem["PARAMS"]["ICON_CLASS"] ?>"></i>
                        <?= $arItem["TEXT"] ?>
                    </span>
                <?endif;?>
            <? endif ?>
                <?
                $previousLevel = $arItem["DEPTH_LEVEL"];
                if (isset($arResult[$key + 1]) && $arResult[$key + 1]["DEPTH_LEVEL"] && $previousLevel > $arResult[$key++]["DEPTH_LEVEL"]) {
                    echo "</div> </li>";
                }
                ?>
            <? endforeach ?>
        </ul>
    <? endif;
    ?>

</div>
<script>
    BX.message({
        ALL_COTEGORIES: '<?=Loc::getMessage('ALL_COTEGORIES')?>',
    });
</script>