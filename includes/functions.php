<?php

function redirect_to($new_location = null) {
    if (isset($new_location)) {
        header("Location: {$new_location}");
        exit;
    }
}

function layout($file) {
    return INC_PATH . $file;
}

function special_to_normal($post_title) {
    $special_chars = array("~", "`", "!", "@", "#", "$", "%", "^", "&", "*", "(", ")", "+", "=", "[", "]", "{", "}", "|", "\\", ";", ":", "'", "\"", "<", ">", "?", "/", ",", ".", "-", " ", "  ");

    $post_title = trim($post_title);
    $post_title = strtolower($post_title);

    foreach ($special_chars as $char) {
        $post_title = str_replace($char, "", $post_title);
    }

    return $post_title;
}

// Convert given into bytes eg. 100kb
function size($size) {
    $number = substr($size, 0, -2);

    switch (strtoupper(substr($size, -2))) {
    case 'KB':
        return $number * 1024;
    case 'MB':
        return $number * pow(1024, 2);
    case 'GB':
        return $number * pow(1024, 3);
    default:
        return $size;
    }
}

function create_file($filename) {
    fopen($filename, 'w');
}

function setup() {
    if (!file_exists(PROJECTS_JSON)) {
        create_file(PROJECTS_JSON);
        chmod(PROJECTS_JSON, 0600);
    }

    if (!file_exists(USER_JSON)) {
        create_file(USER_JSON);
        chmod(USER_JSON, 0600);
    }

    if (!file_exists(ACTIVITY_LOG)) {
        create_file(ACTIVITY_LOG);
        chmod(ACTIVITY_LOG, 0600);
    }

    return true;
}

function activity_log($datetime, $user, $activity) {
    // Delete the log if the last project was uploaded more than 14 days ago
    if (project(last_id(), "unix_time") < strtotime('-14 days')) {
        unlink(ACTIVITY_LOG);
    }

    $activity = date("[D M d g:i:s A]", $datetime) . " " . $user . " " . $activity. "\n";
    $handle = fopen(ACTIVITY_LOG, 'a');

    fwrite($handle, $activity);
}

function user_exists($full_name = "") {
    if (!empty($full_name)) {
        $user_info = file_get_contents(USER_JSON);
        $user_info = json_decode($user_info, true);

        if (array_key_exists("full_name", $user_info) && array_key_exists("password_hash", $user_info)) {
            return ($user_info["full_name"] == $full_name) ? true : false;
        }

        return false;
    }

    return false;
}

function create_user($full_name, $password) {
    $full_name = ucwords($full_name);

    $salt = "$2y$10$";

    for ($i = 0; $i < 22; $i++) {
        $salt .= substr("./ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789", mt_rand(0, 63), 1);
    }

    $hash = crypt($password, $salt);

    $user_info = array("full_name" => $full_name, "password_hash" => $hash);
    $user_info = json_encode($user_info);

    activity_log(time(), "New user created: \"{$full_name}\".", null);

    file_put_contents(USER_JSON, $user_info);

    return true;
}

// Get user info
function user($return_value) {
    $return_value = strtolower($return_value);

    $json_file = file_get_contents(USER_JSON);
    $user_info = json_decode($json_file, true);

    switch ($return_value) {
    case "password_hash":
        return $user_info["password_hash"];
    case "full_name":
        return $user_info["full_name"];
    case "token":
        return (!empty($user_info["token"])) ? $user_info["token"] : false;
    }

    return false;
}

function login($full_name, $password) {
    $full_name = ucwords($full_name);

    if (user_exists($full_name)) {
        $hash = user("password_hash");

        if (crypt($password, $hash) === $hash) {
            if ($full_name === user("full_name")) {
                $hash_array = str_split($hash);
                $name_array = str_split($full_name);

                $user_spice = array();
                $spice = "";

                foreach ($hash_array as $char) {
                    $user_spice[] = $char;
                }

                foreach ($name_array as $char) {
                    $user_spice[] = $char;
                }

                shuffle($user_spice);

                foreach ($user_spice as $char) {
                    $spice .= $char;
                }

                $spice = special_to_normal($spice);
                $spice_array = array("spice" => $spice);

                $json_contents = file_get_contents(USER_JSON);
                $json_contents = json_decode($json_contents, true);
                $json_contents["token"] = $spice;

                $json_contents = json_encode($json_contents);

                file_put_contents(USER_JSON, $json_contents);

                $_SESSION["user"] = $spice;

                activity_log(time(), user("full_name"), "logged in.");

                return true;
            }
        }
    }

    return false;
}

function is_admin() {
    if (user("token")) {
        if (isset($_SESSION["user"]) && $_SESSION["user"] === user("token")) {
            return true;
        }
    }

    return false;
}

