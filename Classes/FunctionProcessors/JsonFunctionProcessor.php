<?php

namespace Classes\FunctionProcessors;

class JsonFunctionProcessor extends BaseFunctionProcessor
{
	public function execute(array $params, array $args): bool|string {
		return json_encode($params[0]);
	}
}