<?php

declare(strict_types=1);

namespace Tulia\Cms\EditLinks\Infrastructure\Framework\Twig\Extension;

use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Tulia\Cms\EditLinks\Domain\Service\EditLinksCollector;
use Tulia\Component\Theme\Customizer\DetectorInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * @author Adam Banaszkiewicz
 */
class EditLinksExtension extends AbstractExtension
{
    protected TranslatorInterface $translator;

    protected EditLinksCollector $collector;

    protected AuthorizationCheckerInterface $authorizationChecker;

    protected DetectorInterface $detector;

    public function __construct(
        TranslatorInterface $translator,
        EditLinksCollector $collector,
        AuthorizationCheckerInterface $authorizationChecker,
        DetectorInterface $detector
    ) {
        $this->translator = $translator;
        $this->collector = $collector;
        $this->authorizationChecker = $authorizationChecker;
        $this->detector = $detector;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('edit_links', function ($context, $object, array $options = []) {
                $request = $context['app']->getRequest();

                if (
                    $this->detector->isCustomizerMode()
                    || $request->cookies->get('tulia_editlinks_show') !== 'yes'
                    || $this->authorizationChecker->isGranted('IS_AUTHENTICATED_FULLY') === false
                ) {
                    return '';
                }

                $links = $this->collector->collect($object, $options);

                if ($links === []) {
                    return '';
                }

                if (count($links) === 1) {
                    $link = end($links);
                    return '<div class="tulia-edit-links"><a class="btn btn-secondary" href="' . $link['link'] . '">' . $link['label'] . '</a></div>';
                }

                $html = [];

                foreach ($links as $link) {
                    $html[] = '<a class="dropdown-item" href="' . $link['link'] . '">' . $link['label'] . '</a>';
                }

                return '<div class="tulia-edit-links"><div class="dropdown">
                <a class="btn btn-secondary dropdown-toggle" href="#" data-bs-toggle="dropdown">' . $this->translator->trans('editOptions') . '</a>
                <div class="dropdown-menu">' . implode('', $html) . '</div>
            </div></div>';
            }, [
                'is_safe' => [ 'html' ],
                'needs_context' => true,
            ]),
        ];
    }
}
