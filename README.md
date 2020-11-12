### PHP API

Создаем объект API и авторизуемся на сервисе:

```php
use WindBridges\BrowserServerClient\BrowserServerClient;

$api = new BrowserServerClient('172.31.192.1');
$api->auth('AUTH_TOKEN');
```

Вызов startSession() приводит к открытию нового окна браузера и возвращает данные сессии, соответствующие новому окну: 

```php
$session = $api->startSession();
```

Используя socketUri сессии, можно подключиться к окну браузера с помощью любой библиотеки, понимающей DevTools protocol. Здесь мы будем использовать `headless-chromium-php`:

```php
use HeadlessChromium\BrowserFactory;
$browser = BrowserFactory::connectToBrowser($session->getSocketUri());
$page = $browser->createPage();
```

Полный пример:
```php
use WindBridges\BrowserServerClient\BrowserServerClient;
use HeadlessChromium\BrowserFactory;

$api = new BrowserServerClient('172.31.192.1');
$api->auth('AUTH_TOKEN');
$session = $api->startSession();
$browser = BrowserFactory::connectToBrowser($session->getSocketUri());
$page = $browser->createPage();
```

Иногда бывает гораздо удобнее дать сессии имя и обращаться к ней по нему, чтобы не хранить идентификаторы сессий, которые назначаются случайным образом. При запросе сессии по имени, сервис будет проверять, открыта ли данная сессия, и если нет, то автоматически создавать ее:

```php
use WindBridges\BrowserServerClient\BrowserServerClient;
use HeadlessChromium\BrowserFactory;

$api = new BrowserServerClient('172.31.192.1');
$api->auth('AUTH_TOKEN');
// Если сессия с именем 'test-session' не существует, то она будет создана
$session = $api->requireSession('test-session');
$browser = BrowserFactory::connectToBrowser($session->getSocketUri());
$page = $browser->createPage();
```

Эту же тактику можно использовать и для страниц браузера. Например, если вам нужна одна вкладка для работы, то можно назначить ей уникальное имя, чтобы не следить за закрытием ненужных вкладок в процессе запуска скрипта:

```php
use WindBridges\BrowserServerClient\BrowserServerClient;

$api = new BrowserServerClient('172.31.192.1');
$api->auth('AUTH_TOKEN');
// Если сессия с именем 'test-session' не существует, то она будет создана
$session = $api->requireSession('test-session');
$sessionPage = $session->requirePage('test-page');
```

`$sessionPage->getSocketUri()` будет каждый раз содержать uri одной и той же страницы. Однако `headless-chromium-php` не позволяет напрямую подключиться к открытой вкладке, поэтому нужно использовать workaround. В будущем для этой цели будет создан хелпер, позволяющий выполнять эти действия в несколько строк.   
 
Workaround для подключения к существующей странице:

```php
use WindBridges\BrowserServerClient\BrowserServerClient;
use HeadlessChromium\BrowserFactory;
use HeadlessChromium\Communication\Message;
use HeadlessChromium\Page;

$api = new BrowserServerClient('172.31.192.1');
$api->auth('AUTH_TOKEN');
// Если сессия с именем 'test-session' не существует, то она будет создана
$session = $api->requireSession('test-session');
$sessionPage = $session->requirePage('test-page');
$browser = BrowserFactory::connectToBrowser($session->getSocketUri());
$target = $browser->getTarget($sessionPage->getTargetId());
$frameTreeResponse = $target->getSession()->sendMessageSync(new Message('Page.getFrameTree'));

if (!$frameTreeResponse->isSuccessful()) {
    throw new Exception('Cannot read frame tree');
}

$page = new Page($target, $frameTreeResponse['result']['frameTree']);
$page->getSession()->sendMessageSync(new Message('Page.enable'));
$page->getSession()->sendMessageSync(new Message('Network.enable'));
$page->getSession()->sendMessageSync(new Message('Runtime.enable'));
$page->getSession()->sendMessageSync(new Message('Page.setLifecycleEventsEnabled', ['enabled' => true]));
// $page->addPreScript($pagePreScript);
``` 