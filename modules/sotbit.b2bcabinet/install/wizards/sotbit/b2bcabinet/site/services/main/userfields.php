<?
if( !defined( "B_PROLOG_INCLUDED" ) || B_PROLOG_INCLUDED !== true )
    die();

use Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);

$module = 'sotbit.b2bcabinet';
CModule::includeModule('sale');
CModule::includeModule($module);
CModule::IncludeModule("iblock");
CModule::IncludeModule("catalog");

$keys = ['UF_P_MANAGER_ID', 'UF_P_MANAGER_EMAIL'];

if(!empty($keys)){
    $oUserTypeEntity    = new \CUserTypeEntity();
    foreach ($keys as $key) {
        $field = \CUserTypeEntity::GetList([], ['FIELD_NAME' => $key])->Fetch();
        if ($field['ID'] > 0) {
            $oUserTypeEntity->Update(
                $field['ID'],
                [
                    'EDIT_FORM_LABEL' => ['ru' => Loc::getMessage('EDIT_FORM_LABEL_' . $key)],
                    'LIST_COLUMN_LABEL' => ['ru' => Loc::getMessage('EDIT_FORM_LABEL_UF_' . $key)],
                    'LIST_FILTER_LABEL' => ['ru' => Loc::getMessage('LIST_FILTER_LABEL_' . $key)],
                    'ERROR_MESSAGE' => ['ru' => Loc::getMessage('ERROR_MESSAGE_' . $key)],
                    'HELP_MESSAGE' => ['ru' => Loc::getMessage('HELP_MESSAGE_' . $key)],
                ]
            );
        } else {
            $arFields = array(
                'ENTITY_ID' => 'USER',
                'FIELD_NAME' => $key,
                'USER_TYPE_ID' => 'string',
                'XML_ID' => $key,
                'SORT' => 100,
                'MULTIPLE' => 'N',
                'MANDATORY' => 'N',
                'SHOW_FILTER' => 'N',
                'SHOW_IN_LIST' => 'Y',
                'EDIT_IN_LIST' => 'Y',
                'IS_SEARCHABLE' => 'N'
            );

            $FIELD_ID = $oUserTypeEntity->Add($arFields);
        }
    }
}
?>