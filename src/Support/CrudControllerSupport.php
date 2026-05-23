<?php

declare(strict_types=1);

/**
 * middag-io/ui — MIDDAG UI contract builders.
 *
 * @author      Michael Meneses <michael@middag.com.br>
 * @copyright   2026 MIDDAG (https://www.middag.com.br)
 * @license     proprietary
 */

namespace Middag\Ui\Support;

use Middag\Ui\Builder\CrudBuilder;
use Middag\Ui\Data\PageContractData;

/**
 * Generic CRUD controller helpers.
 *
 * Composed into extension controllers that use CrudBuilder to generate
 * CRUD pages. Not a standalone controller.
 */
final class CrudControllerSupport
{
    /**
     * Build an index PageContractData from a CrudBuilder.
     *
     * @param array<int, mixed>    $rows
     * @param array<string, mixed> $pagination
     */
    public static function index(CrudBuilder $crud, array $rows = [], array $pagination = []): PageContractData
    {
        return $crud->build('index', [
            'rows' => $rows,
            'pagination' => $pagination,
        ]);
    }

    /**
     * Build a create PageContractData from a CrudBuilder.
     *
     * @param array<string, mixed> $schema
     */
    public static function create(CrudBuilder $crud, array $schema = []): PageContractData
    {
        return $crud->build('create', [
            'schema' => $schema,
        ]);
    }

    /**
     * Build an edit PageContractData from a CrudBuilder.
     *
     * @param array<string, mixed> $values
     * @param array<string, mixed> $schema
     * @param array<string, mixed> $errors
     */
    public static function edit(CrudBuilder $crud, int $id, array $values = [], array $schema = [], array $errors = []): PageContractData
    {
        return $crud->build('edit', [
            'id' => $id,
            'values' => $values,
            'schema' => $schema,
            'errors' => $errors,
        ]);
    }

    /**
     * Build a show PageContractData from a CrudBuilder.
     *
     * @param array<string, mixed> $detail
     * @param array<int, mixed>    $activity
     */
    public static function show(CrudBuilder $crud, array $detail = [], array $activity = []): PageContractData
    {
        return $crud->build('show', [
            'detail' => $detail,
            'activity' => $activity,
        ]);
    }
}
