<?if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();
/**
 * @global array $arParams
 * @global CUser $USER
 * @global CMain $APPLICATION
 * @global string $cartId
 */
?><a href="<?= $arParams['PATH_TO_BASKET'] ?>" class="navbar-nav-link">
    <span class="icon-cart5"></span>
    <span class="badge badge-pill bg-warning-400 ml-auto ml-md-0"><?= $arResult['NUM_PRODUCTS'] ?></span>
</a>

