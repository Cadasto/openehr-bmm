<?php

declare(strict_types=1);

namespace Tests\TestCase\Codec;

use Cadasto\OpenEHR\BMM\Codec\BmmCodecInterface;
use Cadasto\OpenEHR\BMM\Codec\JsonCodec;
use PHPUnit\Framework\TestCase;

final class JsonCodecTest extends TestCase
{
    private JsonCodec $codec;

    protected function setUp(): void
    {
        $this->codec = new JsonCodec();
    }

    public function testImplementsInterface(): void
    {
        self::assertInstanceOf(BmmCodecInterface::class, $this->codec);
    }

    public function testDecodeValidJson(): void
    {
        $json = '{"bmm_version":"2.4","rm_publisher":"openehr"}';

        $result = $this->codec->decode($json);

        self::assertSame('2.4', $result['bmm_version']);
        self::assertSame('openehr', $result['rm_publisher']);
    }

    public function testDecodeInvalidJsonThrows(): void
    {
        $this->expectException(\JsonException::class);

        $this->codec->decode('{invalid json}');
    }

    public function testEncodeArray(): void
    {
        $data = ['bmm_version' => '2.4', 'rm_publisher' => 'openehr'];

        $result = $this->codec->encode($data);

        self::assertJson($result);
        $decoded = json_decode($result, true, 512, JSON_THROW_ON_ERROR);
        self::assertSame('2.4', $decoded['bmm_version']);
    }

    public function testEncodePrettyPrintsWithUnicodeAndSlashes(): void
    {
        $data = ['description' => 'test/value', 'unicode' => "\u{00e9}"];

        $result = $this->codec->encode($data);

        self::assertStringContainsString("\n", $result);
        self::assertStringContainsString('test/value', $result);
        self::assertStringContainsString("\u{00e9}", $result);
    }

    public function testRoundTripWithFixture(): void
    {
        $path = __DIR__ . '/../../resources/openehr_base_1.3.0.bmm.json';
        $original = file_get_contents($path);
        self::assertIsString($original);

        $decoded = $this->codec->decode($original);
        $encoded = $this->codec->encode($decoded);
        $reDecoded = $this->codec->decode($encoded);

        self::assertSame($decoded, $reDecoded);
    }

    public function testRoundTripWithRmFixture(): void
    {
        $path = __DIR__ . '/../../resources/openehr_rm_1.2.0.bmm.json';
        $original = file_get_contents($path);
        self::assertIsString($original);

        $decoded = $this->codec->decode($original);
        $encoded = $this->codec->encode($decoded);
        $reDecoded = $this->codec->decode($encoded);

        self::assertSame($decoded, $reDecoded);
    }

    public function testDecodeFileHelper(): void
    {
        $path = __DIR__ . '/../../resources/openehr_base_1.3.0.bmm.json';

        $result = $this->codec->decodeFile($path);

        self::assertArrayHasKey('bmm_version', $result);
        self::assertSame('2.4', $result['bmm_version']);
    }

    public function testDecodeFileMissingFileThrows(): void
    {
        $this->expectException(\RuntimeException::class);

        $this->codec->decodeFile('/nonexistent/file.json');
    }

    public function testFullRoundTripCodecToModelAndBack(): void
    {
        $path = __DIR__ . '/../../resources/openehr_base_1.3.0.bmm.json';
        $originalData = $this->codec->decodeFile($path);

        // Decode -> model -> toArray -> encode -> decode -> compare
        $schema = \Cadasto\OpenEHR\BMM\Model\BmmSchema::fromArray($originalData);
        $exported = $schema->toArray();
        $json = $this->codec->encode($exported);
        $reimported = $this->codec->decode($json);

        // Key structural assertions
        self::assertSame($originalData['bmm_version'], $reimported['bmm_version']);
        self::assertSame($originalData['rm_publisher'], $reimported['rm_publisher']);
        self::assertSame($originalData['schema_name'], $reimported['schema_name']);
        self::assertSame($originalData['rm_release'], $reimported['rm_release']);
        self::assertCount(count($originalData['packages']), $reimported['packages']);
    }
}
