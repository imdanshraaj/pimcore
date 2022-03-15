<?php

/**
 * Pimcore
 *
 * This source file is available under two different licenses:
 * - GNU General Public License version 3 (GPLv3)
 * - Pimcore Commercial License (PCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     GPLv3 and PCL
 */

namespace Pimcore\Event\Traits;

use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

/**
 * @internal
 */
trait RecursionBlockingEventDispatchHelperTrait
{
    /**
     * @var array
     */
    private array $activeDispatchingEvents = [];

    private ?EventDispatcherInterface $eventDispatcher = null;

    /**
     * Dispatches an event, avoids recursion by checking if the active dispatch event is the same
     *
     * @param object $event
     * @param string|null $eventName
     *
     * @return void
     */
    protected function dispatchEvent(object $event, string $eventName = null): void
    {
        $eventName ??= \get_class($event);
        if (!isset($this->activeDispatchingEvents[$eventName])) {
            $this->activeDispatchingEvents[$eventName] = true;
            $this->getEventDispatcher()->dispatch($event, $eventName);
            unset($this->activeDispatchingEvents[$eventName]);
        }
    }

    /**
     * @return EventDispatcherInterface|null
     */
    private function getEventDispatcher(): ?EventDispatcherInterface
    {
        return $this->eventDispatcher ?? \Pimcore::getEventDispatcher();
    }

    /**
     * @required
     *
     * @param EventDispatcherInterface|null $eventDispatcher
     */
    public function setEventDispatcher(?EventDispatcherInterface $eventDispatcher): void
    {
        $this->eventDispatcher = $eventDispatcher;
    }
}
