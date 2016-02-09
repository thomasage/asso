<?php
namespace AppBundle\Form;

use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
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
                EntityType::class,
                array(
                    'required' => true,
                    'class' => 'AppBundle\Entity\Category',
                    'query_builder' => function (EntityRepository $er) {
                        return $er->createQueryBuilder('c')->addOrderBy('c.name', 'ASC');
                    },
                    'error_bubbling' => true,
                )
            )
            ->add(
                'amount',
                MoneyType::class,
                array(
                    'required' => true,
                    'error_bubbling' => true,
                )
            )
            ->add(
                'information',
                TextType::class,
                array(
                    'required' => false,
                    'error_bubbling' => true,
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
                'data_class' => 'AppBundle\Entity\TransactionDetail',
                'translation_domain' => 'accounting',
            )
        );
    }
}
