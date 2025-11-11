# Image Optimization Plan - Phase 3

**Date:** January 2025  
**Goal:** Reduce total image payload to improve page speed and Core Web Vitals scores while preserving visual quality.

---

## 1. Image Inventory Summary

| Metric | Value |
| --- | --- |
| Total raster/vector assets scanned | 474 |
| High priority (>=500 KB) | 23 files - 21.69 MB |
| Medium priority (200-499 KB) | 26 files - 8.38 MB |
| Low priority (100-199 KB) | 40 files - 5.17 MB |
| Small (<100 KB) | 385 files - 8.17 MB |
| Estimated savings after optimization | **~22.8 MB** (70% reduction on High, 60% on Medium, 50% on Low) |

> Full inventory files: image-inventory.csv, image-inventory-with-category.csv

---

## 2. High Priority Files (>=500 KB)

| Image | Size | Dimensions | Used on | Recommendation |
| --- | --- | --- | --- | --- |
| img/collage1.png | 3.10 MB | 1560x1074 | Not referenced | Remove or convert to WebP 1200px (<=250 KB) |
| img/collage2-bkp.png | 2.17 MB | 1078x1074 | Not referenced | Remove (backup duplicate) |
| img/collage2.png | 1.60 MB | 1078x1074 | Not referenced | Remove or compress to 1200px WebP |
| img/slider/slide9.jpg | 1.41 MB | 4000x2670 | Not referenced | Remove (legacy slider asset) |
| img/dish/Velvet-Taco.jpg | 1.31 MB | 5619x3746 | Not referenced | Archive or resize to 1600px WebP |
| img/slider/assembly_hp_hero.png | 0.92 MB | 1920x999 | Not referenced (JPG already in use) | Delete PNG duplicate |
| img/slider/spices.png, img/spices.png | 0.92 MB | 1920x999 | Not referenced | Delete PNG duplicates (JPEG already in use) |
| img/slider/slide1.png, slide2.png, slide4.png, slide5.png | 0.65-0.82 MB | 1920x999 | Not referenced | Remove unused slider variants |
| css/color/img/slider/*.jpg | 0.53-0.72 MB | 1920x999 | Not referenced | Remove alternative theme assets or compress |
| img/special-dishes-back1.png | 0.68 MB | 1920x731 | Not referenced | Remove or convert to WebP |
| img/dish/bhelpuri.png | 0.59 MB | 800x800 | index.html gallery | Resize to 600px (retina 1200px) & convert to WebP (<=120 KB) |
| img/assembly_hp_hero.png | 0.52 MB | 1035x850 | Not referenced | Remove (hero uses JPG) |
| img/dish/chicken-tandori.jpg | 0.50 MB | 2500x2011 | Not referenced | Remove unused master photo |

**Action:** 22 of 23 "High" assets are unused duplicates/backups. Archiving or deleting them saves ~20 MB immediately. Only img/dish/bhelpuri.png is actively rendered and needs compression.

---

## 3. Medium Priority Files (200-499 KB)

| Image | Size | Dimensions | Used on | Recommendation |
| --- | --- | --- | --- | --- |
| img/paanipuri.jpg | 0.36 MB | 1560x1074 | index.html hero tile | Resize to 1200px, convert to WebP (~140 KB) |
| img/chef/chef2.jpeg | 0.39 MB | 1078x1074 | index.html About section | Resize to 900px, WebP (~120 KB) |
| img/chef/chef.jpeg | 0.34 MB | 1078x1074 | index.html About section | Same as above |
| css/color/img/tasty-menu-back.png | 0.45 MB | 1920x1224 | Alternate color theme | Remove if theme unused, otherwise compress to WebP (~150 KB) |
| img/dish/reshmi.png | 0.36 MB | 800x800 | Dish gallery | Resize to 600px (retina 1200px) & WebP (~110 KB) |
| img/dish/ice-cream.jpeg | 0.38 MB | 800x800 | Dish gallery | Same as above |
| img/paanipuri1.png, img/tomatosoup.png | 0.41-0.49 MB | 512x512 | index_backup.html only | Move to archive or compress to WebP (~60 KB) |
| onts/fontawesome-webfont.svg | 0.36 MB | Vector | Site-wide | Keep (required for icon font) |

---

## 4. Recommended Compression Strategy

| Asset Type | Example | Target Format | Target Width | Target Size Goal |
| --- | --- | --- | --- | --- |
| Hero / Slider | img/slider/assembly_hp_hero.jpg | WebP | 1600 px | <=250 KB |
| Gallery tiles | img/dish/bhelpuri.png | WebP | 600 px (1200 px retina) | <=120 KB |
| About / Chef portraits | img/chef/chef2.jpeg | WebP | 900 px | <=150 KB |
| Backgrounds | css/color/img/tasty-menu-back.png | WebP | 1600 px | <=200 KB |
| Logos / transparency | img/logo.png | PNG (lossless) | Original | <=80 KB |

Guidelines:
- Strip metadata (-strip).
- Use WebP for photographic content (-define webp:method=6 with quality 70-80).
- Retain PNG only when transparency is required.
- Resize to twice the rendered size to support HiDPI/retina displays.

---

## 5. Automation Script

A reusable PowerShell script is available at scripts/optimize-images.ps1.

**Workflow:**
1. Reads image-inventory-with-category.csv.
2. Copies originals to img/_originals.
3. Outputs optimized WebP files to img/optimized with presets:
   - Slider/Hero assets -> 1600px wide @ quality 70.
   - Gallery/Chef images -> 800px wide @ quality 75.
   - Other assets -> 1200px wide @ quality 80.
4. Supports -DryRun to preview commands before execution.

**Prerequisites:** Install ImageMagick (ensure magick is on the PATH with WebP support).

**Usage:**
`powershell
# Preview commands only
.\scripts\optimize-images.ps1 -DryRun

# Execute optimization
.\scripts\optimize-images.ps1
`

Optimized files are written to img/optimized, allowing side-by-side QA before swapping production assets.

---

## 6. Manual Compression Workflow (Alternative)

1. **Backup originals**
   `powershell
   robocopy img img\_originals /E
   `
2. **Convert hero images**
   `powershell
   magick img/slider/assembly_hp_hero.jpg -resize 1600x -strip -quality 70 -define webp:method=6 img/slider/assembly_hp_hero.webp
   `
3. **Convert gallery tiles**
   `powershell
   magick img/dish/bhelpuri.png -resize 1200x -strip -quality 75 -define webp:method=6 img/dish/bhelpuri.webp
   `
4. **Update HTML** to use <picture> with WebP sources (+ JPEG fallback if IE11 support is required).

If command-line tools are unavailable, use Squoosh.app or TinyPNG/TinyJPG for manual compression (ensure consistent quality).

---

## 7. Implementation Checklist

1. [ ] Remove/archive unused high-priority assets (collage files, slider PNGs, legacy dishes).
2. [ ] Run optimize-images.ps1 (DryRun -> execute) and review outputs under img/optimized.
3. [ ] Swap HTML/CSS references to new WebP assets and add srcset for responsive variants.
4. [ ] Validate visual quality across desktop/mobile and run Lighthouse (expect improved LCP).
5. [ ] Purge CDN/cache after deployment.

---

## 8. Expected Impact

- Removing unused assets: **~20 MB saved immediately**.
- Compressing remaining high/medium assets: **~22.8 MB estimated savings** (payload reduced from ~43 MB -> ~20 MB).
- Lighter hero imagery reduces Largest Contentful Paint (LCP) by ~300-400 ms on 4G.
- Smaller dish/gallery tiles improve Time to Interactive on mobile by lowering JavaScript image handling overhead.

---

## 9. Supporting Files

- image-inventory.csv (raw sizes & dimensions)
- image-inventory-with-category.csv (sizes + classification)
- high-priority-image-usage.csv (usage map for >=500 KB assets)
- scripts/optimize-images.ps1 (automation script)

---

**Next Steps:** Confirm which unused assets can be removed, run the optimizer, QA results, and update templates to leverage the new assets.
