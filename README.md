# GalaTV Streaming Website

A modern streaming website interface with a dark/gold theme, featuring a responsive design with animations and interactive elements.

## Features

- **Hero Section** with live streaming interface
- **Weekly Schedule** with program cards and advertising space
- **Commercial Section** with contact form
- **Dark/Gold Theme** with animated golden light trails
- **Responsive Design** for mobile and desktop
- **Bilingual Support** (Spanish/English)

## Project Structure

```
galatv/
├── index.html          # Main HTML file
├── styles.css          # Main stylesheet
├── script.js           # JavaScript functionality
├── assets/             # Images and assets
│   ├── card1.jpg       # Program card images
│   ├── card2.jpg
│   ├── card3.jpg
│   ├── card4.jpg
│   └── logogala.png    # Logo
├── base.jpeg           # Reference screenshot
├── reference.jpg       # Design reference
└── logogala.png        # Logo file
```

## Technologies Used

- **HTML5** - Semantic markup
- **CSS3** - Modern styling with gradients and animations
- **JavaScript** - Interactive functionality
- **Font Awesome** - Icon library (CDN)

## Key Features

### Visual Design
- Dark background (#050505) with golden accents (#FFD700)
- Animated golden light trails and pulsing effects
- Radial gradient overlays for atmospheric lighting
- Responsive grid layout
- Smooth scroll behavior

### Sections
1. **Header** - Logo, navigation, language selector
2. **Hero** - Live streaming player with progress bar
3. **Benefits** - 4-column feature grid
4. **Schedule** - Weekly program cards with ad space
5. **Commercial** - Advertising contact form
6. **Footer** - Logo and social media links

### Interactive Elements
- Language toggle (ES/EN)
- Contact form with validation
- Smooth navigation
- Animated background effects

## Getting Started

### Local Development

1. Clone the repository:
```bash
cd D:\ia\proyectos\galatv
```

2. Start the local server:
```bash
npx http-server -p 8000
```

3. Open in browser:
```
http://127.0.0.1:8000
```

### Files to Edit

- `index.html` - Main HTML structure
- `styles.css` - Styling and animations
- `script.js` - JavaScript functionality

## Styling Highlights

```css
/* Golden light trails overlay */
.golden-trails {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  z-index: 1;
  animation: goldenPulse 10s ease-in-out infinite;
}

/* Animated background */
body {
  background: #050505;
  color: #fff;
  font-family: Arial, Helvetica, sans-serif;
}
```

## Browser Support

- Chrome (latest)
- Firefox (latest)
- Safari (latest)
- Edge (latest)

## License

© 2026 GalaTV. All rights reserved.

---
Created for GalaTV Streaming Platform