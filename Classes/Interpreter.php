<?php

namespace Classes;

class Interpreter
{
	private FunctionRegistry $functionRegistry;
	private array $args;

	public function __construct(FunctionRegistry $functionRegistry, array $args = [])
	{
		$this->functionRegistry = $functionRegistry;
		$this->args = $args;
	}

	public function interpret($dataArray)
	{
		if ($dataArray['type'] === 'function') {
			$function = $this->functionRegistry->get($dataArray['name']);
			$params = array_map([$this, 'interpret'], $dataArray['params']);
			return $function($params, $this->args);
		}

		return $dataArray['value'];
	}
}