<?php
// Session must start before any output - fixes headers already sent warning
require_once 'auth.php';
?>
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
    :root {
      --safe-height: 100vh;
    }

    html, body {
      margin: 0;
      padding: 0;
      height: 100%;
      position: relative;
      box-sizing: border-box;
      overflow: hidden;
    }
    
    body.slideshow-active {
      padding-bottom: 40px;
      overflow: hidden;
    }

    .admin-login-float {
      position: fixed;
      top: 10px;
      right: 10px;
      z-index: 10000;
      opacity: 0.3;
      transition: opacity 0.3s;
    }

    .admin-login-float:hover {
      opacity: 1;
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
      height: var(--safe-height);
      width: 100vw;
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
    }

    .slide {
      display: none;
      height: var(--safe-height);
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
      height: 100%;
      image-rendering: optimizeQuality;
      -webkit-font-smoothing: antialiased;
    }

    .slide video {
      width: 100%;
      height: auto;
      max-height: 100%;
      object-fit: contain;
      image-rendering: optimizeQuality;
      -webkit-font-smoothing: antialiased;
    }

    .carousel-inner,
    .carousel-item {
        height: var(--safe-height) !important;
    }

    .carousel-item {
      display: flex !important;
      align-items: center !important;
      justify-content: center !important;
      height: var(--safe-height) !important;
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
<body class="<?= $isPreview ? 'preview-mode' : '' ?> <?= isset($_GET['folder']) ? 'slideshow-active' : '' ?>">

<div class="admin-login-float">
  <a href="admin.php" class="btn btn-sm btn-outline-light">Admin Login</a>
</div>

<?php
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
                body { background: #f8f9fa; padding: 20px; }
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
                        <h1 class="text-center mb-2" style="font-size: 36px;">Academic Content Display System (ACDS)</h1>
                        <h2 class="text-center mb-3" style="font-size: 26px;">Select Project</h2>
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

<!-- Audio Permission Overlay - TV Optimized -->
<div id="audioPermissionOverlay" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.95); z-index: 99999; display: none; align-items: center; justify-content: center; flex-direction: column; color: white; text-align: center; padding: 40px;">
  <h2 style="margin-bottom: 30px; font-size: 42px;">🔊 Audio Required</h2>
  <p style="max-width: 800px; margin-bottom: 50px; font-size: 28px; line-height: 1.6;">
    Browser security has blocked automatic audio playback.<br>
    Press <strong>OK</strong> on your remote control to enable sound.
  </p>
  <button id="enableAudioButton" style="padding: 35px 80px; font-size: 36px; background: #28a745; color: white; border: 5px solid transparent; border-radius: 16px; cursor: pointer; font-weight: 600; outline: none;">
    ✅ PRESS OK
  </button>
  <p style="margin-top: 40px; opacity: 0.7; font-size: 22px;">
    Works with any remote control, keyboard or button press
  </p>
</div>

<div id="slideshow">
  <?php
    $isActive = true;
    foreach ($media as $item) {
        $ext = strtolower(pathinfo($item, PATHINFO_EXTENSION));
        $isVideo = in_array($ext, ['mp4', 'avi', 'mov', 'wmv']);
        echo '<div class="slide ' . ($isActive ? 'active' : '') . '">';
        if ($isVideo) {
            echo '<video playsinline preload="auto" autoplay muted><source src="' . $item . '?t=' . time() . '" type="video/' . $ext . '"></video>';
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
var audio = new Audio('data:audio/wav;base64,UklGRnoGAABXQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YQoGAACBhYqFbF1fdJivrJBhNjVgodDbq2EcBj+a2/LDciUFLIHO8tiJNwgZaLvt559NEAxQp+PwtmMcBjiR1/LMeSwFJHfH8N2QQAoUXrTp66hVFApGn+DY');
audio.volume = 0.01;
let autoplayAllowed = false;
let audioOverlay = document.getElementById('audioPermissionOverlay');
let enableAudioBtn = document.getElementById('enableAudioButton');

function checkAudioPermission() {
  // ✅ NEW 2026 BROWSER POLICY: ALWAYS SHOW PROMPT AFTER PAGE REFRESH
  // No more automatic audio unlock. User must interact once after every refresh.
  showAudioOverlay();
}

// Handle user click to enable audio (only this will unlock browser audio)
function enableAudio() {
  audio.play().then(() => {
      autoplayAllowed = true;
      sessionStorage.setItem('audioAllowed', 'true');
      audioOverlay.style.display = 'none';
      console.log('✅ Audio permission granted by user');
      // Only unmute videos - DO NOT PLAY THEM! Slideshow will handle playing in correct order
      document.querySelectorAll('video').forEach(v => {
          try { v.muted = false; } catch(e) {}
      });
  }).catch(() => {
      console.log('❌ User still denied audio permission');
      // Show small mute indicator only - no popup alert
      audioOverlay.style.display = 'none';
      // Add visual muted indicator on all videos
      document.querySelectorAll('video').forEach(v => {
          const muteBadge = document.createElement('div');
          muteBadge.style.cssText = 'position:absolute; bottom:20px; right:20px; background:rgba(0,0,0,0.7); color:white; padding:12px 16px; border-radius:8px; font-size:22px; z-index:100; opacity:0.8;';
          muteBadge.innerText = '🔇 Muted';
          v.parentElement.style.position = 'relative';
          v.parentElement.appendChild(muteBadge);
      });
  });
}

enableAudioBtn.addEventListener('click', enableAudio);

// Allow ANY key press to enable audio (TV remote friendly)
document.addEventListener('keydown', function(e) {
  if (audioOverlay.style.display === 'flex') {
      enableAudio();
      e.preventDefault();
      return false;
  }
});

// Auto-focus button for TV remote navigation
function showAudioOverlay() {
  audioOverlay.style.display = 'flex';
  setTimeout(() => {
      enableAudioBtn.focus();
      enableAudioBtn.style.borderColor = '#ffc107';
      enableAudioBtn.style.boxShadow = '0 0 40px #ffc107';
  }, 100);
}

// Initial check on page load
checkAudioPermission();

var currentFolder = '<?= $folder ?>';
var currentMedia = <?= json_encode($media) ?>;
var slideshowInterval;

// ✅ GLOBAL TIMER - CAN BE UPDATED LIVE FROM ADMIN
let timer = <?= intval($settings['timer']) * 1000 ?>;

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
            html += '<video playsinline preload="auto" autoplay muted><source src="' + item + '?t=' + Date.now() + '" type="video/' + ext + '"></video>';
        } else {
            html += '<img src="' + item + '?t=' + Date.now() + '" alt="Media">';
        }
        html += '</div>';
    slideshow.append(html);
    });
    
    // 🔥 Re-attach audio permission to NEW videos after update
    if (autoplayAllowed) {
        setTimeout(() => {
            document.querySelectorAll('video').forEach(v => {
                try {
                    v.muted = false;
                    v.volume = 1;
                } catch(e) {}
            });
        }, 500);
    }
    
    // Restart slideshow
    startSlideshow();
}

