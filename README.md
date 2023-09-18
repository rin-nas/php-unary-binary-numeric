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
2^35  0..?          11110xxx xxxxxxxx xxxxxxxx xxxxxxxx xxxxxxxx
```

```
variable 1-2 bytes length:    2^7 + 2^14                      =         16 512
variable 1-3 bytes length:    2^7 + 2^14 + 2^21               =      2 113 664
variable 1-4 bytes length:    2^7 + 2^14 + 2^21 + 2^28        =    270 549 120
variable 1-5 bytes length:    2^7 + 2^14 + 2^21 + 2^28 + 2^35 = 34 630 287 488

fixed 1-byte  length:         2^8                             =            256
fixed 2-bytes length (int32): 2^16                            =         65 536
fixed 3-bytes length:         2^24                            =     16 777 216
fixed 4-bytes length (int64): 2^32                            =  4 294 967 296
```

## Примечание
  Класс не имеет права "падать" (возвращать фатальную ошибку и завершать работу скрипта),
  если в его методы были переданы данные неверного формата.
  Для проверки входных параметров используется assert(string $php_code)!
  Если проверка активирована и возникла ошибка, все методы класса возвращают FALSE.
  Рекомендуется активировать проверку только в режиме тестирования и отладки скрипта.
