<?if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();
/**
 * @global array $arParams
 * @global CUser $USER
 * @global CMain $APPLICATION
 * @global string $cartId
 */
?><a href="<?= $arParams['PATH_TO_BASKET'] ?>" class="navbar-nav-link btn btn-transparent text-white border-0 p-2 me-sm-3">
    <i class="ph ph-shopping-cart-simple"></i>
    <span class="badge bg-primary position-absolute top-0 end-0 translate-middle-top zindex-1 rounded-pill mt-1 me-1"><?= $arResult['NUM_PRODUCTS'] ?></span>
</a>

