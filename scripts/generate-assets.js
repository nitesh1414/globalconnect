import fs from 'fs';
import path from 'path';
import sharp from 'sharp';
import { fileURLToPath } from 'url';

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);
const outputDir = path.join(__dirname, '../assets/images');

if (!fs.existsSync(outputDir)) {
  fs.mkdirSync(outputDir, { recursive: true });
}

function xmlEscape(str) {
  return String(str || '')
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;')
    .replace(/'/g, '&apos;');
}

// Helper to create card SVG
function createServiceSvg(rawTitle, rawSubtitle, rawCategory, iconType, bgGradient) {
  const title = xmlEscape(rawTitle);
  const subtitle = xmlEscape(rawSubtitle);
  const category = xmlEscape(rawCategory);

  const icons = {
    gold: `
      <rect x="260" y="240" width="280" height="90" rx="8" fill="url(#goldGrad)" stroke="#c69214" stroke-width="3"/>
      <polygon points="260,240 280,210 520,210 540,240" fill="#fef08a" opacity="0.9"/>
      <polygon points="540,240 520,210 520,300 540,330" fill="#ca8a04"/>
      <text x="400" y="295" font-family="'Segoe UI', Roboto, sans-serif" font-size="22" font-weight="bold" fill="#713f12" text-anchor="middle" letter-spacing="4">FINE GOLD 999.5</text>
      <text x="400" y="320" font-family="'Segoe UI', Roboto, sans-serif" font-size="14" fill="#854d0e" text-anchor="middle">NET WT 1000g • GLOBAL CONNECT</text>
      
      <rect x="310" y="160" width="220" height="70" rx="6" fill="url(#goldGrad2)" stroke="#c69214" stroke-width="2"/>
      <polygon points="310,160 325,135 515,135 530,160" fill="#fef08a" opacity="0.9"/>
      <polygon points="530,160 515,135 515,205 530,230" fill="#ca8a04"/>
      <text x="420" y="202" font-family="'Segoe UI', Roboto, sans-serif" font-size="16" font-weight="bold" fill="#713f12" text-anchor="middle" letter-spacing="3">GLOBAL ASSAY 1KG</text>
    `,
    sugar: `
      <circle cx="400" cy="240" r="110" fill="#ffffff" opacity="0.1" stroke="#38bdf8" stroke-width="2" stroke-dasharray="8 8"/>
      <polygon points="400,150 480,210 450,300 350,300 320,210" fill="url(#crystalGrad)" stroke="#e0f2fe" stroke-width="3"/>
      <polygon points="400,150 450,230 400,310 350,230" fill="#ffffff" opacity="0.8"/>
      <path d="M 280,340 Q 400,310 520,340 L 540,380 Q 400,360 260,380 Z" fill="#0284c7" opacity="0.8"/>
      <text x="400" y="365" font-family="'Segoe UI', Roboto, sans-serif" font-size="16" font-weight="bold" fill="#ffffff" text-anchor="middle" letter-spacing="2">ICUMSA 45 • REFINED CANE</text>
    `,
    agro: `
      <path d="M 330,350 Q 340,240 400,160 Q 410,240 400,350" fill="none" stroke="#22c55e" stroke-width="6"/>
      <path d="M 340,310 C 310,290 310,260 345,250 C 370,250 370,280 340,310 Z" fill="#4ade80"/>
      <path d="M 390,270 C 420,250 420,220 385,210 C 360,210 360,240 390,270 Z" fill="#4ade80"/>
      <path d="M 355,220 C 330,195 330,170 360,160 C 380,160 380,190 355,220 Z" fill="#86efac"/>
      <path d="M 400,180 C 425,155 425,130 395,120 C 375,120 375,150 400,180 Z" fill="#86efac"/>
      <circle cx="400" cy="115" r="8" fill="#eab308"/>
      <path d="M 440,350 Q 430,260 480,190" fill="none" stroke="#eab308" stroke-width="5"/>
      <path d="M 470,210 C 495,190 495,170 470,160 C 450,160 450,190 470,210 Z" fill="#fde047"/>
    `,
    minerals: `
      <polygon points="300,330 380,190 480,240 430,350" fill="#64748b" stroke="#94a3b8" stroke-width="3"/>
      <polygon points="380,190 480,240 450,150" fill="#94a3b8"/>
      <polygon points="440,340 510,220 580,270 540,360" fill="#b45309" stroke="#d97706" stroke-width="3"/>
      <polygon points="510,220 580,270 560,190" fill="#f59e0b"/>
      <polygon points="230,350 290,260 350,300 310,360" fill="#0f766e" stroke="#14b8a6" stroke-width="3"/>
      <circle cx="400" cy="240" r="140" fill="none" stroke="#f1f5f9" stroke-width="1.5" stroke-dasharray="6 6" opacity="0.3"/>
    `,
    petroleum: `
      <rect x="290" y="210" width="100" height="150" rx="8" fill="#1e293b" stroke="#475569" stroke-width="3"/>
      <line x1="290" y1="250" x2="390" y2="250" stroke="#334155" stroke-width="4"/>
      <line x1="290" y1="310" x2="390" y2="310" stroke="#334155" stroke-width="4"/>
      <ellipse cx="340" cy="210" rx="50" ry="15" fill="#334155" stroke="#475569" stroke-width="3"/>
      
      <rect x="420" y="170" width="120" height="190" rx="10" fill="#0f172a" stroke="#38bdf8" stroke-width="3"/>
      <line x1="420" y1="220" x2="540" y2="220" stroke="#0284c7" stroke-width="4"/>
      <line x1="420" y1="290" x2="540" y2="290" stroke="#0284c7" stroke-width="4"/>
      <ellipse cx="480" cy="170" rx="60" ry="18" fill="#0369a1" stroke="#38bdf8" stroke-width="3"/>
      <path d="M 480,240 C 465,260 460,275 480,285 C 500,275 495,260 480,240 Z" fill="#38bdf8"/>
    `,
    finance: `
      <rect x="260" y="150" width="280" height="180" rx="12" fill="url(#cardGrad)" stroke="#38bdf8" stroke-width="2" filter="url(#shadow)"/>
      <rect x="285" y="180" width="45" height="35" rx="4" fill="#fbbf24"/>
      <line x1="260" y1="240" x2="540" y2="240" stroke="#0369a1" stroke-width="2"/>
      <text x="285" y="275" font-family="'Courier New', monospace" font-size="18" fill="#f8fafc" letter-spacing="4">&#8226;&#8226;&#8226;&#8226; &#8226;&#8226;&#8226;&#8226; &#8226;&#8226;&#8226;&#8226; 8829</text>
      <text x="285" y="305" font-family="'Segoe UI', sans-serif" font-size="12" fill="#94a3b8">TRADE FINANCE INSTRUMENT</text>
      <circle cx="490" cy="295" r="16" fill="#ef4444" opacity="0.8"/>
      <circle cx="510" cy="295" r="16" fill="#f59e0b" opacity="0.8"/>
      <path d="M 400,90 L 415,120 L 450,125 L 425,150 L 430,185 L 400,170 L 370,185 L 375,150 L 350,125 L 385,120 Z" fill="#eab308" opacity="0.9"/>
    `,
    banking: `
      <polygon points="400,130 260,180 540,180" fill="#0284c7" stroke="#38bdf8" stroke-width="2"/>
      <rect x="280" y="180" width="240" height="15" fill="#0369a1"/>
      <rect x="295" y="195" width="25" height="110" fill="#e2e8f0"/>
      <rect x="345" y="195" width="25" height="110" fill="#e2e8f0"/>
      <rect x="430" y="195" width="25" height="110" fill="#e2e8f0"/>
      <rect x="480" y="195" width="25" height="110" fill="#e2e8f0"/>
      <rect x="270" y="305" width="260" height="25" fill="#0369a1" rx="4"/>
      <circle cx="400" cy="250" r="30" fill="#fbbf24" stroke="#d97706" stroke-width="3"/>
      <text x="400" y="260" font-family="'Segoe UI', sans-serif" font-size="28" font-weight="bold" fill="#78350f" text-anchor="middle">$</text>
    `,
    consulting: `
      <circle cx="400" cy="200" r="70" fill="none" stroke="#38bdf8" stroke-width="4"/>
      <circle cx="400" cy="200" r="5" fill="#38bdf8"/>
      <line x1="400" y1="200" x2="400" y2="150" stroke="#f59e0b" stroke-width="4" stroke-linecap="round"/>
      <line x1="400" y1="200" x2="440" y2="200" stroke="#38bdf8" stroke-width="4" stroke-linecap="round"/>
      <path d="M 280,320 L 330,270 L 380,290 L 440,220 L 520,160" fill="none" stroke="#22c55e" stroke-width="5" stroke-linecap="round" stroke-linejoin="round"/>
      <circle cx="520" cy="160" r="7" fill="#22c55e"/>
      <polygon points="510,150 535,155 525,180" fill="#22c55e"/>
      <rect x="260" y="330" width="280" height="15" rx="4" fill="#1e293b"/>
    `,
    strategy: `
      <polygon points="400,120 500,180 500,300 400,360 300,300 300,180" fill="none" stroke="#38bdf8" stroke-width="3"/>
      <polygon points="400,160 460,200 460,280 400,320 340,280 340,200" fill="#0284c7" opacity="0.3"/>
      <circle cx="400" cy="240" r="35" fill="#fbbf24"/>
      <text x="400" y="248" font-family="'Segoe UI', sans-serif" font-size="22" font-weight="bold" fill="#78350f" text-anchor="middle">GC</text>
      <line x1="400" y1="120" x2="400" y2="205" stroke="#38bdf8" stroke-width="2" stroke-dasharray="4 4"/>
      <line x1="500" y1="300" x2="425" y2="260" stroke="#38bdf8" stroke-width="2" stroke-dasharray="4 4"/>
      <line x1="300" y1="300" x2="375" y2="260" stroke="#38bdf8" stroke-width="2" stroke-dasharray="4 4"/>
    `
  };

  const selectedIcon = icons[iconType] || icons.strategy;

  return `
<svg width="800" height="500" viewBox="0 0 800 500" xmlns="http://www.w3.org/2000/svg">
  <defs>
    <linearGradient id="bgGrad" x1="0%" y1="0%" x2="100%" y2="100%">
      ${bgGradient || '<stop offset="0%" stop-color="#0a192f"/><stop offset="100%" stop-color="#112240"/>'}
    </linearGradient>
    <linearGradient id="goldGrad" x1="0%" y1="0%" x2="100%" y2="0%">
      <stop offset="0%" stop-color="#eab308"/><stop offset="50%" stop-color="#fef08a"/><stop offset="100%" stop-color="#ca8a04"/>
    </linearGradient>
    <linearGradient id="goldGrad2" x1="0%" y1="0%" x2="100%" y2="0%">
      <stop offset="0%" stop-color="#facc15"/><stop offset="50%" stop-color="#ffffff"/><stop offset="100%" stop-color="#eab308"/>
    </linearGradient>
    <linearGradient id="crystalGrad" x1="0%" y1="0%" x2="100%" y2="100%">
      <stop offset="0%" stop-color="#ffffff"/><stop offset="100%" stop-color="#7dd3fc"/>
    </linearGradient>
    <linearGradient id="cardGrad" x1="0%" y1="0%" x2="100%" y2="100%">
      <stop offset="0%" stop-color="#1e293b"/><stop offset="100%" stop-color="#0f172a"/>
    </linearGradient>
    <filter id="shadow" x="-10%" y="-10%" width="120%" height="120%">
      <feDropShadow dx="0" dy="8" stdDeviation="12" flood-color="#000000" flood-opacity="0.4"/>
    </filter>
  </defs>

  <!-- Background -->
  <rect width="800" height="500" fill="url(#bgGrad)"/>

  <!-- Geometric Grid Pattern -->
  <g opacity="0.08" stroke="#ffffff" stroke-width="1">
    <line x1="0" y1="100" x2="800" y2="100"/>
    <line x1="0" y1="200" x2="800" y2="200"/>
    <line x1="0" y1="300" x2="800" y2="300"/>
    <line x1="0" y1="400" x2="800" y2="400"/>
    <line x1="100" y1="0" x2="100" y2="500"/>
    <line x1="200" y1="0" x2="200" y2="500"/>
    <line x1="300" y1="0" x2="300" y2="500"/>
    <line x1="400" y1="0" x2="400" y2="500"/>
    <line x1="500" y1="0" x2="500" y2="500"/>
    <line x1="600" y1="0" x2="600" y2="500"/>
    <line x1="700" y1="0" x2="700" y2="500"/>
  </g>

  <!-- Ambient Glow Circles -->
  <circle cx="400" cy="220" r="180" fill="#0284c7" opacity="0.12"/>
  <circle cx="400" cy="220" r="100" fill="#eab308" opacity="0.08"/>

  <!-- Category Tag -->
  <rect x="50" y="45" width="160" height="28" rx="14" fill="#0284c7" opacity="0.3" stroke="#38bdf8" stroke-width="1"/>
  <text x="130" y="64" font-family="'Segoe UI', Roboto, sans-serif" font-size="12" font-weight="bold" fill="#38bdf8" text-anchor="middle" letter-spacing="2">${category.toUpperCase()}</text>

  <!-- Main Illustration -->
  <g>
    ${selectedIcon}
  </g>

  <!-- Title & Subtitle Banner at bottom -->
  <rect x="0" y="400" width="800" height="100" fill="#051329" opacity="0.92"/>
  <line x1="0" y1="400" x2="800" y2="400" stroke="#0284c7" stroke-width="2"/>
  <text x="40" y="445" font-family="'Segoe UI', Roboto, sans-serif" font-size="24" font-weight="bold" fill="#ffffff">${title}</text>
  <text x="40" y="475" font-family="'Segoe UI', Roboto, sans-serif" font-size="14" fill="#94a3b8">${subtitle}</text>
  
  <!-- Global Connect Watermark -->
  <text x="760" y="460" font-family="'Segoe UI', Roboto, sans-serif" font-size="13" font-weight="bold" fill="#ca8a04" text-anchor="end" letter-spacing="1">GLOBAL CONNECT</text>
</svg>
`;
}

// Logo SVG
function createLogoSvg() {
  return `
<svg width="400" height="100" viewBox="0 0 400 100" xmlns="http://www.w3.org/2000/svg">
  <defs>
    <linearGradient id="logoGlobeGrad" x1="0%" y1="0%" x2="100%" y2="100%">
      <stop offset="0%" stop-color="#0284c7"/>
      <stop offset="100%" stop-color="#0369a1"/>
    </linearGradient>
    <linearGradient id="logoGold" x1="0%" y1="0%" x2="100%" y2="0%">
      <stop offset="0%" stop-color="#ca8a04"/>
      <stop offset="50%" stop-color="#eab308"/>
      <stop offset="100%" stop-color="#facc15"/>
    </linearGradient>
  </defs>

  <!-- Globe Icon Symbol -->
  <g transform="translate(10, 10)">
    <!-- Globe sphere -->
    <circle cx="40" cy="40" r="36" fill="url(#logoGlobeGrad)" stroke="#0c4a6e" stroke-width="2"/>
    
    <!-- Longitude / Latitude orbital lines -->
    <ellipse cx="40" cy="40" rx="36" ry="14" fill="none" stroke="#38bdf8" stroke-width="1.8" opacity="0.8"/>
    <ellipse cx="40" cy="40" rx="14" ry="36" fill="none" stroke="#38bdf8" stroke-width="1.8" opacity="0.8"/>
    <line x1="4" y1="40" x2="76" y2="40" stroke="#38bdf8" stroke-width="1.8" opacity="0.8"/>
    <line x1="40" y1="4" x2="40" y2="76" stroke="#38bdf8" stroke-width="1.8" opacity="0.8"/>

    <!-- Orbital Gold Dynamic Ring -->
    <path d="M 12,62 Q 40,15 68,18 Q 72,40 50,65 Q 25,75 12,62 Z" fill="none" stroke="url(#logoGold)" stroke-width="3.5"/>
    
    <!-- Connectivity Nodes -->
    <circle cx="28" cy="28" r="4.5" fill="#facc15" stroke="#713f12" stroke-width="1.2"/>
    <circle cx="54" cy="48" r="4.5" fill="#facc15" stroke="#713f12" stroke-width="1.2"/>
    <line x1="28" y1="28" x2="54" y2="48" stroke="#facc15" stroke-width="2" stroke-dasharray="2 2"/>
  </g>

  <!-- Typography -->
  <text x="100" y="48" font-family="'Montserrat', 'Segoe UI', Arial, sans-serif" font-size="28" font-weight="900" fill="#0b2545" letter-spacing="2.5">GLOBAL<tspan fill="#ca8a04">CONNECT</tspan></text>
  <text x="102" y="70" font-family="'Segoe UI', Arial, sans-serif" font-size="11.5" font-weight="700" fill="#475569" letter-spacing="3.8">SINGAPORE • INDIA • GLOBAL TRADE</text>
</svg>
`;
}

// Hero Banner SVG
function createBannerSvg() {
  return `
<svg width="1920" height="700" viewBox="0 0 1920 700" xmlns="http://www.w3.org/2000/svg">
  <defs>
    <linearGradient id="heroBg" x1="0%" y1="0%" x2="100%" y2="100%">
      <stop offset="0%" stop-color="#061224"/>
      <stop offset="40%" stop-color="#0a2540"/>
      <stop offset="100%" stop-color="#041830"/>
    </linearGradient>
    <linearGradient id="bannerGold" x1="0%" y1="0%" x2="100%" y2="0%">
      <stop offset="0%" stop-color="#ca8a04"/>
      <stop offset="50%" stop-color="#eab308"/>
      <stop offset="100%" stop-color="#facc15"/>
    </linearGradient>
    <linearGradient id="overlayFade" x1="0%" y1="0%" x2="100%" y2="0%">
      <stop offset="0%" stop-color="#061224" stop-opacity="0.95"/>
      <stop offset="50%" stop-color="#0a2540" stop-opacity="0.8"/>
      <stop offset="100%" stop-color="#061224" stop-opacity="0.95"/>
    </linearGradient>
  </defs>

  <!-- Deep Blue Corporate Canvas -->
  <rect width="1920" height="700" fill="url(#heroBg)"/>

  <!-- World Map Grid & Network Arcs -->
  <g opacity="0.15" stroke="#38bdf8" stroke-width="1">
    <circle cx="960" cy="350" r="300" fill="none" stroke-dasharray="6 6"/>
    <circle cx="960" cy="350" r="450" fill="none" stroke-dasharray="8 8"/>
    <circle cx="960" cy="350" r="600" fill="none" stroke-dasharray="10 10"/>
    
    <!-- Major Trade Route Arcs -->
    <path d="M 400,450 Q 800,150 1200,420" fill="none" stroke="#facc15" stroke-width="2.5" opacity="0.7"/>
    <path d="M 600,400 Q 1100,200 1600,380" fill="none" stroke="#38bdf8" stroke-width="2.5" opacity="0.7"/>
    <path d="M 300,350 Q 960,100 1500,480" fill="none" stroke="#38bdf8" stroke-width="2" opacity="0.5"/>
  </g>

  <!-- Singapore & India Hub Nodes -->
  <g transform="translate(900, 320)">
    <!-- Hub Singapore -->
    <circle cx="280" cy="120" r="14" fill="#ca8a04" opacity="0.3"/>
    <circle cx="280" cy="120" r="8" fill="#eab308"/>
    <circle cx="280" cy="120" r="4" fill="#ffffff"/>
    <text x="305" y="125" font-family="'Segoe UI', sans-serif" font-size="16" font-weight="bold" fill="#facc15">SINGAPORE HQ</text>

    <!-- Hub India -->
    <circle cx="100" cy="40" r="14" fill="#0284c7" opacity="0.3"/>
    <circle cx="100" cy="40" r="8" fill="#38bdf8"/>
    <circle cx="100" cy="40" r="4" fill="#ffffff"/>
    <text x="125" y="45" font-family="'Segoe UI', sans-serif" font-size="16" font-weight="bold" fill="#38bdf8">INDIA HQ</text>

    <!-- Connecting Beam -->
    <path d="M 100,40 Q 200,60 280,120" fill="none" stroke="#facc15" stroke-width="3" stroke-dasharray="4 4"/>
  </g>

  <!-- Skyline Silhouette Vector at Bottom -->
  <path d="M 0,700 L 0,620 L 80,620 L 80,580 L 140,580 L 140,640 L 220,640 L 220,550 L 280,550 L 280,600 L 360,600 L 360,520 L 420,520 L 420,650 L 520,650 L 520,570 L 600,570 L 600,610 L 700,610 L 700,530 L 780,530 L 780,640 L 900,640 L 900,500 L 980,500 L 980,620 L 1100,620 L 1100,540 L 1200,540 L 1200,600 L 1320,600 L 1320,510 L 1420,510 L 1420,630 L 1550,630 L 1550,560 L 1650,560 L 1650,610 L 1780,610 L 1780,540 L 1860,540 L 1860,650 L 1920,650 L 1920,700 Z" fill="#030c18" opacity="0.5"/>

  <!-- Vignette overlay -->
  <rect width="1920" height="700" fill="url(#overlayFade)"/>

  <!-- Floating particles / data nodes -->
  <circle cx="200" cy="180" r="3" fill="#38bdf8" opacity="0.6"/>
  <circle cx="450" cy="120" r="4" fill="#eab308" opacity="0.7"/>
  <circle cx="1400" cy="160" r="3" fill="#38bdf8" opacity="0.6"/>
  <circle cx="1700" cy="240" r="4" fill="#eab308" opacity="0.6"/>
  <circle cx="1600" cy="500" r="3" fill="#38bdf8" opacity="0.5"/>
</svg>
`;
}

// Asset list to generate
const assets = [
  // Core Branding
  { name: 'logo.svg', svg: createLogoSvg(), isSvgOnly: true },
  { name: 'banner-hero-1.png', svg: createBannerSvg(), isPng: true },
  { name: 'global-connect-banner-1.png', svg: createBannerSvg(), isPng: true },

  // Home & About
  { name: 'about-growth.jpg', svg: createServiceSvg('Growth Strategies', 'Strategic Fund Raising & Financial Engineering', 'About Us', 'strategy', '<stop offset="0%" stop-color="#0f172a"/><stop offset="100%" stop-color="#1e3a5f"/>') },
  { name: 'trading-home.jpg', svg: createServiceSvg('Global Trading', 'Commodities, Minerals, Agro & Energy Worldwide', 'Trading', 'minerals', '<stop offset="0%" stop-color="#0a192f"/><stop offset="100%" stop-color="#1e293b"/>') },
  { name: 'finance-home.jpg', svg: createServiceSvg('Trade Finance', 'Letters of Credit, SBLC & Priority Banking', 'Finance', 'finance', '<stop offset="0%" stop-color="#022c43"/><stop offset="100%" stop-color="#053f5e"/>') },
  { name: 'consulting-home.jpg', svg: createServiceSvg('Corporate Advisory', 'M&A, Financial Structuring & Debt Syndication', 'Consulting', 'consulting', '<stop offset="0%" stop-color="#0b1d3a"/><stop offset="100%" stop-color="#1c3b57"/>') },

  // Trading Subpages
  { name: 'gold-bullion.jpg', svg: createServiceSvg('Gold Bullion', 'Gold Bars 999.5 & Worldwide Mining Assays', 'Trading', 'gold', '<stop offset="0%" stop-color="#241a04"/><stop offset="100%" stop-color="#451a03"/>') },
  { name: 'gold-bullion-2.jpg', svg: createServiceSvg('Gold Bars & Assays', '100% Assay Returnable Rates at Certified Refineries', 'Gold Bullion', 'gold', '<stop offset="0%" stop-color="#1e1808"/><stop offset="100%" stop-color="#3d2a0a"/>') },
  { name: 'sugar-trade.jpg', svg: createServiceSvg('Sugar Trading', 'ICUMSA 45 White Refined & Raw Cane Sugar', 'Trading', 'sugar', '<stop offset="0%" stop-color="#0c2438"/><stop offset="100%" stop-color="#075985"/>') },
  { name: 'agro-products.jpg', svg: createServiceSvg('Agro Products', 'Grains, Basmati Rice, Pulses, Spices & Edible Oils', 'Trading', 'agro', '<stop offset="0%" stop-color="#052e16"/><stop offset="100%" stop-color="#14532d"/>') },
  { name: 'minerals-metals.jpg', svg: createServiceSvg('Minerals & Metals', 'Iron Ore, Coal, Copper, Aluminum, Ferroalloys & Steel', 'Trading', 'minerals', '<stop offset="0%" stop-color="#1c1917"/><stop offset="100%" stop-color="#292524"/>') },
  { name: 'mica.jpg', svg: createServiceSvg('MICA Minerals', 'Natural Mica Blocks, Flakes & Industrial Sheets', 'Minerals', 'minerals', '<stop offset="0%" stop-color="#1e293b"/><stop offset="100%" stop-color="#334155"/>') },
  { name: 'petroleum-lubricants.jpg', svg: createServiceSvg('Petroleum & Lubricants', 'Mazut M-100 & Gas Oil D-2 from Leading Producers', 'Trading', 'petroleum', '<stop offset="0%" stop-color="#020617"/><stop offset="100%" stop-color="#0f172a"/>') },
  { name: 'mazut-oil.jpg', svg: createServiceSvg('Mazut M-100 Heavy Fuel Oil', 'GOST 10585/75 Specification Furnace Oil', 'Petroleum', 'petroleum', '<stop offset="0%" stop-color="#020617"/><stop offset="100%" stop-color="#1e293b"/>') },
  { name: 'gas-oil.jpg', svg: createServiceSvg('Gas Oil D-2 Diesel', 'Automotive & Industrial Fuel Specifications', 'Petroleum', 'petroleum', '<stop offset="0%" stop-color="#0c1e33"/><stop offset="100%" stop-color="#0369a1"/>') },

  // Finance Subpages
  { name: 'trade-finance.jpg', svg: createServiceSvg('Trade Finance Solutions', 'SBLC, Transferable LC, Documentary & Back to Back LC', 'Finance', 'finance', '<stop offset="0%" stop-color="#041f38"/><stop offset="100%" stop-color="#0c4a6e"/>') },
  { name: 'banking-support.jpg', svg: createServiceSvg('Banking Support', 'Corporate Banking, Multi-Currency Accounts & Cards', 'Finance', 'banking', '<stop offset="0%" stop-color="#0a2540"/><stop offset="100%" stop-color="#0284c7"/>') },
  { name: 'advisory.jpg', svg: createServiceSvg('Trade Advisory', 'International Consulting, Sourcing & Logistics', 'Finance', 'consulting', '<stop offset="0%" stop-color="#0f172a"/><stop offset="100%" stop-color="#1e3a5f"/>') },
  { name: 'standby-lc.jpg', svg: createServiceSvg('Standby Letter of Credit (SBLC)', 'Guaranteed Financial Obligations & Risk Mitigation', 'Trade Finance', 'finance', '<stop offset="0%" stop-color="#0f172a"/><stop offset="100%" stop-color="#0369a1"/>') },
  { name: 'transferable-lc.jpg', svg: createServiceSvg('Transferable Letter of Credit', 'Direct Supplier Settlement for Intermediary Trades', 'Trade Finance', 'finance', '<stop offset="0%" stop-color="#042f2e"/><stop offset="100%" stop-color="#0f766e"/>') },
  { name: 'documentary-lc.jpg', svg: createServiceSvg('Documentary Letter of Credit (L/C)', 'Secured Payment Upon Verified Cargo Delivery', 'Trade Finance', 'finance', '<stop offset="0%" stop-color="#18181b"/><stop offset="100%" stop-color="#27272a"/>') },
  { name: 'backtoback-lc.jpg', svg: createServiceSvg('Back to Back Letter of Credit', 'Collateral-Backed Financing for Confidential Trades', 'Trade Finance', 'finance', '<stop offset="0%" stop-color="#172554"/><stop offset="100%" stop-color="#1e40af"/>') },
  { name: 'corporate-banking.jpg', svg: createServiceSvg('Corporate Banking', '24x7 International Multi-Currency Corporate Accounts', 'Banking', 'banking', '<stop offset="0%" stop-color="#082f49"/><stop offset="100%" stop-color="#0284c7"/>') },
  { name: 'prepaid-card.jpg', svg: createServiceSvg('Prepaid Master Cards', 'Seamless Global Corporate Expenses & Travel', 'Banking', 'finance', '<stop offset="0%" stop-color="#1e1b4b"/><stop offset="100%" stop-color="#3730a3"/>') },
  { name: 'wire-transfer.jpg', svg: createServiceSvg('International Wire Transfers', 'SWIFT Cross-Border Transfers with Full Due Diligence', 'Banking', 'banking', '<stop offset="0%" stop-color="#042f2e"/><stop offset="100%" stop-color="#115e59"/>') },

  // Advisory Sub-elements
  { name: 'contract-manufacturing.jpg', svg: createServiceSvg('Sourcing & Manufacturing', 'Direct Partnerships with Global Manufacturers', 'Advisory', 'strategy', '<stop offset="0%" stop-color="#1c1917"/><stop offset="100%" stop-color="#44403c"/>') },
  { name: 'logistics.jpg', svg: createServiceSvg('Logistical Support', 'Global Warehousing, Tariffs & Freight Supply Chain', 'Advisory', 'minerals', '<stop offset="0%" stop-color="#0c2438"/><stop offset="100%" stop-color="#0284c7"/>') },
  { name: 'shipping.jpg', svg: createServiceSvg('Shipping Assistance', 'Customs Documentation & Global Freight Forwarding', 'Advisory', 'sugar', '<stop offset="0%" stop-color="#064e3b"/><stop offset="100%" stop-color="#047857"/>') },
  { name: 'negotiation.jpg', svg: createServiceSvg('Direct Negotiations', 'Securing Favourable Terms with Global Suppliers', 'Advisory', 'consulting', '<stop offset="0%" stop-color="#311042"/><stop offset="100%" stop-color="#581c87"/>') },

  // Consulting Pillars
  { name: 'financial-structure.jpg', svg: createServiceSvg('Financial Structuring', 'Balancing Working Capital, Risk & Cost of Funds', 'Consulting', 'finance', '<stop offset="0%" stop-color="#0a2540"/><stop offset="100%" stop-color="#1e3a8a"/>') },
  { name: 'international-trade-finance.jpg', svg: createServiceSvg('International Trade Finance', 'Factoring, Forfaiting, Discounting & Open Account', 'Consulting', 'finance', '<stop offset="0%" stop-color="#022c43"/><stop offset="100%" stop-color="#0369a1"/>') },
  { name: 'merge-acquisition.jpg', svg: createServiceSvg('Merger & Acquisition', 'Global Deal Sourcing & Due Diligence Advisory', 'Consulting', 'strategy', '<stop offset="0%" stop-color="#1e293b"/><stop offset="100%" stop-color="#334155"/>') },
  { name: 'off-shore-fund-raising.jpg', svg: createServiceSvg('Off Shore Fund Raising', 'Competitive Cross-Border Debt & Equity Capital', 'Consulting', 'banking', '<stop offset="0%" stop-color="#082f49"/><stop offset="100%" stop-color="#0284c7"/>') },
  { name: 'unlocking-values.jpg', svg: createServiceSvg('Unlocking Values', 'Strategic Growth Plans & Stock Exchange Listings', 'Consulting', 'strategy', '<stop offset="0%" stop-color="#022c43"/><stop offset="100%" stop-color="#15803d"/>') },
  { name: 'debt-syndication.jpg', svg: createServiceSvg('Debt Syndication', 'In-House CAs, MBAs & Engineering Project Financing', 'Consulting', 'consulting', '<stop offset="0%" stop-color="#172554"/><stop offset="100%" stop-color="#1e40af"/>') },
];

async function generateAll() {
  console.log('Generating website assets...');
  
  // Write logo SVG
  fs.writeFileSync(path.join(outputDir, 'logo.svg'), createLogoSvg());
  console.log('Saved logo.svg');

  // Convert logo to PNG
  await sharp(Buffer.from(createLogoSvg()))
    .png()
    .toFile(path.join(outputDir, 'logo.png'));
  console.log('Saved logo.png');

  for (const asset of assets) {
    const ext = path.extname(asset.name).toLowerCase();
    const filePath = path.join(outputDir, asset.name);
    
    if (asset.isSvgOnly) {
      fs.writeFileSync(filePath, asset.svg);
      console.log(`Saved SVG: ${asset.name}`);
      continue;
    }

    const svgBuffer = Buffer.from(asset.svg);
    if (ext === '.png') {
      await sharp(svgBuffer)
        .png({ quality: 90 })
        .toFile(filePath);
      console.log(`Rendered PNG: ${asset.name}`);
    } else {
      await sharp(svgBuffer)
        .jpeg({ quality: 88 })
        .toFile(filePath);
      console.log(`Rendered JPG: ${asset.name}`);
    }
  }

  console.log('All image assets generated successfully!');
}

generateAll().catch(err => {
  console.error('Error generating assets:', err);
  process.exit(1);
});
