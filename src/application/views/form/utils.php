<html>
    <body>
        <form name="Utils" method="post">
            <label for="action">Операция:</label>
            <input type="text" name="action" id="action" autocomplete="off">
            <br><br>
            <label for="params">Параметры:</label>
            <input type="text" name="params" id="params" autocomplete="off">
            <br><br>
            <label for="key">Секретный ключ:</label>
            <input type="text" name="key" id="key" autocomplete="off">
            <br><br>
            <input type="submit" value="Выполнить">
        </form>
        <? if (isset($message)): ?>
            <style>
                p {
                    color: yellow;
                    font-weight: bold;
                    font-size: 16px;
                    border: 2px solid black;
                    padding: 10px;
                    background-color: red;
                }
            </style>
            <p><?= $message ?></p>
        <? endif ?>
    </body>
</html>