<?php
use Bitrix\Main\Engine\Contract\Controllerable,
    Bitrix\Main\Engine\ActionFilter,
    Sotbit\B2BCabinet\Internals\CalendarEventTable,
    Bitrix\Main\Localization\Loc;

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

Loc::loadMessages(__FILE__);

class B2BCabinetCalendar extends CBitrixComponent implements Controllerable
{
    protected $eventFields = [
        'ORDER_STATUS_CHANGE' => [
            "COLOR" => "#f58646",
            "CLASS_NAMES" => [
                "event__order-shipment"
            ],
        ],
        'ORDER_ADD' => [
            "COLOR" => "#5c6bc0",
            "CLASS_NAMES" => [
                "event__order-shipment"
            ],
        ],
        'CANCELLATION_CANCELED' => [
            "COLOR" => "#45748a",
            "CLASS_NAMES" => [
                "event__order-shipment"
            ],
        ],
        'ORDER_CANCELLED' => [
            "COLOR" => "#ef5350",
            "CLASS_NAMES" => [
                "event__order-shipment"
            ],
        ],
        'PAYMENT_CANCELED' => [
            "COLOR" => "#2196f3",
            "CLASS_NAMES" => [
                "event__order-shipment"
            ],
        ],
        'ORDER_PAID' => [
            "COLOR" => "#25b372",
            "CLASS_NAMES" => [
                "event__order-shipment"
            ],
        ],
        'SHIPMENT_CANCELED' => [
            "COLOR" => "#2cbacc",
            "CLASS_NAMES" => [
                "event__order-shipment"
            ],
        ],
        'ORDER_SHIPPED' => [
            "COLOR" => "#f35c86",
            "CLASS_NAMES" => [
                "event__order-shipment"
            ],
        ],
    ];

    private $eventList = [];
    private $orderStatusList = [];

    public function configureActions()
    {
        return [
            'getEvents' => [
                'prefilters' => [
                    new ActionFilter\Authentication(),
                    new ActionFilter\HttpMethod(
                        array(ActionFilter\HttpMethod::METHOD_GET, ActionFilter\HttpMethod::METHOD_POST)
                    ),
                    new ActionFilter\Csrf(),
                ],
                'postfilters' => []
            ]
        ];
    }

    public function onPrepareComponentParams($params)
    {
        return $params;
    }

    public function getEventsAction()
    {
        global $USER;

        $this->getOrderStatusList();
        $dbEvents = CalendarEventTable::getList(
            [
                "filter" => ["USER_ID" => $USER->GetID()]
            ]
        );

        while ($arEvent = $dbEvents->fetch()) {
            switch ($arEvent["CODE"]) {
                case "ORDER_STATUS_CHANGE":
                    $this->renderEventStatusChange($arEvent);
                    break;
                default:
                    $this->renderEvent($arEvent);
                    break;
            }
        }

        return \Bitrix\Main\Web\Json::encode($this->eventList);
    }

    public function executeComponent()
    {
        $this->includeComponentTemplate();
    }

    private function renderEvent($arEvent)
    {
        $description = $arEvent["VALUES"]["DESCRIPTION"] ? Loc::getMessage("CALENDAR_EVENT_CAUSE", ["#DESCRIPTION#" => $arEvent["VALUES"]["DESCRIPTION"]]) : "";

        $this->eventList[] = [
            "title" => htmlspecialcharsbx(Loc::getMessage("CALENDAR_EVENT_" . $arEvent["CODE"], $arEvent["VALUES"])) . $description,
            "start" => $arEvent["DATE"]->format("Y-m-d H:i:s"),
            "extendedProps" => [
                "color" => $this->eventFields[$arEvent["CODE"]]["COLOR"]
            ]
        ];
    }

    private function renderEventStatusChange($arEvent)
    {
        $obEventDate = new Bitrix\Main\Type\DateTime($arEvent["DATE"]);
        $obEventDate->add("+1 second");

        $this->eventList[] = [
            "title" => htmlspecialcharsbx(Loc::getMessage("CALENDAR_EVENT_ORDER_STATUS_CHANGE", [
                "#ORDER_ID#" => $arEvent["VALUES"]["ACCOUNT_NUMBER"],
                "#STATUS_NAME#" => $this->orderStatusList[$arEvent["VALUES"]["STATUS"]],
            ])),
            "start" => $obEventDate->format("Y-m-d H:i:s"),
            "extendedProps" => [
                "color" => $this->eventFields[$arEvent["CODE"]]["COLOR"]
            ]
        ];
    }

    private function getOrderStatusList()
    {
        if (CModule::IncludeModule("sale")) {
            $dbResult = CSaleStatus::GetList(
                [],
                [],
                false,
                false,
                ["ID", "NAME"]
            );

            while ($arStatus = $dbResult->fetch()) {
                $this->orderStatusList[$arStatus["ID"]] = $arStatus["NAME"];
            }
        }
    }
}