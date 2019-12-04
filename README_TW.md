# Documon 核心模組

## 需求

* PHP 7.2+ 並安裝 pcntl extension
* Node.js 12.0+ 並安裝 yarn

## 安裝

```bash
composer require goez/documon-core
```

## 使用方式

### 在 Laravel/Lumen 應用程式加入指令

在 Laravel 應用程式的 `config/app.php` 加入以下 Provider 類別：

```php
'providers' => [
    // ...
    Goez\Documon\RendererServiceProvider::class, // 加入此行
],
```

若是 Lumen 應用程式的話，則是在 `bootstrap/app.php` 中的 `Register Service Providers` 區域下加入以下程式：

```php
$app->register(Goez\Documon\RendererServiceProvider::class);
```

**重要：如果在 Laravel/Lumen 應用程式中安裝這個套件的話，可能會有版本衝突的問題；因此建議改用 [goez/documon](https://github.com/goez-tools/documon/blob/master/README_TW.md) 提供的 binary 來建置線上文件。**

## 指令使用方式

請參考 [goez/documon](https://github.com/goez-tools/documon/blob/master/README_TW.md) 。

## 建立自訂 Renderer



## 修改

先以全域方式安裝 `spatie/phpunit-watcher` 套件:

```bash
composer global require spatie/phpunit-watcher
```

執行以下指令，以便在修改程式後能自動執行測試：

```bash
phpunit-watcher watch
```

僅執行自動化測試：

```bash
vendor/bin/phpunit
```

## 許可證

[MIT](LICENSE)
