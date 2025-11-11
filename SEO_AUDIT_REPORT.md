# SEO Audit Report - Saffron The Indian Kitchen

## 1. Audit Scope
- Pages reviewed: `index.html`, `contact.html`
- Current technical files: no `robots.txt`, no `sitemap.xml`
- Supporting assets reviewed: JSON-LD scripts, meta tags, heading structure

## 2. Page-Level Findings

### 2.1 Home Page (`index.html`)
- **Title (present, 95 chars)**: exceeds recommended 50-60 characters; contains primary keywords but could be tightened.
- **Meta description (present, 239 chars)**: informative but longer than the 150-160 character guideline.
- **Keywords tag**: present (not harmful but largely ignored by modern search engines).
- **Viewport & charset**: present and correct.
- **Canonical URL**: set to `https://saffrontheindiankitchen.com/` (good).
- **Alternate hreflang**: only `en-us`; acceptable but could be expanded if future locales are added.
- **Open Graph**: comprehensive; includes type, image, and restaurant-specific properties.
- **Twitter Card**: present (summary_large_image) with matching metadata.
- **Structured data**: Restaurant JSON-LD provided, but missing `@id`, `LocalBusiness`/`Organization` extensions, and menu/nutrition specifics. Uses placeholder Google verification code.
- **Heading structure**: three `<h1>` tags inside the hero slider (should be one `<h1>` per page, others demoted to `<h2>`/`<h3>`).
- **Keyword usage**: homepage body copy highlights cuisine, location, delivery options; could add more localized keywords (e.g., “Downtown Nashville Indian food”).
- **Content length**: rich but some sections rely heavily on imagery; consider adding more text around menu highlights.
- **Technical**: references `sitemap.xml` but file not yet present; no `robots.txt`.
- **Local SEO**: phone number formatted with preceding plus sign but link uses `Tel:+6159337786` (missing leading 1). NAP repeated multiple times (good). No review/testimonial markup.

### 2.2 Contact Page (`contact.html`)
- **Title (present, 97 chars)**: descriptive but too long; can be shortened.
- **Meta description (present, 248 chars)**: exceeds recommended length; contains key contact info.
- **Open Graph / Twitter**: basic coverage but missing `og:image:width/height` and custom Twitter handles.
- **Structured data**: none. Should include LocalBusiness/ContactPoint schema mirroring homepage data.
- **Heading structure**: likely duplicate `<h2>` or missing `<h1>` (needs review; currently hero slider removed but ensure one `<h1>`).
- **Content**: includes phone, email, hours and map; consider adding CTA for catering/events plus FAQ for SEO.
- **Technical**: canonical points to contact URL (good). Map scripts load but ensure `loading="lazy"` on iframes for performance.

## 3. Site-Wide Technical Findings
- **robots.txt**: missing. Should allow all and reference sitemap.
- **sitemap.xml**: missing. Need to generate with at least homepage and contact page entries.
- **404 page**: not reviewed; confirm custom 404 for user experience.
- **Performance**: large images recently archived; compression optimization pending (Phase 3 Step 2).
- **Schema coverage**: only Restaurant schema on homepage; needs LocalBusiness, aggregate rating, menu items, social profiles, Google Maps `hasMap`.
- **Internal linking**: top nav anchors present; ensure footer includes cross-links (e.g., “Order Now”, “Menu PDF”).
- **Mobile usability**: responsive but ensure important CTAs near top.
- **Local SEO**: consistent NAP but no embedded Google review badges or instructions for Google Business Profile. Add directions/parking text.

## 4. Immediate Opportunities
1. Shorten and refine title/meta descriptions for both pages.
2. Replace multiple `<h1>` tags with single, descriptive heading per page.
3. Extend structured data with:
   - `@id` references
   - `LocalBusiness` + `Restaurant` combined schema (via `@graph`)
   - `Menu` and `Offer` details for signature dishes
   - `ContactPoint` for phone/email support
4. Create `robots.txt` and `sitemap.xml`; submit to Google Search Console/Bing Webmaster Tools once live.
5. Add FAQ or Q&A section for voice search/local queries (“Where is Saffron located inside Assembly Food Hall?”).
6. Implement breadcrumbs (schema+UI) for multi-page experience as site grows.

## 5. SEO Score Estimate (Current)
- **Homepage**: ~72/100 (strong metadata, needs improved titles, schema completeness, technical files, heading fix).
- **Contact Page**: ~65/100 (metadata too long, no schema, heading review pending, map performance improvements).

## 6. Artifacts to be Delivered in Implementation Phase
- Updated `<head>` sections for both pages with optimized meta/OG/Twitter tags.
- JSON-LD (Restaurant + LocalBusiness) embedded via `<script type="application/ld+json">`.
- New `robots.txt` and `sitemap.xml` at site root.
- `SEO_IMPLEMENTATION_GUIDE.md` describing deployment steps and search console submission.
