<?php

declare(strict_types=1);

namespace Tulia\Component\FormBuilder\Manager;

/**
 * @author Adam Banaszkiewicz
 */
interface ManagerFactoryInterface
{
    /**
     * @param object $object
     * @param string $scope
     *
     * @return ManagerInterface
     */
    public function getInstanceFor(object $object): ManagerInterface;
}
