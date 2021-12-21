<?php

namespace Zenstruck\Foundry\Test;

use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Console\Application;

/**
 * @internal
 */
final class ODMSchemaResetter extends AbstractSchemaResetter
{
    /** @var ManagerRegistry */
    private $registry;

    public function __construct(ManagerRegistry $registry)
    {
        $this->registry = $registry;
    }

    public function resetSchema(): void
    {
        foreach ($this->objectManagersToReset() as $managerName) {
            $manager = $this->registry->getManager($managerName);
            $metadatas = $manager->getMetadataFactory()->getAllMetadata();
            foreach ($metadatas as $metadata) {
                if ($metadata->isMappedSuperclass) {
                    continue;
                }

                $manager->getDocumentCollection($metadata->name)->drop();
            }

            $manager->getSchemaManager()->ensureIndexes();
        }
    }

    /** @return list<string> */
    private function objectManagersToReset(): array
    {
        return [$this->registry->getDefaultManagerName()];
    }
}
