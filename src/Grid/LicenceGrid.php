<?php

namespace App\Grid;

use App\Entity\Licence\Licence;
use Sylius\Bundle\GridBundle\Grid\AbstractGrid;
use Sylius\Bundle\GridBundle\Builder\Filter\Filter;
use Sylius\Bundle\GridBundle\Builder\Field\TwigField;
use Sylius\Bundle\GridBundle\Builder\Action\ShowAction;
use Sylius\Bundle\GridBundle\Builder\Field\StringField;
use Sylius\Bundle\GridBundle\Builder\Filter\DateFilter;
use Sylius\Bundle\GridBundle\Builder\Action\CreateAction;
use Sylius\Bundle\GridBundle\Builder\Action\DeleteAction;
use Sylius\Bundle\GridBundle\Builder\Action\UpdateAction;
use Sylius\Bundle\GridBundle\Builder\Field\DateTimeField;
use Sylius\Bundle\GridBundle\Builder\Filter\StringFilter;
use Sylius\Bundle\GridBundle\Builder\Filter\BooleanFilter;
use Sylius\Bundle\GridBundle\Builder\GridBuilderInterface;
use Sylius\Bundle\GridBundle\Grid\ResourceAwareGridInterface;
use Sylius\Bundle\GridBundle\Builder\ActionGroup\BulkActionGroup;
use Sylius\Bundle\GridBundle\Builder\ActionGroup\ItemActionGroup;
use Sylius\Bundle\GridBundle\Builder\ActionGroup\MainActionGroup;

final class LicenceGrid extends AbstractGrid implements ResourceAwareGridInterface
{
    public function __construct()
    {
        // TODO inject services if required
    }

    public static function getName(): string
    {
        return 'app_admin_licence';
    }

    public function buildGrid(GridBuilderInterface $gridBuilder): void
    {
        $gridBuilder

            ->addFilter(
                StringFilter::create('codeCrm')
            )
            ->addFilter(
                BooleanFilter::create('demo')
            )
            ->addFilter(
                DateFilter::create('startedAt')
            )
            ->addFilter(
                DateFilter::create('endedAt')
            )

    
            // see https://github.com/Sylius/SyliusGridBundle/blob/master/docs/field_types.md
            ->addField(
                DateTimeField::create('startedAt')
                    ->setLabel('StartedAt')
            )
            ->addField(
                DateTimeField::create('endedAt')
                    ->setLabel('EndedAt')
            )
            ->addField(
                StringField::create('platform')
                    ->setLabel('Platform')
                    ->setSortable(true)
            )
            //->addField(
            //    TwigField::create('demo', 'path/to/field/template.html.twig')
            //        ->setLabel('Demo')
            //)
            ->addField(
                StringField::create('state')
                    ->setLabel('State')
                    ->setSortable(true)
            )
            ->addField(
                StringField::create('codeCrm')
                    ->setLabel('CodeCrm')
                    ->setSortable(true)
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
                    // ShowAction::create(),
                    UpdateAction::create(),
                    DeleteAction::create()
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
        return Licence::class;
    }
}
