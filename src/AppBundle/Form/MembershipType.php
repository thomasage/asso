<?php
namespace AppBundle\Form;

use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MembershipType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'number',
                TextType::class,
                array(
                    'required' => false,
                    'label' => 'field.number',
                )
            )
            ->add(
                'season',
                EntityType::class,
                array(
                    'required' => true,
                    'label' => 'field.season',
                    'class' => 'AppBundle\Entity\Season',
                    'attr' => array(
                        'autofocus' => true,
                    ),
                )
            )
            ->add(
                'level',
                EntityType::class,
                array(
                    'required' => false,
                    'label' => 'field.level',
                    'class' => 'AppBundle\Entity\Level',
                    'query_builder' => function (EntityRepository $er) {
                        return $er->createQueryBuilder('l')->addOrderBy('l.name', 'ASC');
                    },
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
                'data_class' => 'AppBundle\Entity\Membership',
                'translation_domain' => 'member',
            )
        );
    }
}
