<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\AbstractBaseController;
use App\Controller\Traits\GeneratesPagerTrait;
use App\Controller\Traits\HandlesFileUploadsTrait;
use App\Dto\Admin\File\AdminFileDto;
use App\Form\Admin\Files\FileDeleteType;
use App\Form\Admin\Files\FileEditType;
use App\Services\FeatureSwitcher\Service\Interfaces\FeatureSwitcherServiceInterface;
use App\Services\ManagedFiles\Domain\ManagedFile;
use App\Services\ManagedFiles\Service\Interfaces\ManagedFileServiceInterface;
use App\Services\User\Domain\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
class AdminFilesController extends AbstractBaseController
{
    use GeneratesPagerTrait;
    use HandlesFileUploadsTrait;
    public function __construct(
        private readonly EntityManagerInterface      $entityManager,
        private readonly ManagedFileServiceInterface $filesService,
        FeatureSwitcherServiceInterface              $featureSwitcherService,
    ){
        parent::__construct($featureSwitcherService);
    }

    #[Route('admin/files', name: 'admin.files.manage')]
    #[IsGranted('manage_all_files')]
    public function index(
        Request $request,
        #[MapQueryParameter] ?int $page = 1,
        #[MapQueryParameter] ?int $resultsPerPage = 100,
    ): Response {
        $files = $this->filesService->findAllPaginated(firstResult: ($resultsPerPage * ($page-1)), maxResults: $resultsPerPage);
        $pageVars = [
            'pageTitle' => 'Manage ManagedFiles',
            'resultsPerPage' => $resultsPerPage,
            'pager' => $this->generatePager(
                page: $page,
                resultsPerPage: $resultsPerPage,
                totalResults: $this->filesService->getFilesCount()
            ),
            'currentPage' => $page,
            'files' => $files,
        ];
        return $this->render('admin/files/index.html.twig', $this->populatePageVars($pageVars, $request));
    }

    #[Route('admin/files/{fileId}/edit', name: 'admin.files.edit')]
    #[IsGranted('manage_all_files')]
    public function edit(
        ManagedFile $file,
        Request $request
    ): Response {

        $dto = AdminFileDto::makeFromFile($file);
        $form = $this->createForm(FileEditType::class, $dto);
        $form->handleRequest($request);

        $user = $this->getUser();
        if (!$user instanceof User) {
            throw new AccessDeniedHttpException();
        }
        if ($form->isSubmitted() && $form->isValid()) {
            $replacementFile = $form->get('file')->getData();
            if ($replacementFile) {
                $this->filesService->uploadFile(
                    file: $replacementFile,
                    title: $dto->title,
                    description: $dto->description,
                    uploader: $user,
                    fileToReplace: $file
                );
            } else {
                $file->setTitle($dto->title);
                $file->setDescription($dto->description);
                $this->filesService->save($file);
            }

            $this->addFlash('success', 'File has been saved.');
            return $this->redirectToRoute('admin.files.manage');

        }
        $pageVars = [
            'pageTitle' => sprintf('Edit File %s', $file->getTitle()),
            'file' => $file,
            'form' => $form
        ];
        return $this->render('admin/files/edit.html.twig', $this->populatePageVars($pageVars, $request));
    }

    #[Route('admin/files/{fileId}/delete', name: 'admin.files.delete')]
    #[IsGranted('manage_all_files')]
    public function delete(
        ManagedFile $file,
        Request $request
    ): Response {

        $form = $this->createForm(FileDeleteType::class);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            $this->filesService->delete($file);
            $this->addFlash('success', 'File has been deleted.');
            return $this->redirectToRoute('admin.files.manage');

        }
        $pageVars = [
            'pageTitle' => sprintf('Really delete File %s?', $file->getTitle()),
            'file' => $file,
            'form' => $form
        ];
        return $this->render('admin/files/delete.html.twig', $this->populatePageVars($pageVars, $request));
    }


}
