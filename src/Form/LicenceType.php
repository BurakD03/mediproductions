<?php

namespace App\Form;

use App\Entity\Order\Order;
use App\Entity\Licence\Licence;
use Doctrine\ORM\Mapping\Entity;
use Symfony\Component\Form\AbstractType;
use App\Form\Type\OrderAutocompleteChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Sylius\Bundle\ResourceBundle\Form\Type\ResourceAutocompleteChoiceType;

class LicenceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('startedAt',  DateType::class, [
                'widget' => 'single_text',
                'attr' => [
                    'placeholder' => 'Start',
                ]
            ])
            ->add('endedAt',  DateType::class, [
                'widget' => 'single_text',
                'attr' => [
                    'placeholder' => 'End',
                ]
            ])
            ->add('platform',  ChoiceType::class, [
                'choices'  => [
                    'CRM' => 'CRM',
                    'Shop' => 'Shop',
                ],
            ])
            ->add('demo')
            ->add('state', ChoiceType::class, [
                'choices'  => [
                    'In progress' => 'In progress',
                    'Completed' => 'Completed',
                ],
            ])
            // ->add('codeCrm')
            ->add('codeCrm')
            ->add('syliusOrder',EntityType::class, [
                'class' => Order::class,
                'choice_label' => 'number'
            ])
            ->add('syliusProductVariant', OrderAutocompleteChoiceType::class, [
                // 'class' => Order::class,
                // 'choice_label' => 'number',
                // 'placeholder' => 'Choose an order',
                    // 'label' => 'App\Entity\Licence\Licence',
                    'resource' => 'app.licence',
                    'choice_name' => 'code',
                    'choice_value' => 'id',
                    'attr' => [
                        'class' => 'ui search dropdown', // Ajoutez la classe CSS pour Semantic UI dropdown
                    ],
            ])
        ;

        // $builder
        //     ->get('customer')->addModelTransformer(
        //     new ReversedTransformer(
        //         new ResourceToIdentifierTransformer($this->customerRepository, 'id')
        //     )
        //  )->addModelTransformer(
        //      new ResourceToIdentifierTransformer($this->customerRepository, 'id')
        //  );
        
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Licence::class,
        ]);
    }
}
