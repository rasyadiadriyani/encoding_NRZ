<?php
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