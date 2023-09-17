# Unary-binary numeric encoding/decoding to/from string

Ported partly from XBUP Project (Extensible Binary Universal Protocol)
http://www.xbup.org/

Numbers are stored in UTF-8 like encoding, first unary length and then binary
value with recursive application. Code is prefix and deterministic.
```
2^7   0..7Fh        0xxxxxxx
2^14  0..3FFFh      10xxxxxx xxxxxxxx
2^21  0..1FFFFFh    110xxxxx xxxxxxxx xxxxxxxx
2^28  0..0FFFFFFFh  1110xxxx xxxxxxxx xxxxxxxx xxxxxxxx
2^35  0..?          11110xxx xxxxxxxx xxxxxxxx xxxxxxxx
```

```
variable 1-2 bytes length:    2^7 + 2^14                      =         16 512
variable 1-3 bytes length:    2^7 + 2^14 + 2^21               =      2 113 664
variable 1-4 bytes length:    2^7 + 2^14 + 2^21 + 2^28        =    270 549 120
variable 1-5 bytes length:    2^7 + 2^14 + 2^21 + 2^28 + 2^35 = 34 630 287 488
fixed 4-bytes length (int32): 2^32                            =  4 294 967 296
```

## Примечание
  Класс не имеет права "падать" (возвращать фатальную ошибку и завершать работу скрипта),
  если в его методы были переданы данные неверного формата.
  Для проверки входных параметров используется assert(string $php_code)!
  Если проверка активирована и возникла ошибка, все методы класса возвращают FALSE.
  Рекомендуется активировать проверку только в режиме тестирования и отладки скрипта.

## Useful links
* http://en.wikipedia.org/wiki/Exponential-Golomb_coding
* http://en.wikipedia.org/wiki/Burrows–Wheeler_transform
* https://www.timescale.com/blog/time-series-compression-algorithms-explained/

## Exponential-Golomb_coding

```
Кодируемое число на входе => Битовое представление => Битовое представление на выходе
0 => 1 => 1
1 => 10 => 010  (добавили вначале один ноль)
2 => 11 => 011
3 => 100 => 00100  (добавили в начале два нуля)
4 => 101 => 00101
5 => 110 => 00110
6 => 111 => 00111
7 => 1000 => 0001000  (добавили в начале три нуля)
8 => 1001 => 0001001
9 => 1010 =>   
...
```
