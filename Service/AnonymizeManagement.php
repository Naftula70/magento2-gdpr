<?php
/**
 * Copyright © 2018 OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Gdpr\Service;

use Magento\Framework\ObjectManager\TMap;

/**
 * Class AnonymizeManagement
 * @api
 */
class AnonymizeManagement
{
    /**
     * @var \Magento\Framework\ObjectManager\TMap
     */
    private $processorPool;

    /**
     * @param \Magento\Framework\ObjectManager\TMap $processorPool
     */
    public function __construct(
        TMap $processorPool
    ) {
        $this->processorPool = $processorPool;
    }

    /**
     * Anonymize all data related to a given entity ID
     *
     * @param int $customerId
     * @return bool
     */
    public function execute(int $customerId): bool
    {
        /** @var \Opengento\Gdpr\Service\Anonymize\ProcessorInterface $processor */
        foreach ($this->processorPool as $processor) {
            if (!$processor->execute($customerId)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Execute an anonymize processor by name
     *
     * @param string $processorName
     * @param int $customerId
     * @return bool
     */
    public function executeProcessor(string $processorName, int $customerId): bool
    {
        if (!$this->processorPool->offsetExists($processorName)) {
            throw new \InvalidArgumentException(\sprintf('Unknown processor type "%s".', $processorName));
        }

        /** @var \Opengento\Gdpr\Service\Anonymize\ProcessorInterface $processor */
        $processor = $this->processorPool->offsetGet($processorName);
        return $processor->execute($customerId);
    }
}
