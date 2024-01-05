<?php

namespace App\Menu;

use Sylius\Bundle\UiBundle\Menu\Event\MenuBuilderEvent;

final class AdminMenuListener
{
    public function addAdminMenuItems(MenuBuilderEvent $event): void
    {
        $menu = $event->getMenu();
        // $office = $event->getOffice;

        // MENU - Subscription
        $subscription = $menu
            ->addChild('subscription')
            ->setLabel('Manager Subscription')
        ;

        $subscription
            ->addChild('subscription', [
                'route' => 'app_admin_subscription_index',
            ])
            ->setLabel('Subscription')
        ;

        // MENU - LICENCE
        $licence = $menu
            ->addChild('licence')
            ->setLabel('Manager Licence')
        ;

        $licence
            ->addChild('licence', [
                'route' => 'app_admin_licence_index',
            ])
            ->setLabel('Licences')
        ;
        
        // MENU - OFFICE
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