<?php

namespace Blokpax\Web3\Types;

use Blokpax\Web3\Contracts\Type as TypeContract;
use Blokpax\Web3\Type;
use InvalidArgumentException;

class Struct implements TypeContract
{
    protected array $properties;
    protected array $types;
    protected array $values = [];

    public function __construct(array $properties)
    {
        foreach ($properties as $name => $type) {
            $this->properties[$name] = $type;
        }
    }

    public function value(): mixed
    {
        return array_combine(
            array_keys($this->proeprties),
            array_map(
                fn ($_, $key) => $this->values[$key],
                $this->properties
            )
        );
    }

    public function properties(): array
    {
        return $this->properties;
    }

    public function set($property, $value = null): self
    {
        if (! is_array($property)) {
            $property = [$property => $value];
        }

        foreach ($property as $key => $value) {
            if (! isset($this->properties[$key])) {
                throw new InvalidArgumentException("Property '{$key}' does not exist in struct.");
            }

            if (! $value instanceof TypeContract) {
                $type = $this->properties[$key];
                $this->values[$key] = new $type($value);
            } else {
                $this->values[$key] = $value;
            }
        }

        return $this;
    }

    public function __get($property)
    {
        if ($property === 'value') {
            return $this->value();
        }

        return $this->get($property);
    }

    public function get($property)
    {
        if (! is_array($property)) {
            $property = [$property];
        }

        $return = [];
        foreach ($property as $prop) {
            if (! isset($this->properties[$prop])) {
                throw new InvalidArgumentException("Property '{$prop}' does not exist in struct.");
            }

            if (! isset($this->values[$prop])) {
                throw new InvalidArgumentException("Property '{$prop}' is not set.");
            }

            $return[$prop] = $this->values[$prop];
        }

        return count($return) === 1
            ? array_pop($return)
            : $return;
    }

    public static function encode(array $value): string
    {
        return implode('', array_map(fn ($v) => $v->encode(), $value));
    }

    public static function decode(string $data, array $properties): self
    {
        $struct = new self($properties);
        $values = Type::decode($data, $properties);
        $struct->set($values);

        return $struct;
    }
}
