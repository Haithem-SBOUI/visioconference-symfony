<?php

namespace App\Controller;

use App\Entity\Enum\MeetingStatus;
use App\Entity\Meeting;
use App\Form\MeetingType;
use App\Form\RoomFormType;
use App\Repository\MeetingRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/meeting')]
class MeetingController extends AbstractController
{
    #[Route('/', name: 'app_meeting_index', methods: ['GET'])]
    public function index(MeetingRepository $meetingRepository): Response
    {
        return $this->redirectToRoute('app_meeting_public');

    }

    #[Route('/public-meetings', name: 'app_meeting_public', methods: ['GET'])]
    public function publicMeetings(MeetingRepository $meetingRepository): Response
    {
        $meetings = $meetingRepository->findBy(['isPublic' => true]);

        return $this->render('meeting/public_meeting.html.twig', [
            'meetings' => $meetings,
        ]);
    }

    #[Route('/join-with-roomId', name: 'app_meeting_join', methods: ['GET'])]
    public function joinMeeting(Request $request, EntityManagerInterface $entityManager,): Response
    {
        $roomId = '';

        $form = $this->createForm(RoomFormType::class, ['roomId' => $roomId]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Redirect to the meeting room using the provided room ID
            $data = $form->getData();


            return $this->redirectToRoute('app_meeting_room', ['roomId' => $data->getRoomId()]);
        }

        return $this->render('meeting/join-with-roomId.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/new', name: 'app_meeting_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $meeting = new Meeting();
        $form = $this->createForm(MeetingType::class, $meeting);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $meeting->setUser($this->getUser());
            $meeting->setRoomId($this->generateRandomString());

            $entityManager->persist($meeting);
            $entityManager->flush();

            return $this->redirectToRoute('app_meeting_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('meeting/new.html.twig', [
            'meeting' => $meeting,
            'form' => $form,
        ]);
    }


    public function generateRandomString($length = 5): string
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randomString = '';

        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, strlen($characters) - 1)];
        }

        return $randomString;
    }


    #[Route('/{id}', name: 'app_meeting_show', methods: ['GET'])]
    public function show(Meeting $meeting): Response
    {
        return $this->render('meeting/show.html.twig', [
            'meeting' => $meeting,
        ]);
    }

    #[Route('/meeting-room/{roomId}', name: 'app_meeting_room', methods: ['GET'])]
    public function showMeetingRoom(string $roomId): Response
    {
        return $this->render('meeting/meeting_room.html.twig', [
            'roomId' => $roomId,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_meeting_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Meeting $meeting, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(MeetingType::class, $meeting);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_meeting_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('meeting/edit.html.twig', [
            'meeting' => $meeting,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_meeting_delete', methods: ['POST'])]
    public function delete(Request $request, Meeting $meeting, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$meeting->getId(), $request->request->get('_token'))) {
            $entityManager->remove($meeting);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_meeting_index', [], Response::HTTP_SEE_OTHER);
    }
}
