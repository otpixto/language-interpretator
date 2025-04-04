<?php

namespace Classes\FunctionProcessors;

class ArrayFunctionProcessor extends BaseFunctionProcessor
{
	public function execute(array $params, array $args): array {
		return $params;
	}
}