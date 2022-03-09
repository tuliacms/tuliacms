<?php

declare(strict_types=1);

namespace Tulia\Cms\ImportExport\UserInterface\Web\Controller\Backend;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Tulia\Cms\Platform\Infrastructure\Framework\Controller\AbstractController;
use Tulia\Cms\Security\Framework\Security\Http\Csrf\Annotation\CsrfToken;
use Tulia\Component\Importer\ImporterInterface;
use Tulia\Component\Templating\ViewInterface;

/**
 * @author Adam Banaszkiewicz
 */
class ImportController extends AbstractController
{
    private ImporterInterface $importer;

    public function __construct(ImporterInterface $importer)
    {
        $this->importer = $importer;
    }

    public function homepage(): ViewInterface
    {
        return $this->view('@backend/import_export/importer.tpl');
    }

    /**
     * @CsrfToken(id="import-export-import-file")
     */
    public function importFile(Request $request): Response
    {
        $this->importer->importFromFile(
            $request->files->get('file')->getPathname(),
            $request->files->get('file')->getClientOriginalName()
        );

        if ($request->query->has('return')) {
            $redirectUrl = $request->getUriForPath($request->query->get('return'));
        } else {
            $redirectUrl = $this->generateUrl('backend.import_export.importer', [], UrlGeneratorInterface::ABSOLUTE_URL);
        }

        $this->addFlash('success', $this->trans('contentTypeFileImported', [], 'import_export'));
        return $this->redirect($redirectUrl);
    }
}
