<?php
declare(strict_types=1);

namespace AppBundle\Form\Type;

use AppBundle\Entity\PaymentMethod;
use AppBundle\Entity\Transaction;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
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
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'date',
                DateType::class,
                [
                    'required' => true,
                    'label' => 'field.date',
                    'widget' => 'single_text',
                ]
            )
            ->add(
                'dateValue',
                DateType::class,
                [
                    'required' => false,
                    'label' => 'field.date_value',
                    'widget' => 'single_text',
                ]
            )
            ->add(
                'amount',
                NumberType::class,
                [
                    'required' => true,
                    'label' => 'field.amount',
                    'scale' => 2,
                    'grouping' => true,
                ]
            )
            ->add(
                'thirdName',
                TextType::class,
                [
                    'required' => true,
                    'label' => 'field.thirdName',
                ]
            )
            ->add(
                'bankName',
                TextType::class,
                [
                    'required' => false,
                    'label' => 'field.bankName',
                ]
            )
            ->add(
                'operationNumber',
                TextType::class,
                [
                    'required' => false,
                    'label' => 'field.operationNumber',
                ]
            )
            ->add(
                'information',
                TextType::class,
                [
                    'required' => false,
                    'label' => 'field.information',
                ]
            )
            ->add(
                'paymentMethod',
                EntityType::class,
                [
                    'required' => true,
                    'label' => 'field.paymentMethod',
                    'class' => PaymentMethod::class,
                    'query_builder' => static function (EntityRepository $er): QueryBuilder {
                        return $er->createQueryBuilder('pm')->addOrderBy('pm.name', 'ASC');
                    },
                ]
            )
            ->add(
                'details',
                CollectionType::class,
                [
                    'required' => false,
                    'label' => 'field.details',
                    'allow_add' => true,
                    'allow_delete' => true,
                    'by_reference' => false,
                    'entry_type' => TransactionDetailType::class,
                ]
            )
            ->add(
                'copy',
                FileType::class,
                [
                    'required' => false,
                    'label' => 'field.copy',
                    'mapped' => false,
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
                'data_class' => Transaction::class,
                'translation_domain' => 'accounting',
            ]
        );
    }
}
