<?php

use ActiveCollab\SDK\Authenticator\SelfHosted;
use ActiveCollab\SDK\Client;
use ActiveCollab\SDK\TokenInterface;
use GuzzleHttp\Promise\Utils;
use Jupitern\Table\Table;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Contracts\Cache\ItemInterface;

require_once '../vendor/autoload.php';

//$client = new GuzzleHttp\Client([
//    'base_uri' => getenv('AC_URL') . '/api/v1/',
//]);
//?>
<!--<pre>-->
<?php
//$response = $client->post('issue-token', [
//    'json' => [
//        'username' => getenv('AC_USER'),
//        'password' => getenv('AC_PASS'),
//        'client_name' => 'AC',
//        'client_vendor' => 'UBC CS',
//    ],
//]);
//
//if ($response->getStatusCode() != 200) {
//    throw new ErrorException('Token not issued');
//}
//if (!str_contains($response->getHeaderLine('Content-Type'), 'application/json')) {
//    throw new ErrorException('Response not json' . $response->getHeaderLine('Content-Type'));
//}
//
//$result = json_decode($response->getBody(), true);
//if (empty($result['is_ok']) || empty($result['token'])) {
//    throw new ErrorException('Authentication rejected');
//}
//$token = $result['token'];
//
//$client = new GuzzleHttp\Client([
//    'base_uri' => getenv('AC_URL') . '/api/v1/',
//    'headers' => [
//        'X-Angie-AuthApiToken' => $token,
//    ]
//]);
//
////$response = $client->get('projects');
////
////$project_labels = json_decode($response->getBody()->getContents(), true);
////var_dump($project_labels);
//
//
//$time_start = microtime(true);
//$client->get('projects');
//$client->get('projects/categories');
//$client->get('labels/project-labels');
//$client->get('labels/task-labels');
//$client->get('users');
//
//$execution_time = (microtime(true) - $time_start);
//echo '<br><b>Sync Total Execution Time:</b> '.$execution_time.' Seconds';
//
//$time_start = microtime(true);
//
//
//$promises = [
//    'projects' => $client->getAsync('projects'),
//    'project-categories' => $client->getAsync('projects/categories'),
//    'project-labels' => $client->getAsync('labels/project-labels'),
//    'task-labels' => $client->getAsync('labels/task-labels'),
//    'users' => $client->getAsync('users'),
//];
//
//// Wait for the requests to complete; throws a ConnectException
//// if any of the requests fail
////$responses = Utils::unwrap($promises);
//$responses = Utils::settle(
//    Utils::unwrap($promises),
//)->wait();
//
//
//$execution_time = (microtime(true) - $time_start);
//echo '<br><b>Async Total Execution Time:</b> '.$execution_time.' Seconds';
//
//
//
//foreach ($responses as $response) {
////    echo $responses['png']->getHeader('Content-Length')[0];
////    var_dump(json_decode($response->getBody()->getContents(), true));
//}
//
//
//// Wait for the requests to complete, even if some of them fail
////$responses = Promise\Utils::settle($promises)->wait();
//
//
//
////$posts = json_decode($responses[0]['value']->getBody()->getContents(), true);
////$comments = json_decode($responses[1]['value']->getBody()->getContents(), true);
//
//
//die();
//?>
<!--</pre>-->
<?php


// https://github.com/activecollab/activecollab-feather-sdk
$authenticator = new SelfHosted('CS', 'CS', getenv('AC_USER'), getenv('AC_PASS'), getenv('AC_URL'));
$token = $authenticator->issueToken();

// Default expiry 10 minutes.
// https://symfony.com/doc/current/components/cache.html
$cache = new FilesystemAdapter('ac', 600, '../cache');

if (!($token instanceof TokenInterface)) {
  print "Invalid response\n";
  die();
}

$client = new Client($token);

// Collect a key/value map of project labels.
// https://developers.activecollab.com/api-documentation/v1/projects/labels.html
$project_label_map = $cache->get('project_label_map', function (ItemInterface $item) use ($client) {
    $item->expiresAfter(60 * 60 * 24);
    $project_labels = $client->get('/labels/project-labels')->getJson();
    return array_column($project_labels, 'name', 'id');
});

// Collect a key/value map of project categories.
// https://developers.activecollab.com/api-documentation/v1/projects/categories.html
$project_category_map = $cache->get('project_category_map', function (ItemInterface $item) use ($client) {
    $item->expiresAfter(60 * 60 * 24);
    $project_categories = $client->get('/projects/categories')->getJson();
    return array_column($project_categories, 'name', 'id');
});

// Collect a key/value map of task labels.
// https://developers.activecollab.com/api-documentation/v1/projects/elements/tasks/labels.html
$task_label_map = $cache->get('task_label_map', function (ItemInterface $item) use ($client) {
    $item->expiresAfter(60 * 60 * 24);
    $task_labels = $client->get('/labels/task-labels')->getJson();
    return array_column($task_labels, 'name', 'id');
});

