<?php

namespace Sotbit\B2bCabinet\Form;

use \Bitrix\Main\Security\Random;

class FormConstructor extends FormTemplates
{
    private $arForms = [];

    public function __construct($id, array $formSettings = [], array $arGroups = null)
    {
        parent::__construct();

        if ($id) {
            $this->arForms[$id]["SETTINGS"] = $formSettings;
        }

        if ($arGroups) {
            $this->arForms[$id]["GROUPS"] = $arGroups;
        }
    }


    public function showForm($formId)
    {
        print $this->replaceTagsTemplate(
            $this->getFormWrapper(),
            [
                "#FORM_HEADER#" => $this->renderFormHeader($formId),
                "#FORM_BODY#" => $this->renderFormBody($formId),
            ]
        );
    }

    private function renderAgreement($formId)
    {
        $confidential = '';
        if ($this->arForms[$formId]["SETTINGS"]["PARAMS"]["USE_AGREEMENT"] == "Y") {
            global $APPLICATION;

            ob_start();
            $APPLICATION->IncludeComponent(
                "bitrix:main.userconsent.request",
                "b2bcabinet",
                array(
                    "AUTO_SAVE" => "Y",
                    "COMPOSITE_FRAME_MODE" => "A",
                    "COMPOSITE_FRAME_TYPE" => "AUTO",
                    "ID" => $this->arForms[$formId]["SETTINGS"]["PARAMS"]["AGREEMENT_ID"],
                    "IS_CHECKED" => "Y",
                    "IS_LOADED" => "N",

                    "COMPONENT_TEMPLATE" => "b2bcabinet"
                ),
                false
            );

            $confidential = ob_get_clean() . '<br>';
        }

        return $confidential;
    }


    private function renderFormButtons($formId)
    {
        $html = '';
        if ($this->arForms[$formId]["SETTINGS"]["BUTTONS"]) {

            $html .=  $this->renderAgreement($formId);

            foreach ($this->arForms[$formId]["SETTINGS"]["BUTTONS"] as $button) {
                $html .= "<input {$this->setAttributesBtn($button)}>" . "&emsp;";
            }
        }

        return $html;
    }

    private function renderGroupInputs(array $itemRows)
    {
        $html = '';
        foreach ($itemRows as $row) {
            if (empty($row["ITEMS"])) {
                continue;
            }
            $templateRow = $row["LABEL_STYLE"] == "BASIC" ? $this->getFormInputRowBasic() : $this->getFormInputRowVertical();
            $templateInput = $row["LABEL_STYLE"] == "BASIC" ? $this->getFormInputBasic() : $this->getFormInputVertical();
            $rowItemSize = intdiv(12, count($row["ITEMS"]));

            $rowInputsHtml = '';

            foreach ($row["ITEMS"] as $item) {
                $rowInputsHtml .= $this->replaceTagsTemplate(
                    $templateInput, [
                        "#LABEL#" => $item["LABEL"] ?: '&nbsp;',
                        "#INPUT_SIZE#" => $item["INPUT_BP_SIZE"] ?: $rowItemSize,
                        "#HELPER_TEXT#" => $item["HELPER_TEXT"],
                        "#INPUT#" => $this->renderInput($item),
                    ]
                );
            }

            $html .= $this->replaceTagsTemplate(
                $templateRow, [
                    "#LABEL#" => $row["LABEL"],
                    "#LABEL_SIZE#" => $row["LABEL_BP_SIZE"],
                    "#INPUT_SIZE#" => 12 - $row["LABEL_BP_SIZE"],
                    "#INPUT#" => $rowInputsHtml,
                ]
            );
        }

        return $html;
    }

    private function renderInput($params)
    {
        $input = '';

        if (!$params) {
            return $input;
        }

        switch (strtoupper($params["INPUT_TYPE"])) {
            case "SELECT":
                $input = $this->renderInputSelect($params);
                break;
            case "CHECKBOX":
                $input = $this->renderInputCheckbox($params);
                break;
            case "RADIO":
                $input = $this->renderInputRadio($params);
                break;
            case "TEXTAREA":
                $input = $this->renderInputTextArea($params);
                break;
            case "LOCATION":
                $input = $this->renderInputLocation($params);
                break;
            case "MEDIA":
                $input = $this->renderMedia($params);
                break;
            default:
                $input = $this->renderInputGeneralType($params);
                break;
        }


        return $input;
    }


    private function renderMedia($params)
    {
        $html = '<div class="card card-body">
                                        <div class="media">
                                            <div class="mr-3">
                                                <a href="'. $params["ATTRIBUTES"]["SRC"] .'" download><i class="icon-file-text2 text-success-400 icon-2x mt-1"></i></a>
                                            </div>

                                            <div class="media-body">
                                                <h6 class="media-title font-weight-semibold">
                                                    <a href="'. $params["ATTRIBUTES"]["SRC"] .'" class="text-default" download>'
            . $params["ATTRIBUTES"]["NAME"] .
            '</a>
                                                </h6>
                                                '. $params["ATTRIBUTES"]["DESCRIPTION"] .'
                                            </div>
                                        </div>
                                    </div>';
        return $html;
    }

    private function renderInputCheckbox($params)
    {
        $html = '<div class="form-check"><label class="form-check-label">';
        $html .= '<input type="checkbox" '.$this->setAttributes($params["ATTRIBUTES"]).' class="form-check-input-styled">' . $params["LABEL"];
        $html .= '</label></div>';

        return $html;
    }

    private function renderInputRadio($params)
    {
        $html = '<div class="form-check"><label class="form-check-label">';
        $html .= '<input type="radio" '.$this->setAttributes($params["ATTRIBUTES"]).' class="form-input-styled">' . $params["LABEL"];
        $html .= '</label></div>';

        return $html;
    }

