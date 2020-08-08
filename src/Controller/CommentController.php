<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Form\CommentType;
use App\Repository\CommentRepository;
use App\Repository\TrickRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

/**
 * @Route("/comment")
 */
class CommentController extends AbstractController
{
    /**
     * @Route("/admin/new/{idTrick}", name="comment_new", methods={"GET","POST"})
     */
    public function new(Request $request, Security $security, $idTrick, TrickRepository $trickRepository): Response
    {
        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);
        $trick = $trickRepository->findOneBy(['id' => $idTrick]);

        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setCreatedAt(new \DateTime());
            $comment->setTrick($trick);
            $comment->setUser($security->getUser());

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($comment);
            $entityManager->flush();

            return $this->redirectToRoute('trick_show', ['id' => $idTrick]);
        }

        $this->addFlash('danger', 'Une erreur est survenue');

        return $this->redirectToRoute('trick_show', ['id' => $idTrick]);
    }

    /**
     * @Route("/charger-commentaires/{idTrick}/{index}", name="comments_load", methods={"GET"})
     */
    public function loadComments($idTrick, $index, CommentRepository $commentRepository): Response
    {
        $comments = $commentRepository->getSome($idTrick, 5, $index);

        $datas = [];

        foreach ($comments as $comment) {
            $data["userAvatar"] = $comment->getUser()->getAvatar();
            $data["userName"] = $comment->getUser()->getUserName();
            $data["content"] = $comment->getContent();

            array_push($datas, $data);
        }

        return $this->json($datas, 200);
    }
}
