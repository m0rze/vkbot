VK Cover Chat-Bot
=
1. В аккаунте создать Standalone приложение, включить его и взять оттуда сервисный ключ <br><br>
2. Настроить Callback для API в управлении сообществом, урл добавить http://domain.com/vkbot/index.php <br><br>
3. Для установки необходимо заполнить данные в Config.php и в браузере запустить `/index.php?install=getPass`, где 
   getPass это значение из Config.php <br><br>
4. Установить права `777` на папку `Logs` и на папку `HeadSrc`, а также на `все содержимое папки HeadSrc` <br><br>
5. В чат-боте для изменения количества смартфонов необходимо ввести nsc:123, где 123 это количество, а nsc: 
   обязательный параметр<br><br>
6. Для ежеминутного обновления обложки необходимо на своем сервере поставить на ежеминутный крон такую команду:<br>
   ```Bash 
   wget -O /dev/null -q http://domain.com/vkbot/cron.php
7. На вашем сервере должно быть установлено PHP расширение imagick 