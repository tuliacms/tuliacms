<?php

declare(strict_types=1);

namespace Tulia\Cms\Settings\UserInterface\Web\Backend\Controller;

use Swift_Plugins_LoggerPlugin;
use Swift_Plugins_Loggers_ArrayLogger;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Tulia\Cms\Options\Domain\WriteModel\OptionsRepositoryInterface;
use Tulia\Cms\Platform\Infrastructure\Framework\Controller\AbstractController;
use Tulia\Cms\Platform\Infrastructure\Mail\MailerInterface;
use Tulia\Cms\Settings\Ports\Domain\Group\SettingsGroupRegistryInterface;
use Tulia\Component\Security\Http\Csrf\Annotation\CsrfToken;
use Tulia\Component\Templating\ViewInterface;

/**
 * @author Adam Banaszkiewicz
 */
class Settings extends AbstractController
{
    private SettingsGroupRegistryInterface $settings;

    private OptionsRepositoryInterface $optionsRepository;

    private FormFactoryInterface $formFactory;

    public function __construct(
        SettingsGroupRegistryInterface $settings,
        OptionsRepositoryInterface $optionsRepository,
        FormFactoryInterface $formFactory
    ) {
        $this->settings = $settings;
        $this->optionsRepository = $optionsRepository;
        $this->formFactory = $formFactory;
    }

    /**
     * @return RedirectResponse|ViewInterface
     * @CsrfToken(id="settings_form")
     */
    public function show(Request $request, ?string $group = null)
    {
        if (! $group) {
            return $this->redirectToRoute('backend.settings', ['group' => 'cms']);
        }

        if ($this->settings->hasGroup($group) === false) {
            throw $this->createNotFoundException($this->trans('settingsGroupNotFound', [], 'settings'));
        }

        $groupObj = $this->settings->getGroup($group);
        $groupObj->setFormFactory($this->formFactory);
        $groupObj->setOptions($this->optionsRepository);
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
     * @CsrfToken(id="cms_settings_test_mail")
     */
    public function sendTestEmail(Request $request, MailerInterface $mailer): JsonResponse
    {
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
            $message->setBody('<p>' . $this->trans('testMessageBody', [], 'settings') . '</p>', 'text/html');

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
