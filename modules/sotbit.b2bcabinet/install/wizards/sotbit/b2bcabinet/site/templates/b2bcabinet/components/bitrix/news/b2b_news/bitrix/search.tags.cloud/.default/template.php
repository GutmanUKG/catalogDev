<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
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
$this->setFrameMode(true); ?>

<? if ($arParams["SHOW_CHAIN"] != "N" && !empty($arResult["TAGS_CHAIN"])): ?>
    <noindex>
        <div class="search-tags-chain" <?= $arParams["WIDTH"] ?>><?
            foreach ($arResult["TAGS_CHAIN"] as $tags):
                ?><a href="<?= $tags["TAG_PATH"] ?>" rel="nofollow"><?= $tags["TAG_NAME"] ?></a> <?
                ?>[<a href="<?= $tags["TAG_WITHOUT"] ?>" class="search-tags-link" rel="nofollow">x</a>]  <?
            endforeach; ?>
        </div>
    </noindex>
<? endif; ?>

<? if (is_array($arResult["SEARCH"]) && !empty($arResult["SEARCH"])): ?>
    <div class="card">
        <div class="card-header bg-transparent header-elements-inline">
            <span class="text-uppercase font-size-sm font-weight-semibold"><?= GetMessage("TAGS_NAME") ?></span>
            <div class="header-elements">
                <div class="list-icons">
                    <span class="list-icons-item" data-action="collapse"></span>
                </div>
            </div>
        </div>

        <div class="card-body pb-2">
            <ul class="list-inline list-inline-condensed mb-0">
                <? foreach ($arResult["SEARCH"] as $key => $res): ?>
                    <li class="list-inline-item">
                        <a href="<?= $res["URL"] ?>">
                            <span class="badge badge-light badge-striped badge-striped-left border-left-b2b mb-2">
                                <?= $res["NAME"] ?>
                            </span>
                        </a>
                    </li>
                <? endforeach; ?>
            </ul>
        </div>
    </div>
<? endif; ?>
