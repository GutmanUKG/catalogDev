<?php
$aMenuLinks = [
    'ORGANIZATION' => [
        "Организации",
        "/b2bcabinet/personal/companies/index.php",
        [
            "/b2bcabinet/personal/companies/add.php",
            "/b2bcabinet/personal/companies/profile_detail.php",
            "/b2bcabinet/personal/companies/profile_list.php"
        ],
        [
            'ICON_CLASS' => 'icon-collaboration'
        ],
        ""
    ],
    'STAFF' => [
        "Сотрудники",
        "/b2bcabinet/personal/staff/index.php",
        [],
        [
            'ICON_CLASS' => 'icon-person'
        ],
        ""
    ],
    'SCORE' => [
        "Личный счет",
        "/b2bcabinet/personal/account/index.php",
        [],
        [
            'ICON_CLASS' => 'icon-credit-card'
        ],
        ""
    ],
];

if (defined("EXTENDED_VERSION_COMPANIES") && EXTENDED_VERSION_COMPANIES != "Y"){
    unset($aMenuLinks['STAFF']);
    $aMenuLinks['ORGANIZATION'] = [
        "Организации",
        "/b2bcabinet/personal/buyer/index.php",
        [
            "/b2bcabinet/personal/buyer/add.php",
            "/b2bcabinet/personal/buyer/profile_detail.php",
            "/b2bcabinet/personal/buyer/profile_list.php"
        ],
        [
            'ICON_CLASS' => 'icon-collaboration'
        ],
        ""
    ];
}

?>