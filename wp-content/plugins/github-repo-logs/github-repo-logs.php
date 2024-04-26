<?php
/*
Plugin Name: GitHub Repo Logs
Description: Muestra los registros de tu repositorio de GitHub en el admin de WordPress.
Version: 1.0
Author: Benjamin Oscco Arias
*/

add_action('admin_menu', 'github_repo_logs_menu');

function github_repo_logs_menu()
{
    add_menu_page(
        'GitHub Repo Logs',
        'GitHub Repo Logs',
        'manage_options', 
        'github_repo_logs', 
        'github_repo_logs_page', 
        'dashicons-admin-tools', 
        30 
    );
}
function display_github_form($username_value = '', $repository_value = '', $token_value = '') {
    echo '<form method="post">';
    echo '<label for="github_username">Nombre de usuario de GitHub:</label><br>';
    echo '<input type="text" id="github_username" name="github_username" value="' . esc_attr($username_value) . '"><br>';
    echo '<label for="github_repository">Repositorio de GitHub:</label><br>';
    echo '<input type="text" id="github_repository" name="github_repository" value="' . esc_attr($repository_value) . '"><br>';
    echo '<label for="github_token">Token de acceso de GitHub:</label><br>';
    echo '<input type="text" id="github_token" name="github_token" value="' . esc_attr($token_value) . '"><br>';
    echo '<input type="submit" name="submit" value="Guardar">';
    echo '</form>';
}
function display_commit_table($commits) {
    echo '<h3>Últimos commits:</h3>';
    echo '<table class="wp-list-table widefat fixed striped">';
    echo '<thead>';
    echo '<tr>';
    echo '<th>Commit</th>';
    echo '<th>Autor</th>';
    echo '</tr>';
    echo '</thead>';
    echo '<tbody>';
    foreach ($commits as $commit) {
        echo '<tr>';
        echo '<td>' . $commit['commit']['message'] . '</td>';
        echo '<td>' . $commit['commit']['author']['name'] . '</td>';
        echo '</tr>';
    }
    echo '</tbody>';
    echo '</table>';
}
function get_github_commits($username, $repository, $access_token) {
    $api_url = "https://api.github.com/repos/$username/$repository/commits";
    $headers = array(
        'Authorization' => 'token ' . $access_token,
    );
    $response = wp_remote_get(
        $api_url,
        array(
            'headers' => $headers,
        )
    );
    if (!is_wp_error($response) && $response['response']['code'] === 200) {
        return json_decode($response['body'], true);
    } else {
        return false;
    }
}
function github_repo_logs_page() {
    echo '<div class="wrap">';
    echo '<h2>GitHub Repo Logs</h2>';
    if (isset($_POST['submit'])) {
        $username = $_POST['github_username'];
        $repository = $_POST['github_repository'];
        $access_token = $_POST['github_token'];
        $commits = get_github_commits($username, $repository, $access_token);
        if ($commits !== false) {
            display_commit_table($commits);
        } else {
            echo 'Error al obtener los commits del repositorio.';
            echo '<br>';
            echo 'Detalles del error:';
            echo '<pre>';
            var_dump($response);
            echo '</pre>';
        }
    }
    display_github_form(
        isset($_POST['github_username']) ? $_POST['github_username'] : '',
        isset($_POST['github_repository']) ? $_POST['github_repository'] : '',
        isset($_POST['github_token']) ? $_POST['github_token'] : ''
    );

    echo '</div>';
}


