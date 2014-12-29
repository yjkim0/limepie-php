limepie
=======

소개
--------

웹 응용 프로그램을 좀더 쉽고 안전하며 효율적으로 개발하기 위해 만들어진, LGPL 라이센스를 채택한 오픈 소스입니다.
빠르며 최소한의 자원을 사용하는 초경량 프레임워크로 무료 제공됩니다.

일반적으로 웹 응용 프로그램 제작에 요구되는 라이브러리와 안전한 구조를 제공하여,
개발 속도와 품질등 생산성을 향상시키므로
코드의 양을 최소화, 보다 창조적인 작업에 집중할수 있게 합니다.

사용자가 접근한 URI를 '/'로 구분해 의미를 부여하고, 연관 프로그램을 호출하는 방식으로 동작 하며,
MVC (Model-View-Controller) 디자인 패턴을 사용해,
웹 응용 프로그램의 시각적 요소와 작동되는 기능들을 분리할수 있습니다.


ROUTE
--------

URI을 분석하여 framework에 필요한 매개변수를 얻습니다.
이 매개변수들에는 호출될 파일명과 위치한 폴더, 클래스명, 메서드명등이 정의 되어 있습니다.
URI와 프로그램과의 대응 규칙을 정의한
Named subpattern 기반의 정규표현식을 ROUTE RULE이라고 합니다.


ROUTE RULE
----------

### Case #1
apps 폴더 안에 클래스 파일 단위로 기능을 구현하여 사용할수 있는 구조로,
URI은 아래와 같이 /controller/action에 매핑됩니다.
```php
<?php

$router = new router(array(
	'(?P<controller>[^/]+)?(?:/(?P<action>[^/]+))?(?:/(?P<parameter>.*))?' => array(
		'module' => 'apps'
	)
));
```

- `GET http://example/`
  - 파일위치  :  /example/html/apps/index.php
  - 클래스명  :  apps_index
  - 매소드명  :  index or get_index

- `GET http://example/news`
  - 파일위치  :  /example/html/apps/news.php
  - 클래스명  :  apps_news
  - 매소드명  :  index or get_index

- `GET http://example/blog/list`
  - 파일위치  :  /example/html/apps/blog.php
  - 클래스명  :  apps_blog
  - 매소드명  :  list or get_list

- `POST http://example/blog/list`
  - 파일위치  :  /example/html/apps/blog.php
  - 클래스명  :  apps_blog
  - 매소드명  :  list or post_list

- `GET http://example/blog/list/field/date/sort/desc`
  - 파일위치  :  /example/html/apps/blog.php
  - 클래스명  :  apps_blog
  - 매소드명  :  list or get_list
  - 매개변수  :
        ```php
        <?php

        $field  = $this->getParam("field"); // date
        $sort   = $this->getParam("sort");  // desc
        $param0 = $this->getSegment(0);     // blog
        $param1 = $this->getSegment(1);     // list
        $param1 = $this->getSegment(2);     // field
        $param2 = $this->getSegment(3);     // date
        $param3 = $this->getSegment(4);     // sort
        $param4 = $this->getSegment(5);     // desc
        ```


### Case #2
모듈 폴더안 클래스 파일의 index메소드를 기본으로 실행하는 구조로 URI은 아래와 같이 /module/controller에 매핑됩니다.

basedir을 apps로 설정하면 Case #1과는 달리 apps가 모듈네임이 아니라 폴더명이 되었으므로
클래스명에서도 "apps_"는 필요없습니다.

각각의 컨트롤러 클래스의 index 메소드(action 기본 지정)를 자동 실행하므로 클래스내에 반드시 존재해야합니다.
```php
<?php

$router = new router(array(
	'(?P<module>[^/]+)?(?:/(?P<controller>[^/]+))?(?:/(?P<parameter>.*))?' => array(
		'basedir' => 'apps', 'action' => 'index'
	)
));
```

- `GET http://example/`
  - 파일위치  :  /example/html/apps/index/index.php
  - 클래스명  :  index_index
  - 매소드명  :  index or get_index

- `GET http://example/news`
  - 파일위치  :  /example/html/apps/news.php
  - 클래스명  :  news_index
  - 매소드명  :  index or get_index

