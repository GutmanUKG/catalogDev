<?php
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
require_once __DIR__ . '/function.php';

$TEMPLATE_PATH = SITE_TEMPLATE_PATH;
?>

<?echo json_encode([
    'status' => 'success',
    "elData"=> $elementData['CARUSEL'] ,
    "message" => "
        <div id=\"item_info\">
            <div class='close_ajax_form'></div>
            <h4>{$elementData['NAME']}</h4>
            
            <div class='body_item'>
                <div class='grid_col-2'>
                   <div class='item_prev_info'>
                        {$elementData['PREV_IMG']}
                        <div class='options_list'>
                             <span>Бренд: <b>{$elementData['BRAND']}</b></span>
                             <span>Артикул: <b>{$elementData['ART']}</b></span>
                             <span>SN: <b>{$elementData['PART']}</b></span>
                             <br>
                             <span>Цена уценки: <b>{$elementData['PRICE']}</b></span>
                             <span>Цена в рознице: <b>{$elementData['RRP']}</b></span>
                        </div>
                    </div>
                    <div class='item_prev_text'>
                        <b>Описание товара</b>
                        <p>
                            {$elementData['PREV_TEXT']}
                        </p>  
                    </div>
                </div>
                <div class='description_kat'>
                    {$elementData['INFO_HTML']}
                </div>
                <div class='bottom_item'>
                   
                     <div class='destroy_text'>
                        <b>Коментарий уценки</b>
                        <p>
                            {$elementData['DISTROY_TEXT']}
                        </p> 
                    </div>
                </div>
            </div>
        </div>"
]);


//<div class='popup_imgs owl-carousel owl-theme'>
//
//" . implode('', $elementData['CARUSEL']) . "
//                     </div>