function logout() {
    session_unset();
    session_destroy();

    activity_log(time(), user("full_name"), "logged out. \n");
}

function is_home() {
    return basename($_SERVER['PHP_SELF']) == "index.php" ? true : false;
}

function project($id, $return_value) {
    $project = array();

    $json_contents = file_get_contents(PROJECTS_JSON);
    $json_contents = json_decode($json_contents, true);

    foreach ($json_contents as $key => $value) {
        if ($value["id"] == $id) {
            $project = $value;
            break;
        }
    }

    switch ($return_value) {
        case "id":
            return $project["id"];
        case "title":
            return $project["title"];
        case "thumb":
            return $project["thumb"];
        case "behance":
            return (!empty($project["behance"])) ? $project["behance"] : "";
        case "dribbble":
            return (!empty($project["dribbble"])) ? $project["dribbble"] : "";
        case "direct_url":
            return (!empty($project["direct_url"])) ? $project["direct_url"] : "";
        case "date":
            if ($project["date"] >= strtotime("-60 minutes")) {
                if ($project["date"] >= strtotime("-1 minute")) {
                    return "a few seconds ago.";
                }

                return floor((time() - $project["date"]) / 60) . " minutes ago.";
            } elseif ($project["date"] >= strtotime("-1 day")) {
                $hours = floor((time() - $project["date"]) / 3600);
                return ($hours == 1) ? $hours . " hour ago." : $hours . " hours ago.";
            } elseif ($project["date"] >= strtotime("-1 year")) {
                return date("F jS", $project["date"]) . ".";
            } else {
                return date("F j, Y", $project["date"]) . ".";
            }
        case "unix_time":
            return $project["date"];
        case "priority":
            return $project["priority"];
        case "published":
            return $project["published"];
        case "desc":
            return $project["desc"];

        default: return $project["id"];

    }
}

function last_id() {
    $json_contents = file_get_contents(PROJECTS_JSON);
    $json_contents = json_decode($json_contents, true);

    if (!empty($json_contents)) {
        $last_project = end($json_contents);

        return $last_project["id"];
    }

    return false;
}

function new_id() {
    return last_id() + 1;
}

function get_thumb_name($id) {
    $id--;
    $thumb = glob("./assets/images/thumb-{$id}.*");

    return (array_key_exists(0, $thumb)) ? $thumb[0] : false;
}

function get_thumb_url($id) {
    $id--;
    $thumb = glob("../public/assets/images/thumb-{$id}.*");

    if (array_key_exists(0, $thumb)) {
        $thumb_url = substr($thumb[0], 1);

        return $thumb_url;
    }

    return false;
}

function create_project($title = "", $thumb = "", $behance = "", $dribbble = "", $direct_url = "", $priority = 1, $published = 1, $id = "") {
    $date = time();

    $json_contents = file_get_contents(PROJECTS_JSON);
    $json_contents = json_decode($json_contents, true);

    $id = new_id();

    $desc = "";

    $json_array = array(
        "id"         => $id,
        "title"      => $title,
        "thumb"      => $thumb,
        "behance"    => $behance,
        "dribbble"   => $dribbble,
        "direct_url" => $direct_url,
        "date"       => $date,
        "priority"   => $priority,
        "published"  => $published
    );

    $json_contents[] = $json_array;

    $json_contents = array_values($json_contents);
    $json_contents = json_encode($json_contents, JSON_UNESCAPED_SLASHES);

    file_put_contents(PROJECTS_JSON, $json_contents);

    activity_log(time(), user("full_name"), "created a new project: \"{$title}\".");
}

function delete_project($id) {
    $json_contents = file_get_contents(PROJECTS_JSON);
    $json_contents = json_decode($json_contents, true);

    foreach ($json_contents as $key => $value) {
        if ($value["id"] == $id) {
            if (file_exists(get_thumb_name($id + 1))) unlink(get_thumb_name($id + 1));
            unset($json_contents[$key]);
            break;
        }
    }

    $json_contents = array_values($json_contents);
    $json_contents = json_encode($json_contents, JSON_UNESCAPED_SLASHES);

    activity_log(time(), user("full_name"), "deleted a project: \"" . project($id, "title") . "\".");

    file_put_contents(PROJECTS_JSON, $json_contents);
}

function count_links($id) {
    $json_contents = file_get_contents(PROJECTS_JSON);
    $json_contents = json_decode($json_contents, true);

    $count = 0;

    foreach ($json_contents as $key => $value) {
        if ($value["id"] == $id) {
            $json_contents = $json_contents[$key];
            break;
        }
    }

    if (!empty($json_contents["behance"]))    $count++;
    if (!empty($json_contents["dribbble"]))   $count++;
    if (!empty($json_contents["direct_url"])) $count++;

    if (is_admin()) $count++;

    switch ($count) {
        case 1: return "one";
        case 2: return "two";
        case 3: return "three";
        case 4: return "four";
        default: return "zero";
    }

    return false;
}

