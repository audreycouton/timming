<?php

namespace App\Controller;

use App\Repository\TeacherRepository;
use App\Repository\ModuleRepository;
use App\Repository\CampainRepository;
use App\Form\ModuleType;
use App\Entity\Teacher;
use App\Entity\Module;
use App\Entity\Campain;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class ModuleController extends AbstractController
{
    /**
     * @Route("/backoffice/modules", name="modules")
     */
    public function listModules(ModuleRepository $modulerepository): Response
    {
        $modules = $modulerepository->findAll();
        $table_teach = [];
        foreach ($modules as $module) {
            $id = $module->getId();
            $teachers = $module->getTeachers();
            $annee = $module->getYear();
            $table_year[] = ["module_id" => $id, "year_description" => $annee];
            foreach ($teachers as $teach) {
                $teacher = $teach->getName();
                $table_teach[] = ["module_id" => $id, "teacher_name" => $teacher];
            }
        }
        return $this->render('backoffice/list_modules.html.twig', [
            'modules' => $modules,
            'teachers' => $table_teach,
            'table_year' => $table_year,
        ]);
    }
    /**
     * @Route("/backoffice/module/add", name="module_add", methods="get")
     */
    public function addModule(TeacherRepository $teacherRepository, CampainRepository $campainrepository)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        return $this->render('backoffice/add_module.html.twig', [
            'teachers' => $teacherRepository->findAll(),
            'years' => $campainrepository->findAll(),
        ]);
    }
    /**
     * Enregistrer un nouveau module
     * @Route("/backoffice/module/add", name="module_add_save", methods="post")
     */
    public function save(ModuleRepository $moduleRepository, EntityManagerInterface $em, Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $module = new Module();
        $module->setName($request->request->get('name'));
        $module->setSemester($request->request->get('semester'));
        foreach ($_POST["teacher"] as $id_teacher) {
            $id = $id_teacher;
            $teacher = $this->getDoctrine()->getRepository(Teacher::class)->find($id);
            $module->addTeacher($teacher);
        };
        $id = $request->request->get('year');
        $year = $this->getDoctrine()->getRepository(Campain::class)->find($id);
        $module->setYear($year);
        $em->persist($module);
        $em->flush();
        dump($_FILES);
        dump($_POST);
        return $this->redirectToRoute('modules');
    }
    /**
     * @Route("/module/{id}/delete", name="module_delete")
     */
    public function delete(Module $module, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        dump($module);
        $em->remove($module);
        $teachers = $module->getTeachers();
        foreach ($teachers as $teacher) {
            $module->removeTeacher($teacher);
        }
        $em->flush();
        return $this->redirectToRoute('modules');
    }
    /**
     * Editer un module
     * 
     * @Route("backoffice/{id}/module", name="edit_module", methods={"GET","POST"})
     */
    public function edit(Request $request, Module $module, EntityManagerInterface $em, CampainRepository $campainrepository, ModuleRepository $modulerepository, TeacherRepository $teacherrepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $form = $this->createForm(ModuleType::class, $module);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            return $this->redirectToRoute('modules');
        }
        $modules = $modulerepository->findAll();
        $years = $campainrepository->findAll();
        return $this->render('backoffice/edit_module.html.twig', [
            'modules' => $modules,
            'years' => $years,
            'teachers' => $teacherrepository->findAll(),
            'form' => $form->createView(),

        ]);
    }
}