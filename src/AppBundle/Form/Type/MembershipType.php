<?php
declare(strict_types=1);

namespace AppBundle\Form\Type;

use AppBundle\Entity\Level;
use AppBundle\Entity\Membership;
use AppBundle\Entity\Season;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MembershipType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'number',
                TextType::class,
                [
                    'required' => false,
                    'label' => 'field.number',
                ]
            )
            ->add(
                'season',
                EntityType::class,
                [
                    'required' => true,
                    'label' => 'field.season',
                    'class' => Season::class,
                    'attr' => [
                        'autofocus' => true,
                    ],
                ]
            )
            ->add(
                'level',
                EntityType::class,
                [
                    'required' => false,
                    'label' => 'field.level',
                    'class' => Level::class,
                    'query_builder' => function (EntityRepository $er) {
                        return $er->createQueryBuilder('l')->addOrderBy('l.name', 'ASC');
                    },
                ]
            )
            ->add(
                'medicalCertificate',
                FileType::class,
                [
                    'required' => false,
                    'label' => 'field.medicalCertificate',
                    'mapped' => false,
                ]
            )
            ->add(
                'registrationForm',
                FileType::class,
                [
                    'required' => false,
                    'label' => 'field.registrationForm',
                    'mapped' => false,
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
                'data_class' => Membership::class,
                'translation_domain' => 'member',
            ]
        );
    }
}