function publish($id) {
    $json_contents = file_get_contents(PROJECTS_JSON);
    $json_contents = json_decode($json_contents, true);

    foreach ($json_contents as $key => $value) {
        if ($value["id"] == $id) {
            $json_contents[$key]["published"] = 1;
            break;
        }
    }

    $json_contents = array_values($json_contents);
    $json_contents = json_encode($json_contents, JSON_UNESCAPED_SLASHES);

    file_put_contents(PROJECTS_JSON, $json_contents);
}

function unpublish($id) {
    $json_contents = file_get_contents(PROJECTS_JSON);
    $json_contents = json_decode($json_contents, true);

    foreach ($json_contents as $key => $value) {
        if ($value["id"] == $id) {
            $json_contents[$key]["published"] = 0;
            break;
        }
    }

    $json_contents = array_values($json_contents);
    $json_contents = json_encode($json_contents, JSON_UNESCAPED_SLASHES);

    file_put_contents(PROJECTS_JSON, $json_contents);
}

function is_published($id) {
    $json_contents = file_get_contents(PROJECTS_JSON);
    $json_contents = json_decode($json_contents, true);

    foreach ($json_contents as $key => $value) {
        if ($value["id"] == $id) {
            return ($value["published"]) ? true : false;
        }
    }

    return false;
}

// Return array of published project IDs
function all_published() {
    $json_contents = file_get_contents(PROJECTS_JSON);
    $json_contents = json_decode($json_contents, true);

    $projects = array();

    if (!empty($json_contents)) {
        foreach ($json_contents as $key => $value) {
            if ($value["published"]) {
                $projects[] = $value;
            }
        }

        return $projects;
    }

    // return empty array if there are no projects
    return array();
}

// Return array of all projects
function all_projects() {
    $json_contents = file_get_contents(PROJECTS_JSON);
    $json_contents = json_decode($json_contents, true);

    if (!empty($json_contents)) {
        return $json_contents;
    }

    return array();
}

function pagination($page, $return_value) {
    // Number of projects per page
    $limit = (is_admin()) ? 5 : 6;

    // Count the total amount of pages
    if (!is_admin()) {
        $pages = ceil(count(all_published()) / $limit);
    } else {
        $pages = ceil(count(all_projects()) / $limit);
    }

    $all_published = all_published();
    $all_projects = all_projects();

    if (isset($page) && !is_null($page)) {
        $start = ($page - 1) * $limit;
        $projects = array();

        if (is_admin()) {
            if (count($all_projects) > 0) {
                foreach ($all_projects as $key => $value) {
                    if (!isset($value["priority"]) || empty($value["priority"])) {
                        // if the priority isn't supplied, push it to last
                        $value["priority"] = count($all_projects) + 1;
                    }
                    $projects[$key] = $value["priority"];
                }

                array_multisort($projects, SORT_ASC, $all_projects, SORT_DESC);

                foreach ($all_projects as $key => $value) {
                    $projects[$key] = $value["id"];
                }

                $projects = array_chunk($projects, $limit);

            }
        } else {
            if (count($all_published) > 0) {
                foreach ($all_published as $key => $value) {
                    if (!isset($value["priority"]) || empty($value["priority"])) {
                        // if the priority isn't supplied, push it to last
                        $value["priority"] = count($all_published) + 1;
                    }

                    $projects[$key] = $value["priority"];
                }

                array_multisort($projects, SORT_ASC, $all_published, SORT_DESC);

                foreach ($all_published as $key => $value) {
                    $projects[$key] = $value["id"];
                }

                $projects = array_chunk($projects, $limit);

            }
        }
    }

    switch ($return_value) {
    case "pages":
        return $pages;
    case "project":
        return (array_key_exists($page - 1, $projects)) ? $projects[$page - 1] : false;
    }
}

function upload_thumb($id, $file = array()) {
    $max_thumb_size = "100kb";
    $thumb_exts = array("jpg", "jpeg", "png", "gif");

    if (isset($file)) {
        $upload_dir = "../public/assets/images/";

        $temp = explode(".", $file["name"]);
        $extension = end($temp);

        $filename = "thumb-{$id}.{$extension}";

        // Build the target path
        $target_path = $upload_dir . $filename;
        $image_path = BASE_URL . "/assets/images/" . $filename;

        if ($file["size"] <= size($max_thumb_size)) {
            if (in_array($extension, $thumb_exts)) {
                if (move_uploaded_file($file['tmp_name'], $target_path)) {
                    return $image_path;
                }
            }
        }
    }
}

?>
