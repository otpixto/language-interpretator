<?php

namespace Classes;

/**
 * Реестр функций интерпретатора
 *
 * Позволяет регистрировать и получать функции по их именам.
 * Используется интерпретатором для хранения и доступа к доступным функциям.
 */
class FunctionRegistry
{
	/**
	 * @var array Ассоциативный массив зарегистрированных функций
	 *            Ключ - имя функции, значение - callable
	 */
	private array $functions = [];

	/**
	 * Регистрирует новую функцию в реестре
	 *
	 * @param string $name Имя функции для вызова в интерпретируемом коде
	 * @param callable $callback Callable функция (например, [new SomeFunction(), 'execute'])
	 *
	 * @return void
	 *
	 * @example
	 * $registry->register('concat', [new ConcatFunctionProcessor(), 'execute']);
	 */
	public function register(string $name, callable $callback): void
	{
		$this->functions[$name] = $callback;
	}

	/**
	 * Возвращает callable функцию по имени
	 *
	 * @param string $name Имя зарегистрированной функции
	 *
	 * @return callable Зарегистрированная callable функция
	 *
	 * @throws \RuntimeException Если функция с указанным именем не найдена
	 *
	 * @example
	 * $func = $registry->get('concat');
	 * $result = $func(['Hello', 'World']);
	 */
	public function get(string $name): callable
	{
		if (!isset($this->functions[$name])) {
			throw new \RuntimeException("Функция не найдена: {$name}");
		}

		return $this->functions[$name];
	}
}