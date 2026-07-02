<?php

declare(strict_types=1);

/**
 * middag-io/ui — MIDDAG UI contract builders.
 *
 * @author      Michael Meneses <michael@middag.io>
 * @copyright   2026 MIDDAG (https://middag.io)
 * @license     Apache-2.0
 */

namespace Middag\Ui\Tests\Schema;

use FilesystemIterator;
use JsonSerializable;
use Middag\Ui\Envelope\Contract\ContractEnvelopeInterface;
use Middag\Ui\Page\PageContract;
use Middag\Ui\Schema\SchemaRegistry;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use ReflectionClass;
use ReflectionEnum;

/**
 * Guards that the schema registry covers every wire VO + enum, so a new
 * serialized type cannot silently ship without a schema.
 *
 * @internal
 */
#[CoversClass(SchemaRegistry::class)]
final class SchemaCoverageTest extends TestCase
{
    #[Test]
    public function testEveryRegisteredTypeHasJsonSchema(): void
    {
        foreach (SchemaRegistry::types() as $name => $fqcn) {
            self::assertTrue(
                method_exists($fqcn, 'jsonSchema'),
                sprintf('%s (registered as %s) must expose static jsonSchema().', $fqcn, $name),
            );
            $schema = $fqcn::jsonSchema();
            self::assertIsArray($schema, $name . '::jsonSchema() must return an array.');
            self::assertNotSame([], $schema, $name . '::jsonSchema() must not be empty.');
        }
    }

    /**
     * Every JsonSerializable VO declared under src/ (plus the PageContract
     * envelope) must be registered — otherwise it serializes with no schema.
     */
    #[Test]
    public function testEveryWireValueObjectIsRegistered(): void
    {
        $registered = array_values(SchemaRegistry::types());

        foreach ($this->wireValueObjects() as $fqcn) {
            self::assertContains(
                $fqcn,
                $registered,
                $fqcn . ' implements JsonSerializable (it reaches the wire) but is not in SchemaRegistry. '
                . 'Add a jsonSchema() and register it, or it will ship unvalidated.',
            );
        }
    }

    /**
     * Every backed enum declared under src/ reaches the wire as a string value, so
     * it must be registered with a $def — otherwise a new enum ships unvalidated and
     * the codegen consumers get no type for it. Symmetric guard to the VO test.
     */
    #[Test]
    public function testEveryWireEnumIsRegistered(): void
    {
        $registered = array_values(SchemaRegistry::types());

        foreach ($this->wireEnums() as $fqcn) {
            self::assertContains(
                $fqcn,
                $registered,
                $fqcn . ' is a backed enum (it reaches the wire) but is not in SchemaRegistry. '
                . 'Apply the ProvidesJsonSchema trait and register it, or it ships unvalidated.',
            );
            self::assertTrue(
                method_exists($fqcn, 'jsonSchema'),
                $fqcn . ' must expose static jsonSchema() (use the ProvidesJsonSchema trait).',
            );
        }
    }

    #[Test]
    public function testRegistryBundlesAreSelfContained(): void
    {
        $defs = SchemaRegistry::defs();
        $names = array_keys($defs);
        $missing = [];

        $walk = static function (mixed $node) use (&$walk, $names, &$missing): void {
            if (!is_array($node)) {
                return;
            }
            foreach ($node as $key => $value) {
                if ($key === '$ref' && is_string($value)) {
                    $ref = str_replace('#/$defs/', '', $value);
                    if (!in_array($ref, $names, true)) {
                        $missing[] = $ref;
                    }

                    continue;
                }
                $walk($value);
            }
        };
        $walk($defs);

        self::assertSame([], array_values(array_unique($missing)), 'Every $ref must resolve to a registered $def.');
    }

    /**
     * Every emitted bundle must stamp x-contract-version with the envelope
     * VERSION, so the react codegen (which fails on a missing/unknown stamp) and
     * the version-coupling rule stay enforceable rather than documentary.
     */
    #[Test]
    public function testBundlesStampTheContractVersion(): void
    {
        foreach (SchemaRegistry::bundles() as $file => $bundle) {
            self::assertArrayHasKey('x-contract-version', $bundle, $file . ' must stamp x-contract-version.');
            self::assertSame(
                ContractEnvelopeInterface::VERSION,
                $bundle['x-contract-version'],
                $file . ' x-contract-version must equal ContractEnvelopeInterface::VERSION.',
            );
        }
    }

    /**
     * Discover all JsonSerializable VOs that land on the wire.
     *
     * @return list<class-string>
     */
    private function wireValueObjects(): array
    {
        $found = [PageContract::class];

        foreach ($this->srcClasses() as $class) {
            if (!class_exists($class)) {
                continue;
            }
            $ref = new ReflectionClass($class);
            if ($ref->implementsInterface(JsonSerializable::class) && !$ref->isAbstract()) {
                $found[] = $class;
            }
        }

        return array_values(array_unique($found));
    }

    /**
     * Discover all backed enums that land on the wire. enum_exists() naturally
     * skips the Concerns/ trait that sits alongside the enums.
     *
     * @return list<class-string>
     */
    private function wireEnums(): array
    {
        $found = [];

        foreach ($this->srcClasses() as $class) {
            if (!enum_exists($class)) {
                continue;
            }
            if ((new ReflectionEnum($class))->isBacked()) {
                $found[] = $class;
            }
        }

        return $found;
    }

    /**
     * Every symbol declared under src/, FQN derived from its PSR-4 path. The
     * concern-first layout scatters the wire types across concern directories,
     * so the guard walks the whole tree instead of a single folder.
     *
     * @return list<class-string>
     */
    private function srcClasses(): array
    {
        $base = dirname(__DIR__, 2) . '/src';
        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($base, FilesystemIterator::SKIP_DOTS),
        );

        $classes = [];
        foreach ($files as $file) {
            if (!$file->isFile()) {
                continue;
            }
            if ($file->getExtension() !== 'php') {
                continue;
            }
            $relative = substr($file->getPathname(), strlen($base) + 1, -4);
            $classes[] = 'Middag\Ui\\' . str_replace('/', '\\', $relative);
        }

        return $classes;
    }
}
