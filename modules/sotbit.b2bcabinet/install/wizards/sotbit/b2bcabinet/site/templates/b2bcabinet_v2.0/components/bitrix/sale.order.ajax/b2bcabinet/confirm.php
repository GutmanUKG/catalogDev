<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;

/**
 * @var array $arParams
 * @var array $arResult
 * @var $APPLICATION CMain
 */

if ($arParams["SET_TITLE"] == "Y") {
	$APPLICATION->SetTitle(Loc::getMessage("SOA_ORDER_COMPLETE"));
}
?>
<? if (!empty($arResult["ORDER"])) : ?>

	<div class="card mb-4">
		<div class="card-body card-p-2">
			<div class="text-center">
				<div class="mx-auto mb-3">
					<svg width="73" height="72" viewBox="0 0 73 72" fill="none" xmlns="http://www.w3.org/2000/svg">
						<g clip-path="url(#clip0_3305_29824)">
							<path d="M36.5 72C56.3823 72 72.5 55.8823 72.5 36C72.5 16.1177 56.3823 0 36.5 0C16.6177 0 0.5 16.1177 0.5 36C0.5 55.8823 16.6177 72 36.5 72Z" fill="#32B76C" />
							<path d="M31.0437 54.6734L14.45 38.0797C14.225 37.8547 14.225 37.5172 14.45 37.2922L19.2313 32.5109C19.4563 32.2859 19.7938 32.2859 20.0188 32.5109L31.4375 43.9297L52.925 22.4422C53.15 22.2172 53.4875 22.2172 53.7125 22.4422L58.4937 27.2234C58.7188 27.4484 58.7188 27.7859 58.4937 28.0109L31.8313 54.6734C31.6063 54.8984 31.2687 54.8984 31.0437 54.6734Z" fill="white" />
						</g>
						<defs>
							<clipPath id="clip0_3305_29824">
								<rect width="72" height="72" fill="white" transform="translate(0.5)" />
							</clipPath>
						</defs>
					</svg>
				</div>

				<h4><?= Loc::getMessage("SOA_ORDER_SUC", array("#ORDER_ID#" => $arResult["ORDER"]["ACCOUNT_NUMBER"])) ?></h4>
				<? if (!empty($arResult['ORDER']["PAYMENT_ID"])) : ?>
					<h6>
						<?= Loc::getMessage("SOA_PAYMENT_SUC", array(
							"#PAYMENT_ID#" => $arResult['PAYMENT'][$arResult['ORDER']["PAYMENT_ID"]]['ACCOUNT_NUMBER']
						)) ?>
					</h6>
				<? endif ?>
				<? if ($arParams['NO_PERSONAL'] !== 'Y') : ?>
					<span class="d-block w-md-50 mx-auto px-md-3"><?= Loc::getMessage('SOA_ORDER_SUC1', ['#LINK#' => $arParams['PATH_TO_PERSONAL']]) ?></span>
				<? endif; ?>
			</div>
		</div>
	</div>

	<div class="card">
		<div class="card-body card-p-2">
			<div class="w-md-75 m-md-auto">
				<?
				if ($arResult["ORDER"]["IS_ALLOW_PAY"] === 'Y') {
					if (!empty($arResult["PAYMENT"])) {
						foreach ($arResult["PAYMENT"] as $payment) {
							if ($payment["PAID"] != 'Y') {
								if (
									!empty($arResult['PAY_SYSTEM_LIST'])
									&& array_key_exists($payment["PAY_SYSTEM_ID"], $arResult['PAY_SYSTEM_LIST'])
								) {
									$arPaySystem = $arResult['PAY_SYSTEM_LIST_BY_PAYMENT_ID'][$payment["ID"]];

									if (empty($arPaySystem["ERROR"])) {
									?>
										<div class="ps_logo">
											<h5 class="pay_name"><?= Loc::getMessage("SOA_PAY_TITLE") ?></h5>
											<?= CFile::ShowImage($arPaySystem["LOGOTIP"], 100, 100, "border=0\" ", "", false) ?>
											<div class="paysystem_name"><?= $arPaySystem["NAME"] ?></div>
										</div>

										<div>
											<? if (strlen($arPaySystem["ACTION_FILE"]) > 0 && $arPaySystem["NEW_WINDOW"] == "Y" && $arPaySystem["IS_CASH"] != "Y") : ?>
												<?
												$orderAccountNumber = urlencode(urlencode($arResult["ORDER"]["ACCOUNT_NUMBER"]));
												$paymentAccountNumber = $payment["ACCOUNT_NUMBER"];
												?>
												<script>
													window.open('<?= $arParams["PATH_TO_PAYMENT"] ?>?ORDER_ID=<?= $orderAccountNumber ?>&PAYMENT_ID=<?= $paymentAccountNumber ?>');
												</script>
												<?= Loc::getMessage("SOA_PAY_LINK", array("#LINK#" => $arParams["PATH_TO_PAYMENT"] . "?ORDER_ID=" . $orderAccountNumber . "&PAYMENT_ID=" . $paymentAccountNumber)) ?>
												<? if (CSalePdf::isPdfAvailable() && $arPaySystem['IS_AFFORD_PDF']) : ?>
													<br />
													<?= Loc::getMessage("SOA_PAY_PDF", array("#LINK#" => $arParams["PATH_TO_PAYMENT"] . "?ORDER_ID=" . $orderAccountNumber . "&pdf=1&DOWNLOAD=Y")) ?>
												<? endif ?>
											<? else : ?>
												<?= $arPaySystem["BUFFERED_OUTPUT"] ?>
											<? endif ?>
										</div>
									<?
									} else {
									?>
										<span style="color:red;"><?= Loc::getMessage("SOA_ORDER_PS_ERROR") ?></span>
									<?
									}
								} else {
									?>
									<span style="color:red;"><?= Loc::getMessage("SOA_ORDER_PS_ERROR") ?></span>
								<?
								}
							}
						}
					}
				} else {
					?>
					<br /><strong><?= $arParams['MESS_PAY_SYSTEM_PAYABLE_ERROR'] ?></strong>
				<?
				}
				?>
			</div>
		</div>
	</div>
<? else : ?>

	<b><?= Loc::getMessage("SOA_ERROR_ORDER") ?></b>
	<br />

	<div class="card">
		<div class="card-body">
			<?= Loc::getMessage("SOA_ERROR_ORDER_LOST", ["#ORDER_ID#" => htmlspecialcharsbx($arResult["ACCOUNT_NUMBER"])]) ?>
			<?= Loc::getMessage("SOA_ERROR_ORDER_LOST1") ?>
		</div>
	</div>
<? endif ?>