--TEST--
PHPUnit_Framework_MockObject_Generator::generate('Foo', array('bar'), 'MockFoo', TRUE, TRUE)
--FILE--
<?php
class Foo
{
    public function bar(Foo $foo)
    {
    }

    public function baz(Foo $foo)
    {
    }
}

require_once dirname(dirname(dirname(dirname(__FILE__)))) . '/Framework/MockObject/Generator.php';

$mock = PHPUnit_Framework_MockObject_Generator::generate(
  'Foo',
  array('bar'),
  'MockFoo',
  TRUE,
  TRUE
);

print $mock['code'];
?>
--EXPECTF--
class MockFoo extends Foo
{
    public static $staticInvocationMocker;
    public $invocationMocker;

    public function __clone()
    {
        $this->invocationMocker = clone $this->invocationMocker;
    }

    public function bar(Foo $foo)
    {
        $args = func_get_args();

        $result = $this->invocationMocker->invoke(
          new PHPUnit_Framework_MockObject_Invocation_Object(
            'Foo', 'bar', $args, $this
          )
        );

        return $result;
    }

    public function expects(PHPUnit_Framework_MockObject_Matcher_Invocation $matcher)
    {
        return $this->invocationMocker->expects($matcher);
    }

    public static function staticExpects(PHPUnit_Framework_MockObject_Matcher_Invocation $matcher)
    {
        return self::$staticInvocationMocker->expects($matcher);
    }

    public function __phpunit_getInvocationMocker()
    {
        return $this->invocationMocker;
    }

    public function __phpunit_verify()
    {
        self::$staticInvocationMocker->verify();
        $this->invocationMocker->verify();
    }
}

MockFoo::$staticInvocationMocker = new PHPUnit_Framework_MockObject_InvocationMocker;
