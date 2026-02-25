<?if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die(); ?>

<li class="nav-item b2b-basket-count" >
    <a href="<?= $arParams['PATH_TO_BASKET'] ?>" class="navbar-nav-link navbar-nav-link-toggler">
        <i class="icon-cart5"></i>
        <span class="badge badge-pill position-absolute bg-warning-400 ml-auto ml-md-0"><?= $arResult['NUM_PRODUCTS'] ?></span>
    </a>
</li>