<?php
namespace Sotbit\B2BCabinet\Shop;

use Bitrix\Main\Engine\CurrentUser;
use Bitrix\Main\Loader;

use Bitrix\Main\Context;
use Bitrix\Sale\Internals as Sale;

class Discount implements \IteratorAggregate {

    private $dicontsDispaly = [];

    public static function new(): self
    {
        $context = Context::getCurrent();
        $user = CurrentUser::get();
        return new Self($user, $context);
    }

    public function __construct(CurrentUser $user, Context $contex)
    {
        if(!Loader::includeModule('sale')) {
            return;
        }

        $discontIdForUser = Sale\DiscountGroupTable::query()
            ->addSelect('DISCOUNT_ID')
            ->whereIn('GROUP_ID', $user->getUserGroups())
            ->addGroup('DISCOUNT_ID')
            ->fetchAll()
        ;

        if (count($discontIdForUser) === 0) {
            return;
        }

        $result = Sale\DiscountTable::query()
            ->setSelect(['NAME'])
            ->whereIn('ID', array_column($discontIdForUser, 'DISCOUNT_ID'))
            ->where('ACTIVE', 'Y')
            ->where('LID', $contex->getSite())
            ->addOrder('SORT', 'ASC')
            ->exec()
        ;

        while ($dicont = $result->fetch()) {
            $this->dicontsDispaly[] = [
                'TITLE' => $dicont['NAME'],
            ];
        }
    }

    public function getIterator(): \Traversable
    {
        return new \ArrayIterator($this->dicontsDispaly);
    }

}


// $joinFilter = Join::on('this.ID', "ref.USER_PROPS_ID");

// $profile = UserPropsTable::query()
//     ->setSelect(['*'])
//     ->where('PERSON_TYPE_ID', $buerType)
//     ->registerRuntimeField(new Reference('VALUE', UserPropsValueTable::class, $joinFilter))
//     ->where('VALUE.VALUE', $companyProps[$innPropsId['ID']]['VALUE'])
//     ->fetch()
// ;