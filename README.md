## Описание запуска приложения


#### Скопировать репозиторий
```markdown
git clone https://github.com/Veedok/Symfony.git
```

#### Перейти в основную деректорию и выполнить
```markdown
docker compose up -d
```

#### Зайти в контейнер docker
```markdown
docker compose exec app bash
```

#### Установить composer
```markdown
composer install
```

#### Создать БД
```markdown
php bin/console doctrine:database:create
```

#### Выполнить миграции
```markdown
php bin/console doctrine:migrations:migrate
```

#### Наполнить БД 2мя командами. Создастя 1 Админ (Логин Veedok пароль 123qwerty) и 20 пользователей (Логин User[0-19] пароль qwerty123). По второой команде создастся 100 заметок рандомно прекрипленных к пользователям
```markdown
php bin/console app:make:users
php bin/console app:make:notes
```
### Дать права на папку для записи файлов
```markdown
 chmod -R 777 public/note
```

### Пометки
#### 1 Не успел написать тесты
#### 2 Не везде успел написать phpDoc
#### 3 Не успел разнести логику из контроллеров в сервисы

##### Я понимаю что дотачивать можно бесконечно но основной функционал готов. Если бы я смог выделить больше времени то с радостью бы допилил. Надеюсь на понимание