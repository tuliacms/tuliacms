<?php

declare(strict_types=1);

namespace Tulia\Cms\Settings\UserInterface\Web\Controller\Backend;

use Swift_Plugins_LoggerPlugin;
use Swift_Plugins_Loggers_ArrayLogger;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tulia\Cms\Options\Application\Service\Options;
use Tulia\Cms\Platform\Infrastructure\Framework\Controller\AbstractController;
use Tulia\Cms\Platform\Infrastructure\Mail\MailerInterface;
use Tulia\Cms\Settings\RegistryInterface;
use Tulia\Component\Security\Http\Csrf\Annotation\CsrfToken;
use Tulia\Component\Security\Http\Csrf\Annotation\IgnoreCsrfToken;
use Tulia\Component\Security\Http\Csrf\Exception\RequestCsrfTokenException;
use Tulia\Component\Templating\ViewInterface;

/**
 * @author Adam Banaszkiewicz
 */
class Settings extends AbstractController
{
    protected RegistryInterface $settings;

    public function __construct(RegistryInterface $settings)
    {
        $this->settings = $settings;
    }

    /**
     * @param Request $request
     * @param FormFactoryInterface $formFactory
     * @param string $group
     *
     * @return RedirectResponse|ViewInterface
     *
     * @throws NotFoundHttpException
     *
     * @CsrfToken(id="settings_form")
     */
    public function show(Request $request, FormFactoryInterface $formFactory, Options $options, ?string $group = null)
    {
        if (!$group) {
            return $this->redirectToRoute('backend.settings', ['group' => 'cms']);
        }

        if ($this->settings->hasGroup($group) === false) {
            throw $this->createNotFoundException($this->trans('settingsGroupNotFound', [], 'settings'));
        }

        $groupObj = $this->settings->getGroup($group);
        $groupObj->setFormFactory($formFactory);
        $groupObj->setOptions($options);
        $form = $groupObj->buildForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $groupObj->saveAction($form->getData());

            $this->setFlash('success', $this->trans('settingsSaved', [], 'settings'));
            return $this->redirectToRoute('backend.settings', [ 'group' => $groupObj->getId() ]);
        }

        $view = $groupObj->buildView();
        $view['data']['form'] = $form->createView();

        return $this->view('@backend/settings/index.tpl', [
            'form'   => $view['data']['form'],
            'group'  => $groupObj,
            'groups' => $this->settings->getGroups(),
            'view'   => [
                'name' => $view['view'],
                'data' => $view['data'],
            ],
        ]);
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     *
     * @IgnoreCsrfToken
     */
    public function sendTestEmail(Request $request, MailerInterface $mailer): JsonResponse
    {
        try {
            $this->validateCsrfToken('cms-settings-test-mail', $request->request->get('_token'));
        } catch (RequestCsrfTokenException $e) {
            return $this->responseJson([
                'message' => 'Invalid CSRF token.',
                'status'  => 'error',
                'log'     => '',
            ]);
        }

        if (filter_var($request->request->get('recipient'), FILTER_VALIDATE_EMAIL) === false) {
            return $this->responseJson([
                'message' => $this->trans('pleaseTypeValidEmailAddress', [], 'settings'),
                'status'  => 'error',
                'log'     => '',
            ]);
        }

        try {
            $message = $mailer->createMessage($this->trans('testMessageSubject', [], 'settings'));
            $message->setTo($request->request->get('recipient'));
            $message->setBody('<p>'.$this->trans('testMessageBody', [], 'settings').'</p>', 'text/html');

            $logger = new Swift_Plugins_Loggers_ArrayLogger;
            $mailer->getMailer()->registerPlugin(new Swift_Plugins_LoggerPlugin($logger));

            $sent = $mailer->send($message);
            $log  = $logger->dump();

            if ($sent) {
                $message = $this->trans('testMessageSentSuccessfull', [], 'settings');
                $status  = 'success';
            } else {
                $message = $this->trans('testMessageNotSendCheckLog', [], 'settings');
                $status  = 'error';
            }
        } catch(\Exception $e) {
            $message = $this->trans('testMessageNotSendCheckLog', [], 'settings');
            $status  = 'error';
            $log     = 'Exception message: '.$e->getMessage();
        }

        return $this->responseJson([
            'message' => $message,
            'status'  => $status,
            'log'     => $log,
        ]);
    }
}
