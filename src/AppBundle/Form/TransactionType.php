<?php
namespace AppBundle\Form;

use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TransactionType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'date',
                DateType::class,
                array(
                    'required' => true,
                    'label' => 'field.date',
                    'widget' => 'single_text',
                )
            )
            ->add(
                'amount',
                NumberType::class,
                array(
                    'required' => true,
                    'label' => 'field.amount',
                    'scale' => 2,
                    'grouping' => true,
                )
            )
            ->add(
                'thirdName',
                TextType::class,
                array(
                    'required' => true,
                    'label' => 'field.thirdName',
                )
            )
            ->add(
                'bankName',
                TextType::class,
                array(
                    'required' => false,
                    'label' => 'field.bankName',
                )
            )
            ->add(
                'operationNumber',
                TextType::class,
                array(
                    'required' => false,
                    'label' => 'field.operationNumber',
                )
            )
            ->add(
                'information',
                TextType::class,
                array(
                    'required' => false,
                    'label' => 'field.information',
                )
            )
            ->add(
                'paymentMethod',
                EntityType::class,
                array(
                    'required' => true,
                    'label' => 'field.paymentMethod',
                    'class' => 'AppBundle\Entity\PaymentMethod',
                    'query_builder' => function (EntityRepository $er) {
                        return $er->createQueryBuilder('pm')->addOrderBy('pm.name', 'ASC');
                    },
                )
            )
            ->add(
                'details',
                CollectionType::class,
                array(
                    'required' => false,
                    'label' => 'field.details',
                    'allow_add' => true,
                    'allow_delete' => true,
                    'by_reference' => false,
                    'entry_type' => TransactionDetailType::class,
                )
            )
            ->add(
                'copies',
                CollectionType::class,
                array(
                    'required' => false,
                    'label' => 'field.copies',
                    'allow_add' => false,
                    'allow_delete' => true,
                    'entry_type' => TransactionCopyType::class,
                )
            )
            ->add(
                'copy_add',
                FileType::class,
                array(
                    'required' => false,
                    'mapped' => false,
                    'multiple' => true,
                    'label' => 'field.copy.add',
                )
            );
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => 'AppBundle\Entity\Transaction',
                'translation_domain' => 'accounting',
            )
        );
    }
}