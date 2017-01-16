<?php
namespace AppBundle\Form;

use AppBundle\Entity\ForecastBudgetItem;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\Exception\AccessException;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ForecastBudgetItemType extends AbstractType
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
                    'label' => 'field.category',
                ]
            )
            ->add(
                'amount',
                MoneyType::class,
                [
                    'required' => true,
                    'label' => 'field.amount',
                    'grouping' => true,
                ]
            );
    }

    /**
     * @param OptionsResolver $resolver
     * @throws AccessException
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => ForecastBudgetItem::class,
                'translation_domain' => 'accounting',
            ]
        );
    }
}
