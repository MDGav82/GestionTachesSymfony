<?php

namespace App\Controller;

use App\Entity\Project;
use App\Entity\Task;
use App\Form\TaskType;
use App\Repository\ProjectRepository;
use App\Repository\TaskRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/task')]
final class TaskController extends AbstractController
{
    #[Route(name: 'app_task_index', methods: ['GET'])]
    public function index(TaskRepository $taskRepository): Response
    {
        return $this->render('task/index.html.twig', [
            'tasks' => $taskRepository->findAll(),
        ]);
    }

    #[Route("/project/{id}", name: 'app_task_project', methods: ['GET','POST'])]
    public function taskByProjects(Request $request, ProjectRepository $projectRepository,TaskRepository $taskRepository): Response
    {
        $id = $request->attributes->get('id');

        // Récupération du projet et des tâches correspondantes
        $project = $projectRepository->find($id);
        $tasks = $taskRepository->findBy([
            "associated_project" => $id
        ]);

        // Création d'une nouvelle tâche par exemple
        $task = new Task();

        // Passage de l'utilisateur connecté dans le formulaire
        $form = $this->createForm(TaskType::class, $task, [
            'current_user' => $this->getUser(),
        ]);

        return $this->render('task/index.html.twig', [
            'tasks' => $tasks,
            'project' => $project,
            'id' => $id,
            'form' => $form->createView(),
        ]);
    }

    #[Route("/project/state/{idProject}/{id}/{state}", name: 'app_task_project_state', methods: ['GET'])]
    public function stateBytaskProject(EntityManagerInterface $entityManager, TaskRepository $taskRepository, Request $request): Response
    {

        $state = $request->attributes->get('state');
        $id = $request->attributes->get('id');
        $idProject = $request->attributes->get('idProject');


        $task = $taskRepository->find($id);

        $task->setState($state);
        $task->updateProgressPercentage();

        $entityManager->flush();


        return $this->redirectToRoute('app_task_project', ['id' => $idProject], Response::HTTP_SEE_OTHER);

        // return new Response(null, Response::HTTP_NO_CONTENT);
    }


    #[Route('/new/{id}', name: 'app_task_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $task = new Task();
        $id = $request->attributes->get("id");
        // Dans votre contrôleur
    
        $form = $this->createForm(TaskType::class, $task, [
            'current_user' => $this->getUser(),
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($task);
            $entityManager->flush();

            return $this->redirectToRoute('app_task_project', ['id' => $id,'user' => $this->getUser()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('task/new.html.twig', [
            'id' => $id,
            'task' => $task,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_task_show', methods: ['GET'])]
    public function show(Task $task): Response
    {
        $project = $task->getAssociatedProject();

        return $this->render('task/show.html.twig', [
            'task' => $task,
            'project' => $project,
        ]);
    }


    #[Route('/{id}/edit', name: 'app_task_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Task $task, EntityManagerInterface $entityManager): Response
    {
        // Dans votre contrôleur
        $form = $this->createForm(TaskType::class, $task, [
            'current_user' => $this->getUser(), // ou la méthode adaptée pour récupérer l'utilisateur connecté
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_task_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('task/edit.html.twig', [
            'task' => $task,
            'form' => $form,
        ]);
    }

    #[Route('/{idProject}/{id}', name: 'app_task_delete', methods: ['POST'])]
    public function delete(Request $request, Task $task, EntityManagerInterface $entityManager): Response
    {
        $idProject=$request->attributes->get('idProject');
        if ($this->isCsrfTokenValid('delete' . $task->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($task);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_task_project', ['id'=>$idProject], Response::HTTP_SEE_OTHER);
    }
}
