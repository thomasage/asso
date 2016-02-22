<?php
namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;

class CategoryCollectionType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'categories',
                CollectionType::class,
                array(
                    'label' => false,
                    'entry_type' => CategoryType::class,
                    'allow_add' => true,
                    'allow_delete' => true,
                    'by_reference' => true,
                )
            );
    }
}
