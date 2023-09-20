<?php
class VendingMachine
{

    private $currency;
    private $drinks;
    private $balance;
    private $allowedCoins;
    public function __construct($currency, $drinks)
    {
        $this->currency = new Currency($currency['sign'], $currency['space'], $currency['position']);
        $this->drinks = [];
        $this->balance = 0.0;
        $this->allowedCoins = [0.05, 0.10, 0.20, 0.50, 1.00];
        foreach ($drinks as $name => $price) {
            $this->drinks[] = new Drinks($name, $price);
        }
    }
    public function viewDrinks()
    {
        echo "Напитки:\n";
        foreach ($this->drinks as $drink) {
            echo $drink->getName() . ": " . $this->currency->of($drink->getPrice()) . "\n";
        }
        return $this;
    }
    public function putCoin($coin)
    {
        if (!in_array($coin, array_values($this->allowedCoins))) {
            echo "Автомата приема монети от: 0.05лв, 0.10лв, 0.20лв, 0.50лв, 1.00лв\n";
        } else {
            $this->balance += $coin;
            echo "Успешно поставихте " . $this->currency->of($coin) . " текущата Ви сума е " . $this->currency->of($this->balance) . "\n";
        }
        return $this;
    }
    public function buyDrink($drinkName)
    {
        $drinkToBuy;
        foreach ($this->drinks as $drink) {
            if ($drink->getName() === $drinkName) {
                $drinkToBuy = $drink;
                break;
            }
        }
        if (!isset($drinkToBuy)) {
            echo "Исканият продукт не е намерен.\n";
        } elseif ($this->balance - $drinkToBuy->getPrice() < 0) {
            echo "Недостатъчна наличност.\n";
        } else {
            $this->balance -= $drinkToBuy->getPrice();
            echo "Успешно закупихте " . $drinkName . " от " . $this->currency->of($drinkToBuy->getPrice()) . " текущата Ви сума е " . $this->currency->of($this->balance) . "\n";
        }
        return $this;
    }
    public function viewAmount()
    {
        echo "Tекущата Ви сума е " . $this->currency->of($this->balance) . "\n";
        return $this;
    }
    public function getCoins()
    {
        if ($this->balance <= 0) {
            echo "Няма ресто за връщане.\n";
            return $this;
        }
        $result = "Получихте ресото " . $this->currency->of($this->balance) . " В монети от ";
        $coins = [];
        foreach ($this->allowedCoins as $entry) {
            $coins[(string) number_format($entry, 2)] = 0;
        }
        for ($arrayIndex = sizeof($this->allowedCoins) - 1; $arrayIndex > 0; $arrayIndex--) {
            $coin = $this->allowedCoins[$arrayIndex];
            while (round($this->balance - $coin, 2) >= 0) {
                $this->balance = round($this->balance - $coin, 2);
                $coins[(string) number_format($coin, 2)] += 1;
            }
        }
        foreach (array_reverse($coins) as $key => $value) {
            if ($value > 0) {
                $result .= $value . "x" . $this->currency->of($key);
            }
        }
        echo $result . "\n";
        return $this;
    }
}

enum CurrencyPosition
{
    case BEFORE;
    case AFTER;
}

class Currency
{
    private $sign;
    private $space;
    private $position;

    public function __construct($sign, $space, $position)
    {
        if (empty($sign) || !isset($sign)) {
            die("Невалидни данни за валутата");
        }
        if (!isset($space)) {
            die("Липсваща информация за разделителния символ на валутата");
        }
        if (!isset($position)) {
            die("Липсващи данни за позицията на валутния знак");
        }
        $this->sign = $sign;
        $this->space = $space;
        $this->position = $position;

    }
    public function of($amount)
    {
        if ($this->position === CurrencyPosition::BEFORE) {
            return $this->sign . number_format($amount, 2) . $this->space;
        } elseif ($this->position === CurrencyPosition::AFTER) {
            return number_format($amount, 2) . $this->space . $this->sign;
        } else {
            die("Невалидна позиция на валутния знак");
        }
    }
}
class Drinks
{
    private $name;
    private $price;
    public function __construct($name, $price)
    {
        if (empty($name) || !isset($name)) {
            die("Липсващо име на напитка");
        }
        if (empty($price) || !isset($price)) {
            die("Липсваща цена на напитка");
        }
        $this->name = $name;
        $this->price = $price;
    }

    public function getPrice()
    {
        return $this->price;
    }
    public function getName()
    {
        return $this->name;
    }

}
$machine = new VendingMachine(
    [
        'sign' => 'лв.',
        'space' => '',
        'position' => CurrencyPosition::AFTER,
    ],
    [
        'Milk' => 0.50,
        'Espresso' => 0.40,
        'Long Espresso' => 0.60,
    ]
);

$machine
    ->buyDrink('espresso')
    ->buyDrink('Espresso')
    ->viewDrinks()
    ->putCoin(2)
    ->putCoin(1)
    ->buyDrink('Espresso')
    ->getCoins()
    ->viewAmount()
    ->getCoins();
?>