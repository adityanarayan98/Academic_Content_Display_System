# Academic Content Display System (ACDS)
## USER MANUAL

---

## 1. INTRODUCTION
The Academic Content Display System is a full-screen digital signage slideshow application designed to display images and videos in public areas, waiting rooms, lobbies, and academic institutions.

---

## 2. SYSTEM REQUIREMENTS
- Any modern web browser (Chrome, Firefox, Edge, Safari)
- JavaScript enabled
- **Important Note:** This system can runs on Linux or Windows servers. All filenames and URLs are case-sensitive.

---

## 3. ACCESSING THE SYSTEM
1.  Open your web browser
2.  Navigate to the system address: `http://[your-server-address]/Display/`
3.  You will see the project selection screen

---

## 4. SELECTING A PROJECT
1.  On the main page, all available projects will be displayed as cards
2.  Each project shows:
    - Project name
    - Display orientation (Landscape / Portrait)
3.  Click on **Select Project** button for the display you want to view
4.  The slideshow will start automatically

---

## 5. VIEWING THE SLIDESHOW

### ✅ First Time Setup: Audio Permission
✅ **IMPORTANT FOR 2026 BROWSERS:**
Modern browsers now require explicit user interaction before allowing any audio playback. This is not optional.

1.  When opening a slideshow you will see an **Audio Required** full screen prompt
2.  Press the **OK** button on your TV remote control OR
3.  Press **Enter** / **Space** on keyboard OR
4.  Click anywhere on screen with mouse
5.  Audio will be enabled permanently for the entire session
6.  **This step MUST be performed ONCE after every page refresh**

✅ **NOTE:**
- This prompt will appear EVERY TIME you refresh or reload the page
- No workaround exists - this is browser security policy
- Slideshow will continue to play without audio if this step is skipped
- All TV remote controls work for this prompt

✅ **Features:**
- Full screen automatic slideshow
- Supports both images (JPG, PNG, GIF) and videos (MP4, AVI, MOV, WMV)
- Auto-advances based on configured timer
- Videos play completely before advancing with optional audio
- Automatically updates when new content is added
- No further user interaction required once started

✅ **Preview Mode:**
- Click "Preview Slideshow" from admin panel to view without full screen
- Shows scrollable view of all content

---

## 6. ADMIN PANEL USAGE

### 6.1 LOGIN TO ADMIN PANEL
1.  Open your web browser
2.  Navigate to `http://[your-server-address]/Display/admin.php`
3.  Enter your username and password
4.  Complete the math security question
5.  Click **Login** button

---

### 6.2 UPLOADING CONTENT
1.  Login to admin panel
2.  Select your project from the dropdown list
3.  Under **Upload Images** section click **Choose Files**
4.  Select one or multiple image/video files
5.  Click **Upload** button
6.  Files will automatically appear in your media list

✅ **Supported File Formats:**
- Images: JPG, JPEG, PNG, GIF
- Videos: MP4, AVI, MOV, WMV

---

### 6.3 REARRANGING SLIDES ORDER
1.  Go to **Media** list in admin panel
2.  Click and hold the **⋮⋮ drag handle** on the left side of any card
3.  Drag the item up or down to new position
4.  Release mouse button to drop
5.  Click **Save Order** button at top to permanently save new sequence

✅ Changes will appear on live display automatically within 5 seconds.

---

### 6.4 USING TEMP FOLDER
Temp folder is shared storage for all projects. Files can be stored here and added to multiple projects.

1.  Upload files normally - files first go to Temp folder
2.  Each temp file has **Add button** (green + icon)
3.  Click **Add** to copy file to currently selected project
4.  Files can be added to many projects multiple times
5.  Drag temp files directly onto media list to add

---

### 6.5 DOWNLOADING FILES
- **From Project:** Click **View** button on any media card, then click **Download** button
- **From Temp Folder:** Click download icon ⬇️ on any temp file
- Files are downloaded with original filenames

---

### 6.6 TIMER SETTINGS
The timer value controls how long each slide is displayed:
- **For images:** Image will display for exactly this many seconds
- **For videos:** Video plays full length, **then** waits this many seconds before advancing

✅ **Recommended values:**
- General use: 3 - 8 seconds
- Text heavy slides: 10 - 15 seconds
- For videos: 0 - 1 second gap

**Changing Timer:**
1.  Enter number of seconds in **Timer (sec)** field
2.  Select display orientation (Landscape / Portrait)
3.  Click **Save Settings** button
4.  Changes take effect immediately on live display

---

### 6.7 USER MANAGEMENT
Only administrators can manage system users.

**Access User Management:**
1.  Login to admin panel
2.  Click **User Management** button
3.  Modal window will open with user controls

| User Action | How |
|---|---|
| Add new user | Enter username and password, click **Add/Update User** |
| Edit user password | Click **Edit** button next to username, enter new password |
| Delete user | Click **Delete** button next to username |

⚠️ At least one administrator user must always exist.

---

### 6.8 ADDITIONAL ADMIN FUNCTIONS
| Action | How |
|---|---|
| Delete file | Click red **Delete** button on media card |
| View file preview | Click green **View** button |
| Duplicate file | Click **View** button then click **Add** button to create duplicate |
| Preview slideshow | Click **Preview Slideshow** link at top right |
| View Total Cycle Time | Displayed at top of Media list showing total slideshow runtime |
| Create new project | Enter project name, click **Create** button |
| Logout | Click **Logout** button at top right |

---

## 7. TROUBLESHOOTING

| Issue | Solution |
|-------|----------|
| **No audio in videos** | Press any key when the Audio Required prompt appears. This is required once after every page refresh for modern browsers. |
| Slideshow not starting | Click anywhere on the start screen to enable autoplay |
| Videos not playing | Check browser console for autoplay permissions. Modern browsers require user interaction before playing media with sound. If still not working open in incognito mode.|
| Black screen | Refresh the browser page. Check internet connection. |
| Old content still showing | Press `Ctrl+F5` to hard refresh the browser cache. The system automatically refreshes every 5 seconds. |
| Images not loading | Verify filenames are exactly correct (case-sensitive on Linux) |

---

## 7. CASE SENSITIVITY IMPORTANT NOTICE
⚠️ **CRITICAL FOR LINUX USAGE:**
- All file names, folder names and URLs are case-sensitive on Linux
- `Image.jpg` is NOT the same as `image.jpg` or `IMAGE.JPG`
- Always use exact matching case when referencing files
- Windows systems ignore case differences so always test filenames on actual deployment server

---

## 8. DISPLAY BEHAVIOR
- Images display for the configured timer duration (default: 5 seconds)
- Videos play in full length automatically, then wait for configured timer before advancing
- Slideshow runs in continuous loop
- All changes made by administrators appear automatically within 5 seconds
- No page refresh required for updates

---

## 9. SUPPORT
For technical issues, contact the system administrator.

---

© 2026 Academic Content Display System