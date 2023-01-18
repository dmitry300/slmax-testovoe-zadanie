<?php

if (!class_exists('User')) {
    throw new Exception('class User does not exist');
} else {
    require_once('./User.php');

    class People
    {
        private $idList;

        public function __construct($sign, $id)
        {
            try {
                $this->validate($sign);
                $this->idList = $this->getIdList($sign, $id);
            } catch (Exception $e) {
                echo 'Выброшено исключение: ', $e->getMessage(), "\n";
            }

        }

        public function validate($sign): bool
        {
            $validSigns = ['>', '<', '!='];
            if (!in_array($sign, $validSigns)) {
                throw new Exception("'$sign' is not valid");
            }
            return true;
        }

        public function getIdList($sign, $id)
        {
            $connect = mysqli_connect("localhost", "root", "root", "people");
            if ($sign == '!=') {
                $sql = 'SELECT id FROM `users` WHERE id <> ' . $id;
            } else {
                $sql = 'SELECT id FROM `users` WHERE id ' . $sign . ' ' . $id;
            }
            return $connect->query($sql);
        }

        public function getUserList(): array
        {
            $userList = [];
            foreach ($this->idList as $id) {
//            $userList[] = (new User(null))->getUserById($id['id']);
                $userList[] = (new User($id['id']));
            }
            return $userList;
        }

        public function deleteById(): bool
        {
            $userList = $this->getUserList();
            foreach ($userList as $user) {
                $user->delete();
            }
            return true;
        }

    }
}