- `GET http://example/blog/list`
  - 파일위치  :  /example/html/apps/blog/list.php
  - 클래스명  :  blog_list
  - 매소드명  :  index or get_index

- `POST http://example/blog/list`
  - 파일위치  :  /example/html/apps/blog/list.php
  - 클래스명  :  blog_list
  - 매소드명  :  index or post_index

- `GET http://example/blog/list/field/date/sort/desc`
  - 파일위치  :  /example/html/apps/blog/list.php
  - 클래스명  :  blog_list
  - 매소드명  :  index or get_index
  - 매개변수  :
        ```php
        <?php

        $field  = $this->getParam("field"); // date
        $sort   = $this->getParam("sort");  // desc
        $param0 = $this->getSegment(0);     // blog
        $param1 = $this->getSegment(1);     // list
        $param1 = $this->getSegment(2);     // field
        $param2 = $this->getSegment(3);     // date
        $param3 = $this->getSegment(4);     // sort
        $param4 = $this->getSegment(5);     // desc
        ```



### Case #3

좀더 복잡한 형태의 라우터 규칙을 만들어 보겠습니다. URI분리는 정규식을 이용하므로 정교한 규칙 설정이 가능합니다.

아래는 http://example.com/param1/param2, http://example.com/param1/param2/param3/param4 등
3개의 path를 가변적으로 인식할수 있는 규칙입니다.
("/"와 "/"사이의 문자열을 매칭시키고 "/"를 제외한 문자열만 추출합니다. 각각은 필수가 아닙니다.)

```php
<?php

$router = new \lime\router(array(
	'(?P<module>[^/]+)?(?:/(?P<controller>[^/]+))?(?:/(?P<action>[^/]+))?(?:/(?P<parameter>.*))?' => array(
		//'basedir' => 'test'
	)
));
```


### Case #4

아래의 예제는 blog 모듈과 board 모듈에 대해서 http://example.com/blog/321 등 두번째 path가 숫자일경우
read로 간주하게 합니다.
http://example.com/blog/list/47 와 같이 두번째 path가 list이고 세번째 path가 숫자일 경우
페이지 번호로 인식하게 합니다. (순차적으로 검사합니다.)

```php
<?php

$router = new \lime\router(array(
	'(?P<module>blog|board)(?:/(?P<sequence>\d+))?(?:/(?P<parameter>.*))?' => array( // read
		'controller' => 'read'
	),
	'(?P<module>blog|board)(?:/(?P<controller>list))?(?:/(?P<pagenum>\d+))?(?:/(?P<parameter>.*))?' => array() // paging
));
```

- `GET http://example.com/blog/321`
  - 파일위치  :  /example/html/apps/blog/read.php
  - 클래스명  :  blog_read
  - 매소드명  :  index or get_index
  - 매개변수  :
        ```php
        <?php

        $sequence = $this->getParam("sequence"); // 321
        $param1   = $this->getSegment(1);        // 321
        ```

- `GET http://example.com/blog/list/47`
  - 파일위치  :  /example/html/apps/blog/list.php
  - 클래스명  :  blog_list
  - 매소드명  :  index or get_index
  - 매개변수  :
        ```php
        <?php

        $pagenum = $this->getParam("pagenum"); // 47
        $param1  = $this->getSegment(2);       // 47
        ```


### Case #5

http://example.com/blog/339/field/date/sort/desc 는 아래의 라우터에 매칭됩니다.
규칙의 마지막에 `(?:/(?P<parameter>.*))?`를 넣어야 "field/date/sort/desc"를 재처리할 대상으로 판단하여
매개변수 `$field = "date"; $sort = "desc";`를 얻을수 있습니다.

```php
<?php

$router = new \lime\router(array(
	'(?P<module>blog|board)(?:/(?P<sequence>\d+))?(?:/(?P<parameter>.*))?' => array( // read
		'controller' => 'read'
	),
));
```

- `GET http://example/blog/list/field/date/sort/desc`
  - 파일위치  :  /example/html/apps/blog.php
  - 클래스명  :  apps_blog
  - 매소드명  :  list or get_list
  - 매개변수  :
        ```php
        <?php

        $field  = $this->getParam("field"); // date
        $sort   = $this->getParam("sort");  // desc
        $param0 = $this->getSegment(0);     // blog
        $param1 = $this->getSegment(1);     // list
        $param1 = $this->getSegment(2);     // field
        $param2 = $this->getSegment(3);     // date
        $param3 = $this->getSegment(4);     // sort
        $param4 = $this->getSegment(5);     // desc
        ```



