<?php
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
require_once __DIR__ . '/function.php';

$TEMPLATE_PATH = SITE_TEMPLATE_PATH;

echo json_encode([
    'status' => 'success',
    "elData"=> $elementData['CARUSEL'] ,
    "message" => "
        <div id=\"item_info-photo\">
            <div class='close_ajax_form'></div>
            <div class='body_item'>
                <div class='bottom'>
                     <div class='popup_imgs owl-carousel owl-theme'>
                      
                        " . implode('', $elementData['CARUSEL']) . "
                     </div>
                   <span class='destroy_descr'>
                 ". $elementData['DISTROY_TEXT'].".
                </span>
                </div>
                
               
            </div>
            
        </div>"
]);