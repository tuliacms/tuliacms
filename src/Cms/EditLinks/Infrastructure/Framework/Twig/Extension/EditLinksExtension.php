<?php

declare(strict_types=1);

namespace Tulia\Cms\EditLinks\Infrastructure\Framework\Twig\Extension;

use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Tulia\Cms\EditLinks\Application\Event\CollectEditLinksEvent;
use Tulia\Component\Theme\Customizer\DetectorInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * @author Adam Banaszkiewicz
 */
class EditLinksExtension extends AbstractExtension
{
    /**
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * @var AuthorizationCheckerInterface
     */
    protected $authorizationChecker;

    /**
     * @var DetectorInterface
     */
    protected $detector;

    /**
     * @param TranslatorInterface $translator
     * @param EventDispatcherInterface $eventDispatcher
     * @param AuthorizationCheckerInterface $authorizationChecker
     * @param DetectorInterface $detector
     */
    public function __construct(
        TranslatorInterface $translator,
        EventDispatcherInterface $eventDispatcher,
        AuthorizationCheckerInterface $authorizationChecker,
        DetectorInterface $detector
    ) {
        $this->translator = $translator;
        $this->eventDispatcher = $eventDispatcher;
        $this->authorizationChecker = $authorizationChecker;
        $this->detector = $detector;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('edit_links', function ($context, $object, array $options = []) {
                $request = $context['app']->getRequest();

                if (
                    $this->detector->isCustomizerMode()
                    || $request->cookies->get('tulia-edit-links-show') !== 'yes'
                    || $this->authorizationChecker->isGranted('IS_AUTHENTICATED_FULLY') === false) {
                    return '';
                }

                $event = new CollectEditLinksEvent($object, $options);
                $this->eventDispatcher->dispatch($event);

                $links = $event->getAll();

                if ($links === []) {
                    return '';
                }

                foreach ($links as $key => $link) {
                    $links[$key] = array_merge([
                        'label'    => '',
                        'link'     => '',
                        'priority' => 0,
                    ], $link);
                }

                if (count($links) === 1) {
                    $link = end($links);
                    return '<div class="tulia-edit-links"><a class="btn btn-secondary" href="' . $link['link'] . '">' . $link['label'] . '</a></div>';
                }

                usort($links, function ($a, $b) {
                    return $a['priority'] - $b['priority'];
                });

                $html = [];

                foreach ($links as $key => $link) {
                    $html[] = '<a class="dropdown-item" href="' . $link['link'] . '">' . $link['label'] . '</a>';
                }

                return '<div class="tulia-edit-links"><div class="dropdown">
                <a class="btn btn-secondary dropdown-toggle" href="#" data-toggle="dropdown">' . $this->translator->trans('editOptions') . '</a>
                <div class="dropdown-menu">' . implode('', $html) . '</div>
            </div></div>';
            }, [
                'is_safe' => [ 'html' ],
                'needs_context' => true,
            ]),
        ];
    }
}