아래의 예에서처럼 parameter 규칙 `(?:/(?P<parameter>.*))?`을 정의하지 않았을 경우
'field/date/sort/desc'등 나머지를 처리할 룰이 없으므로 매개변수 parameter의 값이 NULL이 됩니다.

```php
<?php

$router = new \lime\router(array(
	'(?P<module>blog|board)(?:/(?P<sequence>\d+))?' => array( // read
		'controller' => 'read'
	),
));
```

- `GET http://example/blog/list/field/date/sort/desc`
  - 파일위치  :  /example/html/apps/blog.php
  - 클래스명  :  apps_blog
  - 매소드명  :  list or get_list
  - 매개변수  :
        ```php
        <?php

        $field  = $this->getParam("field"); // NULL
        $sort   = $this->getParam("sort");  // NULL
        $param0 = $this->getSegment(0);     // blog
        $param1 = $this->getSegment(1);     // list
        $param1 = $this->getSegment(2);     // field
        $param2 = $this->getSegment(3);     // date
        $param3 = $this->getSegment(4);     // sort
        $param4 = $this->getSegment(5);     // desc
        ```





정규표현식 서브패턴
-------------------

route rule 문법은 정규식만 허용합니다. named subpattern을 활용하면 직관적이고 간결한 rule을 만들수 있습니다. 아래는 정규표현식 서브패턴에 대한 설명입니다.

- 괄호로 구분되고, 중첩도 가능합니다. 캡쳐된 값은 여는 괄호 기준, 왼쪽에서 오른쪽 순서로 1부터 순차적으로 지정된, 배열의 키에 값으로 저장됩니다.

    ```php
    <?php

    $str = "the red king";
    preg_match("#the ((red|white) (king|queen))#", $str, $match);
    print_r($match);
    ```

        <!-- output -->

        Array
        (
            [0] => the red king // 캡쳐된 전체 문자열
            [1] => red king     // 바깥 괄호 전체
            [2] => red          // red와 white를 감싸고 있는 괄호
            [3] => king         // king과 queen을 감싸고 있는 괄호
        )

- 값을 캡쳐할 필요는 없지만, 그룹화를 위해 서브 패턴을 사용할수 있습니다.

    - red와 white를 감싸고 있는 괄호의, 여는 괄호뒤에 `?:`가 지정되면, 그 패턴은 캡쳐되지 않습니다.

        ```php
        <?php

        $str = "the red king";
        preg_match("#the ((?:red|white) (king|queen))#", $str, $match);
        print_r($match);
        ```

            <!-- output -->

            Array
            (
                 [0] => the red king // 캡쳐된 전체 문자열
                 [1] => red king     // 바깥 괄호 전체
                 [2] => king         // king 과 queen을 감싸고 있는 괄호
            )


    - 가장 바깥의 여는 괄호뒤에 `?:`가 지정되면 안쪽 괄호의 패턴만 캡쳐됩니다.

        ```php
        <?php

        $str = "the red king";
        preg_match("#the (?:(red|white) (king|queen))#", $str, $match);
        print_r($match);
        ```

            <!-- output -->

            Array
            (
                [0] => the red king // 캡쳐된 전체 문자열
                [1] => red          // red와 white를 감싸고 있는 괄호
                [2] => king         // king과 queen을 감싸고 있는 괄호
            )


- NAMED SUBPATTERN

     `(?P<키명>패턴)` 와 같이 여는 괄호뒤에 `?P`와 함께 `<키명>`을 지정하면,
    `패턴`에 매치되는 문자열이 지정한 `키명`의 값으로 저장됩니다.

    ```php
    <?php

    $str = "smith6";
    preg_match("#(?P<name>[a-z0-9]+)#", $str, $match);
    print_r($match);
    ```

        <!-- output -->

        Array
        (
            [0] => smith6
            [name] => smith6 // [a-z0-9]+ 패턴에 해당하는 문자열을 name이란 키명의 값으로 저장
            [1] => smith6
        )

