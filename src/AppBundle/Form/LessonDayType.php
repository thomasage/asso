<?php
namespace AppBundle\Form;

use AppBundle\Entity\Attendance;
use AppBundle\Entity\Lesson;
use AppBundle\Entity\Level;
use AppBundle\Entity\Member;
use AppBundle\Entity\Season;
use AppBundle\Entity\Theme;
use Doctrine\ORM\EntityManager;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LessonDayType extends AbstractType
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
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'comment',
                TextareaType::class,
                [
                    'required' => false,
                    'label' => 'field.comment',
                    'attr' => [
                        'placeholder' => 'Commentaire facultatif',
                    ],
                ]
            )
            ->add(
                'themes',
                EntityType::class,
                [
                    'class' => Theme::class,
                    'required' => false,
                    'label' => 'field.themes',
                    'multiple' => true,
                    'expanded' => true,
                ]
            );

        $builder->addEventListener(FormEvents::PRE_SET_DATA, [$this, 'preSetData']);
    }

    /**
     * @param FormEvent $event
     * @return bool
     */
    public function preSetData(FormEvent $event)
    {
        $data = $event->getData();
        if (!$data instanceof Lesson) {
            return false;
        }

        // Level of lesson
        $level = $data->getLevel();
        if (!$level instanceof Level) {
            return false;
        }

        // Season of lesson
        $season = $this->em->getRepository(Season::class)->findByDate($data->getDate());
        if (!$season instanceof Season) {
            return false;
        }

        // Available attendances
        $attendances = [];
        foreach ($this->em->getRepository(Member::class)->findAvailableAttendances($data) as $member) {
            if (count($member->getAttendances()) > 0) {
                $attendances = array_merge($attendances, $member->getAttendances()->toArray());
            } else {
                $attendance = new Attendance();
                $attendance
                    ->setLesson($data)
                    ->setMember($member);
                $attendances[] = $attendance;
            }
        }

        // Add members to form
        $form = $event->getForm();
        $form->add(
            'attendances',
            CollectionType::class,
            [
                'entry_type' => AttendanceType::class,
                'data' => $attendances,
            ]
        );

        return true;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => Lesson::class,
                'translation_domain' => 'lesson',
            ]
        );
    }
}
