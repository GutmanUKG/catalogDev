<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

use Bitrix\Main\Localization\Loc;

$this->setFrameMode(true);

if ($arResult['SECTIONS_COUNT'] > 0) {
    $mainId = $this->GetEditAreaId($arResult['SECTION']['ID'] . '_' . $arResult['AREA_ID_ADDITIONAL_SALT']);
    $visual = [
        'ID' => $mainId
    ];
}

$sectionEdit = CIBlock::GetArrayByID($arParams['IBLOCK_ID'], 'SECTION_EDIT');
$sectionDelete = CIBlock::GetArrayByID($arParams['IBLOCK_ID'], 'SECTION_DELETE');
$sectionDeleteParams = [
    'CONFIRM' => Loc::getMessage('CT_BCSL_ELEMENT_DELETE_CONFIRM'),
];

$sectionNumber = 0;
?>
<div class="catalog_section__catalog-section-list">
    <?
    foreach ($arResult['SECTIONS'] as &$section):
        $this->addEditAction($section['ID'], $section['EDIT_LINK'], $sectionEdit);
        $this->addDeleteAction($section['ID'], $section['DELETE_LINK'], $sectionDelete, $sectionDeleteParams);

        if (!empty($section['PICTURE'])) {
            $xResizedImage = \CFile::ResizeImageGet(
                $section['PICTURE'],
                [
                    'width' => 300,
                    'height' => 200,
                ]
            );

            $xResizedImage = \Bitrix\Iblock\Component\Tools::getImageSrc([
                'SRC' => $xResizedImage['src']
            ]);
        } else {
            $xResizedImage = SITE_TEMPLATE_PATH . '/assets/images/no_photo.svg';
        }
        ?>
        <div class="section-item" id="<?= $this->getEditAreaId($section['ID']) ?>">
            <div class="card-body text-center">
                <div class="card-img-actions">
                    <a href="<?= $section['SECTION_PAGE_URL'] ?>">
                        <img src="<?= $xResizedImage ?>" alt="<?= $section['NAME'] ?>" title="<?= $section['NAME'] ?>">
                    </a>
                </div>

                <h6 class="font-weight-semibold mb-0"><a href="<?= $section['SECTION_PAGE_URL'] ?>" title="<?=$section['NAME']?>"><?=$section['NAME']?></a></h6>
            </div>
               
            <div class="sub_sections">
                <ul>
                    <h5>
                        <a href="<?=$section['SECTION_PAGE_URL']?>">
                            <?=$section['NAME']?>
                        </a>
                    </h5>
                    <?$count = 0;?>

                    <?foreach ($section['SUBSECTIONS'] as $subsection):?>
                    <?$count++?>
                        <li class="<?=$count > 4 ? 'hidden' : ''?>">
                            <a href="<?=$subsection['SECTION_PAGE_URL']?>" title="<?=$subsection['NAME']?>">
                                <?=TruncateText($subsection['NAME'], 25)?>
                            </a>
                        </li>
                    <?if($count == 5):?>
                        <button class="show_more mt-2"></button>
                    <?endif;?>
                    <?endforeach;?>
                </ul>
            </div>

        </div>
    <? endforeach; ?>
</div>
