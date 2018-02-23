<?php
declare(strict_types=1);

namespace Zalas\PHPUnit\DependencyInjection\Tests\PhpDocumentor;

use phpDocumentor\Reflection\DocBlock\Tag;
use phpDocumentor\Reflection\DocBlock\Tags\Factory\StaticMethod;
use phpDocumentor\Reflection\DocBlock\Tags\Formatter;
use PHPUnit\Framework\TestCase;
use Zalas\PHPUnit\DependencyInjection\PhpDocumentor\Inject;

class InjectTest extends TestCase
{
    public function test_it_is_a_tag()
    {
        $this->assertInstanceOf(Tag::class, new Inject('foo.bar'));
    }

    public function test_it_is_a_static_method()
    {
        $this->assertInstanceOf(StaticMethod::class, new Inject('foo.bar'));
    }

    public function test_it_is_called_inject()
    {
        $inject = new Inject('foo.bar');

        $this->assertSame('inject', $inject->getName());
    }

    public function test_it_returns_the_service_id_if_cast_to_string()
    {
        $this->assertSame('foo.bar', (string) new Inject('foo.bar'));
    }

    public function test_it_can_be_created_from_a_tag_body()
    {
        $inject = Inject::create('foo.bar');

        $this->assertInstanceOf(Inject::class, $inject);
        $this->assertSame('foo.bar', (string) $inject);
    }

    public function test_it_renders_itself()
    {
        $inject = Inject::create('foo.bar');

        $this->assertInstanceOf(Inject::class, $inject);
        $this->assertSame('@inject foo.bar', $inject->render());
    }

    public function test_it_renders_itself_with_a_given_formatter()
    {
        $formatter = new class implements Formatter {
            public function format(Tag $tag)
            {
                return '(@inject foo.bar)';
            }
        };

        $inject = Inject::create('foo.bar');

        $this->assertInstanceOf(Inject::class, $inject);
        $this->assertSame('(@inject foo.bar)', $inject->render($formatter));
    }
}
