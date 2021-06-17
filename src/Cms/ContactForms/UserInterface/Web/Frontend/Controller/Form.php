<?php

declare(strict_types=1);

namespace Tulia\Cms\ContactForms\UserInterface\Web\Frontend\Controller;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Tulia\Cms\ContactForms\Ports\Domain\ReadModel\ContactFormFinderInterface;
use Tulia\Cms\ContactForms\Ports\Infrastructure\Transport\Email\SenderInterface;
use Tulia\Cms\ContactForms\Ports\UserInterface\Web\Frontend\FormBuilder\ContactFormBuilderInterface;
use Tulia\Cms\ContactForms\Ports\Domain\ReadModel\ContactFormFinderScopeEnum;
use Tulia\Cms\ContactForms\UserInterface\Web\Frontend\Service\FormDataExtractor;
use Tulia\Cms\Platform\Infrastructure\Framework\Controller\AbstractController;
use Tulia\Component\Security\Http\Csrf\Annotation\IgnoreCsrfToken;

/**
 * @author Adam Banaszkiewicz
 */
class Form extends AbstractController
{
    private ContactFormBuilderInterface $builder;

    private ContactFormFinderInterface $finder;

    private SenderInterface $sender;

    private FormDataExtractor $dataExtractor;

    public function __construct(
        ContactFormBuilderInterface $builder,
        ContactFormFinderInterface $finder,
        SenderInterface $sender,
        FormDataExtractor $dataExtractor
    ) {
        $this->builder = $builder;
        $this->finder = $finder;
        $this->sender = $sender;
        $this->dataExtractor = $dataExtractor;
    }

    /**
     * @IgnoreCsrfToken
     */
    public function submit(Request $request, string $id): RedirectResponse
    {
        if ($this->isCsrfTokenValid('contact_form_' . $id, $request->request->get('contact_form_' . $id)['_token'] ?? '') === false) {
            throw $this->createAccessDeniedException('CSRF token is not valid.');
        }

        $model = $this->finder->findOne(['id' => $id, 'fetch_fields' => true], ContactFormFinderScopeEnum::SINGLE);

        if ($model === null) {
            throw $this->createNotFoundException();
        }

        $form = $this->builder->build($model);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $data = $this->dataExtractor->extract($model, $form);

            if ($this->sender->send($model, $data)) {
                $this->setFlash(
                    'cms.form.submit_success',
                    $this->trans('formHasBeenSentThankYou', [], 'forms')
                );
            } else {
                $this->setFlash(
                    'cms.form.submit_failed',
                    $this->trans('formNotHasBeenSentTryAgain', [], 'forms')
                );
            }
        } else {
            $this->setFlash('cms.form.last_errors', json_encode($this->getErrorMessages($form)));
            $this->setFlash('cms.form.last_data', json_encode($form->getData()));
        }

        return $this->redirect($request->headers->get('referer') . '#anchor_contact_form_' . $id);
    }

    private function getErrorMessages(FormInterface $form): array
    {
        $errors = [];

        foreach ($form->getErrors() as $error) {
            if ($form->isRoot()) {
                $errors['#'][] = $error->getMessage();
            } else {
                $errors[] = $error->getMessage();
            }
        }

        foreach ($form->all() as $child) {
            if (! $child->isValid()) {
                $errors[$child->getName()] = $this->getErrorMessages($child);
            }
        }

        return $errors;
    }
}
