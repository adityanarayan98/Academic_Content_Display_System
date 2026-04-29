<?php
require_once 'auth.php';
$isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest';

$dirs = glob('*');
if (!is_array($dirs)) $dirs = [];
$folders = array_filter($dirs, function($dir) {
    return is_dir($dir) && !in_array($dir, ['.', '..', '.kilo', '.kilocode']);
});

// Ensure default folder exists
if (!is_dir('iq3')) {
    mkdir('iq3', 0755);
    $folders[] = 'iq3';
}

$selectedFolder = isset($_SESSION['admin_folder']) ? $_SESSION['admin_folder'] : (isset($folders[0]) ? $folders[0] : 'iq3');

$projects = [];
foreach ($folders as $f) {
    $settings = get_settings($f);
    $projects[$f] = $settings['orientation'];
}

if (!is_logged_in()) {
    // Generate math question
    $num1 = rand(1, 10);
    $num2 = rand(1, 10);
    $_SESSION['admin_math_num1'] = $num1;
    $_SESSION['admin_math_num2'] = $num2;
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <title>Academic Content Display System (ACDS) Admin Login</title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    </head>
    <body class="bg-light">
        <div class="container mt-5">
            <div class="row justify-content-center">
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <h3 class="card-title text-center">Admin Login</h3>
                            <form method="post">
                                <div class="form-group">
                                    <label>Username</label>
                                    <input type="text" name="username" class="form-control" required autofocus>
                                </div>
                                <div class="form-group">
                                    <label>Password</label>
                                    <input type="password" name="password" class="form-control" required>
                                </div>
                                <div class="form-group">
                                    <label>What is <?php echo $num1; ?> + <?php echo $num2; ?>?</label>
                                    <input type="number" name="math_answer" class="form-control" required>
                                </div>
                                <button type="submit" name="login" class="btn btn-primary btn-block">Login</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
    </html>
    <?php
} else {
    // Handle form actions
    if (!empty($_FILES['image']['name'][0])) {
        $folder = !empty($_POST['folder']) ? $_POST['folder'] : (!empty($_GET['folder']) ? $_GET['folder'] : 'iq3');
        $uploadedFiles = [];
        $uploadErrors = [];
        $allowedTypes = ['jpg', 'jpeg', 'png', 'gif', 'mp4', 'avi', 'mov', 'wmv'];
        foreach ($_FILES['image']['name'] as $index => $filename) {
            if ($_FILES['image']['error'][$index] === UPLOAD_ERR_OK) {
                $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
                if (!in_array($ext, $allowedTypes)) {
                    $uploadErrors[] = "Invalid file type: $filename";
                    continue;
                }
                $size = $_FILES['image']['size'][$index];
                if ($size > 500 * 1024 * 1024) {
                    $uploadErrors[] = "File too large: $filename";
                    continue;
                }
                $newFilename = $filename;
                $destination = $folder . '/' . $newFilename;
                if (move_uploaded_file($_FILES['image']['tmp_name'][$index], $destination)) {
                    $uploadedFiles[] = $newFilename;
                } else {
                    $uploadErrors[] = "Failed to upload: $filename";
                }
            } else {
                $uploadErrors[] = "Upload error for: $filename";
            }
        }
        if (!empty($uploadedFiles)) {
            // Update sequence
            $settings = get_settings($folder);
            $existingSequence = $settings['sequence'] ?? [];
            $newFiles = array_diff($uploadedFiles, $existingSequence);
            if (!empty($newFiles)) {
                $settings['sequence'] = array_merge($existingSequence, $newFiles);
                save_settings($folder, $settings);
            }
            $_SESSION['message'] = count($uploadedFiles) . " file(s) uploaded successfully";
            if (!empty($uploadErrors)) {
                $_SESSION['message'] .= ". " . implode(" ", $uploadErrors);
            }
        } else {
            $_SESSION['error'] = implode(" ", $uploadErrors);
        }
        if ($isAjax) {
            echo json_encode(['success' => true]);
            exit;
        } else {
            header('Location: admin.php');
            exit;
        }
    }

    $media = get_all_media($selectedFolder);
    $settings = get_settings($selectedFolder);

    // Handle create folder
    if (isset($_POST['create_folder'])) {
        $newFolder = trim($_POST['new_folder']);
        if (!is_dir($newFolder) && mkdir($newFolder, 0755)) {
            get_settings($newFolder); // Initialize
            $_SESSION['admin_folder'] = $newFolder;
            $_SESSION['message'] = "Project '$newFolder' created successfully";
        } else {
            $_SESSION['error'] = "Failed to create project";
        }
        header('Location: admin.php');
        exit;
    }

    // Handle delete file
    if (isset($_POST['delete_file'])) {
        $file = $selectedFolder . '/' . basename($_POST['delete_file']);
        if (file_exists($file)) {
            unlink($file);
            // Remove from sequence
            $settings = get_settings($selectedFolder);
            $sequence = $settings['sequence'] ?? [];
            if (($key = array_search(basename($_POST['delete_file']), $sequence)) !== false) {
                unset($sequence[$key]);
                $settings['sequence'] = array_values($sequence);
                save_settings($selectedFolder, $settings);
            }
            $_SESSION['message'] = "File deleted successfully";
        } else {
            $_SESSION['error'] = "File not found";
        }
        exit;
    }
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Academic Content Display System (ACDS) Admin</title>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
        <script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
        <script src="http://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
        <style>
            body { padding: 10px; background: #f8f9fa; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; font-size: 14px; }
            .image-card { margin-bottom: 10px; border: 1px solid #dee2e6; border-radius: 0.25rem; }
            .drag-handle { cursor: grab; font-size: 18px; color: #ccc; }
            .drag-handle:active { cursor: grabbing; }
            .sortable-ghost { opacity: 0.4; }
            .img-thumbnail { height: 80px; object-fit: cover; width: 100%; border-radius: 0.2rem; }
            .header { margin-bottom: 15px; border-bottom: 2px solid #007bff; padding-bottom: 10px; background: white; padding: 15px; border-radius: 0.3rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
            .card { border: none; box-shadow: 0 1px 3px rgba(0,0,0,0.1); margin-bottom: 10px; }
            .card-header { border-bottom: 1px solid #dee2e6; padding: 0.5rem 1rem; font-size: 13px; }
            .card-body { padding: 0.75rem; }
            .left-card .card-body { padding: 0.5rem; font-size: 13px; }
            .btn { border-radius: 0.2rem; font-size: 13px; padding: 0.375rem 0.75rem; }
            .form-control { border-radius: 0.2rem; font-size: 13px; padding: 0.375rem 0.75rem; }
            .alert { border-radius: 0.2rem; font-size: 13px; padding: 0.5rem 1rem; }
            h2 { font-size: 18px; }
            h5 { font-size: 16px; }
            h6 { font-size: 14px; }
            .badge { font-size: 11px; }
            .text-muted { font-size: 12px; }
            .container { max-width: 1200px; }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="header mb-4">
                <div class="d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Academic Content Display System (ACDS): Admin Panel</h4>
                    <div>
                        <a href="index.php?folder=<?= urlencode($selectedFolder) ?>&preview=1" target="_blank" class="btn btn-outline-info mr-2">Preview Slideshow</a>
                        <a href="?logout" class="btn btn-outline-danger">Logout</a>
                    </div>
                </div>
            </div>

            <?php if (isset($_SESSION['message'])): ?>
                <div class="alert alert-success alert-dismissible fade show mt-3">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    <?= htmlspecialchars($_SESSION['message']) ?>
                    <?php unset($_SESSION['message']); ?>
                </div>
            <?php endif; ?>

            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger alert-dismissible fade show mt-3">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    <?= htmlspecialchars($_SESSION['error']) ?>
                    <?php unset($_SESSION['error']); ?>
                </div>
            <?php endif; ?>

            <div id="statusMessage" class="mt-3"></div>

            <div class="row mb-3">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <h6>Project Management</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <label>Select Project:</label>
                                    <select id="folderSelect" class="form-control">
                                        <?php foreach ($folders as $f): ?>
                                            <option value="<?= $f ?>" <?= $f === $selectedFolder ? 'selected' : '' ?>><?= $f ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label>Create New Project:</label>
                                    <form method="post" class="form-inline">
                                        <input type="text" name="new_folder" class="form-control mr-2" placeholder="Project name" required>
                                        <button type="submit" name="create_folder" class="btn btn-success">Create</button>
                                    </form>
                                </div>
                                <div class="col-md-4">
                                    <strong>Current Project:</strong> <span class="badge badge-secondary"><?= $selectedFolder ?></span><br>
                                    <button type="button" class="btn btn-outline-info btn-sm mt-2" data-toggle="modal" data-target="#userManagementModal">User Management</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <div class="card mb-3 left-card">
                        <div class="card-header">
                            <h6>Settings</h6>
                        </div>
                        <div class="card-body">
                            <label>Timer (sec):</label>
                            <input type="number" id="timerValue" class="form-control mb-2" min="1" max="60" value="<?= $settings['timer'] ?>">
                            <label>Orientation:</label>
                            <select id="orientationValue" class="form-control mb-2">
                                <option value="landscape" <?= $settings['orientation'] === 'landscape' ? 'selected' : '' ?>>Landscape</option>
                                <option value="portrait" <?= $settings['orientation'] === 'portrait' ? 'selected' : '' ?>>Portrait</option>
                            </select>
                            <button id="saveTimer" class="btn btn-success btn-block">Save Settings</button>
                        </div>
                    </div>
                    <div class="card mb-3 left-card">
                        <div class="card-header">
                            <h6>Upload Media</h6>
                        </div>
                        <div class="card-body">
                            <input type="file" id="imageInput" class="form-control mb-2" accept="image/*,video/*" multiple>
                            <button id="uploadBtn" class="btn btn-primary btn-block">Upload</button>
                        </div>
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h6>Media (<?= count($media) ?>)</h6>
                            <button id="saveOrder" class="btn btn-success btn-sm">Save Order</button>
                        </div>
                        <div class="card-body">
                            <small class="text-muted mb-2 d-block">Drag images to reorder</small>
                            <div id="imageList">
                                <?php if (empty($media)): ?>
                                    <p>No media.</p>
                                <?php else: ?>
                                    <?php foreach ($media as $item): ?>
                                        <div class="card mb-2 image-card" data-file="<?= basename($item) ?>">
                                            <div class="row no-gutters">
                                                <div class="col-1 d-flex align-items-center justify-content-center">
                                                    <span class="drag-handle">⋮⋮</span>
                                                </div>
                                                <div class="col-2">
                                                    <?php if (in_array(strtolower(pathinfo($item, PATHINFO_EXTENSION)), ['mp4', 'avi', 'mov', 'wmv'])): ?>
                                                        <video class="img-thumbnail" style="width:100%; height:120px; object-fit:cover;"><source src="<?= $item ?>" type="video/<?= strtolower(pathinfo($item, PATHINFO_EXTENSION)) ?>"></video>
                                                    <?php else: ?>
                                                        <img src="<?= $item ?>" class="img-thumbnail" style="width:100%; height:120px; object-fit:cover;">
                                                    <?php endif; ?>
                                                </div>
                                                <div class="col-6">
                                                    <div class="card-body">
                                                        <h6 class="card-title"><?= basename($item) ?></h6>
                                                    </div>
                                                </div>
                                                <div class="col-3">
                                                    <div class="card-body text-right">
                                                        <button class="btn btn-sm btn-success view-btn" data-image="<?= $item ?>">View</button>
                                                        <button class="btn btn-sm btn-danger delete-btn" data-file="<?= basename($item) ?>">Delete</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- User Management Modal -->
            <div class="modal fade" id="userManagementModal" tabindex="-1">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">User Management</h5>
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <h6>Current Users</h6>
                                    <ul class="list-group" id="userList">
                                        <?php $users = get_users(); foreach ($users as $username => $hash): ?>
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                <?= htmlspecialchars($username) ?>
                                                <div>
                                                    <button class="btn btn-sm btn-warning edit-user me-1" data-username="<?= htmlspecialchars($username) ?>">Edit</button>
                                                    <button class="btn btn-sm btn-danger delete-user" data-username="<?= htmlspecialchars($username) ?>">Delete</button>
                                                </div>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                                <div class="col-md-6">
                                    <h6>Add/Edit User</h6>
                                    <form id="addUserForm">
                                        <div class="form-group">
                                            <input type="text" id="newUsername" class="form-control" placeholder="Username" required>
                                        </div>
                                        <div class="form-group">
                                            <input type="password" id="newPassword" class="form-control" placeholder="Password" required>
                                        </div>
                                        <button type="submit" class="btn btn-success">Add/Update User</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Image Modal -->
            <div class="modal fade" id="imageModal" tabindex="-1">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Media View</h5>
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                        </div>
                        <div class="modal-body text-center">
                            <div id="modalContent"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <script>
            new Sortable(document.getElementById('imageList'), {
                handle: '.drag-handle',
                animation: 300,
                ghostClass: 'sortable-ghost'
            });

            $(document).on('click', '.view-btn', function() {
                var src = $(this).data('image');
                var ext = src.split('.').pop().toLowerCase();
                var modalContent = $('#modalContent');
                if (['mp4', 'avi', 'mov', 'wmv'].includes(ext)) {
                    modalContent.html('<video controls autoplay muted class="img-fluid"><source src="' + src + '" type="video/' + ext + '"></video>');
                } else {
                    modalContent.html('<img src="' + src + '" class="img-fluid">');
                }
                $('#imageModal').modal('show');
            });

            $(document).on('click', '.delete-btn', function() {
                var filename = $(this).data('file');
                if (confirm('Delete ' + filename + '?')) {
                    $.post('admin.php', { delete_file: filename, folder: '<?= $selectedFolder ?>' }, function() {
                        location.reload();
                    });
                }
            });

            $('#folderSelect').change(function() {
                var folder = $(this).val();
                $.post('save_settings.php', {set_admin_folder: folder}, function(res) {
                    var data = JSON.parse(res);
                    if (data.success) {
                        location.reload();
                    } else {
                        showStatus('Failed to switch folder', 'danger');
                    }
                }).fail(function() {
                    showStatus('Failed to switch folder', 'danger');
                });
            });

            $('#addUserForm').submit(function(e) {
                e.preventDefault();
                var username = $('#newUsername').val();
                var password = $('#newPassword').val();
                var action = $('#actionField').val() || 'add';
                var data = { username: username, password: password };
                if (action === 'update') {
                    data.update_user = true;
                } else {
                    data.add_user = true;
                }
                $.post('save_settings.php', data, function(res) {
                    var data = JSON.parse(res);
                    if (data.success) {
                        showStatus('User ' + (action === 'update' ? 'updated' : 'added') + ' successfully!', 'success');
                        $('#newUsername').val('').prop('readonly', false);
                        $('#newPassword').val('');
                        $('#addUserForm button').text('Add/Update User');
                        $('#actionField').remove();
                        location.reload();
                    } else {
                        showStatus('Failed: ' + (data.error || 'Unknown error'), 'danger');
                    }
                }).fail(function() {
                    showStatus('Request failed', 'danger');
                });
            });

            $(document).on('click', '.edit-user', function() {
                var username = $(this).data('username');
                $('#newUsername').val(username).prop('readonly', true);
                $('#newPassword').val('');
                $('#addUserForm button').text('Update User');
                if ($('#actionField').length === 0) {
                    $('#addUserForm').append('<input type="hidden" name="action" value="update" id="actionField">');
                }
            });

            $(document).on('click', '.delete-user', function() {
                var username = $(this).data('username');
                if (confirm('Delete user ' + username + '?')) {
                    $.post('save_settings.php', { delete_user: username }, function(res) {
                        var data = JSON.parse(res);
                        if (data.success) {
                            showStatus('User deleted', 'success');
                            location.reload();
                        } else {
                            showStatus('Failed: ' + (data.error || ''), 'danger');
                        }
                    }).fail(function() {
                        showStatus('Request failed', 'danger');
                    });
                }
            });

            $('#uploadBtn').click(function() {
                var files = $('#imageInput')[0].files;
                if (files.length === 0) return;
                var formData = new FormData();
                for (var i = 0; i < files.length; i++) {
                    formData.append('image[]', files[i]);
                }
                formData.append('folder', '<?= $selectedFolder ?>');
                $.ajax({
                    url: 'admin.php',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function() {
                        location.reload();
                    },
                    error: function() {
                        showStatus('Upload failed', 'danger');
                    }
                });
            });

            $('#saveOrder').click(function() {
                var order = [];
                $('.image-card').each(function() {
                    order.push($(this).data('file'));
                });
                $.post('save_settings.php', { folder: '<?= $selectedFolder ?>', sequence: order }, function(res) {
                    var data = JSON.parse(res);
                    if (data.success) {
                        showStatus('Order saved!', 'success');
                    } else {
                        showStatus('Save failed', 'danger');
                    }
                }).fail(function() {
                    showStatus('Save failed', 'danger');
                });
            });

            $('#saveTimer').click(function() {
                $.post('save_settings.php', { folder: '<?= $selectedFolder ?>', timer: $('#timerValue').val(), orientation: $('#orientationValue').val() }, function(res) {
                    var data = JSON.parse(res);
                    if (data.success) {
                        showStatus('Settings saved!', 'success');
                    } else {
                        showStatus('Save failed', 'danger');
                    }
                }).fail(function() {
                    showStatus('Save failed', 'danger');
                });
            });

            function showStatus(message, type) {
                $('#statusMessage').html('<div class="alert alert-' + type + ' alert-dismissible fade show"><button type="button" class="close" data-dismiss="alert">&times;</button>' + message + '</div>');
                setTimeout(() => $('#statusMessage').empty(), 5000);
            }
        </script>
        <footer style="text-align: center; padding: 10px; background: #f8f9fa; margin-top: 20px;">
            &copy; 2026 Aditya Narayan Sahoo. Licensed under <a href="https://creativecommons.org/licenses/by/4.0/" target="_blank">CC BY 4.0</a>. <a href="https://github.com/adityanarayan98" target="_blank">GitHub</a> | <a href="https://sites.google.com/view/adityanarayansahoo/" target="_blank">Website</a>
        </footer>
    </body>
    </html>
    <?php
}
?>