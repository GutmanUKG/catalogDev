<?php
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

$rsUsers = CUser::GetList(
    ($by = "id"), ($order = "asc"),
    ['EMAIL' => 'project.oper.mng@nt-t.kz']
);

if ($arUser = $rsUsers->Fetch()) {
    echo '<pre>';
    print_r($arUser);
    echo '</pre>';
} else {
    echo "Пользователь не найден.";
}
?>
