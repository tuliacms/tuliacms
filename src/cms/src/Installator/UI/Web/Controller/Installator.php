<?php

declare(strict_types=1);

namespace Tulia\Cms\Installator\UI\Web\Controller;

use Doctrine\DBAL\DriverManager;
use Tulia\Cms\Installator\Application\Requirements\Requirements;
use Tulia\Cms\Installator\UI\Web\Form\Installator\DatabaseForm;
use Tulia\Cms\Installator\UI\Web\Form\Installator\UserForm;
use Tulia\Cms\Installator\UI\Web\Form\Installator\WebsiteForm;
use Tulia\Cms\Platform\Infrastructure\Framework\Controller\AbstractController;
use Tulia\Cms\Website\Application\Service\BackendPrefixGenerator;
use Tulia\Component\Routing\Enum\SslModeEnum;
use Tulia\Component\Templating\ViewInterface;
use Tulia\Framework\Http\Request;
use Tulia\Framework\Security\Http\Csrf\Annotation\CsrfToken;

/**
 * @author Adam Banaszkiewicz
 */
class Installator extends AbstractController
{
    public function index(Request $request): ViewInterface
    {
        $this->finishStep($request, 'index');
        return $this->view('@cms/installator/index.tpl');
    }

    public function requirements(Request $request)
    {
        if ($this->stepFinished($request, 'index') === false) {
            return $this->redirect('installator');
        }

        $requirements    = Requirements::getRequirements($this->getParameter('kernel.project_dir'));
        $requirementsMet = Requirements::requirementsMet($requirements);

        if ($requirementsMet) {
            $this->finishStep($request, 'requirements');
        } else {
            $this->resetStep($request, 'requirements');
        }

        return $this->view('@cms/installator/requirements.tpl', [
            'requirements'   => $requirements,
            'allowGoFurther' => $requirementsMet,
        ]);
    }

    /**
     * @CsrfToken(id="database_form")
     */
    public function database(Request $request)
    {
        if ($this->stepFinished($request, 'requirements') === false) {
            return $this->redirect('installator.requirements');
        }

        $credentials = $request->getSession()->get('installator.db');

        $form = $this->createForm(DatabaseForm::class, [
            'host'     => $credentials['host'] ?? 'localhost',
            'port'     => $credentials['port'] ?? '3306',
            'prefix'   => $credentials['prefix'] ?? 'tulia_',
            'name'     => $credentials['name'] ?? '',
            'username' => $credentials['username'] ?? '',
        ]);
        $form->handleRequest($request);

        $connectionError = null;

        if ($form->isSubmitted() && $form->isValid()) {
            $credentials = $form->getData();

            $connection = DriverManager::getConnection([
                'dbname'   => $credentials['name'],
                'user'     => $credentials['username'],
                'password' => $credentials['password'],
                'host'     => $credentials['host'],
                'port'     => $credentials['port'],
                'driver'   => 'pdo_mysql',
            ]);

            try {
                $connection->connect();

                $request->getSession()->set('installator.db', $credentials);

                $this->finishStep($request, 'database');

                return $this->redirect('installator.website');
            } catch (\Exception $e) {
                if (strpos($e->getMessage(), 'No such file or directory') !== false) {
                    $connectionError = $this->trans('databaseConnectionErrorInvalidHost', [], 'installator');
                } else {
                    $connectionError = $this->trans('databaseConnectionError', [], 'installator');
                }
            }
        }

        return $this->view('@cms/installator/database.tpl', [
            'form' => $form->createView(),
            'connectionError' => $connectionError,
        ]);
    }

    /**
     * @CsrfToken(id="website_form")
     */
    public function website(Request $request)
    {
        if ($this->stepFinished($request, 'database') === false) {
            return $this->redirect('installator.database');
        }

        $credentials = $request->getSession()->get('installator.website');

        $form = $this->createForm(WebsiteForm::class, [
            'id'             => $credentials['id']             ?? $this->uuid(),
            'backend_prefix' => $credentials['backend_prefix'] ?? '/' . BackendPrefixGenerator::generate(),
            'name'           => $credentials['name']           ?? '',
            'domain'         => $credentials['domain']         ?? $request->getHttpHost(),
            'path_prefix'    => $credentials['path_prefix']    ?? '',
            'locale_prefix'  => $credentials['locale_prefix']  ?? '',
            'ssl_mode'       => $credentials['ssl_mode']       ?? SslModeEnum::ALLOWED_BOTH,
            'code'           => $credentials['code']           ?? $request->getPreferredLanguage(),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $request->getSession()->set('installator.website', $form->getData());

            $this->finishStep($request, 'website');

            return $this->redirect('installator.user');
        }

        return $this->view('@cms/installator/website.tpl', [
            'form' => $form->createView(),
            'locale_defaults' => [
                'domain' => $request->getHttpHost(),
                'locale' => $request->getPreferredLanguage(),
            ],
        ]);
    }

    /**
     * @CsrfToken(id="user_form")
     */
    public function user(Request $request)
    {
        if ($this->stepFinished($request, 'database') === false) {
            return $this->redirect('installator.database');
        }

        $credentials = $request->getSession()->get('installator.user');

        $form = $this->createForm(UserForm::class, [
            'username' => $credentials['username'] ?? '',
            'email'    => $credentials['email']    ?? '',
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $request->getSession()->set('installator.user', $form->getData());

            $this->finishStep($request, 'user');

            return $this->redirect('installator.install');
        }

        return $this->view('@cms/installator/user.tpl', [
            'form' => $form->createView(),
        ]);
    }

    public function install(Request $request)
    {
        if ($this->stepFinished($request, 'user') === false) {
            return $this->redirect('installator.user');
        }

        return $this->view('@cms/installator/install.tpl');
    }

    public function finish()
    {
        return $this->view('@cms/installator/finish.tpl');
    }

    private function stepFinished(Request $request, string $step): bool
    {
        $steps = $request->getSession()->get('installator.steps', []);

        return \is_array($steps) && \in_array($step, $steps, true);
    }

    private function finishStep(Request $request, string $step): void
    {
        $steps = $request->getSession()->get('installator.steps', []);

        if (\is_array($steps) === false) {
            $steps = [];
        }

        $steps[] = $step;

        $request->getSession()->set('installator.steps', array_unique($steps));
    }

    private function resetStep(Request $request, string $step): void
    {
        $steps = $request->getSession()->get('installator.steps', []);

        if (\is_array($steps) && ($key = array_search($step, $steps, true)) !== false) {
            unset($steps[$key]);
        }

        $request->getSession()->set('installator.steps', $steps);
    }
}
