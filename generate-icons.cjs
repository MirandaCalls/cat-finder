#!/usr/bin/env node

const fs = require('fs');
const { PNG } = require('pngjs');

// Icon sizes needed for iOS and Android
const sizes = [
  { size: 180, name: 'apple-touch-icon.png' },
  { size: 192, name: 'icon-192.png' },
  { size: 512, name: 'icon-512.png' },
];

// Helper functions for drawing
function setPixel(png, x, y, r, g, b, a = 255) {
  if (x < 0 || x >= png.width || y < 0 || y >= png.height) return;
  const idx = (png.width * y + x) << 2;
  png.data[idx] = r;
  png.data[idx + 1] = g;
  png.data[idx + 2] = b;
  png.data[idx + 3] = a;
}

function fillRect(png, x, y, w, h, r, g, b, a = 255) {
  for (let i = x; i < x + w; i++) {
    for (let j = y; j < y + h; j++) {
      setPixel(png, i, j, r, g, b, a);
    }
  }
}

function drawCircle(png, cx, cy, radius, r, g, b, a = 255, filled = true) {
  for (let y = -radius; y <= radius; y++) {
    for (let x = -radius; x <= radius; x++) {
      const distance = Math.sqrt(x * x + y * y);
      if (filled ? distance <= radius : Math.abs(distance - radius) < 1.5) {
        setPixel(png, cx + x, cy + y, r, g, b, a);
      }
    }
  }
}

function drawLine(png, x1, y1, x2, y2, thickness, r, g, b, a = 255) {
  const dx = x2 - x1;
  const dy = y2 - y1;
  const steps = Math.max(Math.abs(dx), Math.abs(dy));
  const xInc = dx / steps;
  const yInc = dy / steps;

  for (let i = 0; i <= steps; i++) {
    const x = Math.round(x1 + xInc * i);
    const y = Math.round(y1 + yInc * i);
    for (let t = -thickness / 2; t <= thickness / 2; t++) {
      setPixel(png, x + t, y, r, g, b, a);
      setPixel(png, x, y + t, r, g, b, a);
    }
  }
}

function drawTriangle(png, x1, y1, x2, y2, x3, y3, r, g, b, a = 255) {
  // Find bounding box
  const minX = Math.floor(Math.min(x1, x2, x3));
  const maxX = Math.ceil(Math.max(x1, x2, x3));
  const minY = Math.floor(Math.min(y1, y2, y3));
  const maxY = Math.ceil(Math.max(y1, y2, y3));

  // Fill triangle using barycentric coordinates
  for (let y = minY; y <= maxY; y++) {
    for (let x = minX; x <= maxX; x++) {
      const w1 = ((y2 - y3) * (x - x3) + (x3 - x2) * (y - y3)) / ((y2 - y3) * (x1 - x3) + (x3 - x2) * (y1 - y3));
      const w2 = ((y3 - y1) * (x - x3) + (x1 - x3) * (y - y3)) / ((y2 - y3) * (x1 - x3) + (x3 - x2) * (y1 - y3));
      const w3 = 1 - w1 - w2;
      if (w1 >= 0 && w2 >= 0 && w3 >= 0) {
        setPixel(png, x, y, r, g, b, a);
      }
    }
  }
}

function createIcon(size) {
  const png = new PNG({ width: size, height: size });

  // Create gradient background (indigo to purple)
  for (let y = 0; y < size; y++) {
    for (let x = 0; x < size; x++) {
      const t = (x + y) / (size * 2);
      const r = Math.round(99 + (139 - 99) * t);
      const g = Math.round(102 + (92 - 102) * t);
      const b = Math.round(241 + (246 - 241) * t);
      setPixel(png, x, y, r, g, b, 255);
    }
  }

  const scale = size / 512;

  // Draw cat
  const catX = Math.round(210 * scale);
  const catY = Math.round(200 * scale);

  // Cat ears (triangles)
  drawTriangle(
    png,
    Math.round(140 * scale), Math.round(160 * scale),
    Math.round(110 * scale), Math.round(110 * scale),
    Math.round(170 * scale), Math.round(140 * scale),
    255, 255, 255, 255
  );
  drawTriangle(
    png,
    Math.round(280 * scale), Math.round(160 * scale),
    Math.round(310 * scale), Math.round(110 * scale),
    Math.round(250 * scale), Math.round(140 * scale),
    255, 255, 255, 255
  );

  // Cat head (circle)
  drawCircle(png, catX, catY, Math.round(80 * scale), 255, 255, 255);

  // Cat body (ellipse approximated with circles)
  for (let i = -90; i <= 90; i += 5) {
    const y = Math.round(310 * scale + i * scale);
    const rx = Math.round(70 * scale * Math.sqrt(1 - (i / 90) ** 2));
    drawCircle(png, Math.round(210 * scale), y, rx, 255, 255, 255);
  }

  // Draw magnifying glass
  const glassX = Math.round(350 * scale);
  const glassY = Math.round(350 * scale);
  const glassRadius = Math.round(70 * scale);

  // Glass circle (hollow)
  for (let r = glassRadius - Math.round(8 * scale); r <= glassRadius; r++) {
    drawCircle(png, glassX, glassY, r, 255, 255, 255, 255, false);
  }

  // Glass fill (semi-transparent)
  drawCircle(png, glassX, glassY, Math.round(60 * scale), 255, 255, 255, 80);

  // Handle
  drawLine(
    png,
    glassX + Math.round(50 * scale), glassY + Math.round(50 * scale),
    glassX + Math.round(100 * scale), glassY + Math.round(100 * scale),
    Math.round(20 * scale),
    255, 255, 255, 255
  );

  return png;
}

// Generate all icon sizes
sizes.forEach(({ size, name }) => {
  const png = createIcon(size);
  png.pack().pipe(fs.createWriteStream(`./public/${name}`));
  console.log(`Generated ${name} (${size}x${size})`);
});

console.log('All icons generated successfully!');
