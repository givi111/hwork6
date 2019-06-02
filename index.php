<?php
require_once __DIR__ . '/Services/autoload.php';
use Database\DataManager;
$data_manager = new DataManager('webdb', 'root', 'toor');
$where = [
    [
        'logic_operator' => '',
        'column' => 'name',
        'operator' => '=',
        'value' => 'givi',
    ],
    [
        'logic_operator' => 'OR',
        'column' => 'username',
        'operator' => '=',
        'value' => 'username',
    ],
];
$order = [
    'surname' => 'desc',
    'name' => 'asc',
];
// Select data
$data = $data_manager->select(['name', 'username'], 'users', $where, $order, 5);
var_dump($data);
// Insert data
$insert_status = $data_manager->insert(
    'users', ['username' => 'user', 'name' => 'user']
);
var_dump($insert_status);
// Update data
$update_status = $data_manager->update(
    'users', ['username' => 'newUser', 'name' => 'newUser'], $where
);
var_dump($update_status);
// Delete data
$delete_status = $data_manager->delete('users', $where);
var_dump($delete_status);