<?php

namespace Classes\FunctionProcessors;

class GetArgFunctionProcessor extends BaseFunctionProcessor {
	public function execute(array $params, array $args): ?string {
		$index = $params[0];

		// Попробуем преобразовать в integer, если это строка
		if (is_string($index) && ctype_digit($index)) {
			$index = (int) $index;
		}
			
		return $args[$index] ?? null;
	}
}