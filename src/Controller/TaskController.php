<?php

namespace App\Controller;

use App\Entity\Task;
use App\Form\TaskType;
use App\Repository\TaskRepository;
use App\Service\TaskService;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class TaskController extends AbstractController
{
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }
    #[Route('/tasks', name: 'task_list')]
    public function list(TaskRepository $taskRepository): Response
    {
        return $this->render('task/list.html.twig', [
            'tasks' => $taskRepository->findAll()
        ]);
    }

    #[Route('/tasks/create', name: 'task_create')]
    public function createAction(Request $request, EntityManagerInterface $em): Response
    {
        $task =  new Task();
        $form = $this->createForm(TaskType::class, $task);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $user = $this->security->getUser();

            if (!$user) {
                throw new \LogicException('L\'utilisateur doit être connecté pour créer une tâche.');
            }

            $task->setUser($user);
            $task->setCreatedAt(new DateTimeImmutable);
            $task->setDone(false);

            $em->persist($task);
            $em->flush();

            $this->addFlash('success',  'La tâche a été bien  ajoutée.');
            return  $this->redirectToRoute('task_list');
        }
        return $this->render(
            'task/create.html.twig',
            [
                'form' => $form->createView()
            ]
        );
    }


    #[Route('/tasks/{id}/edit', name: 'task_edit')]
    public function edit(Task $task, Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(TaskType::class, $task);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            $this->addFlash('success', 'La tâche a bien été modifiée.');

            return $this->redirectToRoute('task_list');
        }

        return $this->render('task/edit.html.twig', [
            'form' => $form->createView(),
            'task' => $task,
        ]);
    }

    #[Route('/tasks/{id}/toggle', name: 'task_toggle', methods: ['POST'])]
    public function toggleTaskAction(int $id, EntityManagerInterface $em): JsonResponse
    {
        $task = $em->getRepository(Task::class)->find($id);

        if (!$task) {
            return new JsonResponse(['error' => 'Tâche non trouvée'], 404);
        }

        $task->setDone(!$task->isDone());
        $em->flush();

        return new JsonResponse(['success' => true, 'isDone' => $task->isDone()]);
    }


    #[Route('/tasks/{id}/delete', name: 'task_delete', methods: ['POST'])]
    public function deleteTask(Task $task, EntityManagerInterface $em, TaskService $taskService, Request $request): Response
    {
        if (!$this->isCsrfTokenValid('delete-task-' . $task->getId(), $request->request->get('_token'))) {
            return new JsonResponse(['success' => false, 'message' => 'Token CSRF invalide.'], 400);
        }

        if (!$taskService->canDelete($task, $this->getUser())) {
            return new JsonResponse(['success' => false, 'message' => 'Vous n\'êtes pas autorisé à supprimer cette tâche.'], 403);
        }

        try {
            $em->remove($task);
            $em->flush();
            return new JsonResponse(['success' => true, 'message' => 'La tâche a bien été supprimée.']);
        } catch (\Exception $e) {
            return new JsonResponse(['success' => false, 'message' => 'Une erreur est survenue lors de la suppression de la tâche.'], 500);
        }
    }
}
