<?php
/**
 * Unary-binary numeric encoding/decoding to/from string
 *
 * Ported partly from XBUP Project (Extensible Binary Universal Protocol)
 * http://www.xbup.org/
 *
 * Numbers are stored in UTF-8 like encoding, first unary length and then binary
 * value with recursive application. Code is prefix and deterministic.
 *
 * 2^7   0..7Fh        0xxxxxxx
 * 2^14  0..3FFFh      10xxxxxx xxxxxxxx
 * 2^21  0..1FFFFFh    110xxxxx xxxxxxxx xxxxxxxx
 * 2^28  0..0FFFFFFFh  1110xxxx xxxxxxxx xxxxxxxx xxxxxxxx
 *
 * 1-4 bytes length:       2^7 + 2^14 + 2^21 + 2^28 = 270 549 120
 * 4-bytes length (int32): 2^32 = 4 294 967 296
 *
 * ПРИМЕЧАНИЕ
 *   Класс не имеет права "падать" (возвращать фатальную ошибку и завершать работу скрипта),
 *   если в его методы были переданы данные неверного формата.
 *   Для проверки входных параметров используется assert(string $php_code)!
 *   Если проверка активирована и возникла ошибка, все методы класса возвращают FALSE.
 *   Рекомендуется активировать проверку только в режиме тестирования и отладки скрипта.
 *
 * Useful links
 *   http://en.wikipedia.org/wiki/Exponential-Golomb_coding
 *   http://en.wikipedia.org/wiki/Burrows–Wheeler_transform
 *
 * Exponential-Golomb_coding
 *   0 => 1 => 1
 *   1 => 10 => 010  (добавили вначале один ноль)
 *   2 => 11 => 011
 *   3 => 100 => 00100  (добавили в начале два нуля)
 *   4 => 101 => 00101
 *   5 => 110 => 00110
 *   6 => 111 => 00111
 *   7 => 1000 => 0001000  (добавили в начале три нуля)
 *   8 => 1001 => 0001001
 *   ...
 *
 * @license  http://creativecommons.org/licenses/by-nc-sa/3.0/
 * @author   https://github.com/rin-nas
 * @charset  UTF-8
 * @version  1.0.2
 */
class UnaryBinaryNumeric
{
	#запрещаем создание экземпляра класса, вызов методов этого класса только статически!
	private function __construct() {}

	/**
	 * На входе строка (бинарные данные), на выходе массив чисел.
	 * В случае ошибки возвращает FALSE + E_USER_ERROR
	 *
	 * @param   string      $s
	 * @return  array|bool  Returns FALSE if error occurred
	 */
	public static function decode($s)
	{
		if (! ReflectionTypeHint::isValid()) return false;
		$i = 0;
		$len = strlen($s);
		$numbers = array();
		while ($i < $len)
		{
			$input = ord($s{$i}) & 0xFF;
			if ($input < 0x80)
			{
				$numbers[] = ord($s{$i});
				$i += 1;
			}
			elseif ($input < 0xC0)
			{
				$numbers[] = (($input & 0x7F) << 8)
					+ (ord($s{$i + 1}) & 0xFF) + 0x80;
				$i += 2;
			}
			elseif ($input < 0xE0)
			{
				$numbers[] = (($input & 0x3F) << 16)
					+ ((ord($s{$i + 1}) & 0xFF) << 8)
					+ (ord($s{$i + 2}) & 0xFF) + 0x4080;
				$i += 3;
			}
			elseif ($input < 0xF0)
			{
				$numbers[] = (($input & 0x1F) << 24)
					+ ((ord($s{$i + 1}) & 0xFF) << 16)
					+ ((ord($s{$i + 2}) & 0xFF) << 8)
					+ (ord($s{$i + 3}) & 0xFF) + 0x204080;
				$i += 4;
			}
			else
			{
				trigger_error('Value >= 0x10204080', E_USER_WARNING);
				return false;
			}
		}#while
		return $numbers;
	}

	/**
	 * На входе массив чисел, на выходе строка (бинарные данные).
	 * В случае ошибки возвращает FALSE + E_USER_ERROR.
	 * @param   array        $numbers
	 * @return  string|bool  Returns FALSE if error occurred
	 */
	public static function encode($numbers)
	{
		if (! ReflectionTypeHint::isValid()) return false;
		$s = '';
		foreach ($numbers as $i => $n)
		{
			if (! assert('is_int($n) || ctype_digit($n)')) return false;
			if ($n < 0x80) $s .= chr($n);
			elseif ($n < 0x4080)
			{
				$s .= chr((($n - 0x80) >> 8) + 0x80)
					. chr(($n - 0x80) & 0xFF);
			}
			elseif ($n < 0x204080)
			{
				$s .= chr((($n - 0x4080) >> 16) + 0xC0)
					. chr((($n - 0x4080) >> 8) & 0xFF)
					. chr(($n - 0x4080) & 0xFF);
			}
			elseif ($n < 0x10204080)
			{
				$s .= chr((($n - 0x204080) >> 24) + 0xE0)
					. chr((($n - 0x204080) >> 16) & 0xFF)
					. chr((($n - 0x204080) >> 8) & 0xFF)
					. chr(($n - 0x204080) & 0xFF);
			}
			else
			{
				trigger_error('Value >= 0x10204080', E_USER_WARNING);
				return false;
			}
		}
		return $s;
	}

	/**
	 * Возвращает кол-во байт для кодирования числа $number в строку (бинарные данные).
	 * В случае ошибки возвращает FALSE + E_USER_ERROR.
	 *
	 * @param  int|digit  $number
	 * @return int|bool   Returns FALSE if error occurred
	 */
	public static function length($number)
	{
		if (! ReflectionTypeHint::isValid()) return false;
		if ($number < 0x80) return 1;
		if ($number < 0x4080) return 2;
		if ($number < 0x204080) return 3;
		if ($number < 0x10204080) return 4;
		trigger_error('Value >= 0x10204080', E_USER_WARNING);
		return false;
	}
}
