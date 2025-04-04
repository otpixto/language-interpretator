<?php

namespace Classes\FunctionProcessors;

class MapFunctionProcessor extends BaseFunctionProcessor
{
	public function execute(array $params, array $args): array {
		return array_combine($params[0], $params[1]);
	}
}