<?php

declare(strict_types=1);

namespace Tulia\Cms\Theme\Infrastructure\Cms\FrontendToolbar;

use Tulia\Cms\FrontendToolbar\Application\Helper\HelperInterface;
use Tulia\Cms\FrontendToolbar\Application\Links\Link;
use Tulia\Cms\FrontendToolbar\Application\Links\Links;
use Tulia\Cms\FrontendToolbar\Application\Links\AbstractProvider;
use Tulia\Framework\Http\Request;

/**
 * @author Adam Banaszkiewicz
 */
class LinksProvider extends AbstractProvider
{
    /**
     * @var HelperInterface
     */
    private $helper;

    public function __construct(HelperInterface $helper)
    {
        $this->helper = $helper;
    }

    public function provideLinks(Links $links, Request $request): void
    {
        $links->add('theme.customize', $this->createCustomizeLink($request));
    }

    private function createCustomizeLink(Request $request): Link
    {
        $parameters = [];
        $url = $request->getRequestUri();

        if ($url !== '/') {
            $parameters = [
                'open' => $url,
                'returnUrl' => $url
            ];
        }

        return new Link(
            $this->helper->trans('customize'),
            $this->helper->generateUrl('backend.theme.customize.current', $parameters),
            '<svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 122.88 118.3" xml:space="preserve"><style type="text/css">.st0{fill-rule:evenodd;clip-rule:evenodd;}</style><g><path fill="#fff" class="st0" d="M7.51,0h107.85c2.05,0,3.93,0.85,5.29,2.21v0l0.01,0.01l0.01,0.01l0.01,0.01c1.36,1.37,2.2,3.24,2.2,5.29 v103.28c0,2.07-0.85,3.95-2.21,5.31c-1.36,1.36-3.24,2.21-5.31,2.21H7.51c-2.05,0-3.93-0.84-5.3-2.21l-0.01-0.01l-0.01-0.01 l-0.01-0.01c-1.36-1.37-2.2-3.24-2.2-5.29V7.51C0,5.44,0.84,3.56,2.2,2.2c0.08-0.08,0.16-0.16,0.25-0.23C3.79,0.75,5.57,0,7.51,0 L7.51,0z M65.79,98.75c-1.58,0-2.86-1.39-2.86-3.11c0-1.72,1.28-3.11,2.86-3.11h35.22c1.58,0,2.86,1.39,2.86,3.11 c0,1.72-1.28,3.11-2.86,3.11H65.79L65.79,98.75z M20.82,98.75c-1.56,0-2.83-1.39-2.83-3.11c0-1.72,1.27-3.11,2.83-3.11h32.65 c1.56,0,2.83,1.39,2.83,3.11c0,1.72-1.27,3.11-2.83,3.11H20.82L20.82,98.75z M19.69,85.16c-1.56,0-2.83-1.39-2.83-3.11 c0-1.72,1.27-3.11,2.83-3.11h32.65c1.56,0,2.83,1.39,2.83,3.11c0,1.72-1.27,3.11-2.83,3.11H19.69L19.69,85.16z M65.79,85.16 c-1.58,0-2.86-1.39-2.86-3.11c0-1.72,1.28-3.11,2.86-3.11h35.22c1.58,0,2.86,1.39,2.86,3.11c0,1.72-1.28,3.11-2.86,3.11H65.79 L65.79,85.16z M17.59,34.77h85.94v33.65H17.59V34.77L17.59,34.77z M116.09,26.93c-0.24,0.04-0.48,0.06-0.72,0.06H7.51 c-0.25,0-0.49-0.02-0.72-0.06v83.86c0,0.2,0.08,0.38,0.2,0.51l0,0l0.01,0.01c0.13,0.13,0.3,0.2,0.51,0.2h107.85 c0.19,0,0.37-0.08,0.51-0.22c0.13-0.13,0.22-0.31,0.22-0.51V26.93L116.09,26.93z M50.12,9.7c2.7,0,4.88,2.19,4.88,4.88 s-2.19,4.88-4.88,4.88s-4.88-2.19-4.88-4.88S47.43,9.7,50.12,9.7L50.12,9.7z M33.05,9.7c2.7,0,4.88,2.19,4.88,4.88 s-2.19,4.88-4.88,4.88c-2.7,0-4.88-2.19-4.88-4.88S30.36,9.7,33.05,9.7L33.05,9.7z M15.99,9.7c2.7,0,4.88,2.19,4.88,4.88 s-2.19,4.88-4.88,4.88c-2.7,0-4.88-2.19-4.88-4.88S13.29,9.7,15.99,9.7L15.99,9.7z"></path></g></svg>'
        );
    }
}
