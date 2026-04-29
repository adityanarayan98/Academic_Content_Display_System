# Academic Content Display System (ACDS)

A web-based slideshow display system for managing and showing images and videos in projects with customizable settings.

## Features

- **Project-Based Organization**: Create and manage separate projects with their own media collections.
- **Slideshow Display**: Automatic full-screen slideshow playback of images and videos.
- **Media Support**: Upload and display images (JPG, PNG, GIF) and videos (MP4, AVI, MOV, WMV).
- **Admin Panel**: Secure login with user management, media uploads, and settings configuration.
- **Real-Time Updates**: Slideshow automatically refreshes every 5 seconds to show new content.
- **Orientation Modes**: Support for landscape and portrait display orientations.
- **Temporary Media Staging**: Upload files to a temp folder, then add them to projects via drag-and-drop or buttons.
- **Media Management**: View, reorder, duplicate, download, and delete media files.
- **Custom Sequencing**: Drag-and-drop to customize slideshow order.
- **Bulk Upload**: Upload multiple files at once.
- **Preview Mode**: Test slideshow in a scaled-down window.
- **Responsive Design**: Adapts to any screen size and orientation.
- **Video Playback**: Autoplay videos with duration-based timing and muted audio.
- **User Authentication**: Multi-user support with hashed passwords and math captcha.
- **AJAX Interface**: Real-time updates without page reloads.

## Requirements

- **Web Server**: Apache (recommended with XAMPP for local development)
- **PHP**: Version 7.4 or higher
- **Browser**: Modern browser (Chrome, Firefox, Safari) for full video support. Opera Mini has limited video playback.
- **File Permissions**: Write access to folders for media uploads
- **Supported Media Formats**:
  - Images: JPG, JPEG, PNG, GIF
  - Videos: MP4 (H.264 recommended), AVI, MOV, WMV

## Installation

1. **Download/Clone**: Place all files in your web server's root directory (e.g., `htdocs/Display/` in XAMPP).

2. **Permissions**: Ensure the web server can write to the directory and subfolders. In XAMPP, right-click folder > Properties > Security > Give full control to user.

3. **Initial Setup**: The system creates settings automatically. No database required.

## Usage

### 1. Access the Application

- Open your browser and navigate to `http://localhost/Display/` (adjust for your server).
- You'll see the project selection page.

### 2. Select or Create a Project

- **Select Project**: Click on an existing project card to view its slideshow.
- **Create Project** (Admin Only): Login as admin to create new projects.

### 3. Admin Access

- Click "Admin Login" from the project selection page.
- **Login Credentials**:
  - Username: `admin`
  - Password: Set in `config.php` (default: change for security)
  - Or use user accounts created via admin panel
- Solve the math captcha on login.

### 4. Admin Panel Overview

After login, you'll see:
- **Project Management**: Select or create projects
- **Settings**: Configure timer and orientation
- **Upload Media**: Add images/videos
- **Media Management**: View, reorder, and delete media files

### 5. Creating a New Project

1. In the "Project Management" section, enter a project name in "Create New Project".
2. Click "Create".
3. The project folder will be created with default settings.

### 6. Configuring Project Settings

1. Select a project from the dropdown.
2. **Timer**: Set slide duration in seconds (1-60).
3. **Orientation**: Choose Landscape or Portrait.
4. Click "Save Settings".

### 7. Uploading Media

1. In "Upload Images", choose files (multiple selection allowed).
2. Supported formats: JPG, PNG, GIF, MP4, AVI, MOV, WMV.
3. Max file size: 500MB per file.
4. Click "Upload".
5. Files are uploaded to the temp folder with unique names to avoid conflicts.

### 8. Managing Temp Media

- Temp media are stored in a temporary folder with unique names.
- View in grid or list mode; toggle with buttons.
- Hover over grid items to see View, Add, and Delete icons.
- Click "View" to preview in modal.
- Click "Add" to copy file to current project (keeps original in temp).
- Drag and drop items to media list to move them to project.
- Click "Del" to delete from temp.

### 9. Managing Media

- **View**: Click the "View" button to preview media in a modal.
- **Add**: Click the "Add" button in the view modal to duplicate the media in the project (creating a copy) or copy from temp to project if viewing temp media.
- **Reorder**: Drag and drop media cards to change sequence.
- Click "Save Order" after reordering.
- **Delete**: Click "Delete" on a media card, confirm deletion.

### 10. Managing Temp Media

- Temp media are stored in a temporary folder.
- View thumbnails in the "Temp Media" section on the left.
- Drag and drop temp items onto the media list to add them to the current project.
- Click "Del" to delete temp files and free up storage.

### 11. User Management (Admin)

- Click "User Management" to add/edit users for restricted access.
- Add users with username/password.
- Users can view slideshows but not admin functions.

### 12. Viewing the Slideshow

- From project selection, click a project to start the slideshow.
- Slideshow runs automatically based on timer settings.
- Videos play with audio muted; duration based on video length or fallback to timer.
- Press F11 for fullscreen.

### 13. Preview Mode

- In admin panel, click "Preview Slideshow" to view in a smaller window.
- Useful for testing without fullscreen.

### 14. Logout

- Click "Logout" in admin panel or add `?logout` to URL.

## Configuration

- **Settings File**: Each project has a `settings.json` with timer, orientation, and sequence.
- **Config.php**: Contains admin password and other constants. Edit for customization.
- **Auto-Update**: Slideshow checks for new media every 5 seconds.

## Troubleshooting

- **Videos not playing**: Use MP4/H.264. In Opera Mini, videos may not work due to browser limitations.
- **Upload fails**: Check file permissions and size limits.
- **Stretched media**: Videos are fitted to width, centered, maintaining aspect ratio.
- **Permission errors**: Ensure web server user has write access.
- **Math captcha issues**: Refresh page if math problem is incorrect.

## Security Notes

- Change default admin password in `config.php`.
- Use HTTPS in production.
- Restrict access to admin panel.

## Display Compatibility

- Designed for any display size (desktop, TV, projector).
- Works in fullscreen mode (press F11).
- Responsive design adapts to screen orientation.

## Browser Support

- Modern browsers: Chrome, Firefox, Safari, Edge.
- Limited support in Opera Mini and smart TV browsers (videos may not play due to proxy rendering or limited HTML5 support).
- For smart TVs, videos may not auto-play; consider using images only or connecting a computer with a full browser to the TV.
- Ensure JavaScript and HTML5 video support enabled.
- Note: Videos auto-play if browser allows; otherwise, slideshow advances automatically after 10 seconds per video.

## Author

Aditya Narayan Sahoo

- GitHub: [https://github.com/adityanarayan98](https://github.com/adityanarayan98)
- Website: [https://sites.google.com/view/adityanarayansahoo/](https://sites.google.com/view/adityanarayansahoo/)

## Contribution

This project was created and is maintained by Aditya Narayan Sahoo. Contributions are welcome under the CC BY 4.0 license. Please see the LICENSE file for details.

## License

&copy; 2026 Aditya Narayan Sahoo. Licensed under [CC BY 4.0](https://creativecommons.org/licenses/by/4.0/). See LICENSE file.

## Support

For issues, check file permissions, PHP errors, and browser console.</content>
<parameter name="filePath">README.md
