<?php

class User
{
    private $id;
    private $name;
    private $surname;
    private $birthdate;
    private $sex;
    private $city;
    private $connect;
    const DB_HOST = "localhost";
    const DB_USER = "root";
    const DB_PASS = "root";
    const DB_NAME = "people";

    public function __construct($user)
    {
        $this->connect = mysqli_connect(self::DB_HOST, self::DB_USER, self::DB_PASS, self::DB_NAME);
        if (is_array($user)) {
            $this->name = $user['name'];
            $this->surname = $user['surname'];
            $this->birthdate = (new DateTime(trim($user['birthdate'])))->format('Y-m-d');
            $this->city = $user['city'];
            $this->sex = $user['sex'];
            $userFromDb = $this->find($user);
            if ($userFromDb['name'] !== $this->name && $userFromDb['surname'] !== $this->surname &&
                $userFromDb['birthdate'] !== $this->birthdate && $userFromDb['sex'] !== $this->sex && $userFromDb['city'] !== $this->city) {
                $this->save();
            }
        } else {
            $this->setFields($this->getUserById($user));
        }
    }

    public function find($user)
    {
        $sql = 'SELECT * FROM `users` WHERE name = "' . $user['name'] . '" AND surname="' . $user['surname'] . '" AND birthdate="' . $this->birthdate . '" AND sex="' . $user['sex'] . '" AND city="' . $user['city'] . '"';
        $userFromDB = mysqli_query($this->connect, $sql);
        return mysqli_fetch_array($userFromDB, MYSQLI_ASSOC);

    }

    protected function validate($user): bool
    {
        foreach ($user as $param => $value) {
            if ($param == 'name') {
                if (!preg_match("/^[a-zA-Zа-яёА-ЯЁ]+$/u", $value)) {
                    throw new Exception("Name is invalid");
                }
            } elseif ($param == 'surname') {
                if (!preg_match("/^[a-zA-Zа-яёА-ЯЁ]+$/u", $value)) {
                    throw new Exception("Surname is invalid");
                }
            } elseif ($param == 'sex') {
                if (!($value == 0 || $value == 1)) {
                    throw new Exception("Gender is invalid. This system support two genders male(0) and female(1)");
                }
            } elseif ($param == 'birthdate') {
                list($year, $month, $day) = explode("-", $value);
                if (!checkdate($month, $day, $year)) {
                    throw new Exception("Date is invalid.");
                }
            }
        }
        return true;
    }


    public function delete(): bool
    {
        $query = "DELETE FROM `users` WHERE id = {$this->id}";
        $result = mysqli_query($this->connect, $query);
        $this->connect->close();
        return $result;
    }


    public function save()
    {
        $user = array('name' => $this->name, 'surname' => $this->surname, 'birthdate' => $this->birthdate, 'sex' => $this->sex, 'city' => $this->city);
        try {
            if ($this->validate($user)) {
                $sql = 'INSERT INTO users(name, surname, birthdate, city, sex) VALUES ("' . $this->name . '", "' . $this->surname . '", "' . $this->birthdate . '", "' . $this->city . '", "' . $this->sex . '")';
                $this->connect->query($sql);
            }
        } catch (Exception $e) {
            echo 'Выброшено исключение: ', $e->getMessage(), "\n";
        }
    }

    public function setFields($user)
    {
        $this->id = $user['id'];
        $this->name = $user['name'];
        $this->surname = $user['surname'];
        $this->sex = $user['sex'];
        $this->city = $user['city'];
        $this->birthdate = $user['birthdate'];
    }

    public static function sexToString($sex): string
    {
        return $sex == 0 ? 'male' : ($sex == 1 ? 'female' : 'this system support two genders male(0) and female(1)');
    }

    public static function getAge($date): int
    {
        $birthdate = new DateTime($date);
        $now = new DateTime();
        $interval = $now->diff($birthdate);
        return $interval->format('%y');
    }

    public function format(): StdClass
    {
        $sex = self::sexToString($this->sex);
        $birthdate = self::getAge($this->birthdate);

        $user = new StdClass;
        $user->id = $this->id;
        $user->name = $this->name;
        $user->surname = $this->surname;
        $user->birthdate = $birthdate;
        $user->sex = $sex;

        return $user;
    }

    public function getUserById($id)
    {
        $sql = "SELECT * FROM `users` WHERE id = $id";
        $userFromDB = mysqli_query($this->connect, $sql);
        return mysqli_fetch_array($userFromDB, MYSQLI_ASSOC);
    }
}