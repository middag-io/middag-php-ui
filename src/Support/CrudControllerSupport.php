<?php

declare(strict_types=1);

/**
 * middag-io/ui — MIDDAG UI contract builders.
 *
 * @author      Michael Meneses <michael@middag.io>
 * @copyright   2026 MIDDAG (https://middag.io)
 * @license     Apache-2.0
 */

namespace Middag\Ui\Support;

use Middag\Ui\Builder\CrudBuilder;
use Middag\Ui\PageContract;

/**
 * Generic CRUD controller helpers.
 *
 * Composed into extension controllers that use CrudBuilder to generate
 * CRUD pages. Not a standalone controller.
 */
final class CrudControllerSupport
{
    /**
     * Build an index PageContract from a CrudBuilder.
     *
     * @param array<int, mixed>    $rows
     * @param array<string, mixed> $pagination
     */
    public static function index(CrudBuilder $crud, array $rows = [], array $pagination = []): PageContract
    {
        return $crud->build('index', [
            'rows' => $rows,
            'pagination' => $pagination,
        ]);
    }

    /**
     * Build a create PageContract from a CrudBuilder.
     *
     * @param array<string, mixed> $schema
     */
    public static function create(CrudBuilder $crud, array $schema = []): PageContract
    {
        return $crud->build('create', [
            'schema' => $schema,
        ]);
    }

    /**
     * Build an edit PageContract from a CrudBuilder.
     *
     * @param array<string, mixed> $values
     * @param array<string, mixed> $schema
     * @param array<string, mixed> $errors
     */
    public static function edit(CrudBuilder $crud, int $id, array $values = [], array $schema = [], array $errors = []): PageContract
    {
        return $crud->build('edit', [
            'id' => $id,
            'values' => $values,
            'schema' => $schema,
            'errors' => $errors,
        ]);
    }

    /**
     * Build a show PageContract from a CrudBuilder.
     *
     * @param array<string, mixed> $detail
     * @param array<int, mixed>    $activity
     */
    public static function show(CrudBuilder $crud, array $detail = [], array $activity = []): PageContract
    {
        return $crud->build('show', [
            'detail' => $detail,
            'activity' => $activity,
        ]);
    }
}
