<?php

namespace Classes\FunctionProcessors;

class ConcatFunctionProcessor extends BaseFunctionProcessor
{
	public function execute(array $params, array $args): string {
		return $params[0] . $params[1];
	}
}