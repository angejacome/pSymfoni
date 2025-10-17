<?php

namespace App\Controller\Api;

use App\Entity\Task;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/api/tasks')]
class TaskController extends AbstractController
{
    #[Route('', methods: ['GET'])]
    public function index(EntityManagerInterface $em): JsonResponse
    {
        $tasks = $em->getRepository(Task::class)->findAll();

        $data = array_map(fn(Task $t) => [
            'id' => $t->getId(),
            'title' => $t->getTitle(),
            'description' => $t->getDescription(),
            'status' => $t->getStatus(),
            'created_at' => $t->getCreatedAt()->format('Y-m-d H:i:s'),
        ], $tasks);

        return $this->json($data);
    }

    #[Route('', methods: ['POST'])]
    public function store(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (empty($data['title'])) {
            return $this->json(['error' => 'El título es obligatorio'], 400);
        }

        if (!in_array($data['status'], ['pendiente', 'en_progreso', 'completada'])) {
            return $this->json(['error' => 'Estado inválido'], 400);
        }

        $task = new Task();
        $task->setTitle($data['title']);
        $task->setDescription($data['description'] ?? null);
        $task->setStatus($data['status']);
        $task->setCreatedAt(new \DateTimeImmutable());

        $em->persist($task);
        $em->flush();

        return $this->json(['message' => 'Tarea creada', 'id' => $task->getId()]);
    }

    #[Route('/{id}', methods: ['PUT'])]
    public function update(Task $task, Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (isset($data['title']) && $data['title'] === '') {
            return $this->json(['error' => 'El título no puede estar vacío'], 400);
        }

        if (isset($data['status']) && !in_array($data['status'], ['pendiente', 'en_progreso', 'completada'])) {
            return $this->json(['error' => 'Estado inválido'], 400);
        }

        $task->setTitle($data['title'] ?? $task->getTitle());
        $task->setDescription($data['description'] ?? $task->getDescription());
        $task->setStatus($data['status'] ?? $task->getStatus());

        $em->flush();

        return $this->json(['message' => 'Tarea actualizada']);
    }

    #[Route('/{id}', methods: ['DELETE'])]
    public function delete(Task $task, EntityManagerInterface $em): JsonResponse
    {
        $em->remove($task);
        $em->flush();

        return $this->json(['message' => 'Tarea eliminada']);
    }
}
