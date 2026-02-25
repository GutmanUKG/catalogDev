<?php
namespace Onelab\Removeuser;
/* author Gutman.V 17.09.2024
 * Изначально класс предполагал удаления пользователя
 */
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
use Bitrix\Main\Loader;
use Bitrix\Main\Mail\Event;


class Users {
    public static function DeactivateUser($ID) {
        if (!empty($ID)) {
            // Подключаем модуль main, чтобы CUser был доступен
            if (Loader::includeModule('main')) {
                $arGroups = \CUser::GetUserGroup($ID);
                if (in_array(1, $arGroups)) { // ID группы администраторов = 1
                    echo json_encode([
                        'status' => 'error',
                        'message' => 'Пользователь в группе администраторы, деактивация невозможна.'
                    ]);
                    return;
                }
                $fields = Array(
                    "ACTIVE" => "N" // Деактивация пользователя
                );

                $user = new \CUser;
                if ($user->Update($ID, $fields)) {
                    // Получаем данные пользователя
                    $rsUser = \CUser::GetByID($ID);
                    if ($arUser = $rsUser->Fetch()) {
                        // Подготавливаем данные для отправки почтового события
                        $arEventFields = array(
                            "USER_ID"        => $ID,
                            "EMAIL_TO"       => $arUser["EMAIL"],
                            "USER_NAME"      => $arUser["NAME"],
                            "USER_LAST_NAME" => $arUser["LAST_NAME"],
                            "DEACTIVATION_DATE" => date('d.m.Y H:i:s'),
                        );
                        Event::send([
                            "EVENT_NAME" => "USER_DEACTIVATED", // Почтовое событие
                            "LID" => "s1",                      // Идентификатор сайта
                            "C_FIELDS" => $arEventFields,       // Поля для письма

                        ]);

                        // Возвращаем успешный ответ
                        echo json_encode([
                            'status' => 'success',
                            'message' => 'Пользователь деактивирован и уведомление отправлено.'
                        ]);
                    }
                } else {
                    echo json_encode([
                        'status' => 'error',
                        'message' => 'Ошибка при деактивации пользователя: ' . $user->LAST_ERROR
                    ]);
                }
            } else {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Ошибка подключения модуля main'
                ]);
            }
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => 'ID пользователя пустой'
            ]);
        }
    }

    public static function ShowCommentUser($ID)
    {
          if (!empty($ID)) {
              if (Loader::includeModule('main')) {
                  $rsUser = \CUser::GetByID($ID);
                  $arUser = $rsUser->Fetch();
                  if (!empty($arUser['UF_COMMENT'])) {
                  echo json_encode([
                      'status' => 'success',
                      'message' => $arUser['UF_COMMENT']
                  ]);
                      //print_r($arUser['UF_COMMENT']);
                  }
              } else {
                  echo json_encode([
                      'status' => 'error',
                      'message' => 'Пользователь c указанным ID не найден'
                  ]);
              }
          }
    }
    public static function AddCommentUser($ID, $text)
    {
        if (!empty($ID)) {
            if (Loader::includeModule('main')) {
                $user = new \CUser;

                // Получаем текущие данные пользователя
                $rsUser = $user->GetByID($ID);
                $arUser = $rsUser->Fetch();

                // Проверяем, что пользователь найден
                if ($arUser) {
                    // Обновляем поле UF_COMMENT
                    $arFields = [
                        'UF_COMMENT' => $text,
                    ];

                    // Обновляем данные пользователя
                    $result = $user->Update($ID, $arFields);

                    if ($result) {
                        // Успешное обновление
                        echo json_encode([
                            'status' => 'success',
                            'message' => 'Комментарий добавлен,  страница будет перезагруженна'
                        ]);
                    } else {
                        // Ошибка обновления
                        echo json_encode([
                            'status' => 'error',
                            'message' => 'Ошибка обновления: ' . $user->LAST_ERROR
                        ]);
                    }
                } else {
                    // Пользователь не найден
                    echo json_encode([
                        'status' => 'error',
                        'message' => 'Пользователь не найден'
                    ]);
                }
            }
        }
    }
}
?>
