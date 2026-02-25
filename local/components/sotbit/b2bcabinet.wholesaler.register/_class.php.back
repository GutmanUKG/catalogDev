<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

use Bitrix\Main\Config\Option;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Security\Random;
use Bitrix\Main\UserFieldTable;
use Bitrix\Sale\Internals\OrderPropsTable;
use Bitrix\Sale\Internals\OrderPropsVariantTable;
use Bitrix\Sale\Internals\UserPropsValueTable;
use Sotbit\B2bCabinet\Helper\Config;
use Bitrix\Sale\Internals\PersonTypeTable;

Loc::loadMessages(__FILE__);

class OrderTemplatesList extends CBitrixComponent
{

    protected $ufUserFields = [];
    protected $orderFields = [];
    protected $personType;
    protected bool $userRegistration;
    private $requiredModules = [
        'sotbit.b2bcabinet',
        'sale',
        'main'
    ];
    private $errors = [];

    public function onPrepareComponentParams($params)
    {
        $this->userRegistration = Option::get("main", "new_user_registration", "N") === "Y";
        $this->personType = unserialize(Config::get('BUYER_PERSONAL_TYPE', SITE_ID));
        $params['USER_REGISTER_DEFAULT_FIELDS'] = $params['USER_REQUIRED_DEFAULT_FIELDS'] = [
            'EMAIL'
        ];
        if (Option::get("main", "new_user_phone_auth", "N") === 'Y') {
            array_push($params['USER_REGISTER_DEFAULT_FIELDS'], 'PHONE_NUMBER');
            $params['PHONE_REGISTRATION'] = true;
        }
        if (Option::get("main", "new_user_phone_required", "N") === 'Y') {
            array_push($params['USER_REQUIRED_DEFAULT_FIELDS'], 'PHONE_NUMBER');
            $params['PHONE_REQUIRED'] = true;
        }
        return $params;
    }

    function executeComponent()
    {
        if (($module = $this->checkRequiredModules()) !== true) {
            echo Loc::getMessage('REGISTER_ERROR_MODULE_NOT_INSTALL', ['#MODULE_NAME#' => $module]);
            return;
        }

        if (!$this->userRegistration) {
            echo Loc::getMessage('REGISTER_ERROR_DISABLE_REGISTRATION');
            return;
        }

        if (!$this->personType) {
            echo Loc::getMessage('REGISTER_ERROR_NO_PERSON_TYPE');
            return;
        }

        $this->initialParams();
        $this->arResult['PERSON_TYPES'] = $this->getPersonTypes();

        if ($this->request->isPost() && $this->request->get('sotbit_b2b_register') <> '' && !$GLOBALS['USER']->IsAuthorized()) {
            $this->startRegister();
            $this->setRegiterResult();
        }

        $this->includeComponentTemplate();
    }

    protected function checkRequiredModules()
    {
        foreach ($this->requiredModules as $module) {
            if (!Loader::includeModule($module)) {
                return $module;
            }
        }

        return true;
    }

    protected function initialParams()
    {
        foreach ($this->personType as $type) {
            $this->arParams['USER_UF_FIELDS'][$type] = unserialize(Config::get('USER_DOP_FIELDS_' . $type)) ?: [];
            $this->arParams['USER_REGISTER_FIELDS'][$type] = array_unique(array_merge($this->arParams['USER_REGISTER_DEFAULT_FIELDS'],
                unserialize(Config::get('GROUP_FIELDS_' . $type)) ?: [], $this->arParams['USER_UF_FIELDS'][$type]));
            $this->arParams['USER_REQUIRED_FIELDS'][$type] = array_unique(array_merge($this->arParams['USER_REQUIRED_DEFAULT_FIELDS'],
                unserialize(Config::get('GROUP_REQUIRED_FIELDS_' . $type)) ?: []));
            $this->arParams['WHOLESALER_FIELDS'][$type] = unserialize(Config::get('GROUP_ORDER_FIELDS_' . $type)) ?: [];
            $this->arParams['WHOLESALER_UNIQUE_FIELDS'][$type] = Config::get('GROUP_UNIQUE_FIELDS_' . $type);

            $this->ufUserFields = array_diff(array_merge($this->ufUserFields, $this->arParams['USER_UF_FIELDS'][$type]),
                ['']);
            $this->orderFields[$type] = [];
            $this->orderFields[$type] = array_diff(array_merge($this->orderFields[$type],
                $this->arParams['WHOLESALER_FIELDS'][$type], [$this->arParams['WHOLESALER_UNIQUE_FIELDS'][$type]]),
                ['']);
        }

        $this->initialRegister();
        $this->initialWholesalerRegister();
    }

