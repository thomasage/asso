<?php
declare(strict_types=1);

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;

class ForecastBudgetItemCollectionType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'items',
                CollectionType::class,
                [
                    'entry_type' => ForecastBudgetItemType::class,
                    'label' => false,
                    'allow_add' => true,
                    'allow_delete' => true,
                ]
            );
    }
}
