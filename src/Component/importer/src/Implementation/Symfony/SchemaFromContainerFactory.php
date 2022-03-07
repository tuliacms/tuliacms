<?php

declare(strict_types=1);

namespace Tulia\Component\Importer\Implementation\Symfony;

use Tulia\Component\Importer\Schema\Field;
use Tulia\Component\Importer\Schema\ObjectDefinition;
use Tulia\Component\Importer\Schema\Schema;

/**
 * @author Adam Banaszkiewicz
 */
class SchemaFromContainerFactory
{
    public function build(array $objects): Schema
    {
        $schema = new Schema();

        foreach ($objects as $name => $object) {
            $fields = [];

            foreach ($object['mapping'] as $code => $details) {
                $fields[$code] = new Field(
                    $code,
                    $details['type'],
                    (bool) $details['required'],
                    $details['default_value'],
                    (bool) $details['collection']
                );
            }

            $schema->addObject(new ObjectDefinition($name, $fields, $object['importer']));
        }

        return $schema;
    }
}
