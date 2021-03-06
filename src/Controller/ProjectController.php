<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use DateTime;

use App\Entity\WorkingDays;
use App\Entity\Project;
use App\Form\AddTimeInProjectType;
use App\Form\AddTimeInProjectWithoutEmployeeType;
use App\Form\ProjectType;
use App\Manager\AddTimeManager;
use App\Manager\ProjectManager;
use App\Repository\WorkingDaysRepository;
use App\Repository\ProjectRepository;

class ProjectController extends AbstractController
{
    private ProjectRepository $projectRepository;
    private ProjectManager $projectManager;
    private WorkingDaysRepository $workingDaysRepository;
    private AddTimeManager $addTimeManager;

    public function __construct(ProjectRepository $projectRepository,
                                ProjectManager $projectManager,
                                WorkingDaysRepository $workingDaysRepository,
                                AddTimeManager $addTimeManager)
    {
        $this->projectRepository = $projectRepository;
        $this->projectManager = $projectManager;
        $this->workingDaysRepository = $workingDaysRepository;
        $this->addTimeManager = $addTimeManager;
    }

    /**
     * @Route("/project/{page?1}", name="list_project", requirements={"page" = "\d+"},methods={"GET"})
     * @param int|null $page
     * @return Response
     */
    public function listProject(?int $page = 1): Response
    {
        $projects = $this->projectRepository->findProjectByPage($page);
        $countPage = ceil($this->projectRepository->countProjects()[1] / 10);

        return $this->render('project/listProject.html.twig', [
            'projects' => $projects,
            'countPage' => $countPage,
            'actualyPage' => $page,
            'url' => '/project/'
        ]);
    }

    /**
     * @Route("/project/edit", name="create_project")
     * @param Request $request
     * @return Response
     */
    public function createProject(Request $request): Response
    {
        $project = new Project();
        $form = $this->createForm(ProjectType::class, $project);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->projectManager->save($project);
            $this->addFlash('success', 'Project has been created !');
            return $this->redirectToRoute('list_project');
        }

        return $this->render('project/formProject.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/project/edit/{id}", name="edit_project",methods={"GET","POST"})
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function editProject(Request $request, int $id): Response
    {
        $project = $this->projectRepository->find($id);
        $form = $this->createForm(ProjectType::class, $project);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->projectManager->update();
            $this->addFlash('success', 'Project has been updated !');
            return $this->redirectToRoute('list_project');
        }

        return $this->render('project/formProject.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/project/show/{id}/{page?1}", name="show_project")
     * @param Request $request
     * @param int $id
     * @param int|null $page
     * @return Response
     */
    public function showProject(Request $request, int $id, ?int $page): Response
    {
        $project = $this->projectRepository->find($id);
        $infoEmployeeOnPrjs = $this->workingDaysRepository->findValuePersonByProject($id, $page);
        $infoCostProject = $this->workingDaysRepository->findEmployeeByProject($id);
        $url = '/project/show/' . $id . '/';
        $countPage = ceil($this->workingDaysRepository->countLineByProject($id)[1] / 5);

        return $this->render('project/detailProject.html.twig', [
            'project' => $project,
            'infoEmployeeOnPrjs' => $infoEmployeeOnPrjs,
            'infoCostProject' => $infoCostProject,
            'countPage' => $countPage,
            'actualyPage' => $page,
            'url' => $url
        ]);
    }

    /**
     * @Route("/project/push/{id}", name="push_project")
     * @param int $id
     * @return Response
     */
    public function pushProject(int $id): Response
    {
        $project = $this->projectRepository->find($id);
        $project->setDeliveryDate(new DateTime());
        $this->projectManager->update();
        $this->addFlash('success', 'Project has been delivered !');
        return $this->redirectToRoute('list_project');     
    }

}
