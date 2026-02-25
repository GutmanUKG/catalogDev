<?php

namespace Sotbit\UpselingComponent;

class PrivatePriceAdapter
{

    private $privatePrice = [];


    public function __construct(array $productId)
    {
        $params = $this->generateParams();
        $products = \SotbitPrivatePriceMain::makeMainCheckFields($productId, $params);

        foreach ($products as $key => $item) {
            $this->privatePrice[$item[$params['PRODUCT_COLUMN']]] = CurrencyFormat(\CCurrencyRates::ConvertCurrency(
                $item[$params['PRICE_COLUMN']],
                $item[$params['CURRENCY_FORMAT']] ?: $item['PRICE_PRIVATE_CURRENCY'],
                $item['PRICE_PRIVATE_CURRENCY']),
                $item['PRICE_PRIVATE_CURRENCY'],
            );
        }
    }

    public function get(): array
    {
        return $this->privatePrice;
    }

    private function generateParams() {
        $settings = \SotbitPrivatePriceSettings::getInstance()->getOptions();

        $params = [
            "PRODUCT_COLUMN" => $settings["PRODUCT_COLUMN"],
            "PRICE_COLUMN" => $settings["PRICE_COLUMN"],
            "CURRENCY_FORMAT" => $settings["CURRENCY_FORMAT"],
        ];

        if ($settings['WORK_MODE']) {
            $params["ADDITIONAL_USER_FIELDS"] = array();
            $additionalUserSettings = unserialize($settings['USERS_PARAMS'], ['allowed_classes' => false]);

            if (empty(unserialize($settings['ADDITIONAL_PARAMS'], ['allowed_classes' => false])))
                return [];
            else
                $settings['ADDITIONAL_PARAMS'] = unserialize($settings['ADDITIONAL_PARAMS'], ['allowed_classes' => false]);
            foreach (unserialize($settings['USERS_PARAMS'], ['allowed_classes' => false]) as $key => $value) {
                array_push($params['ADDITIONAL_USER_FIELDS'], [$settings['ADDITIONAL_PARAMS'][$key] => $additionalUserSettings[$key]]);
            }
        } else {
            $params["ADDITIONAL_SESSIONS_FIELDS"] = array();
            $additionalSessionSettings = unserialize($settings['SESSION_KEY'], ['allowed_classes' => false]);

            if (empty(unserialize($settings['ADDITIONAL_PARAMS'], ['allowed_classes' => false])))
                return [];
            else {
                $settings['ADDITIONAL_PARAMS'] = unserialize($settings['ADDITIONAL_PARAMS'], ['allowed_classes' => false]);
                $settings['SESSION_KEY'] = unserialize($settings['SESSION_KEY'], ['allowed_classes' => false]);
            }
            foreach ($settings['ADDITIONAL_PARAMS'] as $key => $value) {
                array_push($params['ADDITIONAL_SESSIONS_FIELDS'], [$settings['ADDITIONAL_PARAMS'][$key] => $_SESSION[$additionalSessionSettings[$key]]]);
            }
        }
        return $params;
    }

    public static function privatePriceIsEnabled(): bool
    {
        if (\Bitrix\Main\Loader::includeModule('sotbit.privateprice')) {
            return \SotbitPrivatePriceSettings::getInstance()->getOption('MODULE_STATUS') === "1";
        } else {
            return 0;
        }
    }

}