// Collect a list of all the projects.
$projects = $cache->get('projects', function (ItemInterface $item) use ($client) {
    $item->expiresAfter(60 * 60 * 24);
    return $client->get('projects')->getJson();
});

// Collect a list of all the users.
$users = $cache->get('users', function (ItemInterface $item) use ($client) {
    $item->expiresAfter(60 * 60 * 24);
    return $client->get('users')->getJson();
});

$user_map = array_column($users, 'display_name', 'id');

// Collect a key/value map of task list names.
// Collect a list of task lists per project.
$project_task_list_map = [];
$task_lists_per_project = [];
foreach ($projects as $project) {
    $project_task_lists = $cache->get('projects_task_lists.' . $project['id'], function (ItemInterface $item) use ($client, $project) {
        $item->expiresAfter(3600);
        return $client->get('projects/' . $project['id'] . '/task-lists')->getJson();
    });

    $project_task_list_map += array_column($project_task_lists, 'name', 'id');
    $task_lists_per_project[$project['id']] = $project_task_lists;
}

// Subtasks
// https://developers.activecollab.com/api-documentation/v1/projects/elements/tasks/subtasks.html


// Tasks
// - Filter out is_trashed and is_completed
// - Link Projects
//

?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>AC</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <link href="//cdn.datatables.net/1.13.2/css/jquery.dataTables.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.10.12/css/dataTables.bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/buttons/2.3.4/css/buttons.dataTables.min.css" rel="stylesheet">
    <link href="css/details.css" rel="stylesheet">
</head>
<body>
<div class="container-fluid">
    <details>
        <summary>Projects</summary>
<?php

//    Table::instance()
//        ->setData($project_labels)
//        ->attr('table', 'class', 'datatable table')
//        ->column()->title('#')->value('id')->add()
//        ->column()->title('Name')->value('name')->add()
//        ->render();
//
//    Table::instance()
//        ->setData($task_labels)
//        ->attr('table', 'class', 'datatable table')
//        ->column()->title('#')->value('id')->add()
//        ->column()->title('Name')->value('name')->add()
//        ->render();

// https://github.com/jupitern/table
Table::instance()
    ->setData($projects)
    ->attr('table', 'class', 'datatable table')
    ->attr('table', 'width', '100%')
    ->column()->title('Name')->value(function ($row) {
        return '<a href="' . getenv('AC_URL') . $row['url_path'] . '">' . $row['name'] . '</a>';
    })->add()
    ->column()->title('Label')->value(function ($row) use ($project_label_map) {
        return $project_label_map[$row['label_id']] ?? $row['label_id'];
    })->add() // ->filter(array_combine($project_label_map, $project_label_map))
    ->column()->title('Category')->value(function ($row) use ($project_category_map) {
        return $project_category_map[$row['category_id']] ?? $row['category_id'];
    })->add()  // ->filter(array_combine($project_category_map, $project_category_map))
    ->column()->title('🚀Up Next')->value(function ($row) use ($task_lists_per_project) {
        $task_list_map = array_column($task_lists_per_project[$row['id']], 'name');
        $task_list_ids = array_filter($task_list_map, fn($name) => str_contains($name, 'Up Next'));
        $task_list_id = key($task_list_ids);
        return $task_lists_per_project[$row['id']][$task_list_id]['open_tasks'] ?? 0;
    })->add()
    ->column()->title('🪵Backlog')->value(function ($row) use ($task_lists_per_project) {
        $task_list_map = array_column($task_lists_per_project[$row['id']], 'name');
        $task_list_ids = array_filter($task_list_map, fn($name) => str_contains($name, 'Backlog'));
        $task_list_id = key($task_list_ids);
        return $task_lists_per_project[$row['id']][$task_list_id]['open_tasks'] ?? 0;
    })->add()
    ->column()->title('👀In Review')->value(function ($row) use ($task_lists_per_project) {
        $task_list_map = array_column($task_lists_per_project[$row['id']], 'name');
        $task_list_ids = array_filter($task_list_map, fn($name) => str_contains($name, 'In Review'));
        $task_list_id = key($task_list_ids);
        return $task_lists_per_project[$row['id']][$task_list_id]['open_tasks'] ?? 0;
    })->add()
    ->column()->title('🧙‍Wish/Idea')->value(function ($row) use ($task_lists_per_project) {
        $task_list_map = array_column($task_lists_per_project[$row['id']], 'name');
        $task_list_ids = array_filter($task_list_map, fn($name) => str_contains($name, 'Backlog'));
        $task_list_id = key($task_list_ids);
        return $task_lists_per_project[$row['id']][$task_list_id]['open_tasks'] ?? 0;
    })->add()
    ->column()->title('Other Task Lists')->value(function ($row) use ($task_lists_per_project) {
        $task_list_links = [];
        $split_out_lists = [
            'Up Next',
            'Backlog',
            'In Review',
            'Wish',
        ];
        foreach ($task_lists_per_project[$row['id']] as $task_list) {
            $is_split_out_list = array_filter($split_out_lists, function ($list) use ($task_list) {
                return str_contains($task_list['name'], $list);
            });
            if ($is_split_out_list) {
                continue;
            }
            $task_list_links[] = '<a class="btn btn-outline-secondary btn-sm" href="' . getenv('AC_URL') . $task_list['url_path'] . '">' . $task_list['name'] . ' <span class="badge text-bg-info">' . $task_list['open_tasks'] . '</span></a>';
        }
        return implode('', $task_list_links);
    })->add()
    ->column()->title('Total Tasks')->value('count_tasks')->add()
    ->render();
