<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

?>
<div class="sotbit_cabinet">
	<?
	if (!defined("BX_GADGET_DEFAULT")) {
		define("BX_GADGET_DEFAULT", true);

	?>
		<script type="text/javascript">
			var updateURL = '<?= CUtil::JSEscape(htmlspecialcharsback($arResult['UPD_URL'])) ?>';
			var bxsessid = '<?= CUtil::JSEscape(bitrix_sessid()) ?>';
			var langGDError1 = '<?= CUtil::JSEscape(GetMessage("CMDESKTOP_TDEF_ERR1")) ?>';
			var langGDError2 = '<?= CUtil::JSEscape(GetMessage("CMDESKTOP_TDEF_ERR2")) ?>';
			var langGDConfirm1 = '<?= CUtil::JSEscape(GetMessage("CMDESKTOP_TDEF_CONF")) ?>';
			var langGDConfirmUser = '<?= CUtil::JSEscape(GetMessage("CMDESKTOP_TDEF_CONF_USER")) ?>';
			var langGDConfirmGroup = '<?= CUtil::JSEscape(GetMessage("CMDESKTOP_TDEF_CONF_GROUP")) ?>';
			var langGDClearConfirm = '<?= CUtil::JSEscape(GetMessage("CMDESKTOP_TDEF_CLEAR_CONF")) ?>';
			var langGDClearTitleConfirm = '<?= CUtil::JSEscape(GetMessage("CMDESKTOP_TDEF_TITLE_CLEAR_CONF")) ?>';
			var langGDButtonCancel = "<? echo CUtil::JSEscape(GetMessage("CMDESKTOP_TDEF_CANCEL")) ?>";
			var langGDButtonConfirm = "<? echo CUtil::JSEscape(GetMessage("CMDESKTOP_TDEF_CONFIRM")) ?>";
		</script>
	<?
	}

	if ($arResult["PERMISSION"] > "R") {
		$APPLICATION->AddHeadScript("/bitrix/components/bitrix/desktop/script.js");

		$allGD = array();
		foreach ($arResult['ALL_GADGETS'] as $gd) {
			$allGD[] = array(
				'ID' => $gd["ID"],
				'TEXT' =>
				'<div style="text-align: left;">' . ($gd['ICON1'] ? '<img src="' . ($gd['ICON']) . '" align="left">' : '') .
					'<b>' . (htmlspecialcharsbx($gd['NAME'])) . '</b><br>' . (htmlspecialcharsbx($gd['DESCRIPTION'])) . '</div>',
			);
		}
	?>
		<script type="text/javascript">
			var arGDGroups = <?= CUtil::PhpToJSObject($arResult["GROUPS"]) ?>;
			new SCGadget('<?= $arResult["ID"] ?>', <?= CUtil::PhpToJSObject($allGD) ?>);
		</script>


		<div class="widgets_cabinet show_widgets">
			<button type="button" class="btn-close" onclick="toggleAdd();"></button>
			<div class="widget_buttons">
				<?
				foreach ($arResult['ALL_GADGETS'] as $gd) {
					if($gd['ACTIVE'] == 'Y') :?>
					<div class="widget_button widget_button--active" 
					     onclick="getGadgetHolderSC('<?= AddSlashes($arResult['ID']) ?>').Delete('<?= $gd['ELEMENTS_ID'][0] ?>')"
						 data-id-gadget="<?= $gd['ID'] ?>">
					<? else: ?>
					<div class="widget_button" 
					     onclick="getGadgetHolderSC('<?= AddSlashes($arResult['ID']) ?>').Add('<?= $gd['ID'] ?>')"
						 data-id-gadget="<?= $gd['ID'] ?>">
					<? endif; ?>
						<div class="widgets_cabinet_title">
							<?= $gd['NAME'] ?>
						</div>
						<div class="widgets_cabinet_descr">
							<?= $gd['DESCRIPTION'] ?>
						</div>
					</div>
				<?
				}
				?>
			</div>
		</div>


		<div class="sw--all_widgets">
			<div class="widget-bx-gd-buttons gap-4 position-fixed">
				<div class="fab-menu fab-menu-bottom fab-menu-bottom-end" data-fab-position="custom" data-fab-toggle="click">
					<button type="button" class="fab-menu-btn btn btn-primary btn-icon widget-btn">
						<div class="m-1">
							<i class="fab-icon-open ph-plus"></i>
							<i class="fab-icon-close ph-x"></i>
						</div>
					</button>

					<ul class="fab-menu-inner">
						<? if ($arResult["PERMISSION"] > "W") : ?>
							<? if ($arParams["MODE"] == "SU") {
								$mode = "'SU'";
							} elseif ($arParams["MODE"] == "SG") {
								$mode = "'SG'";
							} else {
								$mode = "";
							}
							?>
							<li>
								<div class="fab-label-center" data-fab-label="<?= Loc::getMessage('CMDESKTOP_TDEF_SET') ?>">
									<button type="button" class="btn btn-xl btn-light btn-icon btn-float" onclick="getGadgetHolderSC('<?= AddSlashes($arResult["ID"]) ?>').SetForAll(<?= $mode ?>);">
										<i class="ph-floppy-disk"></i>
									</button>
								</div>
							</li>
						<? endif; ?>

						<li>
							<div class="fab-label-center" data-fab-label="<?= Loc::getMessage('CMDESKTOP_TDEF_CLEAR') ?>">
								<button type="button" class="btn btn-xl btn-light btn-icon btn-float" onclick="getGadgetHolderSC('<?= AddSlashes($arResult["ID"]) ?>').ClearUserSettingsConfirm();">
									<i class="ph-arrow-counter-clockwise"></i>
								</button>
							</div>
						</li>
						<li>
							<div class="fab-label-center" data-fab-label="<?= Loc::getMessage('CMDESKTOP_TDEF_ADD_WIDGET') ?>">
								<button type="button" class="btn btn-xl btn-light btn-icon btn-float" onclick="getGadgetHolderSC('<?= AddSlashes($arResult["ID"]) ?>').ShowAddGDMenu(this); ">
									<i class="ph-plus"></i>
								</button>
							</div>
						</li>
					</ul>
				</div>

				<? $APPLICATION->IncludeComponent(
					"sotbit:sotbit.personal.manager",
					"b2bcabinet_manager_buttons",
					array(
						"COMPONENT_TEMPLATE" => "b2bcabinet_manager_buttons",
						"SHOW_FIELDS" => array(
							0 => "NAME",
							1 => "PERSONAL_PHOTO",
							2 => "WORK_PHONE",
							3 => "UF_P_MANAGER_EMAIL",
						),
						"USER_PROPERTY" => array(
							0 => "UF_P_MANAGER_ID",
						),
						"NAME_TEMPLATE" => $arGadget["SETTINGS"]["PERSONAL_MANAGER_NAME_TEMPLATE"] ?: "#NOBR##NAME# #LAST_NAME##/NOBR#"
					),
					false
				);
				?>
			</div>
		</div>
	<?
	}
	?>
	<div data-update-widgets-block>
		<!-- widgets -->
		<form action="<?= POST_FORM_ACTION_URI ?>" method="POST" id="GDHolderForm_<?= $arResult["ID"] ?>">
			<?= bitrix_sessid_post() ?>
			<input type="hidden" name="holderid" value="<?= $arResult["ID"] ?>">
			<input type="hidden" name="gid" value="0">
			<input type="hidden" name="action" value="">
		</form>

		<div class="gadgetholder" id="GDHolder_<?= $arResult["ID"] ?>">
			<?
			for ($i = 0; $i < $arResult["COLS"]; $i++) {
			?>
				<div class="gd-page-column gd-page-column<?= $i ?>" id="s<?= $i ?>" style="width: <?= 100 / $arResult["COLS"] ?>%">
					<?
					foreach ($arResult["GADGETS"][$i] as $arGadget) {
						if ($arParams["SHOW_GADGET"] && !in_array("ALL", $arParams["SHOW_GADGET"]) && !in_array($arGadget["GADGET_ID"], $arParams["SHOW_GADGET"])) {
							continue;
						}
						$bChangable = true;

						if (
							!$GLOBALS["USER"]->IsAdmin()
							&& array_key_exists("GADGETS_FIXED", $arParams)
							&& is_array($arParams["GADGETS_FIXED"])
							&& in_array($arGadget["GADGET_ID"], $arParams["GADGETS_FIXED"])
							&& array_key_exists("CAN_BE_FIXED", $arGadget)
							&& $arGadget["CAN_BE_FIXED"]
						)
							$bChangable = false;

					?>
						<div id="t<?= $arGadget["ID"] ?>" data-gadget="<?= $arGadget["ID"] ?>" class="sotbit-cabinet-gadget sotbit-cabinet-gadget-<?= strtolower($arGadget['GADGET_ID']) ?>">
							<div class="card <?= ($arGadget["HIDE"] == "Y" ? 'card-collapsed' : '') ?>">
								<div class="card-header d-flex position-relative" <? if ($GLOBALS["USER"]->IsAuthorized()) : ?> style="cursor:move;" onmousedown="return getGadgetHolderSC('<?= AddSlashes($arResult["ID"]) ?>').DragStart('<?= $arGadget["ID"] ?>', event)" <? endif; ?>>
									<h5 class="mb-0"><?= $arGadget["TITLE"] ?></h5>
									<div class="d-inline-flex ms-auto align-items-center">
										<?
										if ($arResult["PERMISSION"] > "R") {
										?>
											<?
											if ($bChangable) {
											?>
												<a class="text-body px-1 gdsettings <?= ($arGadget["NOPARAMS"] ? ' gdnoparams' : '') ?>" href="javascript:void(0)" onclick="return getGadgetHolderSC('<?= AddSlashes($arResult["ID"]) ?>').ShowSettings('<?= $arGadget["ID"] ?>');" title="<?= GetMessage("CMDESKTOP_TDEF_SETTINGS") ?>"">
														<i class="ph ph-pencil-simple" onmousedown="event.stopPropagation();"></i>
												</a>
												<a class="text-body px-1" data-card-action="remove" href="javascript:void(0)" onclick="return getGadgetHolderSC('<?= AddSlashes($arResult["ID"]) ?>').Delete('<?= $arGadget["ID"] ?>');" title="<?= GetMessage("CMDESKTOP_TDEF_DELETE") ?>" title="<?= GetMessage("CMDESKTOP_TDEF_DELETE") ?>">
													<i class="ph ph-trash" onmousedown="event.stopPropagation();"></i>
												</a>
											<?
											}
											?>
										<?
										}
										?>
										<a class="text-body px-1" href="javascript:void(0)" onclick="savePositionCollapse('<?= $arResult["ID"] ?>');" data-card-action="collapse">
											<i class="ph ph-caret-down" onmousedown="event.stopPropagation();"></i>
										</a>
									</div>
								</div>
								<div class="collapse <?= ($arGadget["HIDE"] != "Y" ? 'show' : '') ?>">
									<div class="card-body">
										<? $yandexRegionId = $arGadget['GADGET_ID'] === 'WEATHER' || $arGadget['GADGET_ID'] === 'PROBKI' ? 'search-yandex-region-id' : '' ?>
										<div class="gdoptions" data-entity="<?= $yandexRegionId ?>" style="display:none" id="dset<?= $arGadget["ID"] ?>"></div>
										<div class="gdcontent" id="dgd<?= $arGadget["ID"] ?>">

											<?= $arGadget["CONTENT"] ?>

										</div>
										<div class="card-footer-action d-inline-flex mt-2 ms-auto">
											
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="gadget-shodown-drag" id="d<?= $arGadget["ID"] ?>"></div>
					<?
					}
					?>
				</div><?
					}
						?>
		</div>
		<script>
			if (document.querySelector(".widgets_cabinet")) {
				if (!document.querySelector(".body_widgets_main")) {
					var el = document.createElement('div');
					el.className = 'body_widgets_main';
					el.setAttribute("onclick", "toggleAdd();");
					document.querySelector(".widgets_cabinet").before(el);
				}
				document.body.classList.add("body_class");
			}
		</script>
		<!-- /widgets -->
	</div>
</div>

