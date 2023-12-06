<?php

namespace App\Menu;

use Sylius\Bundle\UiBundle\Menu\Event\MenuBuilderEvent;

final class AdminMenuListener
{
    public function addAdminMenuItems(MenuBuilderEvent $event): void
    {
        $menu = $event->getMenu();
        // $office = $event->getOffice;
        $office = $menu
            ->addChild('Office')
            ->setLabel('Custom Admin Office')
        ;

        $office
            ->addChild('office', [
                'route' => 'app_admin_office_index',
            ])
            ->setLabel('Cabinets')
        ;
    }
}