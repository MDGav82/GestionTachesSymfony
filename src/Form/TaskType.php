<?php 

namespace App\Form;

use App\Entity\Project;
use App\Entity\Task;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class TaskType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('state', ChoiceType::class, [
                'choices'  => [
                    'Pending'      => 'pending',
                    'In Progress'  => 'in_progress',
                    'Completed'    => 'completed',
                ],
            ])
            // On déclare progress_percent comme champ caché et non mappé pour ignorer toute donnée saisie
            ->add('progress_percent', HiddenType::class, [
                'mapped' => false,
            ])
            ->add('start_date', null, [
                'widget' => 'single_text',
            ])
            ->add('end_date', null, [
                'widget' => 'single_text',
            ])
            ->add('associated_project', EntityType::class, [
                'class' => Project::class,
                // D'après vos fichiers, la propriété "name" contient le nom du projet
                'choice_label' => 'name',
            ])
            ->add('associated_user', EntityType::class, [
                'class' => User::class,
                // D'après vos fichiers, la propriété "username" contient le nom de l'utilisateur
                'choice_label' => 'email',
                'multiple' => true,
            ])
        ;

        // Ajout d'un écouteur d'événement pour définir automatiquement progress_percent
        $builder->addEventListener(FormEvents::POST_SUBMIT, function(FormEvent $event) {
            /** @var Task $task */
            $task = $event->getData();
            switch ($task->getState()) {
                case 'pending':
                    $task->setProgressPercent(0);
                    break;
                case 'in_progress':
                    $task->setProgressPercent(50);
                    break;
                case 'completed':
                    $task->setProgressPercent(100);
                    break;
            }
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Task::class,
        ]);
    }
}
