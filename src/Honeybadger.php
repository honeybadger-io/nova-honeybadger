<?php

namespace HoneybadgerIo\NovaHoneybadger;

use Laravel\Nova\ResourceTool;

class Honeybadger extends ResourceTool
{
    public function __construct()
    {
        parent::__construct();

        $this->withMeta([
            'contextKey' => 'user_id',
            'contextValue' => null,
            'contextAttribute' => null,
            'searchString' => null,
        ]);
    }

    public function withContextKey($key)
    {
        $this->withMeta([
            'contextKey' => $key
        ]);

        return $this;
    }

    public function withContextValue($value)
    {
        $this->withMeta([
            'contextValue' => $value
        ]);

        return $this;
    }

    public function withContextAttribute($attribute)
    {
        $this->withMeta([
            'contextAttribute' => $attribute
        ]);

        return $this;
    }

    public function withSearchString($searchString)
    {
        $this->withMeta([
            'searchString' => $searchString
        ]);

        return $this;
    }

    public static function fromSearchString($searchString)
    {
        $tool = new static();
        return $tool->withContextKey(null)->withSearchString($searchString);
    }

    public static function fromContextKeyAndAttribute($key, $attribute)
    {
        $tool = new static();
        return $tool->withContextKey($key)->withContextAttribute($attribute);
    }

    public static function fromContextKeyAndValue($key, $value)
    {
        $tool = new static();
        return $tool->withContextKey($key)->withContextValue($value);
    }

    /**
     * Get the displayable name of the resource tool.
     *
     * @return string
     */
    public function name()
    {
        return 'Honeybadger';
    }

    /**
     * Get the component name for the resource tool.
     *
     * @return string
     */
    public function component()
    {
        return 'honeybadger-laravel-nova';
    }
}
