<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Form\GroupType;
use App\Entity\Group;
use App\Entity\Campain;
use App\Entity\Task;
use App\Repository\TaskRepository;
use App\Repository\GroupRepository;
use App\Repository\CampainRepository;

class GroupController extends AbstractController
{
    /**
     * @Route("/backoffice/groups", name="group")
     */
    public function index(GroupRepository $grouprepository, CampainRepository $campainrepository): Response
    {
        $groups = $grouprepository->findAll();
        foreach ($groups as $group) {
            $id = $group->getId();
            $tasks = $group->getTasks();
            $annee = $group->getYear()->getYear();
            $table_year[] = ["group_id" => $id, "year_description" => $annee];
            foreach ($tasks as $task) {
                $tache = $task->getDescription();
                $table_tache[] = ["group_id" => $id, "task_description" => $tache];
            }
        }
        return $this->render('backoffice/group.html.twig', [
            'groups' => $groups,
            'table' => $table_tache,
            'table_year' => $table_year,
            'years' => $campainrepository->findAll(),

        ]);
    }

    /*************** FONCTION FILTRE QUI MARCHE PAS ****************************/
    /**
     * @Route("/backoffice/groups/filtered", name="group_filter")
     *  */
    /* public function filter(Campain $year, Request $request, GroupRepository $grouprepository, CampainRepository $campainrepository): Response
    {

        dd('coucou');
        // $filtre = $request->request->get('year');
        // dd($filtre);
        // $groups = $grouprepository->findBy(
        //     ['year' => $filtre]
        // );

        foreach ($groups as $group) {
            $id = $group->getId();
            $tasks = $group->getTasks();
            $annee = $group->getYear()->getYear();
            $table_year[] = ["group_id" => $id, "year_description" => $annee];
            foreach ($tasks as $task) {
                $tache = $task->getDescription();
                $table_tache[] = ["group_id" => $id, "task_description" => $tache];
            }
        }
        return $this->render('backoffice/group.html.twig', [
            // 'groups' => $groups,
            'years' => $campainrepository->findAll(),
            'table' => $table_tache,
            'table_year' => $table_year,
        ]);
    }*/
    /**
     * @Route("/backoffice/groups/add", name="add_group")
     */
    public function add_group(Request $request, EntityManagerInterface $em, CampainRepository $campainrepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $group = new Group();
        // préparer l'objet formulaire
        $form = $this->createForm(GroupType::class, $group);
        // Mettre en place le gestionnaire de formulaire
        $form->handleRequest($request);
        // Si le formulaire a été soumis et que les données sont valides
        if ($form->isSubmitted() && $form->isValid()) {
            // récupérer les données soumises
            $group = $form->getData();
            $em->persist($group);
            $em->flush();
            return $this->redirectToRoute('group');
        }
        // sinon afficher le formulaire
        $years = $campainrepository->findAll();
        return $this->render('backoffice/add_group.html.twig', [
            'form' => $form->createView(),
            'years' => $years,
        ]);
    }
    /**
     * Effacer un groupe.
 
     * @Route("backoffice/group/{id}/delete", name="delete_group", methods="DELETE")
     */
    public function delete(Group $group, EntityManagerInterface $em)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $em->remove($group);
        $em->flush();
        return $this->redirectToRoute('group');
    }
    /**
     * Editer un groupe
     * 
     * @Route("backoffice/{id}/group", name="edit_group", methods={"GET","POST"})
     */
    public function edit(Request $request, Group $group, EntityManagerInterface $em, CampainRepository $campainrepository, GroupRepository $grouprepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $form = $this->createForm(GroupType::class, $group);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            return $this->redirectToRoute('group');
        }
        $groups = $grouprepository->findAll();
        $years = $campainrepository->findAll();
        return $this->render('backoffice/edit_group.html.twig', [
            'groups' => $group,
            'years' => $years,
            'form' => $form->createView(),
        ]);
    }
}