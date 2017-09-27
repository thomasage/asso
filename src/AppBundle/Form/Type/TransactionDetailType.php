<?php
declare(strict_types=1);

namespace AppBundle\Form\Type;

use AppBundle\Entity\TransactionDetail;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TransactionDetailType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'category',
                TextType::class,
                [
                    'required' => true,
                    'error_bubbling' => true,
                ]
            )
            ->add(
                'amount',
                MoneyType::class,
                [
                    'required' => true,
                    'error_bubbling' => true,
                ]
            )
            ->add(
                'information',
                TextType::class,
                [
                    'required' => false,
                    'error_bubbling' => true,
                ]
            );
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
                'data_class' => TransactionDetail::class,
                'translation_domain' => 'accounting',
            ]
        );
    }
}
