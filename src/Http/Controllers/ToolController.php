<?php

namespace HoneybadgerIo\NovaHoneybadger\Http\Controllers;

use HoneybadgerIo\NovaHoneybadger\Api;
use Laravel\Nova\Http\Requests\NovaRequest;

class ToolController
{

    /**
     * Fetch Honeybadger details about the given resource model and configuration.
     *
     * @param NovaRequest $request
     * @param Api $api
     * @return mixed
     */
    public function fetch(NovaRequest $request, Api $api)
    {
        $model = $request->findModelOrFail($request->resourceId);

        $contextKey = $request->get('contextKey', 'user_id');
        $contextValue = $request->get('contextValue');
        $contextAttribute = $request->get('contextAttribute');
        $searchString = '';

        if ($contextKey === 'user_id' && is_null($contextValue)) {
            $searchString = 'context.user_id:"' . $model->getKey() . '"';
        } elseif (!is_null($contextKey) && !is_null($contextAttribute)) {
            $searchString = $contextKey . ':"' . $model->{$contextAttribute} . '"';
        } elseif (!is_null($contextKey) && !is_null($contextValue)) {
            $searchString = $contextKey . ':"' . $contextValue . '"';
        }

        if (!is_null($request->get('searchString'))) {
            $searchString .= ' ' . $request->get('searchString');
        }

        return $api->getFaults(config('services.honeybadger.project_id'), trim($searchString), $request->get('limit', 10), $request->get('order', 'recent'));
    }

    /**
     * Fetch a Honeybadger API response.
     *
     * @param NovaRequest $request
     * @param Api $api
     * @return mixed
     */
    public function url(NovaRequest $request, Api $api)
    {
        return $api->get($request->get('url'));
    }

}