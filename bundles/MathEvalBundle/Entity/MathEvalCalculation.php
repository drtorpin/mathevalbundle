<?php


namespace MathEvalBundle\Entity;

class MathEvalCalculation implements MathEvalInterface
{

    /**
     * @var еstring - текст ошибки
     */
    private $error;

    /**
     * Вычисляем входящее математическое выражение
     * @param $expression
     * @return json - результат + ошибка
     */
    public function getCalculation($expression)
    {
        if ($this->validateExpression($expression)) {
            $result = $this->evalExpression($expression);
        }

        if ($this->getError()) {
            return $this->getError();
        }

        return $result;
    }
    /**
     * Список разрешенных знаков математических операций
     * @return array
     */
    private function getValidSymbols()
    {
        return [
            '-',
            '+',
            '*',
            ':'
        ];
    }
    /**
     * Вычисление
     * @param $number1 integer - исходное число
     * @param $sign string - математический знак из выражения
     * @param $number2 integer - второе число в выражении
     * @return float|int|false - результат вычисления одного действия | false - ошибка
     */
    private function evalMathAction($number1, $sign, $number2)
    {
        $number1 = intval($number1);
        $number2 = intval($number2);
        switch ($sign) {
            case '+':
                return $number1 + $number2;
            case '-':
                return $number1 - $number2;
            case ':':
                if ($number2 == 0) {
                    $this->setError('На 0 делить нельзя!');
                    return false;
                }
                return $number1 / $number2;
            case '*':
                return $number1 * $number2;
        }
        $this->setError('Не определено математическое действие при вычислении.');
        return false;
    }
    /**
     * Вывод разрешенных знаков математических операций для регулярного выражения
     * @return string
     */
    private function getRegValidSymbols()
    {
        return join('', $this->getValidSymbols());
    }
    /**
     * Вывод разрешенных знаков математических операций в виде списка для вывода в тексты ошибок
     * @return string
     */
    private function getListValidSymbols()
    {
        return join(',', $this->getValidSymbols());
    }
    /**
     * Установка текста ошибки
     * @param $message
     */
    private function setError($message)
    {
        $this->error = $message;
    }
    /**
     * Получить сообщение об ошибке
     * @return mixed
     */
    private function getError()
    {
        return $this->error;
    }
    /**
     * Проверка введенного выражения на валидность
     * @param $expression string - входящее математическое выражение в виде строки
     * @return bool - валидное или нет
     */
    private function validateExpression($expression)
    {
        if (!$expression) {
            $this->setError('Не указано математическое выражение');
            return false;
        }
        $validSymbols = $this->getRegValidSymbols();
        if (preg_match('/[^0-9' . $validSymbols . ']/m', $expression)) {
            $this->setError('В выражении используются недопустимые символы. Необходимо использовать только числа и следующие математические знаки: ' . $this->getListValidSymbols());
            return false;
        }
        $checkExpression = preg_replace(['/\d{1,}/', '/[^\d]/'], ['1', '_'], $expression);
        if (preg_match('/[' . $validSymbols . ']{2,}/', $checkExpression)) {
            $this->setError('В выражении нельзя использовать два или больше подряд знаков математических действий.');
            return false;
        }
        if ($checkExpression[0] == '_') {
            $this->setError('Выражение не должно начинаться со знака математического действия.');
            return false;
        }
        if ($checkExpression[strlen($checkExpression) - 1] == '_') {
            $this->setError('Выражение не должно заканчиваться знаком математического действия.');
            return false;
        }
        return true;
    }
    /**
     * @param $expression string - математическое выражение
     * @return integer - результат вычисления
     */
    private function evalExpression($expression)
    {
        $expressionCheck = trim(preg_replace('/(\d{1,})/', ' \1 ', $expression));
        $expressionParts = explode(' ', $expressionCheck);
        $count = count($expressionParts);
        if ($count > 1) {
            $result = $expressionParts[0];
            for ($n = 0; $n < $count; $n += 2) {
                if (isset($expressionParts[$n + 2])) {
                    $result = $this->evalMathAction($result, $expressionParts[$n + 1], $expressionParts[$n + 2]);
                    if ($result === false) {
                        return false;
                    }
                }
            }
        } else {
            return $expression;
        }
        return $result;
    }
}