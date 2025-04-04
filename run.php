<?php

// Настройка автозагрузки классов
spl_autoload_register(function ($class) {
	// Преобразуем пространство имен в путь к файлу
	$file = __DIR__ . '/' . str_replace('\\', '/', $class) . '.php';

	if (file_exists($file)) {
		require $file;
	}
});

// Чтение конфигурации функций
$functionsConfig = require __DIR__ . '/config/functions.php';

/**
 * Основной скрипт интерпретатора функционального языка
 */

// Чтение входного файла с программой
$inputFile = __DIR__ . '/example.txt';
if (!file_exists($inputFile)) {
	// Если файл не найден, завершаем выполнение с ошибкой
	die("Файл не найден: {$inputFile}");
}

// Читаем содержимое файла
$input = file_get_contents($inputFile);

// Получаем аргументы командной строки
$args = array_slice($argv, 1);

// Оборачиваем основной код в try-catch для обработки возможных исключений
try {
	// Создаем парсер и преобразуем входную строку в массив
	$parser = new Classes\DataParser($input);
	$inputDataArray = $parser->parse();
	
	// Создаем массив доступных функций
	$functionsArray = new Classes\FunctionRegistry();

	// Регистрация функций через фабрику
	foreach ($functionsConfig as $functionName) {
		$functionsArray->register(
			$functionName,
			[Classes\FunctionFactory::create($functionName), 'execute']
		);
	}
	
	// Создаем интерпретатор с массивом функций и аргументами
	$interpreter = new Classes\Interpreter($functionsArray, $args);

	// Интерпретируем массив данных и получаем результат
	$result = $interpreter->interpret($inputDataArray);

	// Выводим результат работы программы
	echo $result . PHP_EOL;

} catch (\Exception $e) {
	// Обработка ошибок: выводим сообщение и завершаем с кодом ошибки 1
	echo "Ошибка: " . $e->getMessage() . PHP_EOL;
	exit(1);
}