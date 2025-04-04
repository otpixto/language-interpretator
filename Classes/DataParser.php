<?php

namespace Classes;

/**
 * Класс для преобразования строки в массив данных
 */
class DataParser
{
	/** @var string Исходная строка для парсинга */
	private string $input;

	/** @var int Текущая позиция в исходной строке */
	private int $position = 0;

	/** @var int Длина исходной строки */
	private int $length;

	/** @var array Массив символов после разбиения строки */
	private array $charArray = [];

	/** @var int Текущая позиция в массиве символов */
	private int $charArrayPosition = 0;

	/**
	 * Конструктор
	 * @param string $input Строка для парсинга
	 */
	public function __construct(string $input)
	{
		$this->input = $input;
		$this->length = strlen($this->input);
	}

	/**
	 * Основной метод парсинга
	 * @return array Массив данных
	 */
	public function parse(): array
	{
		// Разбиваем строку на символы
		$this->explodeString();
		// Парсим выражение
		return $this->parseExpression();
	}

	/**
	 * Разбивает строку на символы с определением их типов
	 */
	private function explodeString(): void
	{
		$this->charArray = [];
		$this->position = 0;

		while ($this->position < $this->length) {
			$char = $this->input[$this->position];

			// Пропускаем пробельные символы
			if (in_array($char, [' ', "\t", "\n", "\r"])) {
				$this->position++;
				continue;
			}

			// Обработка специальных символов
			switch ($char) {
				case '(':
					$this->charArray[] = ['type' => 'openBracket', 'value' => '('];
					$this->position++;
					continue 2;
				case ')':
					$this->charArray[] = ['type' => 'closeBracket', 'value' => ')'];
					$this->position++;
					continue 2;
				case ',':
					$this->charArray[] = ['type' => 'comma', 'value' => ','];
					$this->position++;
					continue 2;
				case '"':
					$this->charArray[] = $this->readString();
					continue 2;
			}

			// Обработка чисел
			if ($this->isStartOfNumber($char)) {
				$this->charArray[] = $this->readNumber();
				continue;
			}

			// Обработка символов и ключевых слов
			$this->charArray[] = $this->readSymbol();
		}
	}

	/**
	 * Проверяет, является ли символ началом числа
	 * @param string $char Проверяемый символ
	 * @return bool True если это начало числа
	 */
	private function isStartOfNumber(string $char): bool
	{
		return ctype_digit($char) ||
			($char === '-' && isset($this->input[$this->position + 1]) &&
				ctype_digit($this->input[$this->position + 1]));
	}

	/**
	 * Читает строку в кавычках
	 * @return array символ строки ['type' => 'STRING', 'value' => string]
	 */
	private function readString(): array
	{
		$this->position++; // Пропускаем открывающую кавычку
		$value = '';

		while ($this->position < $this->length) {
			$char = $this->input[$this->position];
			
			// Завершаем чтение при закрывающей кавычке
			if ($char === '"') {
				$this->position++;
				return ['type' => 'STRING', 'value' => $value];
			}

			$value .= $char;
			$this->position++;
		}
		
		return ['type' => 'STRING', 'value' => $value];
	}

	/**
	 * Читает число (целое или с плавающей точкой)
	 * @return array символ числа ['type' => 'INTEGER'|'FLOAT', 'value' => int|float]
	 */
	private function readNumber(): array
	{
		$start = $this->position;
		$isFloat = false;

		// Обработка отрицательных чисел
		if ($this->input[$this->position] === '-') {
			$this->position++;
		}

		// Чтение целой части
		while ($this->position < $this->length && ctype_digit($this->input[$this->position])) {
			$this->position++;
		}

		// Чтение дробной части (если есть)
		if ($this->position < $this->length && $this->input[$this->position] === '.') {
			$isFloat = true;
			$this->position++;
			while ($this->position < $this->length && ctype_digit($this->input[$this->position])) {
				$this->position++;
			}
		}

		$value = substr($this->input, $start, $this->position - $start);
		$numericValue = $isFloat ? (float)$value : (int)$value;

		return ['type' => $isFloat ? 'FLOAT' : 'INTEGER', 'value' => $numericValue];
	}

	/**
	 * Читает символ или ключевое слово
	 * @return array символ ['type' => 'SYMBOL'|'BOOLEAN'|'NULL', 'value' => mixed]
	 */
	private function readSymbol(): array
	{
		$start = $this->position;

		// Собираем все символы до разделителя
		while ($this->position < $this->length) {
			$char = $this->input[$this->position];
			if (in_array($char, ['(', ')', ',', ' ', '"'])) {
				break;
			}
			$this->position++;
		}

		$value = substr($this->input, $start, $this->position - $start);

		// Определяем тип значения
		return match(strtolower($value)) {
			'true' => ['type' => 'BOOLEAN', 'value' => true],
			'false' => ['type' => 'BOOLEAN', 'value' => false],
			'null' => ['type' => 'NULL', 'value' => null],
			default => ['type' => 'SYMBOL', 'value' => $value]
		};
	}

	/**
	 * Парсит выражение (функция или константа)
	 * @return array Массив данных
	 */
	private function parseExpression(): array
	{
		$charArray = $this->charArray[$this->charArrayPosition];

		if ($charArray['type'] === 'openBracket') {
			return $this->parseFunctionCall();
		}

		$this->charArrayPosition++;
		
		return [
			'type' => 'CONSTANT',
			'value' => $this->parseConstant($charArray)
		];
	}

	/**
	 * Парсит вызов функции
	 * @return array Массив данных для вызова функции
	 */
	private function parseFunctionCall(): array
	{
		$this->charArrayPosition++;

		// Получаем имя функции
		$charArray = $this->charArray[$this->charArrayPosition];

		$functionName = $charArray['value'];
		$this->charArrayPosition++;

		$params = [];

		// Обрабатываем параметры, если они есть
		if ($this->charArrayPosition < count($this->charArray) &&
			$this->charArray[$this->charArrayPosition]['type'] === 'comma') {
			// Пропускаем запятую
			$this->charArrayPosition++;
			$params = $this->parseFunctionParameters();
		}

		$this->charArrayPosition++;

		return [
			'type' => 'function',
			'name' => $functionName,
			'params' => $params,
		];
	}

	/**
	 * Парсит параметры функции
	 * @return array Массив параметров
	 */
	private function parseFunctionParameters(): array
	{
		$params = [];

		// Первый параметр обязателен
		$params[] = $this->parseExpression();

		// Обрабатываем дополнительные параметры через запятую
		while ($this->charArrayPosition < count($this->charArray) &&
			$this->charArray[$this->charArrayPosition]['type'] === 'comma') {
			// Пропускаем запятую
			$this->charArrayPosition++;
			$params[] = $this->parseExpression();
		}

		return $params;
	}

	/**
	 * Преобразует символ в значение константы
	 * @param array $charArray символ
	 * @return mixed Значение константы
	 * @throws \RuntimeException При неизвестном типе символа
	 */
	private function parseConstant(array $charArray): mixed
	{
		return match($charArray['type']) {
			'BOOLEAN', 'NULL', 'INTEGER', 'FLOAT', 'STRING' => $charArray['value'],
			'SYMBOL' => throw new \RuntimeException("Неизвестный символ: {$charArray['value']}"),
			default => throw new \RuntimeException("Неожиданный тип символа: {$charArray['type']}")
		};
	}
}