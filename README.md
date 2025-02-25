# WordPress Testimonial Slider Plugin

A responsive WordPress testimonial carousel with video popup functionality, mobile swipe capabilities, and customizable options. Perfect for showcasing testimonials with background images and video content.

## Version
1.0.0

## Features

- Fully responsive design with mobile-friendly layout
- Video popup functionality for testimonial videos
- Customizable colors, fonts, and hover effects
- Admin interface for managing slides and videos
- Drag-and-drop image upload
- Per-slide image position control (left, center, right)
- Mobile swipe navigation
- Optional autoplay
- Shortcode integration for easy placement

## Installation

1. Download the plugin ZIP file
2. Go to your WordPress admin panel
3. Navigate to Plugins > Add New > Upload Plugin
4. Choose the downloaded ZIP file and click "Install Now"
5. Activate the plugin

## Directory Structure

The plugin follows the standard WordPress plugin architecture:

```
advanced-testimonial-slider/
├── advanced-testimonial-slider.php    (Main plugin file)
├── README.md                         (This file)
├── assets/
│   ├── css/
│   │   ├── admin.css                 (Admin styles)
│   │   └── testimonial-slider.css    (Front-end styles)
│   └── js/
│       ├── admin.js                  (Admin scripts)
│       └── testimonial-slider.js     (Front-end scripts)
└── includes/
    ├── class-testimonial-slider-admin.php      (Admin functionality)
    ├── class-testimonial-slider-post-type.php  (Custom post type)
    └── class-testimonial-slider-shortcode.php  (Shortcode handling)
```

## Usage

### Adding a New Testimonial Slide

1. Navigate to **Testimonial Slider > Add New** in the WordPress admin menu
2. Enter the name of the testimonial subject as the title
3. Add a description in the main content editor
4. Upload an image using the **Slide Image** meta box
5. Select the image position (left, center, right) to control how the image appears
6. Add video questions and YouTube links in the **Video Questions** meta box
7. Click **Publish** to save the slide

### Managing Slides

- All slides are displayed in order based on their menu order
- To reorder slides, go to **Testimonial Slider > All Testimonial Slides** and drag to reorder
- Edit, delete, or add new slides as needed
- Each slide supports multiple video questions with YouTube links

### Customizing Appearance

1. Navigate to **Testimonial Slider > Settings**
2. Under the **General Settings** tab, you can:
   - Change the CTA button text and URL
   - Enable/disable mobile swipe functionality
   - Configure autoplay settings
3. Under the **Style Settings** tab, you can:
   - Adjust font sizes for titles and descriptions
   - Change primary, secondary, and button colors
   - Select different button hover effects

### Adding the Slider to Your Site

Use the shortcode `[advanced_testimonial_slider]` to display the slider on any page or post.

Example:
```
[advanced_testimonial_slider]
```

For advanced usage, you can customize the shortcode with these parameters:

```
[advanced_testimonial_slider limit="5" orderby="date" order="DESC"]
```

- `limit`: Maximum number of slides to display (default: -1, show all)
- `orderby`: Sort slides by "menu_order" (default), "date", "title", etc.
- `order`: Sort direction, "ASC" (default) or "DESC"

## Image Positioning

Each slide has an image position option that lets you control how the background image is positioned horizontally:

- **Left**: Aligns the image to the left of the container
- **Center**: Centers the image (default)
- **Right**: Aligns the image to the right of the container

This is useful for optimizing how portrait subjects or key elements appear in each slide.

## Customization

### CSS Customization

The plugin includes comprehensive styling options in the admin interface. However, if you need additional customization, you can add custom CSS to your theme.

Example:
```css
/* Change slide transition speed */
.carousel-track {
    transition: transform 0.3s ease-in-out;
}

/* Customize pagination dots */
.pagination-dot {
    width: 15px;
    height: 15px;
}
```

### Images

- Recommended image size is 600x520px
- Images will be displayed as background images in the left column of each slide
- Use high-quality images for best results

### Videos

- The plugin supports YouTube videos
- You can use either regular YouTube URLs (https://www.youtube.com/watch?v=XXXX) or short URLs (https://youtu.be/XXXX)
- Videos will open in a responsive lightbox overlay

## FAQ

**Q: Can I change the CTA button text and link?**  
A: Yes, navigate to Testimonial Slider > Settings and update the Button Text and Button URL fields.

**Q: How do I change the order of slides?**  
A: Go to Testimonial Slider > All Testimonial Slides and drag to reorder the slides.

**Q: Can I disable swipe on mobile?**  
A: Yes, go to Testimonial Slider > Settings and uncheck "Enable Swipe on Mobile".

**Q: How do I add more video questions to a slide?**  
A: Edit the slide and click the "Add Question" button in the Video Questions section.

**Q: Can I change the colors of the slider?**  
A: Yes, go to Testimonial Slider > Settings and use the color pickers to customize colors.

**Q: How do I control image positioning in a slide?**  
A: When editing a slide, use the "Image Position" dropdown in the Slide Image section to select left, center, or right alignment.

## Troubleshooting

**Videos not playing**
- Ensure you're using valid YouTube URLs (https://youtu.be/XXXX or https://www.youtube.com/watch?v=XXXX)
- Check that your site allows embedded videos

**Mobile swipe not working**
- Verify that "Enable Swipe on Mobile" is checked in the settings
- Make sure no other plugins are interfering with touch events

**Images not displaying correctly**
- Recommended image size is 600x520px
- Try regenerating thumbnails if using an image optimization plugin
- Check that the image position is set appropriately for your content

**Slider not appearing**
- Make sure you've added the correct shortcode: `[advanced_testimonial_slider]`
- Check that you have published testimonial slides
- Verify there are no JavaScript errors in your browser console

## License

This plugin is licensed under the GPL v2 or later.