    private function renderInputLocation($params)
    {
        ob_start();

        \CSaleLocation::proxySaleAjaxLocationsComponent(
            array(
                "AJAX_CALL" => "N",
                'CITY_OUT_LOCATION' => 'Y',
                'COUNTRY_INPUT_NAME' => $params["NAME"] . '_COUNTRY',
                'CITY_INPUT_NAME' => $params["NAME"],
                'LOCATION_VALUE' => $params["VALUE"],
            ),
            array(),
            "",
            true,
            'location-block-wrapper'
        );

        return ob_get_clean();
    }

    private function renderInputTextArea($params)
    {
        $attributes = $params["ATTRIBUTES"];
        unset($attributes["VALUE"]);

        return "<textarea  {$this->setAttributes($attributes)} class=\"form-control\">" .  $params["ATTRIBUTES"]["VALUE"] . "</textarea>";
    }

    private function renderInputGeneralType($params)
    {
        return '<input type="'.strtolower($params["INPUT_TYPE"]).'" '.$this->setAttributes($params["ATTRIBUTES"]).' class="form-control">';
    }

    private function renderInputSelect($params)
    {
        $input = '<select class="form-control" '.$this->setAttributes($params["ATTRIBUTES"]).'>';

        foreach ($params["OPTIONS"] as $option) {
            $attr = $option;
            unset($attr["OPTION_NAME"]);
            $input .= "<option {$this->setAttributes($attr)}>{$option["OPTION_NAME"]}</option>";
        }
        $input .= '</select>';

        return $input;

    }

    private function renderFormBody($formId)
    {
        $html = '';
        if ($this->arForms[$formId] && $this->arForms[$formId]["SETTINGS"]) {
            $html .= $this->replaceTagsTemplate(
                $this->getFormBody(), [
                    "#FORM_GROUPS#" => $this->renderFormGroups($formId),
                    "#FORM_PARAMS#" => $this->setFormAttributes($formId),
                    "#FORM_BUTTONS#" => $this->renderFormButtons($formId),
                ]
            );
        }

        return $html;
    }

    public function renderFormGroups($formId)
    {
        $groupHtml = '';
        if ($this->arForms[$formId] && $this->arForms[$formId]["GROUPS"]) {
            foreach ($this->arForms[$formId]["GROUPS"] as $row) {

                $groupSize = intdiv(12, count($row));

                foreach ($row as $group) {
                    $groupId = $group["ID"] ?: Random::getString(5);
                    $groupCollapse = $group["COLLAPSE"] == "Y" ? $this->replaceTagsTemplate( $this->getFormGroupCollapse(), ["#GROUP_ID#" => $groupId]) : "";
                    $groupIcon = $group["ICON"] ?: "";

                    $groupHtml .= $this->replaceTagsTemplate(
                        $this->getFormGroup(),
                        [
                            "#COL_SIZE#" => $groupSize,
                            "#GROUP_ID#" => $groupId,
                            "#GROUP_TITLE#" => $this->replaceTagsTemplate(
                                $this->getFormGroupTitle(), [
                                    "#GROUP_ICON#" => $groupIcon,
                                    "#GROUP_NAME#" => $group["NAME"],
                                    "#GROUP_COLLAPSE#" => $groupCollapse
                                ]
                            ),
                            "#FORM_ACTION#" => $this->renderHeaderActions($formId),
                            "#GROUP_INPUTS#" =>  $group["ITEM_ROWS"] ? $this->renderGroupInputs($group["ITEM_ROWS"]) : '',
                        ]
                    );
                }
            }
        }

        return $groupHtml;
    }

    private function setFormAttributes($formId)
    {
        $attributes = '';

        if ($this->arForms[$formId] && $this->arForms[$formId]["SETTINGS"]["ATTRIBUTES"]) {
            foreach ($this->arForms[$formId]["SETTINGS"]["ATTRIBUTES"] as $name => $val) {
                $attributes .= ' '.strtolower($name).'="' . $val . '"';
            }
        }

        return $attributes;
    }

    private function renderFormHeader($formId)
    {
        return $this->arForms[$formId] ? $this->replaceTagsTemplate(
            $this->getFormHeader(),
            [
                "#FORM_TITLE#" => $this->arForms[$formId]["SETTINGS"]["PARAMS"]["TITLE"],
                "#FORM_ACTION#" => $this->renderHeaderActions($formId)
            ]
        ) : '';
    }

    private function renderHeaderActions($formId)
    {
        $elements = '';

        if ($this->arForms[$formId] && is_array($this->arForms[$formId]["SETTINGS"]["PARAMS"]["HEADER_ELEMENTS"])) {
            foreach ($this->arForms[$formId]["SETTINGS"]["PARAMS"]["HEADER_ELEMENTS"] as $action) {
                $elements .= $this->replaceTagsTemplate($this->getFormHeaderAction(), ["#ACTION#" => $action]);
            }
        }

        return $elements;
    }

    private function setAttributes($attributes)
    {
        $inputFields = '';
        foreach ($attributes as $name => $value) {
            if ($name == "CLASS") {
                $inputFields .= ' class="form-control '. $value . '"';
                continue;
            }

            $inputFields .= ' ' . strtolower($name) . '="' . $value . '"';
        }

        return $inputFields;
    }

    private function setAttributesBtn($attributes)
    {
        $inputFields = '';
        foreach ($attributes as $name => $value) {
            $inputFields .= ' ' . strtolower($name) . '="' . $value . '"';
        }

        return $inputFields;
    }

}