<?php
/**
 * This file is part of the proophsoftware/identity-mgmt.
 * (c) %year% prooph software GmbH <contact@prooph.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Infrastructure\Util;

trait VOArray
{
    public function mergeProps($self, array $props): void
    {
        foreach ($props as $prop => $val) {
            $self->{$prop} = $val;
        }
    }

    public function toArray(): array
    {
        $props = get_object_vars($this);

        return array_map([$this, 'propToVal'], $props);
    }

    private function propToVal($prop)
    {
        if(is_scalar($prop) || $prop === null) {
            return $prop;
        }

        if(is_array($prop)) {
            foreach ($prop as $subProp => $subVal) {
                $prop[$subProp] = $this->propToVal($subVal);
            }

            return $prop;
        }

        if(!is_object($prop)) {
            return $prop;
        }

        if(method_exists($prop, 'toArray')) {
            return $prop->toArray();
        }

        if(method_exists($prop, 'toString')) {
            return $prop->toString();
        }

        if(method_exists($prop, 'toBool')) {
            return $prop->toBool();
        }

        if(method_exists($prop, 'toInt')) {
            return $prop->toInt();
        }

        if(method_exists($prop, 'toFloat')) {
            return $prop->toFloat();
        }

        return $prop;
    }
}