<?php

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);
$this->setFrameMode(true);
$frame = $this->createFrame()->begin("");
?>

    <div class="navbar-nav-link" id="regions_choose_component" data-toggle="modal"
         data-target="#regions_choose_select-city__modal">
    <span data-entity="select-city">
        <i class="icon-location3"></i>
        <span class="" data-entity="select-city__block__text-city"></span>
    </span>
    </div>

    <div class="select-city__dropdown-wrap" id="regions_choose_component_dropdown" style="display: none;">
        <div class="select-city__dropdown">
            <div class="select-city__dropdown__title-wrap">
        <span class="select-city__dropdown__title" data-entity="select-city__dropdown__title">
            <?= Loc::getMessage(SotbitRegions::moduleId . '_YOUR_CITY') . ' ###?' ?>
        </span>
            </div>
            <div class="select-city__dropdown__choose-wrap">
            <span class="select-city__dropdown__choose__yes select-city__dropdown__choose btn btn-light"
                  data-entity="select-city__dropdown__choose__yes"
            ><?= Loc::getMessage(SotbitRegions::moduleId . '_YES') ?>
            </span>
                <span class="select-city__dropdown__choose__no select-city__dropdown__choose btn btn_b2b"
                      data-entity="select-city__dropdown__choose__no" data-toggle="modal"
                      data-target="#regions_choose_select-city__modal"
                >
                <?= Loc::getMessage(SotbitRegions::moduleId . '_NO') ?>
            </span>
            </div>
        </div>
    </div>

    <div id="regions_choose_select-city__modal" class="select-city__modal modal fade" style="display: none;"
         tabindex="-1" aria-modal="true" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-main text-white">
                    <h6 class="modal-title"><?= Loc::getMessage(SotbitRegions::moduleId . '_MODAL_TITLE') ?></h6>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <div class="modal-body">

                    <h6 class="font-weight-semibold"><?= Loc::getMessage(SotbitRegions::moduleId . '_YOUR_CITY') ?>:
                        <span data-entity="select-city__js"></span></h6>
                    <p><?= Loc::getMessage(SotbitRegions::moduleId . '_WRONG_DETECT') ?></p>

                    <div class="select-city__modal__submit__block-wrap">
                        <div class="select-city__modal__submit__block-wrap__input_wrap">
                            <div class="bitrix-error" style="display:none;"
                                 data-entity="select-city__modal__submit__block-wrap__input_wrap_error">
                                <label class="validation-invalid-label">
                                    <?= Loc::getMessage(SotbitRegions::moduleId . '_ERROR') ?>
                                </label>
                            </div>

                            <div class="form-group-feedback form-group-feedback-right">
                                <input type="text" class="form-control" data-entity="select-city__modal__submit__input"
                                       placeholder="<?= Loc::getMessage(SotbitRegions::moduleId . '_SEARCH_TITLE') ?>">

                                <div class="form-control-feedback form-control-feedback-sm">
                                    <i class="icon-search4"></i>
                                </div>
                            </div>
                            <div class="select-city__modal__submit__vars" data-entity="select-city__modal__submit__vars"
                                 style="display: none;"></div>
                        </div>
                    </div>

                    <ul class="select-city__modal__list" data-entity="select-city__modal__list">
                        <!-- region names -->
                    </ul>
                </div>

                <div class="modal-footer">
                    <button type="button" type="submit" name="submit" class="btn btn_b2b"
                            data-entity="select-city__modal__submit__btn">
                        <?= Loc::getMessage(SotbitRegions::moduleId . '_SELECT_SUBMIT') ?>
                    </button>
                </div>
            </div>
        </div>
    </div>
    <script>
        $componentRegionsChoose = new RegionsChoose();
    </script>
<? $frame->end(); ?>