//    echo '<table>';
//    foreach ($data as $project) {
//        echo $project['name'] ;
//    }
//    echo '<table>';
//    print_r($data);
?>
</details>
<details open>
    <summary>Tasks</summary>
<?php
$tasks = [];
foreach ($projects as $project) {

    $project_tasks = $cache->get('projects_tasks.' . $project['id'], function (ItemInterface $item) use ($client, $project) {
        $item->expiresAfter(3600);
        return $client->get('projects/' . $project['id'] . '/tasks')->getJson();
    });

    foreach ($project_tasks['tasks'] as $task) {
        $task['project_id'] = $project['id'];
        $task['project_name'] = $project['name'];
        $tasks[] = $task;
    }
}
$today_midnight = strtotime('today');
Table::instance()
    ->setData($tasks)
    ->attr('table', 'class', 'datatable table')
    ->attr('table', 'width', '100%')
    ->column()->title('Project')->value(function ($row) {
        return '<a href="' . getenv('AC_URL') . '/projects/' . $row['project_id'] . '">' . $row['project_name'] . '</a>';
    })->add()
    ->column()->title('Task List')->value(function ($row) use ($project_task_list_map) {
        return $project_task_list_map[$row['task_list_id']] ?? $row['task_list_id'];
    })->add()  // ->filter(array_combine($project_category_map, $project_category_map))
    ->column()->title('Name')->value(function ($row) {
        return '<a href="' . getenv('AC_URL') . '/projects/' . $row['project_id'] . '/tasks/' . $row['id'] . '">' . $row['name'] . '</a>';
    })->add()
    ->column()->title('Labels')->value(function ($row) {
        $labels = array_column($row['labels'], 'name');
        return implode(', ', $labels);
    })->add()
    ->column()->title('Due On')->value(function ($row) use ($today_midnight) {
        if (!empty($row['due_on'])) {
            $is_past = $row['due_on'] < $today_midnight;
            $formatted_due_date = date('Y-m-d', $row['due_on']);
            return $is_past ? '<span class="text-danger">' . $formatted_due_date . '</span>' : $formatted_due_date;
        }
        return '';
    })->add()
    ->column()->title('High Priority')->value(function ($row) {
        $is_important = $row['is_important'] ?? FALSE;
        return $is_important ? '<span class="text-danger">High</span>' : '';
    })->add()
    ->column()->title('Assigned')->value(function ($row) use ($user_map) {
        if (!empty($row['assignee_id'])) {
            return $user_map[$row['assignee_id']];
        }
        return '';
    })->add()
//            ->column()->title('Label')->value(function ($row) {
//                return  implode(', ', $row['labels']);
//            })->add() // ->filter(array_combine($project_label_map, $project_label_map))
    ->column()->title('Open Subtasks')->value('open_subtasks')->add()
    ->render();

?>
</details>
</div>

<!-- JQUERY -->
<script
    src="https://code.jquery.com/jquery-3.6.0.slim.min.js"
    integrity="sha256-u7e5khyithlIdTpu22PHhENmPcRdFiHRjhAuHcs05RI="
    crossorigin="anonymous"></script>

<!-- DATATABLES -->
<script src="//cdn.datatables.net/1.13.2/js/jquery.dataTables.min.js"></script>
<script src="//cdn.datatables.net/buttons/2.3.4/js/dataTables.buttons.min.js"></script>
<script src="//cdn.datatables.net/buttons/2.3.4/js/buttons.html5.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>


<!-- Bootstrap and Datatables Bootstrap theme (OPTIONAL) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>

<script type="text/javascript">
    ( function ($) {

        $.extend(true, $.fn.dataTable.defaults, {
            paging:  false, // no paging
            column: {
                orderSequence: ['desc','asc']
            }
        });

        // https://datatables.net/
        $('.datatable').DataTable({
            dom: 'Bfrtip',
            buttons: [
                'copyHtml5',
                'excelHtml5',
                'csvHtml5',
            ]
        });
    })(jQuery);
</script>

</body>
</html>
