<?php

/**
 * @file
 * The PHP page that serves all page requests on a Drupal installation.
 *
 * All Drupal code is released under the GNU General Public License.
 * See COPYRIGHT.txt and LICENSE.txt files in the "core" directory.
 */

$autoloader = require __DIR__ . '/../../../vendor/autoload.php';

// Setup Twig.
$twig_options = array(
  'cache' => dirname(dirname(dirname(__DIR__))) . '/private/cache',
  'autoescape' => TRUE,
  'strict_variables' => FALSE,
  'debug' => FALSE,
  'auto_reload' => TRUE,
);
$loader = new Twig_Loader_Filesystem(dirname(__DIR__) . '/templates');
$twig = new Twig_Environment($loader, $twig_options);

$patterns = [];
$iterator = new DirectoryIterator('../templates/patterns');

foreach ($iterator as $fileInfo) {
  if ($fileInfo->isDot()) {
    continue;
  }

  $patterns[] = $fileInfo->getFilename();
}
sort($patterns);

echo $twig->render('patterns.twig', [
  'patterns' => $patterns,
  'title' => 'IA Patterns - UBC CS',
]);
