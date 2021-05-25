<?php

declare(strict_types=1);

namespace Tulia\Cms\EditLinks\UserInterface\Web\Frontend\FrontendToolbar;

use Tulia\Cms\FrontendToolbar\Application\Helper\HelperInterface;
use Tulia\Cms\FrontendToolbar\Application\Links\Link;
use Tulia\Cms\FrontendToolbar\Application\Links\Links;
use Tulia\Cms\FrontendToolbar\Application\Links\AbstractProvider;
use Tulia\Component\Templating\View;
use Symfony\Component\HttpFoundation\Request;

/**
 * @author Adam Banaszkiewicz
 */
class LinksProvider extends AbstractProvider
{
    private HelperInterface $helper;

    public function __construct(HelperInterface $helper)
    {
        $this->helper = $helper;
    }

    public function provideLinks(Links $links, Request $request): void
    {
        $links->add('edit_links', $this->createEditLinksLink($request));
    }

    public function provideContent(Request $request): string
    {
        return $this->helper->render(new View('@cms/edit_links/script.tpl'));
    }

    private function createEditLinksLink(Request $request): Link
    {
        $translation = $this->helper->trans('turnOn');

        if ($request->cookies->get('tulia_editlinks_show') === 'yes') {
            $translation = $this->helper->trans('turnOff');
        }

        $link = new Link(
            $this->helper->trans('editLinks') . ' - ' . $translation,
            '#',
            '<svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 122.88 122.88" xml:space="preserve"><style type="text/css">.st0{fill-rule:evenodd;clip-rule:evenodd;}</style><g><path fill="#ffffff" class="st0" d="M14.1,0h94.67c7.76,0,14.1,6.35,14.1,14.1v94.67c0,7.75-6.35,14.1-14.1,14.1H14.1c-7.75,0-14.1-6.34-14.1-14.1 V14.1C0,6.34,6.34,0,14.1,0L14.1,0z M81.35,28.38L94.1,41.14c1.68,1.68,1.68,4.44,0,6.11l-7.06,7.06L68.17,35.44l7.06-7.06 C76.91,26.7,79.66,26.7,81.35,28.38L81.35,28.38z M52.34,88.98c-5.1,1.58-10.21,3.15-15.32,4.74c-12.01,3.71-11.95,6.18-8.68-5.37 l5.16-18.2l0,0l-0.02-0.02L64.6,39.01l18.87,18.87l-31.1,31.11L52.34,88.98L52.34,88.98z M36.73,73.36l12.39,12.39 c-3.35,1.03-6.71,2.06-10.07,3.11c-7.88,2.42-7.84,4.05-5.7-3.54L36.73,73.36L36.73,73.36z"/></g></svg>'
        );

        $link->addAttribute('class', 'tulia-edit-links-toggle');

        return $link;
    }
}
