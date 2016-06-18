<?php
namespace AppBundle\Form;

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
    public function buildForm(FormBuilderInterface $builder, array $options)
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
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => 'AppBundle\Entity\TransactionDetail',
                'translation_domain' => 'accounting',
            ]
        );
    }
}
