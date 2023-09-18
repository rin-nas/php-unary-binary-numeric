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

```
2^0  1       0
2^2  4       10xx
2^3  8       110xxx
2^4  16      1110xxxx
2^5  32      11110xxx xx
2^6  64      111110xx xxxx
2^7  128     1111110x xxxxxx
2^8  256     11111110 xxxxxxxx
2^9  512     11111111 0xxxxxxx xx
2^10  1024   11111111 10xxxxxx xxxx
2^11  2048   11111111 110xxxxx xxxxxx
2^12  4096   11111111 1110xxxx xxxxxxxx
2^13  8192   11111111 11110xxx xxxxxxxx xx
2^14  16384  11111111 11110xxx xxxxxxxx xxxx
2^15  32768  11111111 111110xx xxxxxxxx xxxxxx
2^15  32768  11111111 111110xx xxxxxxxx xxxxxxxx
2^16  65536  11111111 1111110x xxxxxxxx xxxxxxxx xx
```