function startSlideshow() {
    if (slideshowInterval) clearTimeout(slideshowInterval);
    var slides = $('.slide');
    if (slides.length === 0) return;
    if (slides.length === 1) return; // No loop needed

    var currentIndex = 0;

    function showSlide(index) {
        // ✅ STOP ALL VIDEOS EXCEPT THE ONE WE ARE ABOUT TO PLAY
        document.querySelectorAll('video').forEach((v, i) => {
            try {
                // ONLY STOP VIDEOS THAT ARE NOT THE CURRENT SLIDE WE ARE SHOWING
                if (i !== index) {
                    v.volume = 0;
                    // DO NOT MUTE IF AUDIO IS ALREADY ENABLED
                    if (!autoplayAllowed) {
                        v.muted = true;
                    }
                    v.pause();
                    v.currentTime = 0;
                }
            } catch(e) {}
        });

        setTimeout(() => {
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
                var totalWait = duration + timer;
                if (duration > 0) {
                    // ✅ SET AUDIO STATE BEFORE PLAYING
                    video.muted = !autoplayAllowed;
                    video.volume = autoplayAllowed ? 1 : 0;
                    
                    video.play().then(() => {
                        // Only if browser still has it muted
                        if (autoplayAllowed && video.muted) {
                            try { video.muted = false; } catch(e) {}
                        }
                        slideshowInterval = setTimeout(function() {
                            currentIndex = (index + 1) % slides.length;
                            showSlide(currentIndex);
                        }, totalWait);
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
        }, 0);
    }

    showSlide(0);
}

// Dynamic safe height calculation - always fits perfectly
function updateSafeHeight() {
    document.documentElement.style.setProperty('--safe-height', window.innerHeight + 'px');
}

// Update immediately and on every resize
updateSafeHeight();
window.addEventListener('resize', updateSafeHeight);
window.addEventListener('orientationchange', updateSafeHeight);

// Start on load
$(document).ready(function() {
    startSlideshow();
});
</script>
<footer style="text-align: center; padding: 10px; background: #000000; color: #ffffff; position: fixed; bottom: 0; width: 100%; z-index: 1000; opacity: 0.6; font-size: 12px;">
            &copy; 2026 Aditya Narayan Sahoo. Licensed under <a href="https://creativecommons.org/licenses/by/4.0/" target="_blank" style="color: #007bff;">CC BY 4.0</a>. <a href="https://github.com/adityanarayan98" target="_blank" style="color: #007bff;">GitHub</a> | <a href="https://sites.google.com/view/adityanarayansahoo/" target="_blank" style="color: #007bff;">Website</a>

</body>
</html>