    private function initialRegister()
    {
        $this->arResult["USE_EMAIL_CONFIRMATION"] = Option::get("main", "new_user_registration_email_confirmation",
                "N") === "Y";

        $registerDefGroups = Option::get("main", "new_user_registration_def_group", null);
        $this->arResult["GROUP_POLICY"] = CUser::GetGroupPolicy($registerDefGroups ? explode(",",
            $registerDefGroups) : []);
        $this->arResult["USE_CAPTCHA"] = Option::get("main", "captcha_registration", "N") === "Y";
        if ($this->arResult["USE_CAPTCHA"]) {
            $this->arResult["CAPTCHA_CODE"] = htmlspecialcharsbx($GLOBALS['APPLICATION']->CaptchaGetCode());
        }
        $this->arResult['USER_REGISTER_FIELDS'] = $this->arParams['USER_REGISTER_FIELDS'];
        $this->arResult['USER_REQUIRED_FIELDS'] = $this->arParams['USER_REQUIRED_FIELDS'];

        $this->getUfUserFields();

    }

    protected function getUfUserFields()
    {
        $query = UserFieldTable::query()
            ->registerRuntimeField(UserFieldTable::getLabelsReference('', Loc::getCurrentLang()))
            ->setSelect(array_merge(['FIELD_NAME'], UserFieldTable::getLabelsSelect()))
            ->addOrder('SORT')
            ->where('ENTITY_ID', 'USER')
            ->whereIn('FIELD_NAME', $this->ufUserFields)
            ->exec();

        while ($result = $query->fetch()) {
            $this->arResult['REGISTER_UF_FIELDS'][$result['FIELD_NAME']] = $result;
        }
    }

    private function initialWholesalerRegister()
    {
        foreach ($this->orderFields as $type => $arFields) {
            if (!$arFields) {
                continue;
            }
            $query = OrderPropsTable::query()
                ->setSelect([
                    'ID',
                    'CODE',
                    'NAME',
                    'REQUIRED',
                    'SETTINGS',
                    'PERSON_TYPE_ID',
                    'DESCRIPTION',
                    'TYPE',
                    'DEFAULT_VALUE',
                    'MULTIPLE',
                    'SORT'
                ])
                ->where('PERSON_TYPE_ID', $type)
                ->whereIn('CODE', $arFields)
                ->exec();

            while ($arProperty = $query->fetch()) {
                if ($arProperty['TYPE'] == "ENUM") {
                    $variantEnumPropObj = OrderPropsVariantTable::getList([
                        'select' => [
                            'ID',
                            'NAME',
                            'VALUE',
                            'ORDER_PROPS_ID'
                        ],
                        'filter' => ['ORDER_PROPS_ID' => $arProperty['ID']]
                    ]);
                    while ($variantEnumProp = $variantEnumPropObj->fetch()) {
                        $arProperty['VARIANTS'][] = $variantEnumProp;
                    }
                }

                if ($arProperty['CODE'] === $this->arParams['WHOLESALER_UNIQUE_FIELDS'][$type]) {
                    $arProperty['REQUIRED'] = 'Y';
                }
                $this->arResult['OPT_ORDER_FIELDS'][$type][$arProperty['CODE']] = $arProperty;
            }
        }
    }

