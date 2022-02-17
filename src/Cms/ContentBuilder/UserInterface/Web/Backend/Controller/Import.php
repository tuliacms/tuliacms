<?php

declare(strict_types=1);

namespace Tulia\Cms\ContentBuilder\UserInterface\Web\Backend\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Tulia\Cms\ContentBuilder\Domain\WriteModel\ContentType\Service\Importer;
use Tulia\Cms\Platform\Infrastructure\Framework\Controller\AbstractController;
use Tulia\Cms\Security\Framework\Security\Http\Csrf\Annotation\CsrfToken;

/**
 * @author Adam Banaszkiewicz
 */
class Import extends AbstractController
{
    private Importer $importer;

    public function __construct(Importer $importer)
    {
        $this->importer = $importer;
    }

    /**
     * @CsrfToken(id="content-builder-import-file")
     */
    public function importFile(Request $request): Response
    {
        $this->importer->importFromFile(
            $request->files->get('file')->getPathname(),
            $request->files->get('file')->getClientOriginalExtension()
        );

        $this->addFlash('success', $this->trans('contentTypeFileImported', [], 'content_builder'));
        return $this->redirectToRoute('backend.content_builder.homepage');
    }
}
