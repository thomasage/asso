<?php
declare(strict_types=1);

namespace AppBundle\Form\Type;

use AppBundle\Entity\Season;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class StatMemberSignatureType extends AbstractType
{
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $seasons = $this->em->getRepository(Season::class)->choicesList();

        $builder
            ->add(
                'season',
                ChoiceType::class,
                [
                    'label' => 'field.season',
                    'required' => true,
                    'choices' => $seasons,
                ]
            )
            ->add(
                'title',
                TextType::class,
                [
                    'label' => 'field.title',
                    'required' => true,
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
                'translation_domain' => 'stat',
            ]
        );
    }
}
