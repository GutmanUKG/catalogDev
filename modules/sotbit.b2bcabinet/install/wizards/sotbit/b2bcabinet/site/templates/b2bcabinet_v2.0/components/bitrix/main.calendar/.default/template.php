<?if(!defined("B_PROLOG_INCLUDED")||B_PROLOG_INCLUDED!==true)die();?>
<?
use Bitrix\Main\Page\Asset;

Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . "/assets/js/plugins/ui/moment/moment.min.js");
Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . "/assets/js/plugins/pickers/daterangepicker.js");
Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . "/assets/js/plugins/pickers/daterangepicker_lang_RU.js");
if ($arParams['SILENT'] == 'Y') return;

$cnt = $arParams['INPUT_NAME_FINISH'] <> '' ? 2 : 1;

for ($i = 0; $i < $cnt; $i++):?>
    <input type="text"
            class="form-control daterange-single"
            id="<?=$arParams['INPUT_NAME'.($i == 1 ? '_FINISH' : '')]?>"
            name="<?=$arParams['INPUT_NAME'.($i == 1 ? '_FINISH' : '')]?>"
            value="<?=$arParams['INPUT_VALUE'.($i == 1 ? '_FINISH' : '')]?>"
        <?=(Array_Key_Exists("~INPUT_ADDITIONAL_ATTR", $arParams)) ? $arParams["~INPUT_ADDITIONAL_ATTR"] : ""?>
    />
<?endfor; ?>

<style>
    .content .daterangepicker {
        transform: translateY(4.5rem);
    }
</style>

<script>
    if (typeof DatePickers === 'undefined') {
        var DatePickers = function() {
            const _componentDaterange = function() {
                if (!$().daterangepicker) {
                        console.warn('Warning - daterangepicker.js is not loaded.');
                        return;
                }
                
                $('.daterange-single').daterangepicker({
                        singleDatePicker: true,
                        showDropdowns: true,
                        opens: 'left',
                        parentEl: $('.content') || document.body,
                        autoUpdateInput: false,
                        locale: {
                            format: 'DD.MM.YYYY',
                        }
                });

                $('.daterange-single').on('apply.daterangepicker', function(ev, picker) {
                    $(this).val(picker.startDate.format('DD.MM.YYYY'));
                });
            }

            return {
                init: function() {
                    _componentDaterange();
                }
            }
        }();
    }

    document.addEventListener('DOMContentLoaded', function() {
        DatePickers.init();
    });
</script>