<?php

/**
 * @file
 * The PHP page that serves all page requests on a Drupal installation.
 *
 * All Drupal code is released under the GNU General Public License.
 * See COPYRIGHT.txt and LICENSE.txt files in the "core" directory.
 */

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

$autoloader = require dirname(__DIR__, 2) . '/vendor/autoload.php';

// Setup Twig.
$twig_options = [
    'cache' => dirname(__DIR__, 2) . '/private/cache',
    'auto_reload' => TRUE,
];

$templates_directory = dirname(__DIR__, 2) . '/templates';
$loader = new FilesystemLoader($templates_directory);
$twig = new Environment($loader, $twig_options);

$directory_name = dirname($_SERVER['SCRIPT_NAME']);

$page = str_replace($directory_name, '', $_SERVER['REQUEST_URI']);
$page = trim($page, '/');
$template_path = $templates_directory . '/prototypes/' . $page . '.twig';
if (!file_exists($template_path)) {
  $page = 'key-landing';
}

print $twig->render('prototypes/' . $page . '.twig', ['title' => 'IA Prototype - UBC CS ' . $page]);