- Single-character quantifiers

     `(서브패턴)?`과 같이 `서브패턴`이 끝난뒤의 `?`는 `(서브패턴){0,1}` 과 동일합니다.
     전체를 기준으로 `서브패턴`에 매칭되는 문자열이 없어도 참입니다.


    - 정규표현식의 마지막에 `?`가 없으므로 모듈과 파라메터는 모두 필수입니다.

        ```php
        <?php

        $str = "board";
        preg_match("#(?P<module>[^/]+)(?:/(?P<parameter>.*))#", $str, $match);
        print_r($match);
        ```

            <!-- output -->

            Array
            (
            )

    - 정규표현식의 마지막에 `?`가 있으므로 모듈은 필수이고, 두번째 서브패턴의 경우, 값이 있을때만 파라메터에 매칭됩니다.
        ```php
        <?php

        $str = "board";
        preg_match("#(?P<module>[^/]+)(?:/(?P<parameter>.*))?#", $str, $match);
        print_r($match);
        ```

            <!-- output -->

            Array
            (
                [0] => board
                [module] => board
                [1] => board
            )




CONTROLLER
----------

비지니스 로직(모델)과 프리젠테이션 로직(뷰)의 상호동작을 관리합니다.

URI는 ROUTE를 거쳐 사용자 컨트롤러 클레스의 액션 메소드를 동작시킵니다.
사용자 컨트롤러 클레스는 반드시 부모 컨트롤러 클레스로 부터 상속(extends) 받아야 하며,
그렇지 않을 경우 컨트롤러의 기능을 사용할 수 없습니다.

```php
<?php
// apps_blog.php

class apps_blog extends \lime\controller {
     function get_list() {
         echo "Hello World!";
     }
}
```


컨트롤러 클래스는 서브 클래스를 만들어 컨트롤러 클래스의 기반이되는 인터페이스와 기능을 새롭게 정의 수 있습니다.
아래는 모든 페이지에서 접속자의 회원정보를 검사하기 위해 컨트롤러 클래스를 확장한 예제입니다.

```php
<?php
// my_controller.php

class my_controller extends \lime\controller {
    public $user = array();    // 접속자의 회원정보

    function __construct() {   // 생성자를 사용한다면
        parent::__construct(); // 반듯이 부모 컨트롤러 클래스의 생성자를 호출해야함

        $user_id = cookie::get("user_id");
         $this->user = $this->getUserInfo($user_id); // 접속자의 회원정보
    }

     function getUserInfo($id) {
         return array("......");
     }
}
```

```php
<?php
// apps_blog.php

class apps_blog extends my_controller {
     function get_list() {
         echo $this->user." Hello World!";
     }
}
```



### 매개변수

URI로 매개변수를 얻는 방법은 3가지가 있습니다.


1. segment

  segment 는 URI에서 0부터 1씩증가하는 형태로 순서대로 접근하여 매개변수를 얻습니다.

  `GET http://example/blog/list/date/desc`

  ```php
  <?php

  class apps_blog extends \lime\controller {
        function get_list() {
            echo $this->getSegment(0); // blog
            echo $this->getSegment(1); // list
            echo $this->getSegment(2); // date
            echo $this->getSegment(3); // desc
        }
  }
  ```

2. parameter

  parameter 는 ROUTE에서 module, controller, action등에 매칭된 나머지로 짝을 맺어 매개변수를 얻습니다.

  `GET http://example/blog/list/field/date/sort/desc`

  ```php
  <?php

  class apps_blog extends \lime\controller {
        function get_list() {
            // blog는 controller
            // list는 action
            echo $this->getParam("field"); // date
            echo $this->getParam("sort");  // desc
        }
  }
  ```


3. argument

  메소드의 argument 로 부터 매개변수를 얻습니다.

  `GET http://example/blog/list/date/desc`

  ```php
  <?php

  class apps_blog extends \lime\controller {
        function get_list($controller, $action, $field, $sort) {
            echo $controller; // blog
            echo $action;     // list
            echo $field;      // date
            echo $sort;       // desc
        }
  }
  ```


### 에러 처리 컨트롤러

