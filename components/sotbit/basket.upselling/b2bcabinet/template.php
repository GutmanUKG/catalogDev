<?

use Bitrix\MAin\Localization\Loc;
use \Bitrix\Main\Web\Json;

$langs = Loc::loadLanguageFile(__FILE__);

?>
<script>
    <?foreach ($langs as $key => $value):?>
    BX.message({<?=$key?>: '<?=$value?>'});
    <?endforeach;?>
</script>

<div class="basket-upselling__upselling">
    <div
            id="basket_upselling_templates"
            style="height: 100%;"
            data-arResult="<?= htmlspecialchars(Json::encode($arResult)) ?>"
            data-arParams="<?= htmlspecialchars(Json::encode($arParams)) ?>"
    ></div>
</div>