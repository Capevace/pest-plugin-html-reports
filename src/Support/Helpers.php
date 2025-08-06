<?php

namespace Mateffy\HtmlReports\Support;

function invade(object $object)
{
	return new class($object)
	{
		private $object;
		private $reflection;

		public function __construct(object $object)
		{
			$this->object = $object;
			$this->reflection = new \ReflectionClass($object);
		}

		public function __get(string $name)
		{
			try {
				$property = $this->reflection->getProperty($name);
				$property->setAccessible(true);
				return $property->getValue($this->object);
			} catch (\ReflectionException $e) {
				// Property might not exist, or other reflection error.
				// Return null or handle as appropriate.
				return null;
			}
		}

		public function __call(string $name, array $arguments)
		{
			try {
				$method = $this->reflection->getMethod($name);
				$method->setAccessible(true);
				return $method->invoke($this->object, ...$arguments);
			} catch (\ReflectionException $e) {
				// Method might not exist.
				return null;
			}
		}
	};
}
