<!DOCTYPE html>
<html lang="en">
<head>
  <title>Academic Content Display System (ACDS)</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- Bootstrap CSS -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">

  <!-- jQuery and Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>

  <style>
    html, body {
      margin: 0;
      padding: 0;
      height: 100%;
      overflow: hidden;
      position: relative;
    }

    /* Start overlay */
    #startOverlay {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: rgba(0, 0, 0, 0.8);
      display: flex;
      align-items: center;
      justify-content: center;
      z-index: 10000;
      color: white;
      font-size: 24px;
    }

    #startButton {
      padding: 20px 40px;
      font-size: 24px;
      background: #007bff;
      color: white;
      border: none;
      border-radius: 5px;
      cursor: pointer;
    }

    /* Preview mode */
    .preview-mode {
      background: #f8f9fa !important;
      overflow: auto !important;
    }

    .preview-mode #demo {
      position: relative !important;
      height: auto !important;
      width: auto !important;
      max-width: 100% !important;
      max-height: 80vh !important;
      margin: 50px auto !important;
      box-shadow: 0 4px 8px rgba(0,0,0,0.2);
    }

    .preview-mode .carousel-inner,
    .preview-mode .carousel-item {
      height: auto !important;
      max-height: 80vh !important;
    }

    .preview-mode .carousel-inner img {
      height: auto !important;
      max-height: 80vh !important;
    }

    #slideshow {
      height: 100vh;
      width: 100vw;
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
    }

    .slide {
      display: none;
      height: 100vh;
      width: 100vw;
      position: absolute;
      top: 0;
      left: 0;
    }

    .slide.active {
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .slide img {
      object-fit: fill;
      width: 100%;
      height: 100vh;
      image-rendering: optimizeQuality;
      -webkit-font-smoothing: antialiased;
    }

    .slide video {
      width: 100%;
      height: auto;
      max-height: 100vh;
      object-fit: contain;
      image-rendering: optimizeQuality;
      -webkit-font-smoothing: antialiased;
    }

    .carousel-inner,
    .carousel-item {
      height: 100vh !important;
    }

    .carousel-item {
      display: flex !important;
      align-items: center !important;
      justify-content: center !important;
      height: 100vh !important;
    }

    .carousel-inner img,
    .carousel-inner video {
      width: 100% !important;
      height: auto !important;
      max-height: 100vh !important;
      image-rendering: optimizeQuality;
      -webkit-font-smoothing: antialiased;
    }



  </style>
</head>
<body class="<?= $isPreview ? 'preview-mode' : '' ?>">

<?php
    require_once 'auth.php';
     $isPreview = isset($_GET['preview']);
     $settings = isset($_GET['folder']) ? get_settings($_GET['folder']) : ['orientation' => 'landscape'];

     // Handle logout
     if (isset($_GET['logout'])) {
         user_logout();
         header('Location: index.php');
         exit;
     }
         ?>

<?php if (!isset($_GET['folder'])) { ?>

        <!DOCTYPE html>
        <html lang="en">
        <head>
            <title>Select Project - Academic Content Display System (ACDS)</title>
            <meta charset="utf-8">
            <meta name="viewport" content="width=device-width, initial-scale=1">
            <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
            <style>
                body { background: #f8f9fa; padding: 50px; }
                .project-card { cursor: pointer; transition: transform 0.2s; }
                .project-card:hover { transform: scale(1.05); }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="text-right mb-3">
                    <a href="admin.php" class="btn btn-outline-primary">Admin Login</a>
                </div>
                <div class="row justify-content-center">
                    <div class="col-md-8">
                        <h1 class="text-center mb-2">Academic Content Display System (ACDS)</h1>
                        <h2 class="text-center mb-4">Select Project</h2>
                                        <div class="row">
                            <?php
                            // Load projects from settings
                            $allSettings = json_decode(file_get_contents(SETTINGS_FILE), true);
                            $folders = array_keys($allSettings['folders'] ?? []);
                            $projects = [];
                            foreach ($folders as $f) {
                                $settings = get_settings($f);
                                $projects[$f] = $settings['orientation'];
                            }
                            foreach ($projects as $proj => $orient): ?>
                                <div class="col-md-6 mb-4">
                                    <div class="card project-card" onclick="window.location.href='?folder=<?= urlencode($proj) ?>'">
                                        <div class="card-body text-center">
                                            <h5 class="card-title"><?= $proj ?></h5>
                                            <p class="card-text">Orientation: <span class="badge badge-<?= $orient === 'landscape' ? 'primary' : 'success' ?>"><?= ucfirst($orient) ?></span></p>
                                            <button class="btn btn-outline-primary">Select Project</button>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
            <footer style="text-align: center; padding: 10px; background: #f8f9fa; margin-top: 20px;">
                &copy; 2026 Aditya Narayan Sahoo. Licensed under <a href="https://creativecommons.org/licenses/by/4.0/" target="_blank">CC BY 4.0</a>. <a href="https://github.com/adityanarayan98" target="_blank">GitHub</a> | <a href="https://sites.google.com/view/adityanarayansahoo/" target="_blank">Website</a>
            </footer>
        </body>
        </html>
        <?php
        exit;
    }

    $folder = $_GET['folder'];
    
    // Validate folder is allowed (exists in settings)
    if (!is_folder_allowed($folder)) {
        header('Location: index.php');
        exit;
    }
    
    $media = get_all_media($folder);
    $settings = get_settings($folder);
?>

<div id="slideshow">
  <?php
    $isActive = true;
    foreach ($media as $item) {
        $ext = strtolower(pathinfo($item, PATHINFO_EXTENSION));
        $isVideo = in_array($ext, ['mp4', 'avi', 'mov', 'wmv']);
        echo '<div class="slide ' . ($isActive ? 'active' : '') . '">';
        if ($isVideo) {
            echo '<video muted playsinline preload="auto" autoplay><source src="' . $item . '?t=' . time() . '" type="video/' . $ext . '"></video>';
        } else {
            echo '<img src="' . $item . '?t=' . time() . '" alt="Media">';
        }
        echo '</div>';
        $isActive = false;
    }
  ?>
</div>

<script>
// Enable autoplay by playing silent audio
var audio = new Audio('data:audio/wav;base64,UklGRnoGAABXQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YQoGAACBhYqFbF1fdJivrJBhNjVgodDbq2EcBj+a2/LDciUFLIHO8tiJNwgZaLvt559NEAxQp+PwtmMcBjiR1/LMeSwFJHfH8N2QQAoUXrTp66hVFApGn+DyvmMcBj+Q1/LNeCwFJHLH8N6QQgoUXrHp66hVFApGn+DyvmMcBj+Q1/LNeCwFJHLH8N6QQgoUXrHp66hVFApGn+DyvmMcBj+Q1/LNeCwFJHLH8N6QQgoUXrHp66hVFApGn+DyvmMcBj+Q1/LNeCwFJHLH8N6QQgoUXrHp66hVFApGn+DyvmMcBj+Q1/LNeCwFJHLH8N6QQgoUXrHp66hVFApGn+DyvmMcBj+Q1/LNeCwFJHLH8N6QQgoUXrHp66hVFApGn+DyvmMcBj+Q1/LNeCwFJHLH8N6QQgoUXrHp66hVFApGn+DyvmMcBj+Q1/LNeCwFJHLH8N6QQgoUXrHp66hVFApGn+DyvmMcBj+Q1/LNeCwFJHLH8N6QQgoUXrHp66hVFApGn+DyvmMcBj+Q1/LNeCwFJHLH8N6QQgoUXrHp66hVFApGn+DyvmMcBj+Q1/LNeCwFJHLH8N6QQgoUXrHp66hVFApGn+DyvmMcBj+Q1/LNeCwFJHLH8N6QQgoUXrHp66hVFApGn+DyvmMcBj+Q1/LNeCwFJHLH8N6QQgoUXrHp66hVFApGn+DyvmMcBj+Q1/LNeCwFJHLH8N6QQgoUXrHp66hVFApGn+DyvmMcBj+Q1/LNeCwFJHLH8N6QQgoUXrHp66hVFApGn+DyvmMcBj+Q1/LNeCwFJHLH8N6QQgoUXrHp66hVFApGn+DyvmMcBj+Q1/LNeCwFJHLH8N6QQgoUXrHp66hVFApGn+DyvmMcBj+Q1/LNeCwFJHLH8N6QQgoUXrHp66hVFApGn+DyvmMcBj+Q1/LNeCwFJHLH8N6QQgoUXrHp66hVFApGn+DyvmMcBj+Q1/LNeCwFJHLH8N6QQgoUXrHp66hVFApGn+DyvmMcBj+Q1/LNeCwFJHLH8N6QQgoUXrHp66hVFApGn+DyvmMcBj+Q1/LNeCwFJHLH8N6QQgoUXrHp66hVFApGn+DyvmMcBj+Q1/LNeCwFJHLH8N6QQgoUXrHp66hVFApGn+DyvmMcBj+Q1/LNeCwFJHLH8N6QQgoUXrHp66hVFApGn+DyvmMcBj+Q1/LNeCwFJHLH8N6QQgoUXrHp66hVFApGn+DyvmMcBj+Q1/LNeCwFJHLH8N6QQgoUXrHp66hVFApGn+DyvmMcBj+Q1/LNeCwFJHLH8N6QQgoUXrHp66hVFApGn+DyvmMcBj+Q1/LNeCwFJHLH8N6QQgoUXrHp66hVFApGn+DyvmMcBj+Q1/LNeCwFJHLH8N6QQgoUXrHp66hVFApGn+DyvmMcBj+Q1/LNeCwFJHLH8N6QQgoUXrHp66hVFApGn+DyvmMcBj+Q1/LNeCwFJHLH8N6QQgoUXrHp66hVFApGn+DyvmMcBj+Q1/LNeCwFJHLH8N6QQgoUXrHp66hVFApGn+DyvmMcBj+Q1/LNeCwFJHLH8N6QQgoUXrHp66hVFApGn+DyvmMcBj+Q1/LNeCwFJHLH8N6QQgoUXrHp66hVFApGn+DyvmMcBj+Q1/LNeCwFJHLH8N6QQgoUXrHp66hVFApGn+DyvmMcBj+Q1/LNeCwFJHLH8N6QQgoUXrHp66hVFApGn+DyvmMcBj+Q1/LNeCwFJHLH8N6QQgoUXrHp66hVFApGn+DyvmMcBj+Q1/LNeCwFJHLH8N6QQgoUXrHp66hVFApGn+DyvmMcBj+Q1/LNeCwFJHLH8N6QQgoUXrHp66hVFApGn+DyvmMcBj+Q1/LNeCwFJHLH8N6QQgoUXrHp66hVFApGn+DyvmMcBj+Q1/LNeCwFJHLH8N6QQgoUXrHp66hVFApGn+DyvmMcBj+Q1/LNeCwFJHLH8N6QQgoUXrHp66hVFApGn+DyvmMcBj+Q1/LNeCwFJHLH8N6QQgoUXrHp66hVFApGn+DyvmMcBj+Q1/LNeCwFJHLH8N6QQgoUXrHp66hVFApGn+DyvmMcBj+Q1/LNeCwFJHLH8N6QQgoUXrHp66hVFApGn+DyvmMcBj+Q1/LNeCwFJHLH8N6QQgoUXrHp66hVFApGn+DyvmMcBj+Q1/LNeCwFJHLH8N6QQgoUXrHp66hVFApGn+DY');
audio.volume = 0.01;
audio.play().catch(() => {});

var currentFolder = '<?= $folder ?>';
var currentMedia = <?= json_encode($media) ?>;
var slideshowInterval;

// Auto update check every 5 seconds
setInterval(function() {
    $.get('get_images.php?folder=' + currentFolder, {cache: false}, function(data) {
        var response = data; // Already parsed as object due to Content-Type
        var newMedia = response.media;
        var newSettings = response.settings;
        var changed = JSON.stringify(newMedia) !== JSON.stringify(currentMedia);
        // Update settings
        timer = newSettings.timer * 1000;
        if (changed) {
            console.log('Updating slideshow', newMedia.length, 'files');
            updateSlideshow(newMedia);
            currentMedia = newMedia;
        }
    }).fail(function() {
        console.log('Failed to fetch media list');
    });
}, 5000);

function updateSlideshow(media) {
    var slideshow = $('#slideshow');
    slideshow.empty();
    media.forEach(function(item, index) {
        var activeClass = index === 0 ? 'active' : '';
        var ext = item.split('.').pop().toLowerCase();
        var isVideo = ['mp4', 'avi', 'mov', 'wmv'].includes(ext);
        var html = '<div class="slide ' + activeClass + '">';
        if (isVideo) {
            html += '<video muted playsinline preload="auto" autoplay><source src="' + item + '?t=' + Date.now() + '" type="video/' + ext + '"></video>';
        } else {
            html += '<img src="' + item + '?t=' + Date.now() + '" alt="Media">';
        }
        html += '</div>';
        slideshow.append(html);
    });
    // Restart slideshow
    startSlideshow();
}

function startSlideshow() {
    if (slideshowInterval) clearTimeout(slideshowInterval);
    var slides = $('.slide');
    var timer = <?= intval($settings['timer']) * 1000 ?>;
    if (slides.length === 0) return;
    if (slides.length === 1) return; // No loop needed

    var currentIndex = 0;

    function showSlide(index) {
        slides.removeClass('active');
        slides.eq(index).addClass('active');
        var slide = slides.eq(index);
        var video = slide.find('video')[0];
        if (video) {
            // Set fallback timeout
            slideshowInterval = setTimeout(function() {
                currentIndex = (index + 1) % slides.length;
                showSlide(currentIndex);
            }, 10000); // 10s fallback
            video.addEventListener('loadedmetadata', function() {
                clearTimeout(slideshowInterval);
                var duration = video.duration * 1000; // in ms
                if (duration > 0) {
                    video.play().then(() => {
                        slideshowInterval = setTimeout(function() {
                            currentIndex = (index + 1) % slides.length;
                            showSlide(currentIndex);
                        }, duration);
                    }).catch(() => {
                        // Play failed, use fallback
                        slideshowInterval = setTimeout(function() {
                            currentIndex = (index + 1) % slides.length;
                            showSlide(currentIndex);
                        }, 10000);
                    });
                } else {
                    // If duration 0, advance immediately
                    currentIndex = (index + 1) % slides.length;
                    showSlide(currentIndex);
                }
            }, {once: true});
            video.load();
        } else {
            slideshowInterval = setTimeout(function() {
                currentIndex = (index + 1) % slides.length;
                showSlide(currentIndex);
            }, timer);
        }
    }

    showSlide(0);
}

// Start on load
$(document).ready(function() {
    startSlideshow();
});
</script>

<footer style="position: absolute; bottom: 0; left: 0; right: 0; text-align: center; padding: 5px; background: rgba(248, 249, 250, 0.9); font-size: 12px; z-index: 1000;">
    &copy; 2026 Aditya Narayan Sahoo. Licensed under <a href="https://creativecommons.org/licenses/by/4.0/" target="_blank">CC BY 4.0</a>. <a href="https://github.com/adityanarayan98" target="_blank">GitHub</a> | <a href="https://sites.google.com/view/adityanarayansahoo/" target="_blank">Website</a>
</footer>

</body>
</html>