프레임웍은 서버상에 실제 존재하는 파일을 실행하는 것이 아니라 URI 요청을 ROUTE의 분석에 의해
사용자 컨트롤러 클레스의 액션 메소드를 실행하여 동작시키므로,
웹서버가 자체적으로 보여주는 에러페이지들을 사용할수 없고,
제공되는 에러 처리 컨트롤러를 사용하거나 확장하여 에러페이지를 작성하여야 합니다.


VIEW
----

몇개의 간단한 기호를 사용함으로 개발자의 반복작업을 줄이거나, 디자이너가 템플릿 파일을 효율적으로 다룰수도 있습니다. 템플릿 기호를 PHP 코드로 변환하고, PHP 파일을 실행하여 Pure PHP 그 이상의 강력한 성능을 발휘합니다.



PHP 코드가 있습니다.

```php
<?php if(TRUE === isset($address) && TRUE === is_array($address) { ?>
     <?php foreach ($address as $key => $addr):?>
         <?php if(isset($addr["name"])) { ?>
             <?php echo $addr["name"]; ?>
         <?php } ?>
     <?php endforeach; ?>
<?php } else { ?>
     <?php echo 'none'; ?>
<?php } ?>
```

템플릿 엔진에서 사용되는 기호는 아래와 같습니다.

```
{@addr=address}
     {?isset(addr.name)}
         {=addr.name}
     {/}
{:}
     none
{/}
```

이 기호들은 템플릿 엔진의 파서를 거쳐 위와 동일한 PHP 파일을 생성합니다.




###명령어

1. 반복문 `{@row=data} ... {/}`

  @는 루프문의 시작을 나타내며,
  data가 리턴하는 배열의 요소수만큼 반복됩니다.
  `foreach($data as $key => $row) { ... }`로 변환.

2. 조건문 `{?expression} ... {/}`

  ?는 조건문의 시작을 나타내며,
  `if($expression) { ... }` 로 변환.


3. 조건문 `{?expression} ... {:} ... {/}`

  :는 else구문을 나타내며,
  `if($expression) { ... } else { ... }` 로 변환.


4. 조건문 `{?expression1} ... {:?expression2} ... {/}`

  :?는 else if 구문을 나타내며,
  `if($expression1) { ... } else if($expression2) { ... }` 로 변환.


5. 종결문 {/}

  /는 루프나 분기문의 끝을 나타냅니다.


6. 출력문 `{=expression}`

  =는 템플릿 변수 또는 표현식의 값을 출력하며 `echo $expression;` 로 변환.
  print_r과 같이 함수 내에서 print하고 boolean값을 리턴하는 경우 "="를 생략해야함을 주의하세요.




###예약변수

```php
<?php
// index.php

...
$tpl->define('index', 'index.tpl');
$tpl->assign('fruit', array('apple'=>'red', 'banana'=>'yellow', 30=>'unknown'));
$tpl->print_('index');
```

1. index_

  루프문 내에서 사용하며 0부터 시작하는 루프번호입니다.

  ```
  <!-- index.tpl -->

  {@row=fruit}
        {=row.index_}
  {/}
  ```

  ```
  <!-- output -->

  0
  1
  2
  ```

2. key_

  루프로 할당된 배열의 키입니다.

  ```
  <!-- index.tpl -->

  {@row=fruit}
        {=row.key_}
  {/}
  ```

  ```
  <!-- output -->

  apple
  banana
  30
  ```

3. value_

  루프로 할당된 배열의 값입니다.

  ```
  <!-- index.tpl -->

  {@row=fruit}
        {=row.value_}
  {/}
  ```

  ```
  <!-- output -->

  red
  yellow
  unknown
  ```

4. size_

  루프의 전체 반복 횟수, 즉 루프로 할당된 배열의 크기입니다.

  루프문 내에서만 유효합니다.

  ```
  <!-- index.tpl -->

  {@row=fruit}
        {=row.index_} : {=fruit.size_}
  {/}
  ```

  ```
  <!-- output -->

  0 : 3
  1 : 3
  2 : 3
  ```

5. last_

  루프의 마지막 요소인지 체크합니다. 아래는 루프의 마지막인 경우 <br />를 생략하는 예제입니다.

  ```
  <!-- index.tpl -->

  {@row=fruit}
        {=row.index_}{?!row.last_} <br />{/}
  {/}
  ```

  ```
  <!-- output -->

  red <br />
  yellow <br />
  unknown
  ```
