# Simply Guillotine Cut

## Найпростіша реалізація розрізання прямоукутного листа на прямокутні елементи методом Гільотинної порізки. Відстань між елементами дорівнює 0.

### Короткий опис файлів

#### Клас `Map2` 
Містить методи для визначення оптимального розташування елементів (`$elements`) на листі (`$bar`).
Крітерієм оптиманості є максимальна площа зайнята елементами в горизонтальному рядку. 

#### Клас `Build`
Потрібен лише для візуальної побудови відображення результату роботи класу `Map2`. Повертає HTML-строку. Кольори вибираються випадковим чином зі списку.

#### Файл `test.html` 
Демонструє побудову карти Гільотинної порізки.
Розташування елементів можна вибрати з двох попередньо визначених варіантів, або згенерувати випадковим чином.
Приклад роботі зображено на скріншоті.
![Simply Guillotine Cut Result Screenshot](https://github.com/andriisgit/Simply-Guillotine-Cut/blob/master/screenshot.png?raw=true)

#### Каталог `tests`
Містить тест одного методу з `Map2`. Призначення цього методу: "Find element with max side size bounded with `$boundWidth` and `$boundHeight`"
Якщо потрібно запустити тест, необхідно встановити PHPUnit (через Composer, можливо, потрібно змінити версію, бо тут - для PHP7.1)