    protected function getPersonTypes()
    {
        $query = PersonTypeTable::query()
            ->setSelect(['ID', 'NAME', 'LID'])
            ->addOrder('SORT')
            ->where('PERSON_TYPE_SITE.SITE_ID', SITE_ID)
            ->whereIn('ID', $this->personType)
            ->exec();

        while ($result = $query->fetch()) {
            $personTypes[$result["ID"]] = $result;
        }

        return $personTypes ?? [];
    }

    protected function startRegister()
    {
        $this->userId = 0;
        $this->arRequestWholeseler = $this->request->get('REGISTER_WHOLESALER');
        $this->curPeronType = $this->arRequestWholeseler['TYPE'];
        $this->arRequestWholeselerFields = $this->request->get('REGISTER_WHOLESALER_OPT')[$this->curPeronType];
        $this->arRequestUserFields = $this->request->get('REGISTER_WHOLESALER_USER')[$this->curPeronType];
        $this->objWholesaler = new \Sotbit\B2BCabinet\Personal\Wholesaler($this->curPeronType);

        if (!$this->checkUserFields()) {
            return;
        }

        if (!$this->checkWholesalerFields()) {
            return;
        }

        $this->registerUser();
        $this->registerWholesaler();
    }

    private function checkUserFields()
    {
        global $USER_FIELD_MANAGER, $APPLICATION, $DB, $USER;

        if (Option::get('main', 'use_encrypted_auth', 'N') === 'Y') {
            $sec = new CRsaSecurity();
            if (($arKeys = $sec->LoadKeys())) {
                $sec->SetKeys($arKeys);
                $sec->AddToForm('regform', [
                    'REGISTER[PASSWORD]',
                    'REGISTER[CONFIRM_PASSWORD]'
                ]);
                if ($errno == CRsaSecurity::ERROR_SESS_CHECK) {
                    $this->errors[] = Loc::getMessage("main_register_sess_expired");
                } elseif ($errno < 0) {
                    $this->errors[] = Loc::getMessage("main_register_decode_err", array("#ERRCODE#" => $errno));
                }
            }
        }

        foreach ($this->arResult['USER_REGISTER_FIELDS'][$this->curPeronType] as $field) {
            if ($field === 'PERSONAL_BIRTHDAY') {
                $value = trim($this->arRequestUserFields[$field]);
                $this->arUserFields[$field] = $value ? ConvertTimeStamp(MakeTimeStamp($value,
                    'YYYY-MM-DD')) : '';
            } elseif ($field !== "PERSONAL_PHOTO" && $field !== "WORK_LOGO") {
                $this->arUserFields[$field] = trim($this->arRequestUserFields[$field]);
                if (in_array($field,
                        $this->arResult["USER_REQUIRED_FIELDS"][$this->curPeronType]) && $this->arUserFields[$field] === '') {
                    $this->errors[] = Loc::getMessage("REGISTER_FIELD_ERROR_REQUIRED",
                        ['#FIELD#' => Loc::getMessage('REGISTER_FIELD_PERSONAL_' . $field)]);
                }
            } else {
                if ($this->request->getFile('REGISTER_WHOLESALER_FILES_' . $field)) {
                    $this->arUserFields[$field] = $this->request->getFile('REGISTER_WHOLESALER_FILES_' . $field);
                    $this->arUserFields[$field]["MODULE_ID"] = "main";
                }
                if (in_array($field,
                        $this->arResult["USER_REQUIRED_FIELDS"][$this->curPeronType]) && !is_uploaded_file($this->request->getFile('REGISTER_WHOLESALER_FILES_' . $field)["tmp_name"])) {
                    $this->errors[] = Loc::getMessage("REGISTER_FIELD_ERROR_REQUIRED",
                        ['#FIELD#' => Loc::getMessage('REGISTER_FIELD_PERSONAL_' . $field)]);
                }
            }
        }

        $def_group = Option::get("main", "new_user_registration_def_group", null);
        $this->arUserFields['GROUP_ID'] = $def_group ? explode(",", $def_group) : [];

        $this->arUserFields["LOGIN"] = $this->arUserFields["EMAIL"];
        $this->arUserFields["PASSWORD"] = $this->request->get("REGISTER")["PASSWORD"];
        $this->arUserFields["CONFIRM_PASSWORD"] = $this->request->get("REGISTER")["CONFIRM_PASSWORD"];
        $this->arUserFields["CHECKWORD"] = Random::getString(32);
        $this->arUserFields["~CHECKWORD_TIME"] = $DB->CurrentTimeFunction();
        $this->arUserFields["ACTIVE"] = ($this->arResult["USE_EMAIL_CONFIRMATION"] || $this->arParams['PHONE_REQUIRED']) ? 'N' : 'Y';
        $this->arUserFields["CONFIRM_CODE"] = ($this->arResult["USE_EMAIL_CONFIRMATION"] ? Random::getString(8) : "");
        $this->arUserFields["LID"] = SITE_ID;
        $this->arUserFields["LANGUAGE_ID"] = LANGUAGE_ID;
        $this->arUserFields["USER_IP"] = $_SERVER["REMOTE_ADDR"];
        $this->arUserFields["USER_HOST"] = @gethostbyaddr($_SERVER["REMOTE_ADDR"]);

        if ($this->arUserFields["AUTO_TIME_ZONE"] <> "Y" && $this->arUserFields["AUTO_TIME_ZONE"] <> "N") {
            $this->arUserFields["AUTO_TIME_ZONE"] = "";
        }

        if (isset($this->arRequestUserFields["TIME_ZONE"])) {
            $this->arUserFields["TIME_ZONE"] = $this->arRequestUserFields["TIME_ZONE"];
        }

        $USER_FIELD_MANAGER->EditFormAddFields("USER", $this->arRequestUserFields);
        if (!$USER_FIELD_MANAGER->CheckFields("USER", 0, $this->arRequestUserFields)) {
            $this->errors[] = mb_substr($APPLICATION->GetException()->GetString(), 0, -4);
            $APPLICATION->ResetException();
        }

        if ($this->arResult["USE_CAPTCHA"]) {
            if (!$APPLICATION->CaptchaCheckCode($this->request->get("captcha_word"),
                $this->request->get("captcha_sid"))) {
                $this->errors[] = Loc::getMessage("REGISTER_WRONG_CAPTCHA");
            }
        }

        $user = new CUser();
        if (!$user->CheckFields($this->arUserFields)) {
            $this->errors[] = $user->LAST_ERROR;
        }

        if ($this->issetErrors()) {
            $this->writeErrorLog();
            return false;
        }

        return true;
    }

