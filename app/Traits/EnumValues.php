<?php

namespace App\Traits;

use Exception;
use Illuminate\Support\Collection;

trait EnumValues
{
    /**
     * Undocumented function
     *
     * @param string $value
     * @return self
     */
    public static function findOrFail(string $value): self
    {
        $enum = self::find($value);

        throw_if(is_null($enum), Exception::class, "{$value} does not exists in " . get_class());

        return $enum;
    }

    /**
     * Undocumented function
     *
     * @param string $value
     * @return self|null
     */
    public static function find(string $value, $strict = true): ?self
    {
        $values = self::cases();

        $index = collect($values)->search(fn(self $enum) => $enum->value === $value, $strict);

        return $index !== false ? $values[$index] : null;
    }

    /**
     * Undocumented function
     *
     * @return Collection
     */
    public static function values(): Collection
    {
        return collect(array_column(self::cases(), "value"));
    }

    /**
     * Undocumented function
     *
     * @param mixed $enum
     * @return boolean
     */
    public function is(mixed $enums): bool
    {
        if (is_array($enums)) {
            return in_array($this, $enums);
        } else {
            return $enums === $this;
        }
    }

    /**
     * Undocumented function
     *
     * @param mixed ...$enums
     * @return boolean
     */
    public function contains(mixed ...$enums): bool
    {
        return collect($enums)->containsStrict($this);
    }

    /**
     * Undocumented function
     *
     * @param string|integer $value
     * @return boolean
     */
    public function isEqual(string|int $value): bool
    {
        return $this->value === $value;
    }
}
