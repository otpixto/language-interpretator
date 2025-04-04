<?php

namespace Classes;

use Classes\FunctionProcessors\BaseFunctionProcessor;

class FunctionFactory
{
	/**
	 * Создает экземпляр функции по имени
	 * @param string $functionName Имя функции (getArg, array, map и т.д.)
	 * @return BaseFunctionProcessor
	 * @throws \RuntimeException Если функция не найдена
	 */
	public static function create(string $functionName): BaseFunctionProcessor
	{
		return match ($functionName) {
			'getArg' => new FunctionProcessors\GetArgFunctionProcessor(),
			'array' => new FunctionProcessors\ArrayFunctionProcessor(),
			'map' => new FunctionProcessors\MapFunctionProcessor(),
			'json' => new FunctionProcessors\JsonFunctionProcessor(),
			'concat' => new FunctionProcessors\ConcatFunctionProcessor(),
			default => throw new \RuntimeException("Неизвестная функция: {$functionName}")
		};
	}
}