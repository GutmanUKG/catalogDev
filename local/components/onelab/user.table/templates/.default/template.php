<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
?>
<div class="btns-wrapper">
    <a href="<?=$APPLICATION->GetCurPageParam('pdf=Y', ['pdf'])?>" target="_blank" class="btn btn-pdf">
        <a href="<?=$APPLICATION->GetCurPageParam('excel=Y', ['excel'])?>" target="_blank" class="btn btn-excel"></a>
</div>


   <table class="table" style="display: none">
        <tr class="table-header">
            <?php foreach ($arResult['SECTIONS'] as $key=>$value):?>
                <?php if($value == 'Менеджер'):?>
                    <td data-key="2000" class="sort" data-sort_2000='desc'>
                        <?=$value?>
                    </td>
                <?elseif($value == 'Текст'):?>
                    <td >
                        <?=$value?>
                    </td>
                <?else:?>
                    <td data-key="<?=$key?>" class="sort" data-sort_<?=$key?>='desc'>
                        <?=$value?>
                    </td>
            <?endif;?>

            <?endforeach;?>
            <td>
                <button class="btn btn-clear">
                    <?=GetMessage('CLEAR')?>
                </button>
            </td>
        </tr>
        <?php foreach ($arResult['USERS'] as $key => $USERITEM):?>
            <tr class="user_row">
                <td><?=$USERITEM['DATE_REGISTER']?></td>
                <td>
                    <?=$USERITEM['USER_NAME']?> <br>
                    <p style="font-size: 14px; font-weight: 500; "> <?=$USERITEM['WORK_COMPANY']?></p>
                </td>
                <td>
                    <?=$USERITEM['UF_MANAGER']?>
                </td>
                <?php if(intval ($USERITEM['DAYS_SINCE_LAST_ORDER']) > 180 || $USERITEM['DAYS_SINCE_LAST_ORDER'] === false):?>
            <td style="background: #f34141; color: #fff">

            <?php if($USERITEM['DAYS_SINCE_LAST_ORDER'] == false){
                echo 'Нет заказов';
            }else{
                echo $USERITEM['DAYS_SINCE_LAST_ORDER'];
            }?>
            <?php else:?>
                <td style="background: #59b77b; color: #fff" >
                    <?=$USERITEM['DAYS_SINCE_LAST_ORDER']?>
                    <?endif;?>

                </td>
                <?php foreach ($USERITEM['SECTIONS'] as $USECTION):?>
                    <?php if(count($USECTION['ELEMENTS']) <= 0):?>
                        <td style="background:#f34141">
                        <a
                                href="https://b2b.ak-cent.kz/bitrix/admin/iblock_list_admin.php?IBLOCK_ID=32&type=sotbit_b2bcabinet_type_document&lang=ru&find_section_section=0&SECTION_ID=0&apply_filter=Y"
                                target="_blank" >
                            <?=GetMessage('ADD')?>
                        </a>
                    <?php else:?>
                        <td style="background:#59b77b">
                            <?php foreach ($USECTION['ELEMENTS'] as $el):?>
                                <a  href="<?=$el?>" target="_blank" >
                                    <?=GetMessage('SHOW')?>
                                </a>
                            <?php endforeach;?>
                        </td>
                    <?php endif;?>
                <?php endforeach;?>
                <td>
                    <?php if(empty($USERITEM['UF_COMMENT'])):?>
                        <button class="add_comment btn" data-id="<?=$USERITEM['USER_ID']?>">

                        </button>
                    <?php else:?>
                        <button class="show_comment btn" data-id="<?=$USERITEM['USER_ID']?>">

                        </button>
                    <?//=$USERITEM['UF_COMMENT']?>
                    <?php endif;?>
                </td>
                <td>
                    <button class="btn remove_btn" data-id="<?=$USERITEM['USER_ID']?>">
                        <?=GetMessage('DISABLE')?>
                    </button>
                </td>
            </tr>
        <?php endforeach;?>
    </table>


    <div id="popup_comment">
        <div class="close"></div>
        <h4>Для комментариев и заметок</h4>
        <form action="">
            <textarea name="text" class="text">
            </textarea>
            <button class="btn save">Сохранить</button>
        </form>
        <div class="status"></div>
    </div>
<div class="overlay"></div>


