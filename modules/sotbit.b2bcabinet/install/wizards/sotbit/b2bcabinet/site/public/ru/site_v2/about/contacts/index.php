<?php
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");

if (!Loader::includeModule('sotbit.b2bcabinet')) {
    header('Location: ' . SITE_DIR);
}

$APPLICATION->SetTitle(Loc::getMessage('CONTACTS'));
?>
<div class="card p-3 b2b-about_contacts">
    <h2><span style="font-size: 22px;">ООО «Компания»</span></h2>
    <div class="table-responsive">
        <table height="228" cellspacing="0" cellpadding="0">
            <tbody>
            <tr>
                <td width="170" valign="top">
                    <span style="font-size: 13px;">&nbsp;Адрес: </span>
                </td>
                <td valign="top">
<span style="font-size: 13px;">197022 г. Санкт-Петербург, м. «Петроградская»,
пр-т Медиков, д. 3 - 5, лит. А, оф. 211
</span>
                </td>
            </tr>
            <tr>
                <td width="170" valign="top">
                    <span style="font-size: 13px;">Телефоны: </span>
                </td>
                <td valign="top">
                    <span style="font-size: 13px;">+7 495 278 08 54</span><br>
                    <span style="font-size: 13px;">
            +7 812 670 07 40</span><br>
                </td>
            </tr>
            <tr>
                <td colspan="1" width="170" valign="top">
                    <span style="font-size: 13px;">Эл. почта: </span>
                </td>
                <td colspan="1" valign="top">
                    <a href="mailto:sale@sotbit.ru"><span style="font-size: 13px;">sale@sotbit.ru</span></a>
                </td>
            </tr>
            <tr>
                <td colspan="1" width="170" valign="top">
                    <span style="font-size: 13px;">Режим работы: </span>
                </td>
                <td colspan="1" valign="top">
                    <span style="font-size: 13px;">Понедельник–пятница, с 10:00 до 18:00</span><br>
                    <span style="font-size: 13px;">
            Суббота, с 9:00 до 15:00</span><br>
                    <span style="font-size: 13px;">
            Воскресенье — выходной </span>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
    <h2 style="font-size: 22px;">
        Отдел оптовой торговли</h2>
    <div class="table-responsive">
        <table style="margin-bottom:25px;" height="228" cellspacing="0" cellpadding="4">
            <tbody>
            <tr>
                <td style="padding-bottom:7px;" width="170" valign="top">
<span style="font-size: 13px;">
            Адрес: </span>
                </td>
                <td style="padding-bottom:7px;" valign="top">
<span style="font-size: 13px;">
 197022 г. Санкт-Петербург, м. «Петроградская»,
пр-т Медиков, д. 3 - 5, лит. А, оф. 211
</span>
                </td>
            </tr>
            <tr>
                <td style="padding-bottom:7px;" width="170" valign="top">
                    <span style="font-size: 13px;">Телефоны: </span>
                </td>
                <td style="padding-bottom:7px;" valign="top">
                    <span style="font-size: 13px;">+7 495 278 08 54</span><br>
                    <span style="font-size: 13px;">+7 812 670 07 40</span><br>
                </td>
            </tr>
            <tr>
                <td colspan="1" style="padding-bottom:7px;" width="170" valign="top">
                    <span style="font-size: 13px;">Эл. почта: </span>
                </td>
                <td colspan="1" style="padding-bottom:7px;" valign="top">
                    <a href="mailto:sale@sotbit.ru"><span style="font-size: 13px;">sale@sotbit.ru</span></a>
                </td>
            </tr>
            <tr>
                <td colspan="1" style="padding-bottom:7px;" width="170" valign="top">
                    <span style="font-size: 13px;">Режим работы: </span>
                </td>
                <td colspan="1" style="padding-bottom:7px;" valign="top">
                    <span style="font-size: 13px;">Понедельник–пятница, с 10:00 до 18:00</span><br>
                    <span style="font-size: 13px;">
            Суббота, с 9:00 до 15:00</span><br>
                    <span style="font-size: 13px;">
            Воскресенье — выходной </span>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
    <h2><span style="font-size: 22px;">Реквизиты компании</span></h2>
    <div style="font-size:13px;line-height:19px;">
        <p style="margin-bottom:20px;">
            Свидетельство: серия 00 № 999999999 от 01.01.2020 года
            выдано ИМНС РФ по Новому району г. Москвы
        </p>
        <p style="margin-bottom:20px;">
            ОГРН 111111111111111<br>
            ИНН 222222222222<br>
            р/с 00000000000000000000<br>
        </p>
        <p style="margin-bottom:20px;">
            БИК 010101011<br>
            к/с 88888810000000000999<br>
            ОКОНХ 88000<br>
        </p>
        <b>Схема проезда:</b>
            <? $APPLICATION->IncludeComponent(
                "bitrix:map.yandex.view",
                ".default",
                array(
                    "API_KEY" => "",
                    "CONTROLS" => array(
                        0 => "ZOOM",
                        1 => "MINIMAP",
                        2 => "TYPECONTROL",
                        3 => "SCALELINE",
                    ),
                    "INIT_MAP_TYPE" => "MAP",
                    "MAP_DATA" => "a:4:{s:10:\"yandex_lat\";d:59.94581241914957;s:10:\"yandex_lon\";d:30.29912602500242;s:12:\"yandex_scale\";i:11;s:10:\"PLACEMARKS\";a:1:{i:0;a:3:{s:3:\"LON\";d:30.316281;s:3:\"LAT\";d:59.969232;s:4:\"TEXT\";s:99:\"Понедельник–пятница, с 10:00 до 18:00###RN###Суббота, с 9:00 до 15:00###RN###Воскресенье — выходной\";}}}",
                    "MAP_HEIGHT" => "500",
                    "MAP_ID" => "b2b_about_contacts",
                    "MAP_WIDTH" => "100%",
                    "OPTIONS" => array(
                        0 => "ENABLE_SCROLL_ZOOM",
                        1 => "ENABLE_DBLCLICK_ZOOM",
                        2 => "ENABLE_DRAGGING",
                    ),
                    "COMPONENT_TEMPLATE" => ".default"
                ),
                false
            ); ?>
    </div>
</div>
<style>
    .b2b-about_contacts .bx-yandex-view-layout {
        max-width: 100% !important;
    }
</style>
<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>