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

$autoloader = require __DIR__ . '/../../../vendor/autoload.php';

// Setup Twig.
$twig_options = [
  'cache' => dirname(__DIR__, 3) . '/private/cache',
  'auto_reload' => TRUE,
];
$loader = new FilesystemLoader(dirname(__DIR__) . '/templates');
$twig = new Environment($loader, $twig_options);

print $twig->render('styleguide.twig', ['title' => 'IA Style Guide - UBC CS']);
