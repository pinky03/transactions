# Тестовое задание PHP

Установка:
1. Настроить подключение к БД в .env
2. Выполнить миграции
3. Выполнить посев тестовых данных
4. Запустить сервер

## Запросы  
Добавить транзакцию пользователю:  
*POST /transactions/*  
Поля согласно модели App\Models\Transaction

Получить баланс пользователя по его id:  
*GET /transactions/balance/{user_id}*  


