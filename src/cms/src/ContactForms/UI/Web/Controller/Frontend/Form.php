<?php

declare(strict_types=1);

namespace Tulia\Cms\ContactForms\UI\Web\Controller\Frontend;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Tulia\Cms\ContactForms\Application\Builder\BuilderInterface;
use Tulia\Cms\ContactForms\Application\Sender\FormDataExtractorInterface;
use Tulia\Cms\ContactForms\Application\Sender\SenderInterface;
use Tulia\Cms\ContactForms\Query\Enum\ScopeEnum;
use Tulia\Cms\ContactForms\Query\FinderFactoryInterface;
use Tulia\Cms\Platform\Infrastructure\Framework\Controller\AbstractController;
use Tulia\Framework\Http\Request;
use Tulia\Framework\Security\Http\Csrf\Annotation\IgnoreCsrfToken;

/**
 * @author Adam Banaszkiewicz
 */
class Form extends AbstractController
{
    /**
     * @var BuilderInterface
     */
    private $builder;

    /**
     * @var FinderFactoryInterface
     */
    private $finderFactory;

    /**
     * @param BuilderInterface $builder
     */
    public function __construct(BuilderInterface $builder, FinderFactoryInterface $finderFactory)
    {
        $this->builder = $builder;
        $this->finderFactory = $finderFactory;
    }

    /**
     * @param Request $request
     * @param string $id
     *
     * @return RedirectResponse
     *
     * @IgnoreCsrfToken
     */
    public function submit(Request $request, SenderInterface $sender, FormDataExtractorInterface $dataExtractor, string $id): RedirectResponse
    {
        $this->validateCsrfToken('contact_form_' . $id, $request->request->get('contact_form_' . $id)['_token'] ?? '');

        $model = $this->finderFactory->find($id, ScopeEnum::SINGLE);

        if ($model === null) {
            throw $this->createNotFoundException();
        }

        $form = $this->builder->build($model);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $data = $dataExtractor->extract($model, $form);

            if ($sender->send($model, $data)) {
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

        return $this->redirectToUrl($request->headers->get('referer') . '#anchor_contact_form_' . $id);
    }

    private function getErrorMessages($form) {
        $errors = [];

        foreach ($form->getErrors() as $key => $error) {
            if ($form->isRoot()) {
                $errors['#'][] = $error->getMessage();
            } else {
                $errors[] = $error->getMessage();
            }
        }

        foreach ($form->all() as $child) {
            if (!$child->isValid()) {
                $errors[$child->getName()] = $this->getErrorMessages($child);
            }
        }

        return $errors;
    }
}