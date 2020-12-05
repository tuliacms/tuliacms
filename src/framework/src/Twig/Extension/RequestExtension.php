<?php

declare(strict_types=1);

namespace Tulia\Framework\Twig\Extension;

use Symfony\Component\HttpFoundation\RequestStack;
use Tulia\Framework\Http\Request;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * @author Adam Banaszkiewicz
 */
class RequestExtension extends AbstractExtension
{
    /**
     * @var RequestStack
     */
    protected $requestStack;

    /**
     * @param RequestStack $requestStack
     */
    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('file_path', function (?string $filepath) {
                return $this->getRequest()->getUriForPath($filepath);
            }, [
                'is_safe' => [ 'html' ]
            ]),
            new TwigFunction('is_homepage', function () {
                return $this->getRequest()->getContentPath() === '/';
            }, [
                'is_safe' => [ 'html' ]
            ]),
            new TwigFunction('get_flashes', function (array $types = []) {
                return $this->getFlashes($types);
            }, [
                'is_safe' => [ 'html' ]
            ]),
            new TwigFunction('flashes', function (array $types = [], array $options = []) {
                $options = array_merge($options, [
                    'html-class-replace' => [],
                    'html-class-base'    => 'alert',
                    'html-class-prefix'  => 'alert-',
                    'dismissable'        => true
                ]);

                $result = '';

                foreach ($this->getFlashes($types) as $type => $messages) {
                    foreach ($messages as $message) {
                        $classname = $options['html-class-replace'][$type] ?? $type;

                        $htmlClass = [];

                        if ($options['html-class-base']) {
                            $htmlClass[] = $options['html-class-base'];
                        }

                        $htmlClass[] = $options['html-class-prefix'].$classname;

                        if ($options['dismissable']) {
                            $htmlClass[] = 'alert-dismissible fade show';
                        }

                        $result .= '<div class="'.implode(' ', $htmlClass).'">'.$message.($options['dismissable'] ? '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>' : '' ).'</div>';
                    }
                }

                return $result;
            }, [
                'is_safe' => [ 'html' ]
            ]),
        ];
    }

    /**
     * @param array $types
     *
     * @return array
     */
    protected function getFlashes(array $types = []): array
    {
        $types    = $types === [] ? [ 'info', 'success', 'warning', 'danger' ] : $types;
        $flashbag = $this->getRequest()->getSession()->getFlashBag();
        $flashes  = [];

        foreach ($types as $type) {
            $flashes[$type] = $flashbag->get($type);
        }

        return $flashes;
    }

    /**
     * @return Request
     */
    protected function getRequest(): Request
    {
        /** @var Request $request */
        $request = $this->requestStack->getMasterRequest();

        return $request;
    }
}
