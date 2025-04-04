<?php

namespace Classes\FunctionProcessors;

abstract class BaseFunctionProcessor
{
	abstract public function execute(array $params, array $args);
}