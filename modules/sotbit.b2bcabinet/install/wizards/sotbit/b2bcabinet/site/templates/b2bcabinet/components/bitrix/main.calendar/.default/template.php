<?if(!defined("B_PROLOG_INCLUDED")||B_PROLOG_INCLUDED!==true)die();?>
<?
if ($arParams['SILENT'] == 'Y') return;

$cnt = $arParams['INPUT_NAME_FINISH'] <> '' ? 2 : 1;
$format = "d.m.Y";
?>
<script>
    if (typeof formatedDateInput === 'undefined') {
        function formatedDateInput(params) {
            const inputDate = document.getElementById(params.fieldName + params.id);
            const inputPropertyDate = document.getElementById(params.fieldName);

            inputDate.addEventListener('change', function(e) {
                if (this.value) {
                    inputPropertyDate.value = BX.date.format(params.format, new Date(this.value));
                } else {
                    inputPropertyDate.value = '';
                }
            })
        }
    }
</script>
<?
for ($i = 0; $i < $cnt; $i++):?>
    <?
    $date = new DateTime($arParams['INPUT_VALUE'.($i == 1 ? '_FINISH' : '')]);
    ?>
    <input type="hidden" name="<?=$arParams['INPUT_NAME'.($i == 1 ? '_FINISH' : '')]?>" id="<?=$arParams['INPUT_NAME'.($i == 1 ? '_FINISH' : '')]?>" value="<?=$date->format($format)?>">
    <input type="date"
            class="form-control"
            id="<?=$arParams['INPUT_NAME'.($i == 1 ? '_FINISH' : '')] . $i?>"
            name="<?=$arParams['INPUT_NAME'.($i == 1 ? '_FINISH' : '')] . $i?>"
            value="<?=$date->format("Y-m-d")?>"
        <?=(Array_Key_Exists("~INPUT_ADDITIONAL_ATTR", $arParams)) ? $arParams["~INPUT_ADDITIONAL_ATTR"] : ""?>
    />
    <script>
        formatedDateInput({
            id: '<?=$i?>',
            format: '<?=$format?>',
            fieldName: '<?=$arParams['INPUT_NAME'.($i == 1 ? '_FINISH' : '')]?>'
        })
    </script>
<?endfor; ?>
