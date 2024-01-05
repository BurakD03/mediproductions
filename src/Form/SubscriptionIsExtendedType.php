<?php

namespace App\Form;

use App\Entity\Order\Order;
use App\Entity\Subscription\Subscription;
use App\Entity\Product\ProductVariant;
use Doctrine\ORM\Mapping\Entity;
use Symfony\Component\Form\AbstractType;
use App\Form\Type\OrderAutocompleteChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;


class SubscriptionIsExtendedType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('isExtended',  CheckboxType::class, [
                'label' => 'Is Extended ',

            ])

        ;
        
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Subscription::class,
        ]);
    }
}
