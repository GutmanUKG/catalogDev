<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
	die();

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Page\Asset;
Loc::loadMessages(__FILE__);

Asset::getInstance()->addCss($arGadget['PATH_SITEROOT'].'/styles.css');
$idUser = intval($USER->GetID());

if(Loader::includeModule('sotbit.b2bcabinet') && $idUser > 0)
{
    $listOrders = new \Sotbit\B2BCabinet\Shop\OrderCollection();
    $listOrders->setLimit(1);

    $filter = array(
        "LID" => SITE_ID,
        'PAYED' => 'N',
        'CANCELED' => 'N'
    );

    if(defined("EXTENDED_VERSION_COMPANIES") && EXTENDED_VERSION_COMPANIES == "Y"){
        $company = new Sotbit\Auth\Company\Company(SITE_ID);
        $filter["ID"] = $company->getCompanyOrders();
    } else {
        $filter["USER_ID"] = $idUser;
    }

    $orders = $listOrders->getOrders($filter);
	foreach ($orders as $order) {
    ?>
    <div class="wait_pay">
        <div class="widget_content widget_payment_waiting d-flex justify-content-between mb-2">
            <span class="text-muted"><?=Loc::getMessage('GD_SOTBIT_CABINET_BUYORDER_SUM')?> </span>
            <span class="fw-bold"><?=$order->getPrice() ?></span>
        </div>
        <div class="widget_content widget_links_btns widget-payment_waiting-content">
            <div class="d-flex justify-content-between mb-2">
                <span class="payment_waiting_text"><?=Loc::getMessage('GD_SOTBIT_CABINET_BUYORDER_DATE')?></span>
                <span><?=$order->getDate()->format("d.m.Y H:i:s")?></span>
            </div>
            <div class="d-flex justify-content-between mb-2">
                <span class="payment_waiting_text"><?=Loc::getMessage('GD_SOTBIT_CABINET_BUYORDER_PERSON_TYPE')?></span>
                <span><?=$order->getPersonType()?></span>
            </div>
        </div>
        <div class="widget_button_wrapper">
            <a href="<?=$order->getUrl($arParams['G_BUYORDER_PATH_TO_ORDER_DETAIL'])?>" class="btn btn-primary widget_button">
                <?=Loc::getMessage('GD_SOTBIT_CABINET_BUYORDER_BUY_ONLINE')?>
            </a>
            
            <?
            $pathToDownload = $order->getDownloadBillLink($arParams['G_BUYORDER_PATH_TO_PAY']);
            if($pathToDownload)
            {
                ?>
                <a href="<?=$pathToDownload?>" class="btn btn-primary btn-icon" title="<?=Loc::getMessage('GD_SOTBIT_CABINET_BUYORDER_DOWNLOAD')?>">
                    <i class="ph-download-simple"></i>
                </a>
                <?
            }?>
        </div>
    </div>
	<?
	}
}
?>
