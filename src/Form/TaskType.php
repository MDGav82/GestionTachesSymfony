<?php
// src/Form/TaskType.php

namespace App\Form;

use App\Entity\Task;
use App\Entity\User;
use App\Entity\Project;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TaskType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // Récupération de l'utilisateur connecté passé en option
        $currentUser = $options['current_user'];
       
      
      
        
        $builder
            ->add('name')
            ->add('state', ChoiceType::class, [
                'choices'  => [
                    'Pending'     => 'pending',
                    'In Progress' => 'in_progress',
                    'Completed'   => 'completed',
                ],
            ])
            // Champ caché pour progress_percent, non mappé pour ignorer toute saisie utilisateur
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
                // On utilise la propriété "name" pour afficher le nom du projet
                'choice_label' => 'name',
            ])
            ->add('associated_user', EntityType::class, [
                'class' => User::class,
                // On affiche l'email de l'utilisateur
                'choice_label' => 'email',
                'multiple' => true,
                'placeholder' => 'Sélectionnez un utilisateur',
                // Utilisation du currentUser pour filtrer les utilisateurs par projets
                'query_builder' => function (EntityRepository $er) use ($currentUser) {
                    $projectIds = array_map(function($project) {
                        return $project->getId();
                    }, $currentUser->getProjects()->toArray());
                    
                    return $er->createQueryBuilder('u')
                        ->innerJoin('u.projects', 'p')
                        ->where('p.id IN (:projectIds)')
                        ->setParameter('projectIds', $projectIds);
                },
            ])
        ;

        // Ajout d'un event listener pour mettre à jour progress_percent en fonction de l'état de la tâche
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

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class'   => Task::class,
            // Ajout de l'option current_user sans supprimer les autres options
            'current_user' => null,
        ]);

        // Définir le type autorisé pour current_user
        $resolver->setAllowedTypes('current_user', ['null', User::class]);
    }
}
