<?php

namespace App\Grid;

use App\Entity\Subscription\Subscription;
use Sylius\Bundle\GridBundle\Grid\AbstractGrid;
use Sylius\Bundle\GridBundle\Builder\Field\TwigField;
use Sylius\Bundle\GridBundle\Builder\Action\ShowAction;
use Sylius\Bundle\GridBundle\Builder\Field\StringField;
use Sylius\Bundle\GridBundle\Builder\Action\CreateAction;
use Sylius\Bundle\GridBundle\Builder\Action\DeleteAction;
use Sylius\Bundle\GridBundle\Builder\Action\UpdateAction;
use Sylius\Bundle\GridBundle\Builder\Field\DateTimeField;
use Sylius\Bundle\GridBundle\Builder\Filter\StringFilter;
use Sylius\Bundle\GridBundle\Builder\GridBuilderInterface;
use Sylius\Bundle\GridBundle\Grid\ResourceAwareGridInterface;
use Sylius\Bundle\GridBundle\Builder\ActionGroup\BulkActionGroup;
use Sylius\Bundle\GridBundle\Builder\ActionGroup\ItemActionGroup;
use Sylius\Bundle\GridBundle\Builder\ActionGroup\MainActionGroup;

final class SubscriptionGrid extends AbstractGrid implements ResourceAwareGridInterface
{
    public function __construct()
    {
        // TODO inject services if required
    }

    public static function getName(): string
    {
        return 'app_admin_subscription';
    }

    public function buildGrid(GridBuilderInterface $gridBuilder): void
    {
        $gridBuilder
            ->addFilter(
                StringFilter::create('state')
            )

            // see https://github.com/Sylius/SyliusGridBundle/blob/master/docs/field_types.md
            ->addField(
                StringField::create('id')
                    ->setLabel('ID')
                    ->setSortable(true)
            )
            ->addField(
                TwigField::create('state', '@SyliusUi/Grid/Field/state.html.twig')
                    ->setLabel('State')
                    ->setSortable(true)
                    ->setOptions([
                        'template' => 'bundles/Mediproductions/Admin/Subscription/Label/State/state.html.twig',
                        'vars' => [
                            'labels' => '@SyliusAdmin/Order/Label/State',
                        ],
                    ])                             
            )
            ->addField(
                StringField::create('echeances')
                    ->setLabel('Échéances')
            )
            ->addField(
                DateTimeField::create('createdAt')
                    ->setLabel('CreatedAt')
            )
            ->addField(
                DateTimeField::create('updatedAt')
                    ->setLabel('UpdatedAt')
            )
            ->addActionGroup(
                MainActionGroup::create(
                    CreateAction::create(),
                )
            )
            ->addActionGroup(
                ItemActionGroup::create(
                    ShowAction::create(),
                    // UpdateAction::create(),
                    // DeleteAction::create()
                )
            )
            ->addActionGroup(
                BulkActionGroup::create(
                    DeleteAction::create()
                )
            )
        ;
    }

    public function getResourceClass(): string
    {
        return Subscription::class;
    }
}