    private function issetErrors()
    {
        return !empty($this->errors);
    }

    private function writeErrorLog()
    {
        if (Option::get("main", "event_log_register_fail", "N") === "Y") {
            CEventLog::Log("SECURITY", "USER_REGISTER_FAIL", "main", false, implode("<br>", $this->errors));
        }
    }

    private function checkWholesalerFields()
    {
        $uniqueField = $this->arParams['WHOLESALER_UNIQUE_FIELDS'][$this->curPeronType];
        if ($uniqueField && (!isset($this->arRequestWholeselerFields[$uniqueField]) || empty($this->arRequestWholeselerFields[$uniqueField]))) {
            $this->errors[] = Loc::getMessage('REGISTER_FIELD_ERROR_REQUIRED',
                ['#FIELD#' => $this->arResult['OPT_ORDER_FIELDS'][$this->curPeronType][$uniqueField]['NAME']]);
            return false;
        }

        $arWholesalerFields['ORDER_FIELDS'] = $this->arRequestWholeselerFields;
        $this->objWholesaler->setFields($arWholesalerFields);

        if (!$this->objWholesaler->checkOrderProps()) {
            $this->errors = array_merge($this->errors, $this->objWholesaler->getError());
            return false;
        }

        if (!$this->objWholesaler->checkUniqueProfile()) {
            $this->errors[] = Loc::getMessage('REGISTER_ERROR_WHOLESALER_EXIST');
            return false;
        }

        return true;
    }

