<?php

namespace pragmatic\sociallinks\models;

use ArrayIterator;
use Countable;
use IteratorAggregate;
use Traversable;
use craft\base\Model;
use pragmatic\sociallinks\fields\SocialLinksField;

class SocialLinksFieldValue extends Model implements IteratorAggregate, Countable
{
    /**
     * @var SocialLinkItem[]
     */
    public array $items = [];

    public function __construct(array $config = [])
    {
        if (isset($config['items']) && is_array($config['items'])) {
            $config['items'] = array_map(
                static fn(mixed $item): SocialLinkItem => $item instanceof SocialLinkItem
                    ? $item
                    : new SocialLinkItem(is_array($item) ? $item : []),
                $config['items']
            );
        }

        parent::__construct($config);
    }

    public function all(): array
    {
        return $this->items;
    }

    public function isEmpty(): bool
    {
        return count($this->items) === 0;
    }

    public function count(): int
    {
        return count($this->items);
    }

    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->items);
    }

    public function asArray(): array
    {
        return array_map(static fn(SocialLinkItem $item): array => $item->toArray(), $this->items);
    }

    public function formatted(string $variant = 'text'): array
    {
        return array_map(static function(SocialLinkItem $item): array {
            $network = SocialLinksField::socialNetworkDefinition($item->network);

            return [
                'network' => $item->network,
                'label' => $network['label'] ?? $item->network,
                'url' => $item->url,
                'icon' => $network['icon'] ?? '',
            ];
        }, $this->items);
    }
}
