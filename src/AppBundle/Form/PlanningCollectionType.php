<?php
namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;

class PlanningCollectionType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'elements',
                CollectionType::class,
                array(
                    'label' => false,
                    'entry_type' => PlanningType::class,
                    'allow_add' => true,
                    'allow_delete' => true,
                    'by_reference' => true,
                )
            )
            ->add(
                'ignore',
                CollectionType::class,
                array(
                    'label' => false,
                    'entry_type' => PeriodType::class,
                    'allow_add' => true,
                    'allow_delete' => true,
                    'by_reference' => true,
                )
            );
    }
}