    private function registerUser()
    {
        global $APPLICATION, $USER;

        foreach (\Bitrix\Main\EventManager::getInstance()->findEventHandlers('main',
            "OnBeforeUserRegister") as $arEvent) {
            if (ExecuteModuleEventEx($arEvent, array(&$this->arUserFields)) === false) {
                if ($err = $APPLICATION->GetException()) {
                    $this->errors[] = $err->GetString();
                }
                break;
            }
        }

        if ($this->issetErrors()) {
            $this->writeErrorLog();
            return false;
        }

        $user = new CUser();
        $this->userId = $user->Add($this->arUserFields);

        if (intval($this->userId) > 0) {
            if ($this->arParams["PHONE_REGISTRATION"] && $this->arUserFields["PHONE_NUMBER"] <> '') {
                list($code, $phoneNumber) = CUser::GeneratePhoneCode($this->userId);

                $sms = new \Bitrix\Main\Sms\Event(
                    "SMS_USER_CONFIRM_NUMBER",
                    [
                        "USER_PHONE" => $phoneNumber,
                        "CODE" => $code,
                    ]
                );
                $smsResult = $sms->send(true);

                if (!$smsResult->isSuccess()) {
                    $this->errors = array_merge($this->errors, $smsResult->getErrorMessages());
                }

                $this->arResult["SHOW_SMS_FIELD"] = true;
                $this->arResult["SIGNED_DATA"] = \Bitrix\Main\Controller\PhoneAuth::signData(['phoneNumber' => $phoneNumber]);
            } elseif ($this->arUserFields["ACTIVE"] === 'Y') {
                if (!$arAuthResult = $USER->Login($this->arUserFields["LOGIN"], $this->arUserFields["PASSWORD"])) {
                    $this->errors[] = $arAuthResult;
                }
            }

            $this->arUserFields["USER_ID"] = $this->userId;

            $arEventFields = $this->arUserFields;
            unset($arEventFields["PASSWORD"]);
            unset($arEventFields["CONFIRM_PASSWORD"]);

            $event = new CEvent;
            $event->SendImmediate("NEW_USER", SITE_ID, $arEventFields);
            if ($this->arResult["USE_EMAIL_CONFIRMATION"]) {
                $event->SendImmediate("NEW_USER_CONFIRM", SITE_ID, $arEventFields);
            }

        } else {
            $this->errors[] = $user->LAST_ERROR;
        }

        if ($this->issetErrors()) {
            $this->writeErrorLog();
            return false;
        }

        if (Option::get("main", "event_log_register", "N") === "Y") {
            CEventLog::Log("SECURITY", "USER_REGISTER", "main", $this->userId);
        }

        foreach (\Bitrix\Main\EventManager::getInstance()->findEventHandlers('main',
            "OnAfterUserRegister") as $arEvent) {
            ExecuteModuleEventEx($arEvent, array(&$this->arUserFields));
        }

        $this->arResult['REGISTER_RESULT']['SUCCESS'] = true;

        return true;
    }

    private function registerWholesaler()
    {
        $this->objWholesaler->setField('USER_ID', $this->userId);
        $resultAdd = $this->objWholesaler->addBuyer();

        if (!$resultAdd && $this->objWholesaler->getError()) {
            $this->errors = array_merge($this->errors, $this->objWholesaler->getError());
        }
    }

    protected function setRegiterResult()
    {
        $this->arResult['VALUES']['WHOLESALER_FIELDS'][$this->curPeronType] = $this->arUserFields;
        $this->arResult['VALUES']['WHOLESALER_ORDER_FIELDS'][$this->curPeronType] = $this->arRequestWholeselerFields;
        $this->arResult['ERRORS'] = $this->errors;
    }
}