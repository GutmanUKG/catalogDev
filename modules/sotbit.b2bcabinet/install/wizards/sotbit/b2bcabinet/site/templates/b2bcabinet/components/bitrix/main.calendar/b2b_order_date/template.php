<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>


<?
    $id = 'b2bOrder_' . $this->randString();
    $format = "d.m.Y";
    $date = new DateTime($arParams["VALUE"]);
?>

<input type="hidden" name="<?=$arParams["FIELD_NAME"]?>" id="<?= $arParams["FIELD_NAME"]?>" value="<?=$date->format($format)?>">
<input type="date" class="form-control"
       name="<?= $id . $arParams["FIELD_NAME"] ?>"
       id="<?= $id . $arParams["FIELD_NAME"] ?>"
       placeholder="<?= GetMessage("SOA_SELECT_DATE") ?>"
       value="<?=$date->format("Y-m-d")?>"
    <?= $arParams["REEDONLY"] == "Y" ? "readonly" : "" ?>
/>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        orderDate({
            id: '<?=$id?>',
            format: '<?=$format?>',
            fieldName: '<?=$arParams["FIELD_NAME"]?>',
        });

        function orderDate(params) {
            const inputDate = document.getElementById(params.id + params.fieldName);
            const inputPropertyDate = document.getElementById(params.fieldName);

            inputDate.addEventListener('change', function (e) {
                if (this.value) {
                    inputPropertyDate.value = BX.date.format(params.format ,new Date(this.value));
                }
            });
        }
    });
</script>